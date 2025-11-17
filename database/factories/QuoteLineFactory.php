<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuoteLine>
 */
class QuoteLineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $purchasePrice = fake()->randomFloat(2, 10, 200);
        $salePrice = $purchasePrice * fake()->randomFloat(2, 1.2, 2.5);
        $tvaRate = 20.00;

        $calculator = new \App\Services\Quotes\QuoteCalculator;
        $calculated = $calculator->fromSalePriceHt(
            (string) $purchasePrice,
            (string) $salePrice,
            (string) $tvaRate
        );

        return [
            'quote_id' => \App\Models\Quote::factory(),
            'title' => fake()->words(3, true),
            'reference' => fake()->optional(0.7)->bothify('REF-####'),
            'purchase_price_ht' => number_format($purchasePrice, 2, '.', ''),
            'sale_price_ht' => $calculated['sale_price_ht'],
            'sale_price_ttc' => $calculated['sale_price_ttc'],
            'margin_amount_ht' => $calculated['margin_amount_ht'],
            'margin_rate' => $calculated['margin_rate'],
            'tva_rate' => $tvaRate,
            'position' => 0,
        ];
    }
}
