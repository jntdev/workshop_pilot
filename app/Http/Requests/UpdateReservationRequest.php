<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Client (ne peut pas être changé après création)
            'client_id' => ['sometimes', 'exists:clients,id'],

            // Dates
            'date_contact' => ['sometimes', 'date'],
            'date_reservation' => ['sometimes', 'date'],
            'date_retour' => ['sometimes', 'date', 'after_or_equal:date_reservation'],

            // Logistique livraison
            'livraison_necessaire' => ['sometimes', 'boolean'],
            'adresse_livraison' => ['nullable', 'string'],
            'contact_livraison' => ['nullable', 'string', 'max:255'],
            'creneau_livraison' => ['nullable', 'string', 'max:255'],

            // Logistique récupération
            'recuperation_necessaire' => ['sometimes', 'boolean'],
            'adresse_recuperation' => ['nullable', 'string'],
            'contact_recuperation' => ['nullable', 'string', 'max:255'],
            'creneau_recuperation' => ['nullable', 'string', 'max:255'],

            // Finances
            'prix_total_ttc' => ['sometimes', 'numeric', 'min:0'],
            'acompte_demande' => ['sometimes', 'boolean'],
            'acompte_montant' => ['nullable', 'numeric', 'min:0'],
            'acompte_paye_le' => ['nullable', 'date'],
            'paiement_final_le' => ['nullable', 'date'],

            // Statut
            'statut' => ['sometimes', 'in:reserve,en_attente_acompte,en_cours,paye,annule'],
            'raison_annulation' => ['nullable', 'string'],

            // Meta
            'commentaires' => ['nullable', 'string'],

            // Items (vélos)
            'items' => ['sometimes', 'array', 'min:1'],
            'items.*.bike_type_id' => ['required_with:items', 'exists:bike_types,id'],
            'items.*.quantite' => ['required_with:items', 'integer', 'min:1'],

            // Sélection calendrier (vélos individuels avec dates)
            'selection' => ['sometimes', 'array'],
            'selection.*.bike_id' => ['required_with:selection', 'string'],
            'selection.*.dates' => ['required_with:selection', 'array', 'min:1'],
            'selection.*.dates.*' => ['date_format:Y-m-d'],
            'selection.*.is_hs' => ['sometimes', 'boolean'],

            // Couleur de la réservation (0-29)
            'color' => ['sometimes', 'integer', 'min:0', 'max:29'],

            // Paiements
            'payments' => ['sometimes', 'array'],
            'payments.*.amount' => ['required', 'numeric', 'min:0.01'],
            'payments.*.method' => ['required', 'in:cb,liquide,cheque,virement,autre'],
            'payments.*.paid_at' => ['required', 'date'],
            'payments.*.note' => ['nullable', 'string'],

            // Mise à jour des données client
            'update_client' => ['sometimes', 'array'],
            'update_client.prenom' => ['required_with:update_client', 'string', 'max:255'],
            'update_client.nom' => ['required_with:update_client', 'string', 'max:255'],
            'update_client.telephone' => ['required_with:update_client', 'string', 'max:20'],
            'update_client.email' => ['nullable', 'email', 'max:255'],
            'update_client.adresse' => ['nullable', 'string'],
            'update_client.origine_contact' => ['nullable', 'string', 'max:255'],
            'update_client.commentaires' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.exists' => 'Le client sélectionné n\'existe pas.',

            'date_retour.after_or_equal' => 'La date de retour doit être égale ou postérieure à la date de réservation.',

            'prix_total_ttc.numeric' => 'Le prix total TTC doit être un nombre.',
            'prix_total_ttc.min' => 'Le prix total TTC doit être positif.',

            'acompte_montant.numeric' => 'Le montant de l\'acompte doit être un nombre.',
            'acompte_montant.min' => 'Le montant de l\'acompte doit être positif.',

            'statut.in' => 'Le statut doit être : réservé, en attente d\'acompte, en cours, payé ou annulé.',

            'items.min' => 'Au moins un vélo doit être sélectionné.',
            'items.*.bike_type_id.exists' => 'Le type de vélo sélectionné n\'existe pas.',
            'items.*.quantite.min' => 'La quantité doit être d\'au moins 1.',

            'payments.*.amount.required' => 'Le montant du paiement est obligatoire.',
            'payments.*.amount.numeric' => 'Le montant du paiement doit être un nombre.',
            'payments.*.amount.min' => 'Le montant du paiement doit être supérieur à 0.',
            'payments.*.method.required' => 'Le mode de paiement est obligatoire.',
            'payments.*.method.in' => 'Le mode de paiement doit être : CB, Espèces, Chèque, Virement ou Autre.',
            'payments.*.paid_at.required' => 'La date du paiement est obligatoire.',
            'payments.*.paid_at.date' => 'La date du paiement doit être une date valide.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $reservation = $this->route('reservation');

            // Statut en_attente_acompte requiert acompte_demande
            $statut = $this->input('statut', $reservation?->statut);
            $acompteDemande = $this->input('acompte_demande', $reservation?->acompte_demande);

            if ($statut === 'en_attente_acompte' && ! $acompteDemande) {
                $validator->errors()->add('acompte_demande', 'L\'acompte doit être demandé si le statut est "en attente d\'acompte".');
            }

            // Statut paye requiert que total encaissé >= prix_total_ttc
            if ($statut === 'paye') {
                $prixTotal = (float) ($this->input('prix_total_ttc') ?? $reservation?->prix_total_ttc ?? 0);

                // Paiements : soit ceux fournis, soit ceux existants
                if ($this->has('payments')) {
                    $payments = $this->input('payments', []);
                    $totalPaiements = array_sum(array_column($payments, 'amount'));
                } else {
                    $totalPaiements = $reservation ? (float) $reservation->payments()->sum('amount') : 0;
                }

                // Acompte : prendre les valeurs fournies ou existantes
                $acomptePaye = $this->input('acompte_paye_le', $reservation?->acompte_paye_le);
                $acompteMontant = (float) ($this->input('acompte_montant') ?? $reservation?->acompte_montant ?? 0);
                $acompteEncaisse = $acomptePaye ? $acompteMontant : 0;

                $totalEncaisse = $totalPaiements + $acompteEncaisse;

                if ($totalEncaisse < $prixTotal) {
                    $manque = number_format($prixTotal - $totalEncaisse, 2, ',', ' ');
                    $validator->errors()->add('statut', "Le statut \"Payé\" requiert un encaissement complet. Il manque {$manque} €.");
                }
            }

            // Statut annulé requiert raison_annulation
            $raisonAnnulation = $this->input('raison_annulation', $reservation?->raison_annulation);
            if ($statut === 'annule' && ! $raisonAnnulation) {
                $validator->errors()->add('raison_annulation', 'La raison d\'annulation est obligatoire si le statut est "annulé".');
            }

            // Adresse livraison requise si livraison_necessaire
            $livraisonNecessaire = $this->input('livraison_necessaire', $reservation?->livraison_necessaire);
            $adresseLivraison = $this->input('adresse_livraison', $reservation?->adresse_livraison);
            if ($livraisonNecessaire && ! $adresseLivraison) {
                $validator->errors()->add('adresse_livraison', 'L\'adresse de livraison est obligatoire si la livraison est nécessaire.');
            }

            // Adresse récupération requise si recuperation_necessaire
            $recuperationNecessaire = $this->input('recuperation_necessaire', $reservation?->recuperation_necessaire);
            $adresseRecuperation = $this->input('adresse_recuperation', $reservation?->adresse_recuperation);
            if ($recuperationNecessaire && ! $adresseRecuperation) {
                $validator->errors()->add('adresse_recuperation', 'L\'adresse de récupération est obligatoire si la récupération est nécessaire.');
            }
        });
    }
}
