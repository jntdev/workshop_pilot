<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\ReservationPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReservationPayment>
 */
class ReservationPaymentFactory extends Factory
{
    protected $model = ReservationPayment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reservation_id' => Reservation::factory(),
            'amount' => $this->faker->randomFloat(2, 20, 200),
            'method' => $this->faker->randomElement(['cb', 'liquide', 'cheque', 'virement', 'autre']),
            'paid_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'note' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Payment by credit card.
     */
    public function cb(): static
    {
        return $this->state(fn (array $attributes) => [
            'method' => 'cb',
        ]);
    }

    /**
     * Payment by cash.
     */
    public function liquide(): static
    {
        return $this->state(fn (array $attributes) => [
            'method' => 'liquide',
        ]);
    }

    /**
     * Payment by check.
     */
    public function cheque(): static
    {
        return $this->state(fn (array $attributes) => [
            'method' => 'cheque',
        ]);
    }
}
