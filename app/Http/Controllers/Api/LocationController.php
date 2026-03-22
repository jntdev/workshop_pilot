<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bike;
use App\Models\BikeCategory;
use App\Models\BikeSize;
use App\Models\Reservation;
use App\Services\Agenda\AgendaVersioner;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct(
        private AgendaVersioner $agendaVersioner
    ) {}

    /**
     * GET /api/location/version
     * Returns only the current agenda version (lightweight endpoint for polling).
     */
    public function version(): JsonResponse
    {
        return response()->json([
            'version' => $this->agendaVersioner->current(),
        ]);
    }

    /**
     * GET /api/location/full
     * Returns the full agenda payload (bikes, categories, sizes, reservations) + version.
     * Loads all reservations for the current year.
     */
    public function full(): JsonResponse
    {
        return response()->json([
            'version' => $this->agendaVersioner->current(),
            'bikes' => $this->getBikes(),
            'bikeCategories' => $this->getBikeCategories(),
            'bikeSizes' => $this->getBikeSizes(),
            'reservations' => $this->getReservationsForYear(),
        ]);
    }

    /**
     * Get all bikes formatted for the agenda.
     */
    private function getBikes(): array
    {
        return Bike::with(['category', 'size'])->ordered()->get()->map(fn ($bike) => [
            'id' => $bike->id,
            'column_id' => 'bike_'.$bike->id,
            'bike_category_id' => $bike->bike_category_id,
            'bike_size_id' => $bike->bike_size_id,
            'category' => $bike->category ? [
                'id' => $bike->category->id,
                'name' => $bike->category->name,
                'color' => $bike->category->color,
                'has_battery' => $bike->category->has_battery,
                'sort_order' => $bike->category->sort_order,
            ] : null,
            'size' => $bike->size ? [
                'id' => $bike->size->id,
                'name' => $bike->size->name,
                'color' => $bike->size->color,
                'sort_order' => $bike->size->sort_order,
            ] : null,
            'frame_type' => $bike->frame_type,
            'model' => $bike->model,
            'battery_type' => $bike->battery_type,
            'name' => $bike->name,
            'status' => $bike->status,
            'notes' => $bike->notes,
        ])->toArray();
    }

    /**
     * Get all bike categories formatted for the agenda.
     */
    private function getBikeCategories(): array
    {
        return BikeCategory::ordered()->get()->map(fn ($cat) => [
            'id' => $cat->id,
            'name' => $cat->name,
            'color' => $cat->color,
            'has_battery' => $cat->has_battery,
            'sort_order' => $cat->sort_order,
        ])->toArray();
    }

    /**
     * Get all bike sizes formatted for the agenda.
     */
    private function getBikeSizes(): array
    {
        return BikeSize::ordered()->get()->map(fn ($size) => [
            'id' => $size->id,
            'name' => $size->name,
            'color' => $size->color,
            'sort_order' => $size->sort_order,
        ])->toArray();
    }

    /**
     * Get all reservations for the current year, formatted for the agenda.
     */
    private function getReservationsForYear(): array
    {
        $startYear = now()->startOfYear();
        $endYear = now()->endOfYear();

        return Reservation::with(['client', 'items', 'payments'])
            ->where('statut', '!=', 'annule')
            ->where(function ($query) use ($startYear, $endYear) {
                // Réservations dont la période chevauche l'année
                $query->where('date_retour', '>=', $startYear)
                    ->where('date_reservation', '<=', $endYear);
            })
            ->orderBy('date_reservation')
            ->get()
            ->map(fn ($r) => $this->formatReservationFull($r))
            ->toArray();
    }

    /**
     * Format a single reservation for the agenda (full format).
     */
    private function formatReservationFull(Reservation $r): array
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
                'origine_contact' => $r->client->origine_contact,
                'commentaires' => $r->client->commentaires,
                'avantage_type' => $r->client->avantage_type,
                'avantage_valeur' => $r->client->avantage_valeur,
                'avantage_expiration' => $r->client->avantage_expiration,
            ] : null,
            'date_contact' => $r->date_contact?->format('Y-m-d\TH:i'),
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
            'prix_total_ttc' => $r->prix_total_ttc,
            'acompte_demande' => $r->acompte_demande,
            'acompte_montant' => $r->acompte_montant,
            'acompte_paye_le' => $r->acompte_paye_le?->format('Y-m-d'),
            'paiement_final_le' => $r->paiement_final_le?->format('Y-m-d'),
            'statut' => $r->statut,
            'raison_annulation' => $r->raison_annulation,
            'commentaires' => $r->commentaires,
            'color' => $r->color ?? 0,
            'selection' => $r->selection ?? [],
            'items' => $r->items->map(fn ($item) => [
                'bike_type_id' => $item->bike_type_id,
                'quantite' => $item->quantite,
            ])->toArray(),
            'payments' => $r->payments->map(fn ($p) => [
                'id' => $p->id,
                'amount' => $p->amount,
                'method' => $p->method,
                'paid_at' => $p->paid_at->format('Y-m-d\TH:i'),
                'note' => $p->note,
            ])->toArray(),
            'total_paid' => $r->totalPaid(),
            'remaining' => $r->remaining(),
        ];
    }

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
            'version' => $this->agendaVersioner->current(),
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
