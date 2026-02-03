<?php

namespace Tests\Feature;

use App\Models\MonthlyKpi;
use App\Models\Quote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RebuildMonthlyKpisCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Nettoyer les tables pour éviter les interférences avec d'autres tests
        MonthlyKpi::query()->delete();
        Quote::query()->forceDelete();
    }

    public function test_rebuild_creates_kpis_from_invoices(): void
    {
        // Créer des factures sur deux mois différents
        Quote::factory()->atelier()->create([
            'total_ht' => 1000.00,
            'margin_total_ht' => 300.00,
            'invoiced_at' => now()->startOfMonth(),
        ]);

        Quote::factory()->atelier()->create([
            'total_ht' => 2000.00,
            'margin_total_ht' => 600.00,
            'invoiced_at' => now()->subMonth()->startOfMonth(),
        ]);

        $this->artisan('kpis:rebuild-monthly', ['--metier' => 'atelier'])
            ->assertExitCode(0);

        $this->assertDatabaseCount('monthly_kpis', 2);

        $this->assertDatabaseHas('monthly_kpis', [
            'metier' => 'atelier',
            'year' => now()->year,
            'month' => now()->month,
            'invoice_count' => 1,
            'revenue_ht' => 1000.00,
            'margin_ht' => 300.00,
        ]);

        $this->assertDatabaseHas('monthly_kpis', [
            'metier' => 'atelier',
            'year' => now()->subMonth()->year,
            'month' => now()->subMonth()->month,
            'invoice_count' => 1,
            'revenue_ht' => 2000.00,
            'margin_ht' => 600.00,
        ]);
    }

    public function test_rebuild_aggregates_multiple_invoices_in_same_month(): void
    {
        Quote::factory()->atelier()->count(3)->create([
            'total_ht' => 1000.00,
            'margin_total_ht' => 300.00,
            'invoiced_at' => now()->startOfMonth(),
        ]);

        $this->artisan('kpis:rebuild-monthly', ['--metier' => 'atelier'])
            ->assertExitCode(0);

        $this->assertDatabaseHas('monthly_kpis', [
            'metier' => 'atelier',
            'year' => now()->year,
            'month' => now()->month,
            'invoice_count' => 3,
            'revenue_ht' => 3000.00,
            'margin_ht' => 900.00,
        ]);
    }

    public function test_rebuild_by_metier_only_affects_specified_metier(): void
    {
        Quote::factory()->atelier()->create([
            'total_ht' => 1000.00,
            'margin_total_ht' => 300.00,
            'invoiced_at' => now(),
        ]);

        Quote::factory()->vente()->create([
            'total_ht' => 2000.00,
            'margin_total_ht' => 600.00,
            'invoiced_at' => now(),
        ]);

        $this->artisan('kpis:rebuild-monthly', ['--metier' => 'atelier'])
            ->assertExitCode(0);

        $this->assertDatabaseHas('monthly_kpis', [
            'metier' => 'atelier',
        ]);

        $this->assertDatabaseMissing('monthly_kpis', [
            'metier' => 'vente',
        ]);
    }

    public function test_rebuild_all_rebuilds_all_metiers(): void
    {
        Quote::factory()->atelier()->create([
            'total_ht' => 1000.00,
            'margin_total_ht' => 300.00,
            'invoiced_at' => now(),
        ]);

        Quote::factory()->vente()->create([
            'total_ht' => 2000.00,
            'margin_total_ht' => 600.00,
            'invoiced_at' => now(),
        ]);

        Quote::factory()->location()->create([
            'total_ht' => 500.00,
            'margin_total_ht' => 150.00,
            'invoiced_at' => now(),
        ]);

        $this->artisan('kpis:rebuild-monthly', ['--all' => true])
            ->assertExitCode(0);

        $this->assertDatabaseHas('monthly_kpis', ['metier' => 'atelier']);
        $this->assertDatabaseHas('monthly_kpis', ['metier' => 'vente']);
        $this->assertDatabaseHas('monthly_kpis', ['metier' => 'location']);
    }

    public function test_rebuild_deletes_existing_kpis_before_rebuild(): void
    {
        MonthlyKpi::factory()->atelier()->forMonth(now()->year, now()->month)->create([
            'invoice_count' => 99,
            'revenue_ht' => 99999.00,
            'margin_ht' => 33333.00,
        ]);

        Quote::factory()->atelier()->create([
            'total_ht' => 1000.00,
            'margin_total_ht' => 300.00,
            'invoiced_at' => now(),
        ]);

        $this->artisan('kpis:rebuild-monthly', ['--metier' => 'atelier'])
            ->assertExitCode(0);

        $this->assertDatabaseHas('monthly_kpis', [
            'metier' => 'atelier',
            'invoice_count' => 1,
            'revenue_ht' => 1000.00,
            'margin_ht' => 300.00,
        ]);

        $this->assertDatabaseMissing('monthly_kpis', [
            'invoice_count' => 99,
        ]);
    }

    public function test_rebuild_fails_without_metier_or_all_option(): void
    {
        $this->artisan('kpis:rebuild-monthly')
            ->assertExitCode(1);
    }
}
