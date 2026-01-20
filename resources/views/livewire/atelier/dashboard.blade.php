<div class="atelier-dashboard">
    <style>
    .atelier-dashboard {
        padding: 20px 0;
    }

    .atelier-dashboard__filters {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .atelier-dashboard__filter-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .atelier-dashboard__label {
        font-size: 14px;
        font-weight: 600;
        color: #495057;
    }

    .atelier-dashboard__select {
        padding: 8px 12px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 14px;
        background: white;
        cursor: pointer;
    }

    .atelier-dashboard__select:focus {
        outline: none;
        border-color: #2196F3;
        box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
    }

    .atelier-dashboard__metrics {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .atelier-dashboard__metrics > * {
        flex: 1;
        min-width: 280px;
    }

    .atelier-dashboard__metric-card {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 24px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .atelier-dashboard__metric-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 16px;
    }

    .atelier-dashboard__metric-title {
        font-size: 14px;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    .atelier-dashboard__metric-period {
        font-size: 12px;
        color: #adb5bd;
    }

    .atelier-dashboard__metric-value {
        font-size: 32px;
        font-weight: 700;
        color: #212529;
        margin-bottom: 12px;
    }

    .atelier-dashboard__metric-comparison {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .atelier-dashboard__metric-comparison--positive {
        color: #28a745;
    }

    .atelier-dashboard__metric-comparison--negative {
        color: #dc3545;
    }

    .atelier-dashboard__metric-detail {
        font-size: 13px;
        color: #6c757d;
    }
    </style>

    <div class="atelier-dashboard__filters">
        <div class="atelier-dashboard__filter-group">
            <label for="year-select" class="atelier-dashboard__label">Année</label>
            <select
                id="year-select"
                wire:model.live="selectedYear"
                class="atelier-dashboard__select"
            >
                @foreach($availableYears as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </div>

        <div class="atelier-dashboard__filter-group">
            <label for="month-select" class="atelier-dashboard__label">Mois</label>
            <select
                id="month-select"
                wire:model.live="selectedMonth"
                class="atelier-dashboard__select"
            >
                <option value="1">Janvier</option>
                <option value="2">Février</option>
                <option value="3">Mars</option>
                <option value="4">Avril</option>
                <option value="5">Mai</option>
                <option value="6">Juin</option>
                <option value="7">Juillet</option>
                <option value="8">Août</option>
                <option value="9">Septembre</option>
                <option value="10">Octobre</option>
                <option value="11">Novembre</option>
                <option value="12">Décembre</option>
            </select>
        </div>
    </div>

    <div class="atelier-dashboard__metrics">
        {{-- Chiffre d'affaires --}}
        <div class="atelier-dashboard__metric-card">
            <div class="atelier-dashboard__metric-header">
                <h3 class="atelier-dashboard__metric-title">Chiffre d'affaires HT</h3>
                <span class="atelier-dashboard__metric-period">
                    {{ DateTime::createFromFormat('!m', $selectedMonth)->format('F') }} {{ $selectedYear }}
                </span>
            </div>
            <div class="atelier-dashboard__metric-value">
                {{ number_format($stats['revenue'], 2, ',', ' ') }} €
            </div>
            @if($comparisonStats['revenue'] > 0)
                @php
                    $revenueDiff = $stats['revenue'] - $comparisonStats['revenue'];
                    $revenuePercent = ($revenueDiff / $comparisonStats['revenue']) * 100;
                @endphp
                <div class="atelier-dashboard__metric-comparison {{ $revenueDiff >= 0 ? 'atelier-dashboard__metric-comparison--positive' : 'atelier-dashboard__metric-comparison--negative' }}">
                    {{ $revenueDiff >= 0 ? '+' : '' }}{{ number_format($revenuePercent, 1) }}%
                    vs {{ DateTime::createFromFormat('!m', $selectedMonth)->format('F') }} {{ $selectedYear - 1 }}
                </div>
            @endif
            <div class="atelier-dashboard__metric-detail">
                {{ $stats['count'] }} facture{{ $stats['count'] > 1 ? 's' : '' }}
            </div>
        </div>

        {{-- Marge brute --}}
        <div class="atelier-dashboard__metric-card">
            <div class="atelier-dashboard__metric-header">
                <h3 class="atelier-dashboard__metric-title">Marge brute HT</h3>
                <span class="atelier-dashboard__metric-period">
                    {{ DateTime::createFromFormat('!m', $selectedMonth)->format('F') }} {{ $selectedYear }}
                </span>
            </div>
            <div class="atelier-dashboard__metric-value">
                {{ number_format($stats['margin'], 2, ',', ' ') }} €
            </div>
            @if($comparisonStats['margin'] > 0)
                @php
                    $marginDiff = $stats['margin'] - $comparisonStats['margin'];
                    $marginPercent = ($marginDiff / $comparisonStats['margin']) * 100;
                @endphp
                <div class="atelier-dashboard__metric-comparison {{ $marginDiff >= 0 ? 'atelier-dashboard__metric-comparison--positive' : 'atelier-dashboard__metric-comparison--negative' }}">
                    {{ $marginDiff >= 0 ? '+' : '' }}{{ number_format($marginPercent, 1) }}%
                    vs {{ DateTime::createFromFormat('!m', $selectedMonth)->format('F') }} {{ $selectedYear - 1 }}
                </div>
            @endif
            <div class="atelier-dashboard__metric-detail">
                Taux de marge : {{ number_format($stats['margin_rate'], 1) }}%
            </div>
        </div>

        {{-- Panier moyen --}}
        <div class="atelier-dashboard__metric-card">
            <div class="atelier-dashboard__metric-header">
                <h3 class="atelier-dashboard__metric-title">Panier moyen HT</h3>
                <span class="atelier-dashboard__metric-period">
                    {{ DateTime::createFromFormat('!m', $selectedMonth)->format('F') }} {{ $selectedYear }}
                </span>
            </div>
            <div class="atelier-dashboard__metric-value">
                @php
                    $averageBasket = $stats['count'] > 0 ? $stats['revenue'] / $stats['count'] : 0;
                @endphp
                {{ number_format($averageBasket, 2, ',', ' ') }} €
            </div>
            @if($comparisonStats['count'] > 0)
                @php
                    $prevAverageBasket = $comparisonStats['revenue'] / $comparisonStats['count'];
                    $basketDiff = $averageBasket - $prevAverageBasket;
                    $basketPercent = ($basketDiff / $prevAverageBasket) * 100;
                @endphp
                <div class="atelier-dashboard__metric-comparison {{ $basketDiff >= 0 ? 'atelier-dashboard__metric-comparison--positive' : 'atelier-dashboard__metric-comparison--negative' }}">
                    {{ $basketDiff >= 0 ? '+' : '' }}{{ number_format($basketPercent, 1) }}%
                    vs {{ DateTime::createFromFormat('!m', $selectedMonth)->format('F') }} {{ $selectedYear - 1 }}
                </div>
            @endif
            <div class="atelier-dashboard__metric-detail">
                Basé sur {{ $stats['count'] }} facture{{ $stats['count'] > 1 ? 's' : '' }}
            </div>
        </div>
    </div>
</div>
