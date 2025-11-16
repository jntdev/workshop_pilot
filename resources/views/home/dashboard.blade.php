<x-layouts.main>
    <x-slot:title>Tableau de bord</x-slot:title>

    <div class="dashboard">
        <h1 class="dashboard__title">Tableau de bord</h1>

        <!-- Dashboard Cards -->
        <div class="dashboard__cards">
            <!-- Clients Card -->
            <a href="{{ route('clients.index') }}" class="dashboard-card">
                <div class="dashboard-card__content">
                    <h2 class="dashboard-card__title">Clients</h2>
                    <p class="dashboard-card__description">Gérer vos clients et leurs informations</p>
                </div>
            </a>

            <!-- Atelier Card -->
            <a href="{{ route('atelier.index') }}" class="dashboard-card">
                <div class="dashboard-card__content">
                    <h2 class="dashboard-card__title">Atelier</h2>
                    <p class="dashboard-card__description">Réparations, vente et conversion de vélos</p>
                </div>
            </a>

            <!-- Location Card -->
            <a href="{{ route('location.index') }}" class="dashboard-card">
                <div class="dashboard-card__content">
                    <h2 class="dashboard-card__title">Location</h2>
                    <p class="dashboard-card__description">Courte et longue durée</p>
                </div>
            </a>
        </div>
    </div>
</x-layouts.main>
