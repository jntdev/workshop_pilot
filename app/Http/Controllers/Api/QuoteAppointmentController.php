<?php

namespace App\Http\Controllers\Api;

use App\Enums\QuoteStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuoteAppointmentRequest;
use App\Http\Requests\UpdateQuoteAppointmentRequest;
use App\Models\Quote;
use App\Models\QuoteAppointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuoteAppointmentController extends Controller
{
    /** Durée par défaut d'un créneau quand le devis n'a pas de temps estimé. */
    private const DEFAULT_DURATION_MINUTES = 30;

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after:start'],
        ]);

        $appointments = QuoteAppointment::with('quote.client')
            ->whereBetween('starts_at', [$validated['start'], $validated['end']])
            ->orderBy('starts_at')
            ->get()
            ->map(fn (QuoteAppointment $appointment) => $this->formatAppointment($appointment));

        return response()->json($appointments);
    }

    public function unscheduled(): JsonResponse
    {
        $quotes = Quote::with('client')
            ->whereIn('status', QuoteStatus::quoteStatuses())
            ->notArchived()
            ->whereDoesntHave('appointments')
            ->get()
            ->map(fn (Quote $quote) => [
                'id' => $quote->id,
                'reference' => $quote->reference,
                'client_name' => trim($quote->client->prenom.' '.$quote->client->nom),
                'bike_description' => $quote->bike_description,
                'status' => $quote->status?->value,
                'status_label' => $quote->status?->label(),
                'total_estimated_time_minutes' => $quote->total_estimated_time_minutes,
                'default_duration_minutes' => $quote->total_estimated_time_minutes ?? self::DEFAULT_DURATION_MINUTES,
            ])
            ->sortByDesc('id')
            ->values();

        return response()->json($quotes);
    }

    public function store(StoreQuoteAppointmentRequest $request, Quote $quote): JsonResponse
    {
        $appointment = $quote->appointments()->create($request->validated());
        $appointment->load('quote.client');

        return response()->json($this->formatAppointment($appointment), 201);
    }

    public function update(UpdateQuoteAppointmentRequest $request, QuoteAppointment $appointment): JsonResponse
    {
        $appointment->update($request->validated());
        $appointment->load('quote.client');

        return response()->json($this->formatAppointment($appointment));
    }

    public function destroy(QuoteAppointment $appointment): JsonResponse
    {
        $appointment->delete();

        return response()->json(null, 204);
    }

    protected function formatAppointment(QuoteAppointment $appointment): array
    {
        return [
            'id' => $appointment->id,
            'quote_id' => $appointment->quote_id,
            'quote_reference' => $appointment->quote->reference,
            'client_name' => trim($appointment->quote->client->prenom.' '.$appointment->quote->client->nom),
            'bike_description' => $appointment->quote->bike_description,
            'status' => $appointment->quote->status?->value,
            'status_label' => $appointment->quote->status?->label(),
            'starts_at' => $appointment->starts_at->toISOString(),
            'ends_at' => $appointment->ends_at->toISOString(),
            'notes' => $appointment->notes,
        ];
    }
}
