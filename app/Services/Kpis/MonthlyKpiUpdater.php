<?php

namespace App\Services\Kpis;

use App\Models\MonthlyKpi;
use App\Models\Quote;
use Illuminate\Support\Facades\DB;

class MonthlyKpiUpdater
{
    public function applyInvoice(Quote $quote): void
    {
        if ($quote->invoiced_at === null) {
            throw new \InvalidArgumentException('La quote doit être une facture (invoiced_at non null).');
        }

        $year = $quote->invoiced_at->year;
        $month = $quote->invoiced_at->month;
        $metier = $quote->metier->value;

        DB::transaction(function () use ($metier, $year, $month, $quote) {
            $kpi = MonthlyKpi::lockForUpdate()
                ->firstOrCreate(
                    [
                        'metier' => $metier,
                        'year' => $year,
                        'month' => $month,
                    ],
                    [
                        'invoice_count' => 0,
                        'revenue_ht' => 0,
                        'margin_ht' => 0,
                    ]
                );

            $kpi->increment('invoice_count');
            $kpi->increment('revenue_ht', $quote->total_ht);
            $kpi->increment('margin_ht', $quote->margin_total_ht);
        });
    }
}
