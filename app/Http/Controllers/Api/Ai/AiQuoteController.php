<?php

namespace App\Http\Controllers\Api\Ai;

use App\Enums\QuoteStatus;
use App\Http\Controllers\Api\QuoteController;
use App\Models\Client;
use App\Models\Quote;
use App\Models\QuoteLine;
use App\Services\Quotes\QuoteCalculator;
use App\Services\Quotes\QuoteMerger;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Accès simplifié aux devis pour l'API AI : recherche, création à partir de lignes simples,
 * modification de l'en-tête et des lignes d'un devis (pas d'une facture). Les tarifs, marges
 * et totaux sont calculés côté serveur, comme à l'écran. Chaque modification accepte dry_run
 * pour montrer le résultat au gérant avant de l'appliquer.
 */
class AiQuoteController extends QuoteController
{
    private const DEFAULT_TVA_RATE = '20';

    private const DEFAULT_VALIDITY_DAYS = 30;

    /** Champs d'une ligne repris tels quels quand on n'en modifie qu'une partie. */
    private const LINE_FIELDS = [
        'article_id', 'title', 'reference', 'quantity', 'purchase_price_ht', 'sale_price_ht', 'sale_price_ttc',
        'tva_rate', 'estimated_time_minutes', 'needs_order', 'ordered_at', 'received_at',
    ];

    public function __construct(
        private QuoteCalculator $quoteCalculator,
        QuoteMerger $merger,
    ) {
        parent::__construct($quoteCalculator, $merger);
    }

    /**
     * Recherche par référence, client (nom, prénom, téléphone), vélo ou statut.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:'.collect(QuoteStatus::cases())->map(fn ($s) => $s->value)->join(',')],
            'client_id' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Quote::with('client')->notArchived()->orderByDesc('id');

        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (! empty($validated['client_id'])) {
            $query->where('client_id', $validated['client_id']);
        }

        if (! empty($validated['search'])) {
            $term = '%'.strtolower($validated['search']).'%';
            $query->where(function ($q) use ($term) {
                $q->whereRaw('LOWER(reference) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(bike_description) LIKE ?', [$term])
                    ->orWhereHas('client', function ($c) use ($term) {
                        $c->whereRaw('LOWER(prenom) LIKE ?', [$term])
                            ->orWhereRaw('LOWER(nom) LIKE ?', [$term])
                            ->orWhereRaw('LOWER(telephone) LIKE ?', [$term]);
                    });
            });
        }

        $quotes = $query->limit($validated['limit'] ?? 20)->get()->map(fn (Quote $quote) => [
            'id' => $quote->id,
            'reference' => $quote->reference,
            'client_id' => $quote->client_id,
            'client_name' => trim($quote->client->prenom.' '.$quote->client->nom),
            'bike_description' => $quote->bike_description,
            'reception_comment' => $quote->reception_comment,
            'status' => $quote->status?->value,
            'status_label' => $quote->status?->label(),
            'total_ttc' => $quote->total_ttc,
            'created_at' => $quote->created_at->toISOString(),
        ]);

        return response()->json($quotes);
    }

    /**
     * Crée un devis (statut « reception »). Le client est soit existant
     * (client_id), soit créé à partir du bloc client.
     */
    public function storeSimple(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'client_id' => ['nullable', 'integer', 'exists:clients,id', 'required_without:client'],
            'client' => ['nullable', 'array', 'required_without:client_id'],
            'client.prenom' => ['required_with:client', 'string', 'max:255'],
            'client.nom' => ['required_with:client', 'string', 'max:255'],
            'client.telephone' => ['required_with:client', 'string', 'max:255'],
            'client.email' => ['nullable', 'email', 'max:255', 'unique:clients,email'],
            'bike_description' => ['required', 'string', 'max:255'],
            'reception_comment' => ['required', 'string'],
            'remarks' => ['nullable', 'string'],
            'valid_until' => ['nullable', 'date'],
            'lines' => ['nullable', 'array'],
            'lines.*.title' => ['required', 'string', 'max:255'],
            'lines.*.quantity' => ['nullable', 'numeric', 'min:0.01'],
            'lines.*.purchase_price_ht' => ['nullable', 'numeric', 'min:0'],
            'lines.*.sale_price_ht' => ['nullable', 'numeric', 'min:0', 'required_without:lines.*.sale_price_ttc'],
            'lines.*.sale_price_ttc' => ['nullable', 'numeric', 'min:0', 'required_without:lines.*.sale_price_ht'],
            'lines.*.tva_rate' => ['nullable', 'numeric', 'min:0'],
            'lines.*.estimated_time_minutes' => ['nullable', 'integer', 'min:0'],
        ]);

        $lines = collect($validated['lines'] ?? [])
            ->map(fn (array $line) => $this->computeLine($line))
            ->all();

        $totals = $this->quoteCalculator->aggregateTotals($lines);

        $client = isset($validated['client_id'])
            ? Client::findOrFail($validated['client_id'])
            : Client::create(array_merge(
                ['avantage_type' => 'aucun', 'avantage_valeur' => 0],
                $validated['client'],
            ));

        $quote = Quote::create([
            'client_id' => $client->id,
            'bike_description' => $validated['bike_description'],
            'reception_comment' => $validated['reception_comment'],
            'remarks' => $validated['remarks'] ?? null,
            'reference' => $this->generateReference(),
            'status' => QuoteStatus::Reception,
            'valid_until' => $validated['valid_until'] ?? now()->addDays(self::DEFAULT_VALIDITY_DAYS)->toDateString(),
            'total_ht' => $totals['total_ht'],
            'total_tva' => $totals['total_tva'],
            'total_ttc' => $totals['total_ttc'],
            'margin_total_ht' => $totals['margin_total_ht'],
            'total_estimated_time_minutes' => $this->calculateTotalEstimatedTime($lines),
        ]);

        $this->syncLines($quote, $lines);

        $quote->load('client', 'lines');

        return response()->json($this->formatQuote($quote), 201);
    }

    /**
     * Modifie l'en-tête d'un devis : vélo, commentaire de réception, remarques, note du mail, validité, remise.
     * Avec dry_run, renvoie les totaux qui en résulteraient sans rien enregistrer.
     */
    public function updateFields(Request $request, Quote $quote): JsonResponse
    {
        if ($refused = $this->refuseInvoice($quote)) {
            return $refused;
        }

        $fields = $request->validate([
            'bike_description' => ['sometimes', 'required', 'string', 'max:255'],
            'reception_comment' => ['sometimes', 'required', 'string'],
            'remarks' => ['sometimes', 'nullable', 'string'],
            'email_note' => ['sometimes', 'nullable', 'string'],
            'valid_until' => ['sometimes', 'required', 'date'],
            'discount_type' => ['sometimes', 'nullable', 'in:amount,percent'],
            'discount_value' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'dry_run' => ['sometimes', 'boolean'],
        ]);
        unset($fields['dry_run']);

        if ($fields === []) {
            throw ValidationException::withMessages(['fields' => ['Aucun champ à modifier.']]);
        }

        if (array_key_exists('discount_type', $fields) || array_key_exists('discount_value', $fields)) {
            $value = array_key_exists('discount_value', $fields) ? $fields['discount_value'] : $quote->discount_value;
            $type = array_key_exists('discount_type', $fields) ? $fields['discount_type'] : $quote->discount_type;
            $fields['discount_value'] = $value ?: null;
            $fields['discount_type'] = $value ? ($type ?? 'percent') : null;
        }

        return $this->apply(
            $quote,
            $this->currentLines($quote),
            $request->boolean('dry_run'),
            fn () => $quote->update($fields),
            fields: $fields,
        );
    }

    /**
     * Ajoute une ligne en fin de devis. Prix de vente HT ou TTC (le TTC l'emporte si les deux sont donnés),
     * marges et totaux calculés comme à l'écran. Avec dry_run, renvoie la ligne et les totaux sans rien enregistrer.
     */
    public function addLine(Request $request, Quote $quote): JsonResponse
    {
        if ($refused = $this->refuseInvoice($quote)) {
            return $refused;
        }

        $validated = $request->validate($this->lineRules(creating: true));
        unset($validated['dry_run']);

        $line = $this->computeLine($validated);
        $this->requireOrderReference($line);
        $line['position'] = $quote->lines()->exists() ? (int) $quote->lines()->max('position') + 1 : 0;

        return $this->apply(
            $quote,
            [...$this->currentLines($quote), $line],
            $request->boolean('dry_run'),
            fn () => QuoteLine::create(['quote_id' => $quote->id, 'position' => $line['position']] + $this->lineAttributes($line)),
            line: $line,
            created: true,
        );
    }

    /**
     * Modifie une ligne. Seuls les champs envoyés changent ; sans nouveau prix de vente, le prix TTC
     * de la ligne est conservé et la marge recalculée. Avec dry_run, rien n'est enregistré.
     */
    public function updateLine(Request $request, Quote $quote, QuoteLine $line): JsonResponse
    {
        if ($refused = $this->refuseInvoice($quote)) {
            return $refused;
        }

        $validated = $request->validate($this->lineRules(creating: false));
        unset($validated['dry_run']);

        $data = array_merge($line->only(self::LINE_FIELDS), $validated);
        if (isset($validated['sale_price_ht']) && ! isset($validated['sale_price_ttc'])) {
            unset($data['sale_price_ttc']);
        }

        $computed = $this->computeLine($data);
        $this->requireOrderReference($computed);

        $lines = array_map(
            fn (array $current) => $current['id'] === $line->id ? $computed + ['id' => $line->id] : $current,
            $this->currentLines($quote),
        );

        return $this->apply(
            $quote,
            $lines,
            $request->boolean('dry_run'),
            fn () => tap($line)->update($this->lineAttributes($computed)),
            line: $computed + ['id' => $line->id],
        );
    }

    /**
     * Supprime une ligne. Avec dry_run, renvoie les totaux sans la ligne, sans rien enregistrer.
     */
    public function deleteLine(Request $request, Quote $quote, QuoteLine $line): JsonResponse
    {
        if ($refused = $this->refuseInvoice($quote)) {
            return $refused;
        }

        $request->validate(['dry_run' => ['sometimes', 'boolean']]);

        return $this->apply(
            $quote,
            array_values(array_filter($this->currentLines($quote), fn (array $current) => $current['id'] !== $line->id)),
            $request->boolean('dry_run'),
            fn () => $line->delete(),
            line: $line->toArray(),
        );
    }

    /** Une facture ne se modifie plus, ni à l'écran ni par l'API. */
    private function refuseInvoice(Quote $quote): ?JsonResponse
    {
        return $quote->canEdit()
            ? null
            : response()->json(['message' => 'Ce document est une facture : il ne se modifie plus.'], 422);
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function lineRules(bool $creating): array
    {
        return [
            'title' => [$creating ? 'required' : 'sometimes', 'string', 'max:255'],
            'reference' => ['sometimes', 'nullable', 'string', 'max:100'],
            'quantity' => ['sometimes', 'numeric', 'min:0.01'],
            'purchase_price_ht' => ['sometimes', 'numeric', 'min:0'],
            'sale_price_ht' => [$creating ? 'required_without:sale_price_ttc' : 'sometimes', 'numeric', 'min:0'],
            'sale_price_ttc' => [$creating ? 'required_without:sale_price_ht' : 'sometimes', 'numeric', 'min:0'],
            'tva_rate' => ['sometimes', 'numeric', 'min:0'],
            'estimated_time_minutes' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'article_id' => ['sometimes', 'nullable', 'integer', 'exists:articles,id'],
            'needs_order' => ['sometimes', 'boolean'],
            'ordered_at' => ['sometimes', 'nullable', 'date'],
            'received_at' => ['sometimes', 'nullable', 'date'],
            'dry_run' => ['sometimes', 'boolean'],
        ];
    }

    /** Même règle qu'à l'écran : une ligne à commander a une référence. */
    private function requireOrderReference(array $line): void
    {
        if (! empty($line['needs_order']) && empty($line['reference'])) {
            throw ValidationException::withMessages(['reference' => ['La référence est obligatoire pour les lignes à commander.']]);
        }
    }

    /**
     * @param  array<string, mixed>  $line
     * @return array<string, mixed>
     */
    private function lineAttributes(array $line): array
    {
        return [
            'article_id' => $line['article_id'] ?? null,
            'title' => $line['title'],
            'reference' => $line['reference'] ?? null,
            'quantity' => $line['quantity'],
            'purchase_price_ht' => $line['purchase_price_ht'],
            'sale_price_ht' => $line['sale_price_ht'],
            'sale_price_ttc' => $line['sale_price_ttc'],
            'margin_amount_ht' => $line['margin_amount_ht'],
            'margin_rate' => $line['margin_rate'],
            'tva_rate' => $line['tva_rate'],
            'line_purchase_ht' => $line['line_purchase_ht'],
            'line_margin_ht' => $line['line_margin_ht'],
            'line_total_ht' => $line['line_total_ht'],
            'line_total_ttc' => $line['line_total_ttc'],
            'estimated_time_minutes' => $line['estimated_time_minutes'] ?? null,
            'needs_order' => (bool) ($line['needs_order'] ?? false),
            'ordered_at' => $line['ordered_at'] ?? null,
            'received_at' => $line['received_at'] ?? null,
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function currentLines(Quote $quote): array
    {
        return $quote->lines()->get()->map(fn (QuoteLine $line) => $line->toArray())->all();
    }

    /**
     * Totaux du devis pour ces lignes, remise comprise : même calcul que l'enregistrement à l'écran.
     *
     * @param  list<array<string, mixed>>  $lines
     * @return array<string, mixed>
     */
    private function totalsFor(array $lines, ?string $discountType, mixed $discountValue): array
    {
        $totals = $this->quoteCalculator->aggregateTotals($lines);

        if (! empty($discountValue) && (float) $discountValue !== 0.0) {
            $totals = array_merge($totals, $this->quoteCalculator->applyDiscount(
                $totals['total_ht'],
                $totals['total_tva'],
                $discountType ?? 'percent',
                (string) $discountValue,
            ));
        }

        return $totals + ['total_estimated_time_minutes' => $this->calculateTotalEstimatedTime($lines)];
    }

    /**
     * Calcule le résultat d'une modification ; l'enregistre et recalcule les totaux du devis, sauf en dry_run.
     *
     * @param  list<array<string, mixed>>  $lines  lignes du devis après la modification
     * @param  array<string, mixed>  $fields  champs d'en-tête modifiés
     * @param  array<string, mixed>|null  $line  ligne ajoutée, modifiée ou supprimée
     */
    private function apply(Quote $quote, array $lines, bool $dryRun, Closure $change, array $fields = [], ?array $line = null, bool $created = false): JsonResponse
    {
        $before = $quote->only(['total_ht', 'total_tva', 'total_ttc', 'margin_total_ht', 'total_estimated_time_minutes']);

        $after = $this->totalsFor(
            $lines,
            array_key_exists('discount_type', $fields) ? $fields['discount_type'] : $quote->discount_type,
            array_key_exists('discount_value', $fields) ? $fields['discount_value'] : $quote->discount_value,
        );

        if ($dryRun) {
            return response()->json(array_filter([
                'dry_run' => true,
                'fields' => $fields ?: null,
                'line' => $line,
                'totals_before' => $before,
                'totals_after' => $after,
            ], fn ($value) => $value !== null));
        }

        $result = DB::transaction(function () use ($quote, $change) {
            $result = $change();
            $quote->refresh();
            $quote->update($this->totalsFor($this->currentLines($quote), $quote->discount_type, $quote->discount_value));

            return $result;
        });

        $quote->load('client', 'lines');

        return response()->json(array_filter([
            'dry_run' => false,
            'fields' => $fields ?: null,
            'line' => $result instanceof QuoteLine ? $result->fresh()?->toArray() : $line,
            'totals_before' => $before,
            'totals_after' => $quote->only(['total_ht', 'total_tva', 'total_ttc', 'margin_total_ht', 'total_estimated_time_minutes']),
            'quote' => $this->formatQuote($quote),
        ], fn ($value) => $value !== null), $created ? 201 : 200);
    }

    /**
     * Complète une ligne simple (prix de vente HT ou TTC) avec marges et totaux.
     *
     * @param  array<string, mixed>  $line
     * @return array<string, mixed>
     */
    private function computeLine(array $line): array
    {
        $quantity = (string) ($line['quantity'] ?? 1);
        $purchase = (string) ($line['purchase_price_ht'] ?? 0);
        $tva = (string) ($line['tva_rate'] ?? self::DEFAULT_TVA_RATE);

        $prices = isset($line['sale_price_ttc'])
            ? $this->quoteCalculator->fromSalePriceTtc($purchase, (string) $line['sale_price_ttc'], $tva)
            : $this->quoteCalculator->fromSalePriceHt($purchase, (string) $line['sale_price_ht'], $tva);

        return array_merge(
            $line,
            $prices,
            $this->quoteCalculator->calculateLineTotals(
                $quantity,
                $purchase,
                $prices['sale_price_ht'],
                $prices['sale_price_ttc'],
                $prices['margin_amount_ht'],
            ),
            ['quantity' => $quantity, 'purchase_price_ht' => $purchase, 'tva_rate' => $tva],
        );
    }
}
