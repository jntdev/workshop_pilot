<?php

namespace Database\Factories;

use App\Enums\SaleStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference' => null,
            'client_id' => null,
            'user_id' => \App\Models\User::factory(),
            'status' => SaleStatus::Draft,
            'payment_method' => null,
            'total_ht' => 0,
            'total_tva' => 0,
            'total_ttc' => 0,
            'completed_at' => null,
            'cancelled_at' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'reference' => 'VC-'.now()->format('Ymd').'-'.fake()->unique()->numberBetween(1, 100000),
            'status' => SaleStatus::Completed,
            'completed_at' => now(),
        ]);
    }
}
