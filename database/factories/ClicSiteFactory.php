<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClicSite>
 */
class ClicSiteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $visiteurs = fake()->numberBetween(1, 30);

        return [
            'jour' => fake()->dateTimeBetween('-6 months')->format('Y-m-d'),
            'categorie' => 'accueil',
            'cle' => fake()->randomElement(['acheter', 'reparer', 'louer', 'blackfriday']),
            'contexte' => '',
            'visiteurs' => $visiteurs,
            'clics' => $visiteurs + fake()->numberBetween(0, 10),
        ];
    }
}
