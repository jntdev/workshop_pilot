<?php

use App\Models\QuoteLine;
use App\Services\Quotes\QuoteCalculator;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Recalcule les totaux de ligne pour les lignes existantes
     * qui n'ont pas encore ces valeurs.
     */
    public function up(): void
    {
        $calculator = new QuoteCalculator();

        QuoteLine::whereNull('line_total_ht')
            ->orWhereNull('line_total_ttc')
            ->chunk(100, function ($lines) use ($calculator) {
                foreach ($lines as $line) {
                    $totals = $calculator->calculateLineTotals(
                        (string) ($line->quantity ?? '1'),
                        (string) ($line->purchase_price_ht ?? '0'),
                        (string) ($line->sale_price_ht ?? '0'),
                        (string) ($line->sale_price_ttc ?? '0'),
                        (string) ($line->margin_amount_ht ?? '0')
                    );

                    $line->update([
                        'line_purchase_ht' => $totals['line_purchase_ht'],
                        'line_margin_ht' => $totals['line_margin_ht'],
                        'line_total_ht' => $totals['line_total_ht'],
                        'line_total_ttc' => $totals['line_total_ttc'],
                    ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pas de rollback nécessaire - les données calculées restent valides
    }
};
