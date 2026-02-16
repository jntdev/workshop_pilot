<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Client;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Reservation::with(['client', 'items.bikeType'])
            ->orderBy('date_reservation', 'desc');

        // Filtre par statut
        if ($request->has('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        // Filtre par client
        if ($request->has('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        // Filtre par période
        if ($request->has('date_from')) {
            $query->where('date_reservation', '>=', $request->input('date_from'));
        }
        if ($request->has('date_to')) {
            $query->where('date_reservation', '<=', $request->input('date_to'));
        }

        $reservations = $query->get()->map(fn (Reservation $reservation) => $this->formatReservation($reservation));

        return response()->json($reservations);
    }

    /**
     * Charge les réservations pour une fenêtre de dates spécifique.
     * Utilisé pour le lazy loading quand l'utilisateur scroll au-delà de la fenêtre initiale.
     */
    public function window(Request $request): JsonResponse
    {
        $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after_or_equal:start'],
        ]);

        $start = Carbon::parse($request->start)->startOfDay();
        $end = Carbon::parse($request->end)->endOfDay();

        $reservations = Reservation::with(['client', 'items.bikeType'])
            ->where('statut', '!=', 'annule')
            ->where(function ($query) use ($start, $end) {
                // Réservations dans la fenêtre demandée
                $query->whereBetween('date_reservation', [$start, $end])
                    // OU réservations commencées avant mais toujours actives dans la fenêtre
                    ->orWhere(function ($q) use ($start) {
                        $q->where('date_reservation', '<', $start)
                            ->where('date_retour', '>=', $start);
                    });
            })
            ->orderBy('date_reservation')
            ->get()
            ->map(fn (Reservation $reservation) => $this->formatReservationForCalendar($reservation));

        return response()->json($reservations);
    }

    /**
     * Format réservation pour l'affichage calendrier (même format que la page /location).
     */
    private function formatReservationForCalendar(Reservation $r): array
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
        ];
    }

    public function show(string $id): JsonResponse
    {
        $reservation = Reservation::with(['client', 'items.bikeType'])->find($id);

        if (! $reservation) {
            return response()->json([
                'message' => 'Réservation non trouvée',
            ], 404);
        }

        return response()->json([
            'data' => $this->formatReservation($reservation),
        ]);
    }

    public function store(StoreReservationRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $items = $validated['items'];
        $newClientData = $validated['new_client'] ?? null;
        $updateClientData = $validated['update_client'] ?? null;
        unset($validated['items'], $validated['new_client'], $validated['update_client']);

        $reservation = DB::transaction(function () use ($validated, $items, $newClientData, $updateClientData) {
            // Créer le client si nouveau
            if ($newClientData) {
                $client = Client::create([
                    'prenom' => $newClientData['prenom'],
                    'nom' => $newClientData['nom'],
                    'telephone' => $newClientData['telephone'],
                    'email' => $newClientData['email'] ?? null,
                    'adresse' => $newClientData['adresse'] ?? null,
                    'origine_contact' => $newClientData['origine_contact'] ?? null,
                    'commentaires' => $newClientData['commentaires'] ?? null,
                    'avantage_type' => $newClientData['avantage_type'] ?? 'aucun',
                    'avantage_valeur' => $newClientData['avantage_valeur'] ?? 0,
                    'avantage_expiration' => $newClientData['avantage_expiration'] ?? null,
                ]);
                $validated['client_id'] = $client->id;
            }

            // Mettre à jour le client existant si demandé
            if ($updateClientData && $validated['client_id']) {
                $client = Client::find($validated['client_id']);
                if ($client) {
                    $client->update([
                        'prenom' => $updateClientData['prenom'],
                        'nom' => $updateClientData['nom'],
                        'telephone' => $updateClientData['telephone'],
                        'email' => $updateClientData['email'] ?? $client->email,
                        'adresse' => $updateClientData['adresse'] ?? $client->adresse,
                        'origine_contact' => $updateClientData['origine_contact'] ?? $client->origine_contact,
                        'commentaires' => $updateClientData['commentaires'] ?? $client->commentaires,
                    ]);
                }
            }

            $reservation = Reservation::create($validated);

            foreach ($items as $item) {
                $reservation->items()->create([
                    'bike_type_id' => $item['bike_type_id'],
                    'quantite' => $item['quantite'],
                ]);
            }

            return $reservation;
        });

        $reservation->load(['client', 'items.bikeType']);

        return response()->json([
            'data' => $this->formatReservation($reservation),
        ], 201);
    }

    public function update(UpdateReservationRequest $request, string $id): JsonResponse
    {
        $reservation = Reservation::find($id);

        if (! $reservation) {
            return response()->json([
                'message' => 'Réservation non trouvée',
            ], 404);
        }

        $validated = $request->validated();
        $items = $validated['items'] ?? null;
        unset($validated['items']);

        DB::transaction(function () use ($reservation, $validated, $items) {
            $reservation->update($validated);

            if ($items !== null) {
                // Supprimer les anciens items et recréer
                $reservation->items()->delete();
                foreach ($items as $item) {
                    $reservation->items()->create([
                        'bike_type_id' => $item['bike_type_id'],
                        'quantite' => $item['quantite'],
                    ]);
                }
            }
        });

        $reservation->load(['client', 'items.bikeType']);

        return response()->json([
            'data' => $this->formatReservation($reservation),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $reservation = Reservation::find($id);

        if (! $reservation) {
            return response()->json([
                'message' => 'Réservation non trouvée',
            ], 404);
        }

        $reservation->delete();

        return response()->json(null, 204);
    }

    private function formatReservation(Reservation $reservation): array
    {
        return [
            'id' => $reservation->id,
            'client_id' => $reservation->client_id,
            'client' => $reservation->client ? [
                'id' => $reservation->client->id,
                'prenom' => $reservation->client->prenom,
                'nom' => $reservation->client->nom,
                'email' => $reservation->client->email,
                'telephone' => $reservation->client->telephone,
            ] : null,
            'date_contact' => $reservation->date_contact?->format('Y-m-d H:i:s'),
            'date_reservation' => $reservation->date_reservation?->format('Y-m-d'),
            'date_retour' => $reservation->date_retour?->format('Y-m-d'),
            'livraison_necessaire' => $reservation->livraison_necessaire,
            'adresse_livraison' => $reservation->adresse_livraison,
            'contact_livraison' => $reservation->contact_livraison,
            'creneau_livraison' => $reservation->creneau_livraison,
            'recuperation_necessaire' => $reservation->recuperation_necessaire,
            'adresse_recuperation' => $reservation->adresse_recuperation,
            'contact_recuperation' => $reservation->contact_recuperation,
            'creneau_recuperation' => $reservation->creneau_recuperation,
            'prix_total_ttc' => $reservation->prix_total_ttc,
            'acompte_demande' => $reservation->acompte_demande,
            'acompte_montant' => $reservation->acompte_montant,
            'acompte_paye_le' => $reservation->acompte_paye_le?->format('Y-m-d'),
            'paiement_final_le' => $reservation->paiement_final_le?->format('Y-m-d'),
            'statut' => $reservation->statut,
            'raison_annulation' => $reservation->raison_annulation,
            'commentaires' => $reservation->commentaires,
            'items' => $reservation->items->map(fn ($item) => [
                'id' => $item->id,
                'bike_type_id' => $item->bike_type_id,
                'bike_type' => $item->bikeType ? [
                    'id' => $item->bikeType->id,
                    'label' => $item->bikeType->label,
                    'category' => $item->bikeType->category,
                    'size' => $item->bikeType->size,
                    'frame_type' => $item->bikeType->frame_type,
                    'stock' => $item->bikeType->stock,
                ] : null,
                'quantite' => $item->quantite,
            ])->toArray(),
            'selection' => $reservation->selection,
            'color' => $reservation->color ?? 0,
            'created_at' => $reservation->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $reservation->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
