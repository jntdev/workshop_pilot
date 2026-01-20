<x-layouts.main>
    <x-slot:title>Atelier</x-slot:title>

    <x-layouts.chapter title="Atelier">
        <div class="atelier-index">
            <div class="atelier-index__dashboard">
                <livewire:atelier.dashboard />
            </div>

            <div class="atelier-index__actions">
                <a href="{{ route('atelier.quotes.create') }}" class="atelier-index__btn atelier-index__btn--primary">
                    Nouveau devis
                </a>
            </div>

            <div class="atelier-index__quotes">
                <livewire:atelier.quotes-list />
            </div>
        </div>
    </x-layouts.chapter>
</x-layouts.main>
