<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AgendaEvent>
 */
class AgendaEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('now', '+2 weeks');

        return [
            'title' => $this->faker->sentence(3),
            'detail' => $this->faker->optional()->paragraph(),
            'starts_at' => $start,
            'ends_at' => (clone $start)->modify('+30 minutes'),
        ];
    }
}
