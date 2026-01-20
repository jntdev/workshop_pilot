<?php

namespace App\Services\Quotes;

class QuoteCalculator
{
    /**
     * Calculate line values from sale price HT.
     *
     * @return array{sale_price_ht: string, sale_price_ttc: string, margin_amount_ht: string|null, margin_rate: string|null}
     */
    public function fromSalePriceHt(?string $purchasePriceHt, string $salePriceHt, string $tvaRate): array
    {
        $saleHt = $this->toDecimal($salePriceHt);
        $tva = $this->toDecimal($tvaRate);
        $saleTtc = bcmul($saleHt, bcadd('1', bcdiv($tva, '100', 6), 6), 6);

        // Si purchase_price_ht est null, ne pas calculer les marges
        if ($purchasePriceHt === null || $purchasePriceHt === '') {
            return [
                'sale_price_ht' => $this->round($saleHt, 2),
                'sale_price_ttc' => $this->round($saleTtc, 2),
                'margin_amount_ht' => null,
                'margin_rate' => null,
            ];
        }

        $purchaseHt = $this->toDecimal($purchasePriceHt);
        $marginHt = bcsub($saleHt, $purchaseHt, 6);
        $marginRate = $saleHt !== '0.00' && $saleHt !== '0'
            ? bcmul(bcdiv($marginHt, $saleHt, 6), '100', 6)
            : '0';

        return [
            'sale_price_ht' => $this->round($saleHt, 2),
            'sale_price_ttc' => $this->round($saleTtc, 2),
            'margin_amount_ht' => $this->round($marginHt, 2),
            'margin_rate' => $this->round($marginRate, 4),
        ];
    }

    /**
     * Calculate line values from sale price TTC.
     *
     * @return array{sale_price_ht: string, sale_price_ttc: string, margin_amount_ht: string|null, margin_rate: string|null}
     */
    public function fromSalePriceTtc(?string $purchasePriceHt, string $salePriceTtc, string $tvaRate): array
    {
        $saleTtc = $this->toDecimal($salePriceTtc);
        $tva = $this->toDecimal($tvaRate);
        $saleHt = bcdiv($saleTtc, bcadd('1', bcdiv($tva, '100', 6), 6), 6);

        // Si purchase_price_ht est null, ne pas calculer les marges
        if ($purchasePriceHt === null || $purchasePriceHt === '') {
            return [
                'sale_price_ht' => $this->round($saleHt, 2),
                'sale_price_ttc' => $this->round($saleTtc, 2),
                'margin_amount_ht' => null,
                'margin_rate' => null,
            ];
        }

        $purchaseHt = $this->toDecimal($purchasePriceHt);
        $marginHt = bcsub($saleHt, $purchaseHt, 6);
        $marginRate = $saleHt !== '0.00' && $saleHt !== '0'
            ? bcmul(bcdiv($marginHt, $saleHt, 6), '100', 6)
            : '0';

        return [
            'sale_price_ht' => $this->round($saleHt, 2),
            'sale_price_ttc' => $this->round($saleTtc, 2),
            'margin_amount_ht' => $this->round($marginHt, 2),
            'margin_rate' => $this->round($marginRate, 4),
        ];
    }

    /**
     * Calculate line values from margin amount.
     *
     * @return array{sale_price_ht: string, sale_price_ttc: string, margin_amount_ht: string, margin_rate: string}
     */
    public function fromMarginAmount(string $purchasePriceHt, string $marginAmount, string $tvaRate): array
    {
        $purchaseHt = $this->toDecimal($purchasePriceHt);
        $margin = $this->toDecimal($marginAmount);
        $tva = $this->toDecimal($tvaRate);

        $saleHt = bcadd($purchaseHt, $margin, 6);
        $saleTtc = bcmul($saleHt, bcadd('1', bcdiv($tva, '100', 6), 6), 6);
        $marginRate = $saleHt !== '0.00' && $saleHt !== '0'
            ? bcmul(bcdiv($margin, $saleHt, 6), '100', 6)
            : '0';

        return [
            'sale_price_ht' => $this->round($saleHt, 2),
            'sale_price_ttc' => $this->round($saleTtc, 2),
            'margin_amount_ht' => $this->round($margin, 2),
            'margin_rate' => $this->round($marginRate, 4),
        ];
    }

