<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VisiteFiche>
 */
class VisiteFicheFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $marque = fake()->randomElement(['mmr', 'ncm', 'adris', 'neomouv']);
        $visiteurs = fake()->numberBetween(1, 30);

        return [
            'jour' => fake()->dateTimeBetween('-6 months')->format('Y-m-d'),
            'slug' => $marque.'-'.fake()->unique()->slug(2),
            'marque' => $marque,
            'visiteurs' => $visiteurs,
            'vues' => $visiteurs + fake()->numberBetween(0, 20),
        ];
    }
}
