<?php

namespace Database\Factories;

use App\Models\ArticleSubcategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'article_subcategory_id' => ArticleSubcategory::factory(),
            'reference' => $this->faker->unique()->bothify('??-###'),
            'designation' => $this->faker->sentence(3),
            'purchase_price_ht' => $this->faker->numberBetween(100, 5000),
            'sale_price_ht' => $this->faker->numberBetween(200, 8000),
            'tva_rate' => 20.00,
            'unit' => $this->faker->randomElement(['pièce', 'paire', 'kit', 'litre']),
            'supplier' => $this->faker->optional()->company(),
            'notes' => $this->faker->optional()->sentence(),
            'sort_order' => 0,
        ];
    }
}
