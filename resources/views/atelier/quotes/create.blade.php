<x-layouts.main>
    <div class="page-header">
        <div class="breadcrumb">
            <a href="{{ route('atelier.index') }}">Atelier</a>
            <span>&gt;</span>
            <a href="{{ route('atelier.quotes.index') }}">Devis</a>
            <span>&gt;</span>
            <span>Nouveau</span>
        </div>
        <h1>Nouveau devis</h1>
    </div>

    <livewire:atelier.quotes.form />
</x-layouts.main>
