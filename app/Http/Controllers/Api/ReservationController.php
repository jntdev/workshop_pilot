<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Client;
use App\Models\Reservation;
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
            'created_at' => $reservation->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $reservation->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
