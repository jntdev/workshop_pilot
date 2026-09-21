<?php

namespace Database\Factories;

use App\Models\Quote;
use App\Models\QuotePayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuotePayment>
 */
class QuotePaymentFactory extends Factory
{
    protected $model = QuotePayment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quote_id' => Quote::factory(),
            'amount' => $this->faker->randomFloat(2, 20, 200),
            'method' => $this->faker->randomElement(['cb', 'liquide', 'cheque', 'virement', 'autre']),
            'paid_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'note' => $this->faker->optional()->sentence(),
        ];
    }
}
