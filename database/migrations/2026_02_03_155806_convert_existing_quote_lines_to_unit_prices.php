<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Convertit les données existantes où les colonnes de "prix unitaire"
     * contenaient en réalité les totaux de ligne (unitaire × quantité).
     *
     * Logique :
     * 1. Copier les valeurs actuelles vers les colonnes line_* (ce sont les totaux)
     * 2. Diviser par la quantité pour obtenir les vrais prix unitaires
     */
    public function up(): void
    {
        // Récupérer toutes les lignes qui n'ont pas encore de line_total_ht
        $lines = DB::table('quote_lines')
            ->whereNull('line_total_ht')
            ->get();

        foreach ($lines as $line) {
            $quantity = (float) $line->quantity ?: 1;

            // Les valeurs actuelles sont des totaux de ligne
            $lineTotalHt = (float) $line->sale_price_ht;
            $lineTotalTtc = (float) $line->sale_price_ttc;
            $linePurchaseHt = (float) $line->purchase_price_ht;
            $lineMarginHt = (float) $line->margin_amount_ht;

            // Calculer les vrais prix unitaires
            $unitSalePriceHt = $quantity > 0 ? $lineTotalHt / $quantity : $lineTotalHt;
            $unitSalePriceTtc = $quantity > 0 ? $lineTotalTtc / $quantity : $lineTotalTtc;
            $unitPurchasePriceHt = $quantity > 0 ? $linePurchaseHt / $quantity : $linePurchaseHt;
            $unitMarginAmountHt = $quantity > 0 ? $lineMarginHt / $quantity : $lineMarginHt;

            DB::table('quote_lines')
                ->where('id', $line->id)
                ->update([
                    // Stocker les totaux de ligne
                    'line_total_ht' => number_format($lineTotalHt, 2, '.', ''),
                    'line_total_ttc' => number_format($lineTotalTtc, 2, '.', ''),
                    'line_purchase_ht' => number_format($linePurchaseHt, 2, '.', ''),
                    'line_margin_ht' => number_format($lineMarginHt, 2, '.', ''),
                    // Convertir en prix unitaires
                    'sale_price_ht' => number_format($unitSalePriceHt, 2, '.', ''),
                    'sale_price_ttc' => number_format($unitSalePriceTtc, 2, '.', ''),
                    'purchase_price_ht' => number_format($unitPurchasePriceHt, 2, '.', ''),
                    'margin_amount_ht' => number_format($unitMarginAmountHt, 2, '.', ''),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * Reconvertir les prix unitaires en totaux de ligne
     */
    public function down(): void
    {
        $lines = DB::table('quote_lines')
            ->whereNotNull('line_total_ht')
            ->get();

        foreach ($lines as $line) {
            // Restaurer les totaux dans les colonnes de prix unitaire
            DB::table('quote_lines')
                ->where('id', $line->id)
                ->update([
                    'sale_price_ht' => $line->line_total_ht,
                    'sale_price_ttc' => $line->line_total_ttc,
                    'purchase_price_ht' => $line->line_purchase_ht,
                    'margin_amount_ht' => $line->line_margin_ht,
                    // Vider les colonnes line_*
                    'line_total_ht' => null,
                    'line_total_ttc' => null,
                    'line_purchase_ht' => null,
                    'line_margin_ht' => null,
                ]);
        }
    }
};
