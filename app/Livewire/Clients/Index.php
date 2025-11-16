<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Index extends Component
{
    public string $search = '';

    #[Computed]
    public function filteredClients()
    {
        $query = Client::query()->orderBy('nom')->orderBy('prenom');

        if ($this->search !== '') {
            $search = strtolower($this->search);
            $query->whereRaw('LOWER(prenom) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(nom) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(telephone) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"]);
        }

        return $query->get();
    }

    public function render()
    {
        return view('livewire.clients.index');
    }
}
