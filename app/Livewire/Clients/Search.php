<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;

class Search extends Component
{
    public string $searchTerm = '';

    public function selectClient(int $clientId): void
    {
        $client = Client::findOrFail($clientId);

        $this->dispatch('clientSelected', [
            'id' => $client->id,
            'prenom' => $client->prenom,
            'nom' => $client->nom,
            'email' => $client->email,
            'telephone' => $client->telephone,
            'adresse' => $client->adresse,
        ]);

        $this->searchTerm = '';
    }

    public function render()
    {
        $clients = [];

        if (strlen($this->searchTerm) >= 2) {
            $clients = Client::query()
                ->where(function ($query) {
                    $query->where('nom', 'like', '%'.$this->searchTerm.'%')
                        ->orWhere('prenom', 'like', '%'.$this->searchTerm.'%')
                        ->orWhere('email', 'like', '%'.$this->searchTerm.'%')
                        ->orWhere('telephone', 'like', '%'.$this->searchTerm.'%');
                })
                ->limit(10)
                ->get();
        }

        return view('livewire.clients.search', [
            'clients' => $clients,
        ]);
    }
}
