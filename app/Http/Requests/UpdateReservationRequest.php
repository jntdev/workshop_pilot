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
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Statut en_attente_acompte requiert acompte_demande
            $statut = $this->input('statut', $this->route('reservation')?->statut);
            $acompteDemande = $this->input('acompte_demande', $this->route('reservation')?->acompte_demande);

            if ($statut === 'en_attente_acompte' && ! $acompteDemande) {
                $validator->errors()->add('acompte_demande', 'L\'acompte doit être demandé si le statut est "en attente d\'acompte".');
            }

            // Statut payé requiert paiement_final_le
            $paiementFinalLe = $this->input('paiement_final_le', $this->route('reservation')?->paiement_final_le);
            if ($statut === 'paye' && ! $paiementFinalLe) {
                $validator->errors()->add('paiement_final_le', 'La date de paiement final est obligatoire si le statut est "payé".');
            }

            // Statut annulé requiert raison_annulation
            $raisonAnnulation = $this->input('raison_annulation', $this->route('reservation')?->raison_annulation);
            if ($statut === 'annule' && ! $raisonAnnulation) {
                $validator->errors()->add('raison_annulation', 'La raison d\'annulation est obligatoire si le statut est "annulé".');
            }

            // Adresse livraison requise si livraison_necessaire
            $livraisonNecessaire = $this->input('livraison_necessaire', $this->route('reservation')?->livraison_necessaire);
            $adresseLivraison = $this->input('adresse_livraison', $this->route('reservation')?->adresse_livraison);
            if ($livraisonNecessaire && ! $adresseLivraison) {
                $validator->errors()->add('adresse_livraison', 'L\'adresse de livraison est obligatoire si la livraison est nécessaire.');
            }

            // Adresse récupération requise si recuperation_necessaire
            $recuperationNecessaire = $this->input('recuperation_necessaire', $this->route('reservation')?->recuperation_necessaire);
            $adresseRecuperation = $this->input('adresse_recuperation', $this->route('reservation')?->adresse_recuperation);
            if ($recuperationNecessaire && ! $adresseRecuperation) {
                $validator->errors()->add('adresse_recuperation', 'L\'adresse de récupération est obligatoire si la récupération est nécessaire.');
            }
        });
    }
}
