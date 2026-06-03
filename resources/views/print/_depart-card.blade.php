@php
    // Noms des vélos individuels depuis la selection
    $bikeNames = collect($r->selection ?? [])
        ->map(function ($sel) use ($bikes) {
            $id = (int) str_replace('bike_', '', $sel['bike_id'] ?? '');
            return $bikes[$id]->name ?? null;
        })
        ->filter()
        ->values();

    // Fallback sur les items (type) si pas de selection
    if ($bikeNames->isEmpty()) {
        $bikeNames = collect($r->items)->map(function ($item) {
            $label = $item->bikeType?->label ?? $item->bike_type_id;
            return $item->quantite > 1 ? $item->quantite.'× '.$label : $label;
        });
    }
@endphp

<div class="card">
    <div class="card__client">{{ $r->client?->prenom }} {{ $r->client?->nom }}</div>

    @if($r->client?->telephone)
        <div class="card__phone">{{ $r->client->telephone }}</div>
    @endif

    <div class="card__bikes">
        @foreach($bikeNames as $name)
            <span class="card__bike">{{ $name }}</span>
        @endforeach
    </div>

    @if($r->commentaires)
        <div class="card__comment">{{ $r->commentaires }}</div>
    @endif

    @if($r->livraison_necessaire && $r->adresse_livraison)
        <div class="card__logistics">
            <div class="card__logistics-address">{{ $r->adresse_livraison }}</div>
            @if($r->contact_livraison)
                <div class="card__logistics-detail">{{ $r->contact_livraison }}</div>
            @endif
            @if($r->creneau_livraison)
                <div class="card__logistics-detail">Créneau : {{ $r->creneau_livraison }}</div>
            @endif
        </div>
    @endif
</div>
