<?php

namespace App\Http\Controllers\Api\Ai;

use App\Http\Controllers\Controller;
use App\Models\Bike;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Locations pour l'API AI, en lecture seule : recherche des réservations (client, période, statut)
 * et détail d'une réservation (vélos, paiements, contrat), par exemple pour préparer une facture.
 */
class AiReservationController extends Controller
{
    /**
     * Recherche par client (nom, prénom, téléphone, e-mail) ou commentaire, statut, et période :
     * une réservation est retenue si elle chevauche la période [from, to].
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'client_id' => ['nullable', 'integer'],
            'statut' => ['nullable', 'string', 'in:reserve,en_attente_acompte,en_cours,paye,annule'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'order' => ['nullable', 'in:asc,desc'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:200'],
        ]);

        $query = Reservation::query()
            ->with(['client', 'items.bikeType', 'payments'])
            ->orderBy('date_reservation', $validated['order'] ?? 'desc')
            ->orderBy('id');

        if (! empty($validated['client_id'])) {
            $query->where('client_id', $validated['client_id']);
        }

        if (! empty($validated['statut'])) {
            $query->where('statut', $validated['statut']);
        }

        if (! empty($validated['from'])) {
            $query->where(fn (Builder $q) => $q->where('date_retour', '>=', $validated['from'])
                ->orWhere(fn (Builder $q) => $q->whereNull('date_retour')->where('date_reservation', '>=', $validated['from'])));
        }

        if (! empty($validated['to'])) {
            $query->where('date_reservation', '<=', $validated['to']);
        }

        if (! empty($validated['search'])) {
            $term = '%'.strtolower($validated['search']).'%';
            $query->where(function (Builder $q) use ($term) {
                $q->whereRaw('LOWER(commentaires) LIKE ?', [$term])
                    ->orWhereHas('client', function (Builder $c) use ($term) {
                        $c->whereRaw('LOWER(prenom) LIKE ?', [$term])
                            ->orWhereRaw('LOWER(nom) LIKE ?', [$term])
                            ->orWhereRaw('LOWER(telephone) LIKE ?', [$term])
                            ->orWhereRaw('LOWER(email) LIKE ?', [$term]);
                    });
            });
        }

        $reservations = $query->limit($validated['limit'] ?? 50)->get()
            ->map(fn (Reservation $reservation) => $this->summary($reservation));

        return response()->json($reservations);
    }

    /**
     * Détail d'une réservation : résumé, livraison et récupération, vélos attribués, paiements, contrat.
     * Chargée ici (et non par liaison de route) pour que le jeton soit vérifié avant toute recherche.
     */
    public function show(string $id): JsonResponse
    {
        $reservation = Reservation::with(['client', 'items.bikeType', 'payments', 'latestContract'])->findOrFail($id);

        $selection = collect($reservation->selection ?? []);
        $bikes = Bike::query()->whereIn('id', $selection->pluck('bike_id')->filter()->all())->get()->keyBy('id');
        $contract = $reservation->latestContract;

        return response()->json(array_merge($this->summary($reservation), [
            'date_contact' => $reservation->date_contact?->format('Y-m-d H:i'),
            'livraison' => $reservation->livraison_necessaire ? [
                'adresse' => $reservation->adresse_livraison,
                'contact' => $reservation->contact_livraison,
                'creneau' => $reservation->creneau_livraison,
            ] : null,
            'recuperation' => $reservation->recuperation_necessaire ? [
                'adresse' => $reservation->adresse_recuperation,
                'contact' => $reservation->contact_recuperation,
                'creneau' => $reservation->creneau_recuperation,
            ] : null,
            'velos_attribues' => $selection->map(fn (array $choix) => [
                'bike_id' => $choix['bike_id'] ?? null,
                'nom' => $bikes->get($choix['bike_id'] ?? null)?->name,
                'modele' => $bikes->get($choix['bike_id'] ?? null)?->model,
                'du' => $choix['start_date'] ?? null,
                'au' => $choix['end_date'] ?? null,
            ])->values(),
            'paiements' => $reservation->payments->map(fn ($payment) => [
                'montant' => $payment->amount,
                'moyen' => $payment->method,
                'le' => $payment->paid_at?->format('Y-m-d'),
                'note' => $payment->note,
            ])->values(),
            'contrat' => $contract ? [
                'signe_le' => $contract->signed_at?->format('Y-m-d H:i'),
                'signataire' => $contract->signer_name,
                'caution' => $contract->caution_amount,
                'accessoires' => $contract->accessories,
                'retour_prevu' => $contract->return_time_text,
            ] : null,
        ]));
    }

    /**
     * @return array<string, mixed>
     */
    private function summary(Reservation $reservation): array
    {
        return [
            'id' => $reservation->id,
            'client_id' => $reservation->client_id,
            'client_name' => $reservation->client ? trim($reservation->client->prenom.' '.$reservation->client->nom) : null,
            'date_reservation' => $reservation->date_reservation?->format('Y-m-d'),
            'date_recuperation' => $reservation->date_recuperation?->format('Y-m-d'),
            'date_retour' => $reservation->date_retour?->format('Y-m-d'),
            'statut' => $reservation->statut,
            'raison_annulation' => $reservation->raison_annulation,
            'commentaires' => $reservation->commentaires,
            'velos' => $reservation->items->map(fn ($item) => [
                'type' => $item->bikeType?->label,
                'categorie' => $item->bikeType?->category,
                'taille' => $item->bikeType?->size,
                'quantite' => $item->quantite,
            ])->values(),
            'livraison_necessaire' => $reservation->livraison_necessaire,
            'recuperation_necessaire' => $reservation->recuperation_necessaire,
            'prix_total_ttc' => $reservation->prix_total_ttc,
            'acompte_montant' => $reservation->acompte_paye_le ? $reservation->acompte_montant : null,
            'acompte_paye_le' => $reservation->acompte_paye_le?->format('Y-m-d'),
            'paiement_final_le' => $reservation->paiement_final_le?->format('Y-m-d'),
            'total_paye' => $reservation->totalPaid(),
            'reste_a_payer' => $reservation->remaining(),
        ];
    }
}