    /**
     * Calculate line values from margin rate.
     *
     * @return array{sale_price_ht: string, sale_price_ttc: string, margin_amount_ht: string, margin_rate: string}
     */
    public function fromMarginRate(string $purchasePriceHt, string $marginRate, string $tvaRate): array
    {
        $purchaseHt = $this->toDecimal($purchasePriceHt);
        $rate = $this->toDecimal($marginRate);
        $tva = $this->toDecimal($tvaRate);

        // PV HT = PA HT / (1 - (Marge % / 100))
        $divisor = bcsub('1', bcdiv($rate, '100', 6), 6);
        $saleHt = $divisor !== '0' && $divisor !== '0.00'
            ? bcdiv($purchaseHt, $divisor, 6)
            : $purchaseHt;

        $saleTtc = bcmul($saleHt, bcadd('1', bcdiv($tva, '100', 6), 6), 6);
        $marginHt = bcsub($saleHt, $purchaseHt, 6);

        return [
            'sale_price_ht' => $this->round($saleHt, 2),
            'sale_price_ttc' => $this->round($saleTtc, 2),
            'margin_amount_ht' => $this->round($marginHt, 2),
            'margin_rate' => $this->round($rate, 4),
        ];
    }

    /**
     * Aggregate totals from lines array.
     *
     * @param  array<int, array{sale_price_ht: string, sale_price_ttc: string, margin_amount_ht: string|null}>  $lines
     * @return array{total_ht: string, total_tva: string, total_ttc: string, margin_total_ht: string}
     */
    public function aggregateTotals(array $lines): array
    {
        $totalHt = '0';
        $totalTtc = '0';
        $totalMargin = '0';

        foreach ($lines as $line) {
            $totalHt = bcadd($totalHt, $this->toDecimal($line['sale_price_ht'] ?? '0'), 6);
            $totalTtc = bcadd($totalTtc, $this->toDecimal($line['sale_price_ttc'] ?? '0'), 6);
            // Ignorer les marges null (lignes incomplètes)
            if (isset($line['margin_amount_ht']) && $line['margin_amount_ht'] !== null) {
                $totalMargin = bcadd($totalMargin, $this->toDecimal($line['margin_amount_ht']), 6);
            }
        }

        $totalTva = bcsub($totalTtc, $totalHt, 6);

        return [
            'total_ht' => $this->round($totalHt, 2),
            'total_tva' => $this->round($totalTva, 2),
            'total_ttc' => $this->round($totalTtc, 2),
            'margin_total_ht' => $this->round($totalMargin, 2),
        ];
    }

    /**
     * Apply discount to totals.
     *
     * @return array{total_ht: string, total_tva: string, total_ttc: string}
     */
    public function applyDiscount(string $totalHt, string $totalTva, string $discountType, string $discountValue): array
    {
        $ht = $this->toDecimal($totalHt);
        $tva = $this->toDecimal($totalTva);
        $ttc = bcadd($ht, $tva, 6);
        $discount = $this->toDecimal($discountValue);

        // Calculer le taux de TVA moyen
        $tvaRate = $ht !== '0' && $ht !== '0.00'
            ? bcmul(bcdiv($tva, $ht, 6), '100', 6)
            : '0';

        // Appliquer la remise sur le HT
        if ($discountType === 'amount') {
            $ht = bcsub($ht, $discount, 6);
        } elseif ($discountType === 'percent') {
            $discountAmount = bcmul($ht, bcdiv($discount, '100', 6), 6);
            $ht = bcsub($ht, $discountAmount, 6);
        }

        // Ensure HT doesn't go negative
        if (bccomp($ht, '0', 6) < 0) {
            $ht = '0';
        }

        // Recalculer la TVA et le TTC après application de la remise
        $tva = bcmul($ht, bcdiv($tvaRate, '100', 6), 6);
        $ttc = bcadd($ht, $tva, 6);

        return [
            'total_ht' => $this->round($ht, 2),
            'total_tva' => $this->round($tva, 2),
            'total_ttc' => $this->round($ttc, 2),
        ];
    }

    /**
     * Convert value to decimal string.
     */
    private function toDecimal(string|int|float $value): string
    {
        return (string) $value;
    }

    /**
     * Round value to specified precision.
     */
    private function round(string $value, int $precision): string
    {
        $multiplier = bcpow('10', (string) $precision, 0);
        $rounded = bcdiv(
            bcadd(
                bcmul($value, $multiplier, 0),
                bccomp($value, '0', $precision + 2) >= 0 ? '0.5' : '-0.5',
                0
            ),
            $multiplier,
            $precision
        );

        return $rounded;
    }
}
