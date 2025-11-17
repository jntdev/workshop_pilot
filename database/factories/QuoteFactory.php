<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quote>
 */
class QuoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => \App\Models\Client::factory(),
            'reference' => 'DEV-'.fake()->unique()->numberBetween(1000, 9999),
            'status' => fake()->randomElement(['draft', 'validated']),
            'valid_until' => fake()->dateTimeBetween('now', '+30 days'),
            'discount_type' => fake()->optional(0.3)->randomElement(['amount', 'percent']),
            'discount_value' => fake()->optional(0.3)->randomFloat(2, 0, 50),
            'total_ht' => '0.00',
            'total_tva' => '0.00',
            'total_ttc' => '0.00',
            'margin_total_ht' => '0.00',
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function validated(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'validated',
        ]);
    }
}
