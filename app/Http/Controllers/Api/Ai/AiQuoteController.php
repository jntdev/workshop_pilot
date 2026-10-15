<?php

namespace App\Http\Controllers\Api\Ai;

use App\Enums\QuoteStatus;
use App\Http\Controllers\Api\QuoteController;
use App\Models\Client;
use App\Models\Quote;
use App\Services\Quotes\QuoteCalculator;
use App\Services\Quotes\QuoteMerger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Accès simplifié aux devis pour l'API AI : recherche, et création à partir de
 * lignes simples (les tarifs, marges et totaux sont calculés côté serveur).
 */
class AiQuoteController extends QuoteController
{
    private const DEFAULT_TVA_RATE = '20';

    private const DEFAULT_VALIDITY_DAYS = 30;

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
