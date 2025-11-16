<div class="clients-list">
    <div class="clients-list__header">
        <div class="clients-list__search-bar">
            <input
                type="text"
                wire:model.live="search"
                placeholder="Rechercher un client..."
                class="clients-list__search-input"
            >
            <a href="{{ route('clients.create') }}" class="btn btn-primary">
                + Créer nouveau client
            </a>
        </div>
    </div>

    <div class="clients-list__grid">
        @forelse($this->filteredClients as $client)
            <a href="{{ route('clients.show', $client->id) }}" class="client-card">
                <div class="client-card__name">{{ $client->prenom }} {{ $client->nom }}</div>
                <div class="client-card__phone">{{ $client->telephone }}</div>
                @if($client->email)
                    <div class="client-card__email">{{ $client->email }}</div>
                @endif
            </a>
        @empty
            <div class="clients-list__empty">
                Aucun client trouvé.
            </div>
        @endforelse
    </div>
</div>
