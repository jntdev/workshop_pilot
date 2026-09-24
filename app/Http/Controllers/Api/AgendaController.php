<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgendaEvent;
use App\Models\QuoteAppointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    /**
     * Liste fusionnée des créneaux devis et des événements libres de l'agenda,
     * triés par heure de début, pour un affichage unique dans la grille.
     */
    public function items(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after:start'],
        ]);

        $appointments = QuoteAppointment::with('quote.client')
            ->whereBetween('starts_at', [$validated['start'], $validated['end']])
            ->get()
            ->map(fn (QuoteAppointment $appointment) => [
                'kind' => 'quote',
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
            ]);

        $events = AgendaEvent::whereBetween('starts_at', [$validated['start'], $validated['end']])
            ->get()
            ->map(fn (AgendaEvent $event) => [
                'kind' => 'event',
                'id' => $event->id,
                'title' => $event->title,
                'detail' => $event->detail,
                'starts_at' => $event->starts_at->toISOString(),
                'ends_at' => $event->ends_at->toISOString(),
            ]);

        $items = $appointments->concat($events)->sortBy('starts_at')->values();

        return response()->json($items);
    }
}
