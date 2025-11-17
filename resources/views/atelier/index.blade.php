<x-layouts.main>
    <x-slot:title>Atelier</x-slot:title>

    <x-layouts.chapter title="Atelier">
        <div class="atelier-index">
            <div class="atelier-index__actions">
                <a href="{{ route('atelier.quotes.create') }}" class="atelier-index__btn atelier-index__btn--primary">
                    Nouveau devis
                </a>
                <a href="{{ route('atelier.quotes.index') }}" class="atelier-index__btn atelier-index__btn--secondary">
                    Voir tous les devis
                </a>
            </div>
        </div>
    </x-layouts.chapter>
</x-layouts.main>
