<?php

namespace Tests\Feature;

use App\Models\MonthlyKpi;
use App\Models\Quote;
use App\Services\Kpis\MonthlyKpiUpdater;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonthlyKpiUpdaterTest extends TestCase
{
    use RefreshDatabase;

    public function test_apply_invoice_creates_kpi_entry(): void
    {
        $quote = Quote::factory()->atelier()->create([
            'total_ht' => 1000.00,
            'margin_total_ht' => 300.00,
            'invoiced_at' => now(),
        ]);

        $updater = new MonthlyKpiUpdater;
        $updater->applyInvoice($quote);

        $this->assertDatabaseHas('monthly_kpis', [
            'metier' => 'atelier',
            'year' => now()->year,
            'month' => now()->month,
            'invoice_count' => 1,
            'revenue_ht' => 1000.00,
            'margin_ht' => 300.00,
        ]);
    }

    public function test_apply_invoice_increments_existing_kpi(): void
    {
        MonthlyKpi::factory()->atelier()->forMonth(now()->year, now()->month)->create([
            'invoice_count' => 5,
            'revenue_ht' => 5000.00,
            'margin_ht' => 1500.00,
        ]);

        $quote = Quote::factory()->atelier()->create([
            'total_ht' => 1000.00,
            'margin_total_ht' => 300.00,
            'invoiced_at' => now(),
        ]);

        $updater = new MonthlyKpiUpdater;
        $updater->applyInvoice($quote);

        $this->assertDatabaseHas('monthly_kpis', [
            'metier' => 'atelier',
            'year' => now()->year,
            'month' => now()->month,
            'invoice_count' => 6,
            'revenue_ht' => 6000.00,
            'margin_ht' => 1800.00,
        ]);
    }

    public function test_apply_invoice_uses_quote_metier(): void
    {
        $quote = Quote::factory()->vente()->create([
            'total_ht' => 2000.00,
            'margin_total_ht' => 800.00,
            'invoiced_at' => now(),
        ]);

        $updater = new MonthlyKpiUpdater;
        $updater->applyInvoice($quote);

        $this->assertDatabaseHas('monthly_kpis', [
            'metier' => 'vente',
            'year' => now()->year,
            'month' => now()->month,
            'invoice_count' => 1,
            'revenue_ht' => 2000.00,
            'margin_ht' => 800.00,
        ]);

        $this->assertDatabaseMissing('monthly_kpis', [
            'metier' => 'atelier',
        ]);
    }

    public function test_apply_invoice_throws_exception_if_not_invoiced(): void
    {
        $quote = Quote::factory()->asQuote()->create();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('La quote doit être une facture');

        $updater = new MonthlyKpiUpdater;
        $updater->applyInvoice($quote);
    }

    public function test_convert_to_invoice_updates_kpi(): void
    {
        $quote = Quote::factory()->atelier()->asQuote()->create([
            'total_ht' => 1500.00,
            'margin_total_ht' => 450.00,
        ]);

        $quote->convertToInvoice();

        $this->assertDatabaseHas('monthly_kpis', [
            'metier' => 'atelier',
            'year' => now()->year,
            'month' => now()->month,
            'invoice_count' => 1,
            'revenue_ht' => 1500.00,
            'margin_ht' => 450.00,
        ]);
    }
}
