<?php

namespace Database\Factories;

use App\Enums\Metier;
use App\Enums\QuoteStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quote>
 */
class QuoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => \App\Models\Client::factory(),
            'bike_description' => fake()->randomElement(['VTT noir', 'Vélo de ville bleu', 'Nakamura vert', 'VTT bleu avec roue blanche']),
            'reception_comment' => fake()->sentence(),
            'metier' => Metier::Atelier,
            'reference' => 'DEV-'.fake()->unique()->numberBetween(1000, 9999),
            'status' => QuoteStatus::Reception,
            'valid_until' => fake()->dateTimeBetween('now', '+30 days'),
            'discount_type' => fake()->optional(0.3)->randomElement(['amount', 'percent']),
            'discount_value' => fake()->optional(0.3)->randomFloat(2, 0, 50),
            'total_ht' => '0.00',
            'total_tva' => '0.00',
            'total_ttc' => '0.00',
            'margin_total_ht' => '0.00',
            'total_estimated_time_minutes' => null,
            'actual_time_minutes' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => QuoteStatus::Reception,
        ]);
    }

    public function ready(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => QuoteStatus::Validated,
        ]);
    }

    public function invoiced(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => QuoteStatus::Invoiced,
            'invoiced_at' => now(),
        ]);
    }

    public function asInvoice(): static
    {
        return $this->state(fn (array $attributes) => [
            'invoiced_at' => now(),
            'status' => QuoteStatus::Invoiced,
        ]);
    }

    public function asQuote(): static
    {
        return $this->state(fn (array $attributes) => [
            'invoiced_at' => null,
            'status' => QuoteStatus::Reception,
        ]);
    }

    public function validated(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => QuoteStatus::Validated,
        ]);
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

    public function withTimeTracking(int $estimatedMinutes = 120, ?int $actualMinutes = null): static
    {
        return $this->state(fn (array $attributes) => [
            'total_estimated_time_minutes' => $estimatedMinutes,
            'actual_time_minutes' => $actualMinutes,
        ]);
    }
}
