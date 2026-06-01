<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DemandePartenaire>
 */
class DemandePartenaireFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $debut = fake()->dateTimeBetween('now', '+30 days');
        $fin = fake()->dateTimeBetween($debut, '+60 days');

        return [
            'partenaire_id' => \App\Models\Partenaire::factory(),
            'date_debut' => $debut->format('Y-m-d'),
            'date_fin' => $fin->format('Y-m-d'),
            'velos_demandes' => ['VAE_m' => 2, 'VTC_s' => 1],
            'commentaire' => fake()->optional(0.4)->sentence(),
            'statut' => 'en_attente',
        ];
    }

    public function confirmee(): static
    {
        return $this->state(fn (array $attributes) => ['statut' => 'confirmee']);
    }

    public function annulee(): static
    {
        return $this->state(fn (array $attributes) => ['statut' => 'annulee']);
    }
}
