<x-layouts.main>
    <div class="page-header">
        <div class="breadcrumb">
            <a href="{{ route('atelier.index') }}">Atelier</a>
            <span>&gt;</span>
            <a href="{{ route('atelier.quotes.index') }}">Devis</a>
            <span>&gt;</span>
            <a href="{{ route('atelier.quotes.show', $quote) }}">{{ $quote->reference }}</a>
            <span>&gt;</span>
            <span>Modifier</span>
        </div>
        <h1>Modifier le devis {{ $quote->reference }}</h1>
    </div>

    <livewire:atelier.quotes.form :quoteId="$quote->id" />
</x-layouts.main>
