<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function planning(Request $request): JsonResponse
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        $targetDate = Carbon::parse($date)->startOfDay();

        // Départs : réservations dont date_reservation = jour sélectionné
        $departures = Reservation::with(['client', 'items.bikeType'])
            ->where('statut', '!=', 'annule')
            ->whereDate('date_reservation', $targetDate)
            ->orderBy('livraison_necessaire', 'desc')
            ->orderBy('creneau_livraison')
            ->get()
            ->map(fn (Reservation $r) => $this->formatReservation($r));

        // Retours : réservations dont date_retour = jour sélectionné
        $returns = Reservation::with(['client', 'items.bikeType'])
            ->where('statut', '!=', 'annule')
            ->whereDate('date_retour', $targetDate)
            ->orderBy('recuperation_necessaire', 'desc')
            ->orderBy('creneau_recuperation')
            ->get()
            ->map(fn (Reservation $r) => $this->formatReservation($r));

        return response()->json([
            'departures' => $departures,
            'returns' => $returns,
        ]);
    }

    private function formatReservation(Reservation $r): array
    {
        return [
            'id' => $r->id,
            'client_id' => $r->client_id,
            'client_name' => $r->client ? "{$r->client->prenom} {$r->client->nom}" : 'Client inconnu',
            'client' => $r->client ? [
                'id' => $r->client->id,
                'prenom' => $r->client->prenom,
                'nom' => $r->client->nom,
                'email' => $r->client->email,
                'telephone' => $r->client->telephone,
                'adresse' => $r->client->adresse,
            ] : null,
            'date_reservation' => $r->date_reservation->format('Y-m-d'),
            'date_retour' => $r->date_retour->format('Y-m-d'),
            'livraison_necessaire' => $r->livraison_necessaire,
            'adresse_livraison' => $r->adresse_livraison,
            'contact_livraison' => $r->contact_livraison,
            'creneau_livraison' => $r->creneau_livraison,
            'recuperation_necessaire' => $r->recuperation_necessaire,
            'adresse_recuperation' => $r->adresse_recuperation,
            'contact_recuperation' => $r->contact_recuperation,
            'creneau_recuperation' => $r->creneau_recuperation,
            'acompte_demande' => $r->acompte_demande,
            'acompte_paye_le' => $r->acompte_paye_le?->format('Y-m-d'),
            'statut' => $r->statut,
            'commentaires' => $r->commentaires,
            'color' => $r->color ?? 0,
            'items' => $r->items->map(fn ($item) => [
                'bike_type_id' => $item->bike_type_id,
                'quantite' => $item->quantite,
                'bike_type' => $item->bikeType ? [
                    'id' => $item->bikeType->id,
                    'label' => $item->bikeType->label,
                    'category' => $item->bikeType->category,
                    'size' => $item->bikeType->size,
                    'frame_type' => $item->bikeType->frame_type,
                ] : null,
            ])->toArray(),
        ];
    }
}
