<?php

namespace Tests\Unit\Services;

use App\Services\Quotes\QuoteCalculator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuoteCalculatorTest extends TestCase
{
    private QuoteCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new QuoteCalculator;
    }

    #[Test]
    public function it_calculates_from_sale_price_ht(): void
    {
        $result = $this->calculator->fromSalePriceHt('100.00', '150.00', '20.0000');

        $this->assertEquals('150.00', $result['sale_price_ht']);
        $this->assertEquals('180.00', $result['sale_price_ttc']);
        $this->assertEquals('50.00', $result['margin_amount_ht']);
        $this->assertEquals('33.3333', $result['margin_rate']);
    }

    #[Test]
    public function it_calculates_from_sale_price_ttc(): void
    {
        $result = $this->calculator->fromSalePriceTtc('100.00', '180.00', '20.0000');

        $this->assertEquals('150.00', $result['sale_price_ht']);
        $this->assertEquals('180.00', $result['sale_price_ttc']);
        $this->assertEquals('50.00', $result['margin_amount_ht']);
        $this->assertEquals('33.3333', $result['margin_rate']);
    }

    #[Test]
    public function it_calculates_from_margin_amount(): void
    {
        $result = $this->calculator->fromMarginAmount('100.00', '50.00', '20.0000');

        $this->assertEquals('150.00', $result['sale_price_ht']);
        $this->assertEquals('180.00', $result['sale_price_ttc']);
        $this->assertEquals('50.00', $result['margin_amount_ht']);
        $this->assertEquals('33.3333', $result['margin_rate']);
    }

    #[Test]
    public function it_calculates_from_margin_rate(): void
    {
        $result = $this->calculator->fromMarginRate('100.00', '33.3333', '20.0000');

        $this->assertEquals('149.99', $result['sale_price_ht']);
        $this->assertEquals('179.99', $result['sale_price_ttc']);
        $this->assertEquals('49.99', $result['margin_amount_ht']);
        $this->assertEquals('33.3333', $result['margin_rate']);
    }

    #[Test]
    public function it_aggregates_totals_from_lines(): void
    {
        $lines = [
            [
                'sale_price_ht' => '100.00',
                'sale_price_ttc' => '120.00',
                'margin_amount_ht' => '20.00',
            ],
            [
                'sale_price_ht' => '50.00',
                'sale_price_ttc' => '60.00',
                'margin_amount_ht' => '10.00',
            ],
        ];

        $result = $this->calculator->aggregateTotals($lines);

        $this->assertEquals('150.00', $result['total_ht']);
        $this->assertEquals('30.00', $result['total_tva']);
        $this->assertEquals('180.00', $result['total_ttc']);
        $this->assertEquals('30.00', $result['margin_total_ht']);
    }

    #[Test]
    public function it_applies_amount_discount(): void
    {
        $result = $this->calculator->applyDiscount('100.00', '20.00', 'amount', '10.00');

        // Après remise de 10€ sur 100€ HT : 90€ HT
        // TVA recalculée : 90€ * 20% = 18€
        // Total TTC : 90€ + 18€ = 108€
        $this->assertEquals('90.00', $result['total_ht']);
        $this->assertEquals('18.00', $result['total_tva']);
        $this->assertEquals('108.00', $result['total_ttc']);
    }

    #[Test]
    public function it_applies_percent_discount(): void
    {
        $result = $this->calculator->applyDiscount('100.00', '20.00', 'percent', '10.00');

        // Après remise de 10% sur 100€ HT : 90€ HT
        // TVA recalculée : 90€ * 20% = 18€
        // Total TTC : 90€ + 18€ = 108€
        $this->assertEquals('90.00', $result['total_ht']);
        $this->assertEquals('18.00', $result['total_tva']);
        $this->assertEquals('108.00', $result['total_ttc']);
    }

    #[Test]
    public function it_prevents_negative_totals_with_discount(): void
    {
        $result = $this->calculator->applyDiscount('100.00', '20.00', 'amount', '150.00');

        // Remise supérieure au HT : le HT est ramené à 0€
        // TVA recalculée : 0€ * 20% = 0€
        // Total TTC : 0€ + 0€ = 0€
        $this->assertEquals('0.00', $result['total_ht']);
        $this->assertEquals('0.00', $result['total_tva']);
        $this->assertEquals('0.00', $result['total_ttc']);
    }
}
