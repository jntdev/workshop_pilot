<?php

namespace App\Http\Controllers\Api;

use App\Enums\QuoteStatus;
use App\Http\Controllers\Controller;
use App\Mail\QuoteMail;
use App\Models\Client;
use App\Models\Quote;
use App\Models\QuoteLine;
use App\Services\Quotes\QuoteCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class QuoteController extends Controller
{
    public function __construct(private QuoteCalculator $calculator) {}

    public function show(Quote $quote): JsonResponse
    {
        $quote->load('client', 'lines');

        return response()->json($this->formatQuote($quote));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateQuoteRequest($request);
        $this->validateLines($validated['lines']);

        $client = $this->resolveClient($request, $validated);

        $totalEstimatedTime = $this->calculateTotalEstimatedTime($validated['lines']);

        $quote = Quote::create([
            'client_id' => $client->id,
            'bike_description' => $validated['bike_description'],
            'reception_comment' => $validated['reception_comment'],
            'remarks' => $validated['remarks'] ?? null,
            'reference' => $this->generateReference(),
            'status' => QuoteStatus::Reception,
            'valid_until' => $validated['valid_until'],
            'discount_type' => $validated['discount_value'] ? $validated['discount_type'] : null,
            'discount_value' => $validated['discount_value'] ?: null,
            'total_ht' => $validated['totals']['total_ht'],
            'total_tva' => $validated['totals']['total_tva'],
            'total_ttc' => $validated['totals']['total_ttc'],
            'margin_total_ht' => $validated['totals']['margin_total_ht'],
            'total_estimated_time_minutes' => $totalEstimatedTime,
            'actual_time_minutes' => $validated['actual_time_minutes'] ?? null,
        ]);

        $this->syncLines($quote, $validated['lines']);

        $quote->load('client', 'lines');

        return response()->json($this->formatQuote($quote), 201);
    }

    public function update(Request $request, Quote $quote): JsonResponse
    {
        if ($quote->isInvoice()) {
            return response()->json(['message' => 'Impossible de modifier une facture.'], 422);
        }

        $validated = $this->validateQuoteRequest($request);
        $this->validateLines($validated['lines'], $quote);

        $client = $this->resolveClient($request, $validated);

        $totalEstimatedTime = $this->calculateTotalEstimatedTime($validated['lines']);

        $quote->update([
            'client_id' => $client->id,
            'bike_description' => $validated['bike_description'],
            'reception_comment' => $validated['reception_comment'],
            'remarks' => $validated['remarks'] ?? null,
            'valid_until' => $validated['valid_until'],
            'discount_type' => $validated['discount_value'] ? $validated['discount_type'] : null,
            'discount_value' => $validated['discount_value'] ?: null,
            'total_ht' => $validated['totals']['total_ht'],
            'total_tva' => $validated['totals']['total_tva'],
            'total_ttc' => $validated['totals']['total_ttc'],
            'margin_total_ht' => $validated['totals']['margin_total_ht'],
            'total_estimated_time_minutes' => $totalEstimatedTime,
            'actual_time_minutes' => $validated['actual_time_minutes'] ?? null,
        ]);

        $this->syncLines($quote, $validated['lines']);

        $quote->load('client', 'lines');

        return response()->json($this->formatQuote($quote));
    }

    public function destroy(Quote $quote): JsonResponse
    {
        if (! $quote->canDelete()) {
            return response()->json(['message' => 'Impossible de supprimer une facture.'], 422);
        }

        $quote->delete();

        return response()->json(null, 204);
    }

    public function convertToInvoice(Quote $quote): JsonResponse
    {
        if ($quote->isInvoice()) {
            return response()->json(['message' => 'Ce document est déjà une facture.'], 422);
        }

        $quote->convertToInvoice();
        $quote->load('client', 'lines');

        return response()->json($this->formatQuote($quote));
    }

    public function updateActualTime(Request $request, Quote $quote): JsonResponse
    {
        $validated = $request->validate([
            'actual_time_minutes' => 'nullable|integer|min:0',
        ]);

        $quote->update([
            'actual_time_minutes' => $validated['actual_time_minutes'],
        ]);

        $quote->load('client', 'lines');

        return response()->json($this->formatQuote($quote));
    }

    public function sendEmail(Request $request, Quote $quote): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $quote->load('client', 'lines');

        try {
            Mail::to($validated['email'])->send(new QuoteMail($quote));

            return response()->json([
                'success' => true,
                'message' => 'Email envoyé avec succès',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function calculateLine(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'purchase_price_ht' => 'required|numeric',
            'tva_rate' => 'required|numeric',
            'calculation_type' => 'required|in:sale_price_ht,sale_price_ttc,margin_amount,margin_rate',
            'value' => 'required|numeric',
            'quantity' => 'nullable|numeric|min:0',
        ]);

        $result = match ($validated['calculation_type']) {
            'sale_price_ht' => $this->calculator->fromSalePriceHt(
                $validated['purchase_price_ht'],
                $validated['value'],
                $validated['tva_rate']
            ),
            'sale_price_ttc' => $this->calculator->fromSalePriceTtc(
                $validated['purchase_price_ht'],
                $validated['value'],
                $validated['tva_rate']
            ),
            'margin_amount' => $this->calculator->fromMarginAmount(
                $validated['purchase_price_ht'],
                $validated['value'],
                $validated['tva_rate']
            ),
            'margin_rate' => $this->calculator->fromMarginRate(
                $validated['purchase_price_ht'],
                $validated['value'],
                $validated['tva_rate']
            ),
        };

        // Calculate line totals if quantity is provided
        $quantity = $validated['quantity'] ?? '1';
        if ((float) $quantity > 0) {
            $lineTotals = $this->calculator->calculateLineTotals(
                $quantity,
                $validated['purchase_price_ht'],
                $result['sale_price_ht'],
                $result['sale_price_ttc'],
                $result['margin_amount_ht']
            );
            $result = array_merge($result, $lineTotals);
        }

        return response()->json($result);
    }

    public function calculateTotals(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lines' => 'required|array',
            'lines.*.sale_price_ht' => 'nullable|numeric',
            'lines.*.sale_price_ttc' => 'nullable|numeric',
            'lines.*.margin_amount_ht' => 'nullable|numeric',
            'lines.*.line_total_ht' => 'nullable|numeric',
            'lines.*.line_total_ttc' => 'nullable|numeric',
            'lines.*.line_margin_ht' => 'nullable|numeric',
            'discount_type' => 'nullable|in:amount,percent',
            'discount_value' => 'nullable|numeric',
        ]);

        $totals = $this->calculator->aggregateTotals($validated['lines']);

        if (! empty($validated['discount_value']) && $validated['discount_value'] != 0) {
            $discounted = $this->calculator->applyDiscount(
                $totals['total_ht'],
                $totals['total_tva'],
                $validated['discount_type'] ?? 'percent',
                $validated['discount_value']
            );

            $totals['total_ht'] = $discounted['total_ht'];
            $totals['total_tva'] = $discounted['total_tva'];
            $totals['total_ttc'] = $discounted['total_ttc'];
        }

        return response()->json($totals);
    }

    public function updateStatus(Request $request, Quote $quote): JsonResponse
    {
        if ($quote->isInvoice()) {
            return response()->json(['message' => 'Impossible de modifier le statut d\'une facture.'], 422);
        }

        $allowedValues = collect(QuoteStatus::quoteStatuses())->map(fn ($s) => $s->value)->join(',');

        $validated = $request->validate([
            'status' => 'required|string|in:'.$allowedValues,
        ]);

        $quote->update(['status' => $validated['status']]);

        return response()->json(['status' => $quote->fresh()->status?->value]);
    }

    protected function validateQuoteRequest(Request $request): array
    {
        return $request->validate([
            'client_id' => 'nullable|integer|exists:clients,id',
            'client_prenom' => 'required|string|max:255',
            'client_nom' => 'required|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_telephone' => 'nullable|string|max:20',
            'client_adresse' => 'nullable|string|max:500',
            'client_origine_contact' => 'nullable|string|max:255',
            'client_commentaires' => 'nullable|string',
            'client_avantage_type' => 'nullable|in:aucun,pourcentage,montant',
            'client_avantage_valeur' => 'nullable|numeric|min:0',
            'client_avantage_expiration' => 'nullable|date',
            'bike_description' => 'required|string|max:255',
            'reception_comment' => 'required|string',
            'remarks' => 'nullable|string',
            'valid_until' => 'required|date',
            'discount_type' => 'nullable|in:amount,percent',
            'discount_value' => 'nullable|numeric|min:0',
            'lines' => 'present|array',
            'lines.*.title' => 'required|string|max:255',
            'lines.*.reference' => 'nullable|string|max:100',
            'lines.*.quantity' => 'required|numeric|min:0.01',
            'lines.*.purchase_price_ht' => 'required|numeric|min:0',
            'lines.*.sale_price_ht' => 'required|numeric|min:0',
            'lines.*.sale_price_ttc' => 'required|numeric|min:0',
            'lines.*.margin_amount_ht' => 'required|numeric',
            'lines.*.margin_rate' => 'required|numeric',
            'lines.*.tva_rate' => 'required|numeric|min:0',
            'lines.*.line_purchase_ht' => 'nullable|numeric',
            'lines.*.line_margin_ht' => 'nullable|numeric',
            'lines.*.line_total_ht' => 'nullable|numeric',
            'lines.*.line_total_ttc' => 'nullable|numeric',
            'lines.*.estimated_time_minutes' => 'nullable|integer|min:0',
            'lines.*.id' => 'nullable|integer',
            'lines.*.needs_order' => 'boolean',
            'lines.*.ordered_at' => 'nullable|date',
            'lines.*.received_at' => 'nullable|date',
            'actual_time_minutes' => 'nullable|integer|min:0',
            'totals' => 'required|array',
            'totals.total_ht' => 'required|numeric',
            'totals.total_tva' => 'required|numeric',
            'totals.total_ttc' => 'required|numeric',
            'totals.margin_total_ht' => 'required|numeric',
        ]);
    }

    protected function resolveClient(Request $request, array $validated): Client
    {
        $clientData = [
            'prenom' => $validated['client_prenom'],
            'nom' => $validated['client_nom'],
            'email' => $validated['client_email'],
            'telephone' => $validated['client_telephone'],
            'adresse' => $validated['client_adresse'],
            'origine_contact' => $validated['client_origine_contact'] ?? null,
            'commentaires' => $validated['client_commentaires'] ?? null,
            'avantage_type' => $validated['client_avantage_type'] ?? 'aucun',
            'avantage_valeur' => $validated['client_avantage_valeur'] ?? 0,
            'avantage_expiration' => $validated['client_avantage_expiration'] ?? null,
        ];

        if ($validated['client_id']) {
            $client = Client::findOrFail($validated['client_id']);

            if ($this->hasClientDataChanged($client, $clientData)) {
                $client->update($clientData);
            }

            return $client;
        }

        return Client::create($clientData);
    }

    protected function hasClientDataChanged(Client $client, array $currentData): bool
    {
        foreach ($currentData as $key => $value) {
            $dbValue = $client->{$key} ?? '';
            $currentValue = $value ?? '';

            if ($currentValue !== $dbValue) {
                return true;
            }
        }

        return false;
    }

    protected function validateLines(array $lines, ?Quote $quote = null): void
    {
        $errors = [];

        $existingIds = $quote
            ? $quote->lines()->pluck('id')->all()
            : [];

        foreach ($lines as $index => $line) {
            if (! empty($line['id']) && $quote && ! in_array($line['id'], $existingIds)) {
                $errors["lines.{$index}.id"] = ["La ligne {$line['id']} n'appartient pas à ce devis."];
            }

            if (! empty($line['needs_order']) && empty($line['reference'])) {
                $errors["lines.{$index}.reference"] = ['La référence est obligatoire pour les lignes à commander.'];
            }
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    protected function syncLines(Quote $quote, array $lines): void
    {
        $incomingIds = collect($lines)->pluck('id')->filter()->values();

        $quote->lines()->whereNotIn('id', $incomingIds)->delete();

        foreach ($lines as $index => $lineData) {
            $lineTotals = isset($lineData['line_total_ht'])
                ? [
                    'line_purchase_ht' => $lineData['line_purchase_ht'] ?? null,
                    'line_margin_ht' => $lineData['line_margin_ht'] ?? null,
                    'line_total_ht' => $lineData['line_total_ht'] ?? null,
                    'line_total_ttc' => $lineData['line_total_ttc'] ?? null,
                ]
                : $this->calculator->calculateLineTotals(
                    $lineData['quantity'],
                    $lineData['purchase_price_ht'],
                    $lineData['sale_price_ht'],
                    $lineData['sale_price_ttc'],
                    $lineData['margin_amount_ht']
                );

            $attributes = [
                'quote_id' => $quote->id,
                'title' => $lineData['title'],
                'reference' => $lineData['reference'] ?? null,
                'quantity' => $lineData['quantity'],
                'purchase_price_ht' => $lineData['purchase_price_ht'],
                'sale_price_ht' => $lineData['sale_price_ht'],
                'sale_price_ttc' => $lineData['sale_price_ttc'],
                'margin_amount_ht' => $lineData['margin_amount_ht'],
                'margin_rate' => $lineData['margin_rate'],
                'tva_rate' => $lineData['tva_rate'],
                'line_purchase_ht' => $lineTotals['line_purchase_ht'],
                'line_margin_ht' => $lineTotals['line_margin_ht'],
                'line_total_ht' => $lineTotals['line_total_ht'],
                'line_total_ttc' => $lineTotals['line_total_ttc'],
                'position' => $index,
                'estimated_time_minutes' => $lineData['estimated_time_minutes'] ?? null,
                'needs_order' => $lineData['needs_order'] ?? false,
                'ordered_at' => isset($lineData['ordered_at']) ? \Carbon\Carbon::parse($lineData['ordered_at']) : null,
                'received_at' => isset($lineData['received_at']) ? \Carbon\Carbon::parse($lineData['received_at']) : null,
            ];

            if (! empty($lineData['id'])) {
                $quote->lines()->where('id', $lineData['id'])->update($attributes);
            } else {
                QuoteLine::create($attributes);
            }
        }
    }

    protected function calculateTotalEstimatedTime(array $lines): ?int
    {
        $total = 0;
        $hasAnyTime = false;

        foreach ($lines as $line) {
            if (isset($line['estimated_time_minutes']) && $line['estimated_time_minutes'] !== null) {
                $total += (int) $line['estimated_time_minutes'];
                $hasAnyTime = true;
            }
        }

        return $hasAnyTime ? $total : null;
    }

    protected function generateReference(): string
    {
        $today = now();
        $datePrefix = $today->format('Ymd');

        // Include soft-deleted quotes to avoid duplicate references
        $countToday = Quote::withTrashed()->whereDate('created_at', $today->toDateString())->count();

        return sprintf('%s-%d', $datePrefix, $countToday + 1);
    }

    protected function formatQuote(Quote $quote): array
    {
        return [
            'id' => $quote->id,
            'reference' => $quote->reference,
            'client_id' => $quote->client_id,
            'client' => [
                'id' => $quote->client->id,
                'prenom' => $quote->client->prenom,
                'nom' => $quote->client->nom,
                'email' => $quote->client->email,
                'telephone' => $quote->client->telephone,
                'adresse' => $quote->client->adresse,
            ],
            'bike_description' => $quote->bike_description,
            'reception_comment' => $quote->reception_comment,
            'remarks' => $quote->remarks,
            'valid_until' => $quote->valid_until->format('Y-m-d'),
            'discount_type' => $quote->discount_type,
            'discount_value' => $quote->discount_value,
            'total_ht' => $quote->total_ht,
            'total_tva' => $quote->total_tva,
            'total_ttc' => $quote->total_ttc,
            'margin_total_ht' => $quote->margin_total_ht,
            'total_estimated_time_minutes' => $quote->total_estimated_time_minutes,
            'actual_time_minutes' => $quote->actual_time_minutes,
            'invoiced_at' => $quote->invoiced_at?->toISOString(),
            'created_at' => $quote->created_at->toISOString(),
            'status' => $quote->status?->value,
            'is_invoice' => $quote->isInvoice(),
            'can_edit' => $quote->canEdit(),
            'can_delete' => $quote->canDelete(),
            'lines' => $quote->lines->map(fn (QuoteLine $line) => [
                'id' => $line->id,
                'title' => $line->title,
                'reference' => $line->reference,
                'quantity' => $line->quantity,
                'purchase_price_ht' => $line->purchase_price_ht,
                'sale_price_ht' => $line->sale_price_ht,
                'sale_price_ttc' => $line->sale_price_ttc,
                'margin_amount_ht' => $line->margin_amount_ht,
                'margin_rate' => $line->margin_rate,
                'tva_rate' => $line->tva_rate,
                'line_purchase_ht' => $line->line_purchase_ht,
                'line_margin_ht' => $line->line_margin_ht,
                'line_total_ht' => $line->line_total_ht,
                'line_total_ttc' => $line->line_total_ttc,
                'position' => $line->position,
                'estimated_time_minutes' => $line->estimated_time_minutes,
                'needs_order' => $line->needs_order,
                'ordered_at' => $line->ordered_at?->toISOString(),
                'received_at' => $line->received_at?->toISOString(),
            ])->toArray(),
        ];
    }
}
