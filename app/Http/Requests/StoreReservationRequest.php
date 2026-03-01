<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Client existant OU nouveau client
            'client_id' => ['nullable', 'exists:clients,id'],

            // Nouveau client (si client_id est null)
            'new_client' => ['nullable', 'array', 'required_without:client_id'],
            'new_client.prenom' => ['required_with:new_client', 'string', 'max:255'],
            'new_client.nom' => ['required_with:new_client', 'string', 'max:255'],
            'new_client.telephone' => ['required_with:new_client', 'string', 'max:255'],
            'new_client.email' => ['nullable', 'email', 'max:255', 'unique:clients,email'],
            'new_client.adresse' => ['nullable', 'string'],
            'new_client.origine_contact' => ['nullable', 'string', 'max:255'],
            'new_client.commentaires' => ['nullable', 'string'],
            'new_client.avantage_type' => ['nullable', 'in:aucun,pourcentage,montant'],
            'new_client.avantage_valeur' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'new_client.avantage_expiration' => ['nullable', 'date'],

            // Mise à jour client existant (si client_id est fourni)
            'update_client' => ['nullable', 'array'],
            'update_client.prenom' => ['required_with:update_client', 'string', 'max:255'],
            'update_client.nom' => ['required_with:update_client', 'string', 'max:255'],
            'update_client.telephone' => ['required_with:update_client', 'string', 'max:255'],
            'update_client.email' => ['nullable', 'email', 'max:255'],
            'update_client.adresse' => ['nullable', 'string'],
            'update_client.origine_contact' => ['nullable', 'string', 'max:255'],
            'update_client.commentaires' => ['nullable', 'string'],

            // Dates
            'date_contact' => ['required', 'date'],
            'date_reservation' => ['required', 'date'],
            'date_retour' => ['required', 'date', 'after_or_equal:date_reservation'],

            // Logistique livraison
            'livraison_necessaire' => ['required', 'boolean'],
            'adresse_livraison' => ['nullable', 'required_if:livraison_necessaire,true', 'string'],
            'contact_livraison' => ['nullable', 'string', 'max:255'],
            'creneau_livraison' => ['nullable', 'string', 'max:255'],

            // Logistique récupération
            'recuperation_necessaire' => ['required', 'boolean'],
            'adresse_recuperation' => ['nullable', 'required_if:recuperation_necessaire,true', 'string'],
            'contact_recuperation' => ['nullable', 'string', 'max:255'],
            'creneau_recuperation' => ['nullable', 'string', 'max:255'],

            // Finances
            'prix_total_ttc' => ['required', 'numeric', 'min:0'],
            'acompte_demande' => ['required', 'boolean'],
            'acompte_montant' => ['nullable', 'numeric', 'min:0'],
            'acompte_paye_le' => ['nullable', 'date'],
            'paiement_final_le' => ['nullable', 'date'],

            // Statut
            'statut' => ['required', 'in:reserve,en_attente_acompte,en_cours,paye,annule'],
            'raison_annulation' => ['nullable', 'required_if:statut,annule', 'string'],

            // Meta
            'commentaires' => ['nullable', 'string'],

            // Items (vélos)
            'items' => ['required', 'array', 'min:1'],
            'items.*.bike_type_id' => ['required', 'exists:bike_types,id'],
            'items.*.quantite' => ['required', 'integer', 'min:1'],

            // Sélection calendrier (vélos individuels)
            'selection' => ['nullable', 'array'],
            'selection.*.bike_id' => ['required_with:selection', 'string'],
            'selection.*.start_date' => ['required_with:selection', 'date'],
            'selection.*.end_date' => ['required_with:selection', 'date'],
            'selection.*.dates' => ['required_with:selection', 'array'],
            'selection.*.dates.*' => ['date'],
            'selection.*.is_hs' => ['nullable', 'boolean'],

            // Couleur de la réservation (0-29)
            'color' => ['nullable', 'integer', 'min:0', 'max:29'],

            // Paiements
            'payments' => ['nullable', 'array'],
            'payments.*.amount' => ['required', 'numeric', 'min:0.01'],
            'payments.*.method' => ['required', 'in:cb,liquide,cheque,virement,autre'],
            'payments.*.paid_at' => ['required', 'date'],
            'payments.*.note' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.exists' => 'Le client sélectionné n\'existe pas.',

            'new_client.required_without' => 'Un client existant ou un nouveau client est requis.',
            'new_client.prenom.required_with' => 'Le prénom du client est obligatoire.',
            'new_client.nom.required_with' => 'Le nom du client est obligatoire.',
            'new_client.telephone.required_with' => 'Le téléphone du client est obligatoire.',
            'new_client.email.email' => 'L\'email du client doit être valide.',
            'new_client.email.unique' => 'Cette adresse email est déjà utilisée.',

            'update_client.prenom.required_with' => 'Le prénom du client est obligatoire.',
            'update_client.nom.required_with' => 'Le nom du client est obligatoire.',
            'update_client.telephone.required_with' => 'Le téléphone du client est obligatoire.',
            'update_client.email.email' => 'L\'email du client doit être valide.',

            'date_contact.required' => 'La date de contact est obligatoire.',
            'date_reservation.required' => 'La date de réservation est obligatoire.',
            'date_retour.required' => 'La date de retour est obligatoire.',
            'date_retour.after_or_equal' => 'La date de retour doit être égale ou postérieure à la date de réservation.',

            'adresse_livraison.required_if' => 'L\'adresse de livraison est obligatoire si la livraison est nécessaire.',
            'adresse_recuperation.required_if' => 'L\'adresse de récupération est obligatoire si la récupération est nécessaire.',

            'prix_total_ttc.required' => 'Le prix total TTC est obligatoire.',
            'prix_total_ttc.numeric' => 'Le prix total TTC doit être un nombre.',
            'prix_total_ttc.min' => 'Le prix total TTC doit être positif.',

            'acompte_montant.numeric' => 'Le montant de l\'acompte doit être un nombre.',
            'acompte_montant.min' => 'Le montant de l\'acompte doit être positif.',

            'statut.required' => 'Le statut est obligatoire.',
            'statut.in' => 'Le statut doit être : réservé, en attente d\'acompte, en cours, payé ou annulé.',

            'raison_annulation.required_if' => 'La raison d\'annulation est obligatoire si le statut est annulé.',

            'items.required' => 'Au moins un vélo doit être sélectionné.',
            'items.min' => 'Au moins un vélo doit être sélectionné.',
            'items.*.bike_type_id.required' => 'Le type de vélo est obligatoire.',
            'items.*.bike_type_id.exists' => 'Le type de vélo sélectionné n\'existe pas.',
            'items.*.quantite.required' => 'La quantité est obligatoire.',
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
            // Statut en_attente_acompte requiert acompte_demande
            if ($this->input('statut') === 'en_attente_acompte' && ! $this->input('acompte_demande')) {
                $validator->errors()->add('acompte_demande', 'L\'acompte doit être demandé si le statut est "en attente d\'acompte".');
            }

            // Statut paye requiert que total encaissé >= prix_total_ttc
            if ($this->input('statut') === 'paye') {
                $prixTotal = (float) ($this->input('prix_total_ttc') ?? 0);
                $payments = $this->input('payments', []);
                $totalPaiements = array_sum(array_column($payments, 'amount'));

                // Ajouter l'acompte si payé
                $acomptePaye = $this->input('acompte_paye_le') ? (float) ($this->input('acompte_montant') ?? 0) : 0;
                $totalEncaisse = $totalPaiements + $acomptePaye;

                if ($totalEncaisse < $prixTotal) {
                    $manque = number_format($prixTotal - $totalEncaisse, 2, ',', ' ');
                    $validator->errors()->add('statut', "Le statut \"Payé\" requiert un encaissement complet. Il manque {$manque} €.");
                }
            }

            // Avertissement si durée > 30 jours
            $dateReservation = $this->input('date_reservation');
            $dateRetour = $this->input('date_retour');
            if ($dateReservation && $dateRetour) {
                $diff = (new \DateTime($dateReservation))->diff(new \DateTime($dateRetour))->days;
                if ($diff > 30) {
                    $validator->errors()->add('date_retour', 'Attention : la durée de location dépasse 30 jours.');
                }
            }
        });
    }
}
