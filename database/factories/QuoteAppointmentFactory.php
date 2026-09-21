<?php

namespace Database\Factories;

use App\Models\Quote;
use App\Models\QuoteAppointment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuoteAppointment>
 */
class QuoteAppointmentFactory extends Factory
{
    protected $model = QuoteAppointment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('now', '+2 weeks');

        return [
            'quote_id' => Quote::factory(),
            'starts_at' => $start,
            'ends_at' => (clone $start)->modify('+30 minutes'),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
