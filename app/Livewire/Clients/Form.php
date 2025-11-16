<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;

class Form extends Component
{
    public ?int $clientId = null;

    public $prenom = '';

    public $nom = '';

    public $telephone = '';

    public $email = '';

    public $adresse = '';

    public $origine_contact = '';

    public $commentaires = '';

    public $avantage_type = 'aucun';

    public $avantage_valeur = 0;

    public $avantage_expiration = '';

    public function mount(?int $clientId = null): void
    {
        $this->clientId = $clientId;

        if ($this->clientId) {
            $client = Client::findOrFail($this->clientId);
            $this->prenom = $client->prenom;
            $this->nom = $client->nom;
            $this->telephone = $client->telephone;
            $this->email = $client->email ?? '';
            $this->adresse = $client->adresse ?? '';
            $this->origine_contact = $client->origine_contact ?? '';
            $this->commentaires = $client->commentaires ?? '';
            $this->avantage_type = $client->avantage_type;
            $this->avantage_valeur = $client->avantage_valeur;
            $this->avantage_expiration = $client->avantage_expiration ? $client->avantage_expiration->format('Y-m-d') : '';
        }
    }

    protected function rules(): array
    {
        $rules = [
            'prenom' => ['required', 'string', 'max:255'],
            'nom' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'adresse' => ['nullable', 'string'],
            'origine_contact' => ['nullable', 'string', 'max:255'],
            'commentaires' => ['nullable', 'string'],
            'avantage_type' => ['required', 'in:aucun,pourcentage,montant'],
            'avantage_valeur' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'avantage_expiration' => ['nullable', 'date'],
        ];

        // En mode édition, exclure l'email actuel de la validation unique
        if ($this->clientId) {
            $rules['email'][] = "unique:clients,email,{$this->clientId}";
        } else {
            $rules['email'][] = 'unique:clients,email';
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'prenom.required' => 'Le prénom est obligatoire.',
            'nom.required' => 'Le nom est obligatoire.',
            'telephone.required' => 'Le téléphone est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'avantage_type.in' => 'Le type d\'avantage doit être : aucun, pourcentage ou montant.',
            'avantage_valeur.numeric' => 'La valeur de l\'avantage doit être un nombre.',
            'avantage_valeur.min' => 'La valeur de l\'avantage doit être positive.',
        ];
    }

    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    public function save(): void
    {
        $validated = $this->validate();

        // Validation métier pour avantage
        if ($this->avantage_type === 'pourcentage' && ($this->avantage_valeur <= 0 || $this->avantage_valeur > 100)) {
            $this->addError('avantage_valeur', 'Pour un pourcentage, la valeur doit être entre 0 et 100.');

            return;
        }

        if ($this->avantage_type === 'montant' && $this->avantage_valeur <= 0) {
            $this->addError('avantage_valeur', 'Pour un montant, la valeur doit être supérieure à 0.');

            return;
        }

        if ($this->avantage_type === 'aucun' && $this->avantage_valeur != 0) {
            $this->addError('avantage_valeur', 'Pour aucun avantage, la valeur doit être 0.');

            return;
        }

        // Filtrer les valeurs vides pour les champs nullable
        $data = array_filter($validated, function ($value) {
            return $value !== '' && $value !== null;
        });

        if ($this->clientId) {
            // Mode édition : mise à jour du client existant
            $client = Client::findOrFail($this->clientId);
            $client->update($data);
            $this->dispatch('client-saved');
        } else {
            // Mode création : création d'un nouveau client
            Client::create($data);
            $this->reset();
            $this->dispatch('client-saved');
        }
    }

    public function delete(): void
    {
        if (! $this->clientId) {
            $this->addError('delete', 'Impossible de supprimer un client en mode création.');

            return;
        }

        $client = Client::findOrFail($this->clientId);
        $client->delete();

        $this->dispatch('client-deleted');
    }

    public function render()
    {
        return view('livewire.clients.form');
    }
}
