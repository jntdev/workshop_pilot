<?php

namespace App\Services\Quotes;

class QuoteCalculator
{
    /**
     * Calculate line values from sale price HT.
     *
     * @return array{sale_price_ht: string, sale_price_ttc: string, margin_amount_ht: string, margin_rate: string}
     */
    public function fromSalePriceHt(string $purchasePriceHt, string $salePriceHt, string $tvaRate): array
    {
        $saleHt = (float) $salePriceHt;
        $tva = (float) $tvaRate;
        $saleTtc = $saleHt * (1 + $tva / 100);

        $purchaseHt = (float) $purchasePriceHt;
        $marginHt = $saleHt - $purchaseHt;
        $marginRate = $saleHt != 0
            ? ($marginHt / $saleHt) * 100
            : 0;

        return [
            'sale_price_ht' => $this->formatMoney($saleHt),
            'sale_price_ttc' => $this->floorMoney($saleTtc),
            'margin_amount_ht' => $this->floorMoney($marginHt),
            'margin_rate' => $this->formatRate($marginRate),
        ];
    }

    /**
     * Calculate line values from sale price TTC.
     *
     * @return array{sale_price_ht: string, sale_price_ttc: string, margin_amount_ht: string, margin_rate: string}
     */
    public function fromSalePriceTtc(string $purchasePriceHt, string $salePriceTtc, string $tvaRate): array
    {
        $saleTtc = (float) $salePriceTtc;
        $tva = (float) $tvaRate;
        $saleHt = $saleTtc / (1 + $tva / 100);

        $purchaseHt = (float) $purchasePriceHt;
        $marginHt = $saleHt - $purchaseHt;
        $marginRate = $saleHt != 0
            ? ($marginHt / $saleHt) * 100
            : 0;

        return [
            'sale_price_ht' => $this->floorMoney($saleHt),
            'sale_price_ttc' => $this->formatMoney($saleTtc),
            'margin_amount_ht' => $this->floorMoney($marginHt),
            'margin_rate' => $this->formatRate($marginRate),
        ];
    }

    /**
     * Calculate line values from margin amount.
     *
     * @return array{sale_price_ht: string, sale_price_ttc: string, margin_amount_ht: string, margin_rate: string}
     */
    public function fromMarginAmount(string $purchasePriceHt, string $marginAmount, string $tvaRate): array
    {
        $purchaseHt = (float) $purchasePriceHt;
        $margin = (float) $marginAmount;
        $tva = (float) $tvaRate;

        $saleHt = $purchaseHt + $margin;
        $saleTtc = $saleHt * (1 + $tva / 100);
        $marginRate = $saleHt != 0
            ? ($margin / $saleHt) * 100
            : 0;

        return [
            'sale_price_ht' => $this->floorMoney($saleHt),
            'sale_price_ttc' => $this->floorMoney($saleTtc),
            'margin_amount_ht' => $this->formatMoney($margin),
            'margin_rate' => $this->formatRate($marginRate),
        ];
    }

    /**
     * Calculate line values from margin rate.
     *
     * @return array{sale_price_ht: string, sale_price_ttc: string, margin_amount_ht: string, margin_rate: string}
     */
    public function fromMarginRate(string $purchasePriceHt, string $marginRate, string $tvaRate): array
    {
        $purchaseHt = (float) $purchasePriceHt;
        $rate = (float) $marginRate;
        $tva = (float) $tvaRate;

        // PV HT = PA HT / (1 - (Marge % / 100))
        $divisor = 1 - ($rate / 100);
        $saleHt = $divisor != 0
            ? $purchaseHt / $divisor
            : $purchaseHt;

        $saleTtc = $saleHt * (1 + $tva / 100);
        $marginHt = $saleHt - $purchaseHt;

        return [
            'sale_price_ht' => $this->floorMoney($saleHt),
            'sale_price_ttc' => $this->floorMoney($saleTtc),
            'margin_amount_ht' => $this->floorMoney($marginHt),
            'margin_rate' => $this->formatRate($rate),
        ];
    }

