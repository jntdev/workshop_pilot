<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SaleLine>
 */
class SaleLineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = 1;
        $unitPriceTtc = $this->faker->numberBetween(200, 8000);

        return [
            'sale_id' => Sale::factory(),
            'article_id' => Article::factory(),
            'designation' => $this->faker->sentence(3),
            'reference' => $this->faker->bothify('??-###'),
            'quantity' => $quantity,
            'purchase_price_ht' => $this->faker->numberBetween(100, 5000),
            'unit_price_ttc' => $unitPriceTtc,
            'tva_rate' => 20.00,
            'line_total_ttc' => $quantity * $unitPriceTtc,
            'position' => 0,
        ];
    }
}
