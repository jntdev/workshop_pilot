<?php

namespace Database\Seeders;

use App\Models\MonthlyKpi;
use App\Models\Quote;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MonthlyKpiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Calculates KPIs from actual invoice data in the database.
     */
    public function run(): void
    {
        // Clear existing KPIs
        MonthlyKpi::truncate();

        // Aggregate invoice data by year, month, and métier
        $kpiData = Quote::whereNotNull('invoiced_at')
            ->select(
                'metier',
                DB::raw('YEAR(invoiced_at) as year'),
                DB::raw('MONTH(invoiced_at) as month'),
                DB::raw('COUNT(*) as invoice_count'),
                DB::raw('SUM(total_ht) as revenue_ht'),
                DB::raw('SUM(margin_total_ht) as margin_ht')
            )
            ->groupBy('metier', DB::raw('YEAR(invoiced_at)'), DB::raw('MONTH(invoiced_at)'))
            ->get();

        $count = 0;

        foreach ($kpiData as $data) {
            MonthlyKpi::create([
                'metier' => $data->metier,
                'year' => $data->year,
                'month' => $data->month,
                'invoice_count' => $data->invoice_count,
                'revenue_ht' => $data->revenue_ht ?? 0,
                'margin_ht' => $data->margin_ht ?? 0,
            ]);
            $count++;
        }

        $this->command->info("✓ {$count} enregistrements KPIs créés à partir des factures.");
    }
}
