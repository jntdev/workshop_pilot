<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'prenom' => $this->faker->firstName(),
            'nom' => $this->faker->lastName(),
            'telephone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'adresse' => $this->faker->address(),
            'origine_contact' => $this->faker->randomElement(['Recommandation', 'Publicité', 'Internet', 'Passage']),
            'commentaires' => $this->faker->optional()->sentence(),
            'avantage_type' => 'aucun',
            'avantage_valeur' => 0,
            'avantage_expiration' => null,
            'avantage_applique' => false,
            'avantage_applique_le' => null,
        ];
    }
}
