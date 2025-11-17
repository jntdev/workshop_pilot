<div class="client-search">
    <div class="client-search__input-wrapper">
        <input
            type="text"
            wire:model.live.debounce.300ms="searchTerm"
            class="client-search__input"
            placeholder="Rechercher un client (nom, prénom, email, téléphone)..."
        >
    </div>

    @if(count($clients) > 0)
        <div class="client-search__results">
            @foreach($clients as $client)
                <button
                    type="button"
                    wire:click="selectClient({{ $client->id }})"
                    class="client-search__result-item"
                >
                    <div class="client-search__result-name">
                        {{ $client->prenom }} {{ $client->nom }}
                    </div>
                    <div class="client-search__result-details">
                        @if($client->email)
                            <span>{{ $client->email }}</span>
                        @endif
                        @if($client->telephone)
                            <span>{{ $client->telephone }}</span>
                        @endif
                    </div>
                </button>
            @endforeach
        </div>
    @endif
</div>
