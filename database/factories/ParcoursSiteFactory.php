<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ParcoursSite>
 */
class ParcoursSiteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'jour' => fake()->dateTimeBetween('-6 months')->format('Y-m-d'),
            'parcours' => 'blackfriday',
            'rang' => 0,
            'etape' => 'accueil',
            'visites' => fake()->numberBetween(1, 50),
        ];
    }
}
