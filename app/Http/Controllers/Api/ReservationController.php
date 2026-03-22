<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Mail\AcompteRequestMail;
use App\Models\Client;
use App\Models\Reservation;
use App\Services\Agenda\AgendaVersioner;
use App\Services\Kpis\MonthlyKpiUpdater;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ReservationController extends Controller
{
    public function __construct(
        private MonthlyKpiUpdater $kpiUpdater,
        private AgendaVersioner $agendaVersioner
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = Reservation::with(['client', 'items.bikeType', 'payments'])
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
        $reservation = Reservation::with(['client', 'items.bikeType', 'payments'])->find($id);

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
        $payments = $validated['payments'] ?? [];
        $newClientData = $validated['new_client'] ?? null;
        $updateClientData = $validated['update_client'] ?? null;
        unset($validated['items'], $validated['payments'], $validated['new_client'], $validated['update_client']);

        $reservation = DB::transaction(function () use ($validated, $items, $payments, $newClientData, $updateClientData) {
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

            foreach ($payments as $payment) {
                $reservation->payments()->create($payment);
            }

            return $reservation;
        });

        // Mettre à jour les KPIs Location si des paiements ont été créés
        if (! empty($payments) || $reservation->acompte_paye_le) {
            $this->kpiUpdater->syncReservationPayments($reservation);
        }

        // Incrémenter la version de l'agenda et récupérer la nouvelle version
        $newVersion = $this->agendaVersioner->bump();

        $reservation->load(['client', 'items.bikeType', 'payments']);

        return response()->json([
            'data' => $this->formatReservation($reservation),
            'version' => $newVersion,
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
        $payments = $validated['payments'] ?? null;
        $updateClientData = $validated['update_client'] ?? null;
        unset($validated['items'], $validated['payments'], $validated['update_client']);

        DB::transaction(function () use ($reservation, $validated, $items, $payments, $updateClientData) {
            // Mettre à jour le client si demandé
            if ($updateClientData && $reservation->client_id) {
                $client = Client::find($reservation->client_id);
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

            if ($payments !== null) {
                // Supprimer les anciens paiements et recréer
                $reservation->payments()->delete();
                foreach ($payments as $payment) {
                    $reservation->payments()->create($payment);
                }
            }
        });

        // Mettre à jour les KPIs Location
        $this->kpiUpdater->syncReservationPayments($reservation);

        // Incrémenter la version de l'agenda et récupérer la nouvelle version
        $newVersion = $this->agendaVersioner->bump();

        $reservation->load(['client', 'items.bikeType', 'payments']);

        return response()->json([
            'data' => $this->formatReservation($reservation),
            'version' => $newVersion,
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

        // Incrémenter la version de l'agenda et récupérer la nouvelle version
        $newVersion = $this->agendaVersioner->bump();

        return response()->json([
            'version' => $newVersion,
        ]);
    }

    public function sendAcompteEmail(string $id): JsonResponse
    {
        $reservation = Reservation::with('client')->find($id);

        if (! $reservation) {
            return response()->json([
                'success' => false,
                'error' => 'Réservation non trouvée',
            ], 404);
        }

        if (! $reservation->client) {
            return response()->json([
                'success' => false,
                'error' => 'Aucun client associé à cette réservation',
            ], 422);
        }

        if (! $reservation->client->email) {
            return response()->json([
                'success' => false,
                'error' => 'Le client n\'a pas d\'adresse email',
            ], 422);
        }

        $montantAcompte = $reservation->acompte_montant;
        if (! $montantAcompte || $montantAcompte <= 0) {
            return response()->json([
                'success' => false,
                'error' => 'Le montant de l\'acompte n\'est pas renseigné',
            ], 422);
        }

        Mail::mailer('location')
            ->to($reservation->client->email)
            ->send(new AcompteRequestMail($reservation, $montantAcompte));

        return response()->json([
            'success' => true,
            'message' => "Email envoyé à {$reservation->client->email}",
        ]);
    }

    /**
     * Envoyer un email d'acompte sans réservation sauvegardée.
     * Accepte les données directement dans le body.
     */
    public function sendAcompteEmailDirect(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'client_email' => ['required', 'email'],
            'client_nom' => ['required', 'string'],
            'montant_acompte' => ['required', 'numeric', 'min:0.01'],
            'date_reservation' => ['required', 'date'],
            'date_retour' => ['required', 'date'],
        ]);

        // Créer un objet temporaire pour le mail
        $reservationData = (object) [
            'date_reservation' => $validated['date_reservation'],
            'date_retour' => $validated['date_retour'],
            'client' => (object) [
                'nom' => $validated['client_nom'],
                'prenom' => '',
            ],
        ];

        try {
            Mail::mailer('location')
                ->to($validated['client_email'])
                ->send(new AcompteRequestMail($reservationData, $validated['montant_acompte']));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de l\'envoi : ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "Email envoyé à {$validated['client_email']}",
        ]);
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
            'payments' => $reservation->payments->map(fn ($p) => [
                'id' => $p->id,
                'amount' => $p->amount,
                'method' => $p->method,
                'paid_at' => $p->paid_at->format('Y-m-d\TH:i'),
                'note' => $p->note,
            ])->toArray(),
            'total_paid' => $reservation->totalPaid(),
            'remaining' => $reservation->remaining(),
            'created_at' => $reservation->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $reservation->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
