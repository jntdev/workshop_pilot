<?php

namespace App\Livewire\Atelier;

use App\Enums\Metier;
use App\Models\MonthlyKpi;
use Livewire\Component;

class Dashboard extends Component
{
    public int $selectedYear;

    public int $selectedMonth;

    public array $stats = [];

    public array $comparisonStats = [];

    public function mount(): void
    {
        $this->selectedYear = now()->year;
        $this->selectedMonth = now()->month;
        $this->loadStats();
    }

    public function updatedSelectedYear(): void
    {
        $this->loadStats();
        $this->dispatch('dashboard-filter-changed', year: $this->selectedYear, month: $this->selectedMonth);
    }

    public function updatedSelectedMonth(): void
    {
        $this->loadStats();
        $this->dispatch('dashboard-filter-changed', year: $this->selectedYear, month: $this->selectedMonth);
    }

    public function loadStats(): void
    {
        // Stats du mois sélectionné
        $this->stats = $this->getStatsForMonth($this->selectedYear, $this->selectedMonth);

        // Stats du même mois l'année précédente pour comparaison
        $this->comparisonStats = $this->getStatsForMonth($this->selectedYear - 1, $this->selectedMonth);
    }

    protected function getStatsForMonth(int $year, int $month): array
    {
        $kpi = MonthlyKpi::where('metier', Metier::Atelier)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        if (! $kpi) {
            return [
                'revenue' => 0,
                'margin' => 0,
                'count' => 0,
                'margin_rate' => 0,
            ];
        }

        $revenue = (float) $kpi->revenue_ht;
        $margin = (float) $kpi->margin_ht;

        return [
            'revenue' => $revenue,
            'margin' => $margin,
            'count' => $kpi->invoice_count,
            'margin_rate' => $revenue > 0 ? ($margin / $revenue) * 100 : 0,
        ];
    }

    public function getAvailableYears(): array
    {
        $years = MonthlyKpi::where('metier', Metier::Atelier)
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($years)) {
            return [now()->year];
        }

        return $years;
    }

    public function render()
    {
        return view('livewire.atelier.dashboard', [
            'availableYears' => $this->getAvailableYears(),
        ]);
    }
}
