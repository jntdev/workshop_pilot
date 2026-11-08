<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VisiteSource>
 */
class VisiteSourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $visiteurs = fake()->numberBetween(1, 20);

        return [
            'jour' => fake()->dateTimeBetween('-6 months')->format('Y-m-d'),
            'cible' => 'accueil',
            'source' => fake()->randomElement(['direct', 'moteur', 'interne', 'reseau_social']),
            'visiteurs' => $visiteurs,
            'vues' => $visiteurs + fake()->numberBetween(0, 10),
        ];
    }
}
