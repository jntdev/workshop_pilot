<?php

namespace App\Livewire\Atelier;

use App\Models\Quote;
use Illuminate\Support\Facades\DB;
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
        $startDate = now()->setYear($year)->setMonth($month)->startOfMonth();
        $endDate = now()->setYear($year)->setMonth($month)->endOfMonth();

        $invoices = Quote::whereNotNull('invoiced_at')
            ->whereBetween('invoiced_at', [$startDate, $endDate])
            ->get();

        $revenue = $invoices->sum('total_ht');
        $margin = $invoices->sum('margin_total_ht');
        $count = $invoices->count();

        return [
            'revenue' => $revenue,
            'margin' => $margin,
            'count' => $count,
            'margin_rate' => $revenue > 0 ? ($margin / $revenue) * 100 : 0,
        ];
    }

    public function getAvailableYears(): array
    {
        $years = Quote::whereNotNull('invoiced_at')
            ->select(DB::raw('YEAR(invoiced_at) as year'))
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
