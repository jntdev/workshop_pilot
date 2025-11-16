<x-layouts.main>
    <x-slot:title>Fiche client</x-slot:title>

    <x-layouts.chapter title="Fiche client">
        <livewire:clients.form :client-id="$clientId" />
    </x-layouts.chapter>
</x-layouts.main>
