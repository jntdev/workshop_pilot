<?php

namespace Database\Factories;

use App\Enums\StockMovementType;
use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockMovement>
 */
class StockMovementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'article_id' => Article::factory(),
            'quantity' => $this->faker->numberBetween(1, 20),
            'type' => StockMovementType::ManualIn,
            'source_type' => null,
            'source_id' => null,
            'unit_price_ht' => $this->faker->optional()->numberBetween(100, 5000),
            'note' => $this->faker->optional()->sentence(),
        ];
    }

    public function manualOut(): static
    {
        return $this->state(fn () => [
            'type' => StockMovementType::ManualOut,
            'quantity' => -$this->faker->numberBetween(1, 10),
        ]);
    }
}