    /**
     * Calculate line totals from unit prices and quantity.
     *
     * @return array{line_purchase_ht: string, line_margin_ht: string, line_total_ht: string, line_total_ttc: string}
     */
    public function calculateLineTotals(
        string $quantity,
        string $purchasePriceHt,
        string $salePriceHt,
        string $salePriceTtc,
        string $marginAmountHt
    ): array {
        $qty = (float) $quantity;

        return [
            'line_purchase_ht' => $this->floorMoney((float) $purchasePriceHt * $qty),
            'line_margin_ht' => $this->floorMoney((float) $marginAmountHt * $qty),
            'line_total_ht' => $this->floorMoney((float) $salePriceHt * $qty),
            'line_total_ttc' => $this->floorMoney((float) $salePriceTtc * $qty),
        ];
    }

    /**
     * Aggregate totals from lines array (uses line totals if available, otherwise unit prices).
     *
     * @param  array<int, array{sale_price_ht?: string, sale_price_ttc?: string, margin_amount_ht?: string, line_total_ht?: string, line_total_ttc?: string, line_margin_ht?: string}>  $lines
     * @return array{total_ht: string, total_tva: string, total_ttc: string, margin_total_ht: string}
     */
    public function aggregateTotals(array $lines): array
    {
        $totalHt = 0.0;
        $totalTtc = 0.0;
        $totalMargin = 0.0;

        foreach ($lines as $line) {
            // Use line totals if available, otherwise fall back to unit prices
            $totalHt += (float) ($line['line_total_ht'] ?? $line['sale_price_ht'] ?? 0);
            $totalTtc += (float) ($line['line_total_ttc'] ?? $line['sale_price_ttc'] ?? 0);
            $totalMargin += (float) ($line['line_margin_ht'] ?? $line['margin_amount_ht'] ?? 0);
        }

        $totalTva = $totalTtc - $totalHt;

        return [
            'total_ht' => $this->floorMoney($totalHt),
            'total_tva' => $this->floorMoney($totalTva),
            'total_ttc' => $this->floorMoney($totalTtc),
            'margin_total_ht' => $this->floorMoney($totalMargin),
        ];
    }

    /**
     * Apply discount to totals.
     *
     * @return array{total_ht: string, total_tva: string, total_ttc: string}
     */
    public function applyDiscount(string $totalHt, string $totalTva, string $discountType, string $discountValue): array
    {
        $ht = (float) $totalHt;
        $tva = (float) $totalTva;
        $discount = (float) $discountValue;

        // Calculer le taux de TVA moyen
        $tvaRate = $ht != 0 ? ($tva / $ht) * 100 : 0;

        // Appliquer la remise sur le HT
        if ($discountType === 'amount') {
            $ht -= $discount;
        } elseif ($discountType === 'percent') {
            $discountAmount = $ht * ($discount / 100);
            $ht -= $discountAmount;
        }

        // Ensure HT doesn't go negative
        if ($ht < 0) {
            $ht = 0;
        }

        // Recalculer la TVA et le TTC après application de la remise
        $tva = $ht * ($tvaRate / 100);
        $ttc = $ht + $tva;

        return [
            'total_ht' => $this->floorMoney($ht),
            'total_tva' => $this->floorMoney($tva),
            'total_ttc' => $this->floorMoney($ttc),
        ];
    }

    /**
     * Format money value (2 decimals, no rounding adjustment).
     */
    private function formatMoney(float $value): string
    {
        return number_format($value, 2, '.', '');
    }

    /**
     * Floor money value (round down for billing - pessimistic for revenue).
     */
    private function floorMoney(float $value): string
    {
        return number_format(floor($value * 100) / 100, 2, '.', '');
    }

    /**
     * Ceil money value (round up for costs - pessimistic for expenses).
     */
    private function ceilMoney(float $value): string
    {
        return number_format(ceil($value * 100) / 100, 2, '.', '');
    }

    /**
     * Format rate value (4 decimals).
     */
    private function formatRate(float $value): string
    {
        return number_format($value, 4, '.', '');
    }
}
