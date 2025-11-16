<x-layouts.main>
    <x-slot:title>Tableau de bord</x-slot:title>

    <div class="dashboard">
        <h1 class="text-4xl font-semibold mb-8">Tableau de bord</h1>

        <!-- Dashboard Cards -->
        <div class="dashboard-cards grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Clients Card -->
            <a href="{{ route('clients.index') }}" class="dashboard-card">
                <div class="dashboard-card__content">
                    <h2 class="text-2xl font-semibold mb-2">Clients</h2>
                    <p class="text-neutral-600">Gérer vos clients et leurs informations</p>
                </div>
            </a>

            <!-- Atelier Card -->
            <a href="{{ route('atelier.index') }}" class="dashboard-card">
                <div class="dashboard-card__content">
                    <h2 class="text-2xl font-semibold mb-2">Atelier</h2>
                    <p class="text-neutral-600">Réparations, vente et conversion de vélos</p>
                </div>
            </a>

            <!-- Location Card -->
            <a href="{{ route('location.index') }}" class="dashboard-card">
                <div class="dashboard-card__content">
                    <h2 class="text-2xl font-semibold mb-2">Location</h2>
                    <p class="text-neutral-600">Courte et longue durée</p>
                </div>
            </a>
        </div>
    </div>
</x-layouts.main>
