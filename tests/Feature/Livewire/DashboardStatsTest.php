<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Atelier\Dashboard;
use App\Models\MonthlyKpi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_reads_kpi_values(): void
    {
        MonthlyKpi::factory()->atelier()->forMonth(now()->year, now()->month)->create([
            'invoice_count' => 10,
            'revenue_ht' => 15000.00,
            'margin_ht' => 4500.00,
        ]);

        Livewire::test(Dashboard::class)
            ->assertSet('stats.revenue', 15000.00)
            ->assertSet('stats.margin', 4500.00)
            ->assertSet('stats.count', 10)
            ->assertSet('stats.margin_rate', 30.0);
    }

    public function test_dashboard_returns_zeros_when_no_kpi_exists(): void
    {
        Livewire::test(Dashboard::class)
            ->assertSet('stats.revenue', 0)
            ->assertSet('stats.margin', 0)
            ->assertSet('stats.count', 0)
            ->assertSet('stats.margin_rate', 0);
    }

    public function test_dashboard_comparison_with_previous_year(): void
    {
        // KPI du mois actuel
        MonthlyKpi::factory()->atelier()->forMonth(now()->year, now()->month)->create([
            'invoice_count' => 20,
            'revenue_ht' => 20000.00,
            'margin_ht' => 6000.00,
        ]);

        // KPI du même mois l'année précédente
        MonthlyKpi::factory()->atelier()->forMonth(now()->year - 1, now()->month)->create([
            'invoice_count' => 15,
            'revenue_ht' => 15000.00,
            'margin_ht' => 4500.00,
        ]);

        Livewire::test(Dashboard::class)
            ->assertSet('stats.revenue', 20000.00)
            ->assertSet('comparisonStats.revenue', 15000.00)
            ->assertSet('comparisonStats.count', 15);
    }

    public function test_dashboard_filter_by_year_and_month(): void
    {
        MonthlyKpi::factory()->atelier()->forMonth(2024, 6)->create([
            'invoice_count' => 5,
            'revenue_ht' => 5000.00,
            'margin_ht' => 1500.00,
        ]);

        MonthlyKpi::factory()->atelier()->forMonth(2024, 7)->create([
            'invoice_count' => 8,
            'revenue_ht' => 8000.00,
            'margin_ht' => 2400.00,
        ]);

        Livewire::test(Dashboard::class)
            ->set('selectedYear', 2024)
            ->set('selectedMonth', 6)
            ->call('loadStats')
            ->assertSet('stats.count', 5)
            ->assertSet('stats.revenue', 5000.00);
    }

    public function test_dashboard_available_years_from_kpis(): void
    {
        MonthlyKpi::factory()->atelier()->forMonth(2023, 1)->create();
        MonthlyKpi::factory()->atelier()->forMonth(2024, 1)->create();
        MonthlyKpi::factory()->atelier()->forMonth(2025, 1)->create();

        $component = Livewire::test(Dashboard::class);
        $availableYears = $component->viewData('availableYears');

        $this->assertEquals([2025, 2024, 2023], $availableYears);
    }

    public function test_dashboard_shows_current_year_when_no_kpis(): void
    {
        $component = Livewire::test(Dashboard::class);
        $availableYears = $component->viewData('availableYears');

        $this->assertEquals([now()->year], $availableYears);
    }

    public function test_dashboard_only_reads_atelier_kpis(): void
    {
        MonthlyKpi::factory()->atelier()->forMonth(now()->year, now()->month)->create([
            'invoice_count' => 10,
            'revenue_ht' => 10000.00,
            'margin_ht' => 3000.00,
        ]);

        MonthlyKpi::factory()->vente()->forMonth(now()->year, now()->month)->create([
            'invoice_count' => 20,
            'revenue_ht' => 20000.00,
            'margin_ht' => 6000.00,
        ]);

        Livewire::test(Dashboard::class)
            ->assertSet('stats.count', 10)
            ->assertSet('stats.revenue', 10000.00);
    }
}
