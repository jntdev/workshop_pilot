<?php

namespace Database\Factories;

use App\Enums\Metier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MonthlyKpi>
 */
class MonthlyKpiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'metier' => Metier::Atelier,
            'year' => now()->year,
            'month' => now()->month,
            'invoice_count' => fake()->numberBetween(1, 50),
            'revenue_ht' => fake()->randomFloat(2, 1000, 50000),
            'margin_ht' => fake()->randomFloat(2, 500, 25000),
        ];
    }

    public function atelier(): static
    {
        return $this->state(fn (array $attributes) => [
            'metier' => Metier::Atelier,
        ]);
    }

    public function vente(): static
    {
        return $this->state(fn (array $attributes) => [
            'metier' => Metier::Vente,
        ]);
    }

    public function location(): static
    {
        return $this->state(fn (array $attributes) => [
            'metier' => Metier::Location,
        ]);
    }

    public function forMonth(int $year, int $month): static
    {
        return $this->state(fn (array $attributes) => [
            'year' => $year,
            'month' => $month,
        ]);
    }
}
