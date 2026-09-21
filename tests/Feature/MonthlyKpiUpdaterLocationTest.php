<?php

namespace Tests\Feature;

use App\Models\Bike;
use App\Models\BikeMaintenanceLog;
use App\Models\MonthlyKpi;
use App\Models\Reservation;
use App\Models\ReservationPayment;
use App\Services\Kpis\MonthlyKpiUpdater;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonthlyKpiUpdaterLocationTest extends TestCase
{
    use RefreshDatabase;

    private function bike(): Bike
    {
        return Bike::create(['name' => 'Vélo test']);
    }

    public function test_location_margin_subtracts_done_maintenance_cost(): void
    {
        $reservation = Reservation::factory()->create();
        ReservationPayment::factory()->create([
            'reservation_id' => $reservation->id,
            'amount' => 120.00,
            'paid_at' => now()->setDay(5),
        ]);

        BikeMaintenanceLog::create([
            'bike_id' => $this->bike()->id,
            'date' => now()->setDay(10),
            'description' => 'Chaîne + pneu',
            'cost' => 2000, // 20 € en centimes
            'status' => 'done',
        ]);

        $updater = new MonthlyKpiUpdater;
        $updater->rebuildLocationKpiForMonth(now()->year, now()->month);

        $kpi = MonthlyKpi::where('metier', 'location')
            ->where('year', now()->year)
            ->where('month', now()->month)
            ->first();

        $revenueHt = round(120.00 / 1.20, 2);
        $expectedMargin = round($revenueHt - 20.00, 2);

        $this->assertEquals($expectedMargin, (float) $kpi->margin_ht);
    }

    public function test_location_margin_ignores_todo_maintenance(): void
    {
        $reservation = Reservation::factory()->create();
        ReservationPayment::factory()->create([
            'reservation_id' => $reservation->id,
            'amount' => 120.00,
            'paid_at' => now()->setDay(5),
        ]);

        BikeMaintenanceLog::create([
            'bike_id' => $this->bike()->id,
            'date' => now()->setDay(10),
            'description' => 'Révision à prévoir',
            'cost' => 5000,
            'status' => 'todo',
        ]);

        $updater = new MonthlyKpiUpdater;
        $updater->rebuildLocationKpiForMonth(now()->year, now()->month);

        $kpi = MonthlyKpi::where('metier', 'location')
            ->where('year', now()->year)
            ->where('month', now()->month)
            ->first();

        $revenueHt = round(120.00 / 1.20, 2);

        $this->assertEquals($revenueHt, (float) $kpi->margin_ht);
    }

    public function test_location_margin_ignores_maintenance_from_other_months(): void
    {
        $reservation = Reservation::factory()->create();
        ReservationPayment::factory()->create([
            'reservation_id' => $reservation->id,
            'amount' => 120.00,
            'paid_at' => now()->setDay(5),
        ]);

        BikeMaintenanceLog::create([
            'bike_id' => $this->bike()->id,
            'date' => now()->subMonth(),
            'description' => 'Entretien mois précédent',
            'cost' => 3000,
            'status' => 'done',
        ]);

        $updater = new MonthlyKpiUpdater;
        $updater->rebuildLocationKpiForMonth(now()->year, now()->month);

        $kpi = MonthlyKpi::where('metier', 'location')
            ->where('year', now()->year)
            ->where('month', now()->month)
            ->first();

        $revenueHt = round(120.00 / 1.20, 2);

        $this->assertEquals($revenueHt, (float) $kpi->margin_ht);
    }

    public function test_location_margin_can_go_negative_when_maintenance_exceeds_revenue(): void
    {
        $reservation = Reservation::factory()->create();
        ReservationPayment::factory()->create([
            'reservation_id' => $reservation->id,
            'amount' => 50.00,
            'paid_at' => now()->setDay(5),
        ]);

        BikeMaintenanceLog::create([
            'bike_id' => $this->bike()->id,
            'date' => now()->setDay(10),
            'description' => 'Grosse réparation',
            'cost' => 10000,
            'status' => 'done',
        ]);

        $updater = new MonthlyKpiUpdater;
        $updater->rebuildLocationKpiForMonth(now()->year, now()->month);

        $kpi = MonthlyKpi::where('metier', 'location')
            ->where('year', now()->year)
            ->where('month', now()->month)
            ->first();

        $this->assertTrue((float) $kpi->margin_ht < 0);
    }
}
