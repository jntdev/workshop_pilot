<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dateReservation = $this->faker->dateTimeBetween('now', '+30 days');
        $dateRetour = $this->faker->dateTimeBetween($dateReservation, '+60 days');

        return [
            'client_id' => Client::factory(),
            'date_contact' => now(),
            'date_reservation' => $dateReservation,
            'date_retour' => $dateRetour,
            'livraison_necessaire' => false,
            'adresse_livraison' => null,
            'contact_livraison' => null,
            'creneau_livraison' => null,
            'recuperation_necessaire' => false,
            'adresse_recuperation' => null,
            'contact_recuperation' => null,
            'creneau_recuperation' => null,
            'prix_total_ttc' => $this->faker->randomFloat(2, 50, 500),
            'acompte_demande' => false,
            'acompte_montant' => null,
            'acompte_paye_le' => null,
            'paiement_final_le' => null,
            'statut' => 'reserve',
            'raison_annulation' => null,
            'commentaires' => $this->faker->optional()->sentence(),
            'selection' => [],
            'color' => $this->faker->numberBetween(0, 29),
        ];
    }

    /**
     * Indicate that the reservation is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'annule',
            'raison_annulation' => $this->faker->sentence(),
        ]);
    }

    /**
     * Indicate that the reservation is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'paye',
            'paiement_final_le' => now(),
        ]);
    }

    /**
     * Indicate that the reservation is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'en_cours',
        ]);
    }

    /**
     * Indicate that the reservation requires delivery.
     */
    public function withDelivery(): static
    {
        return $this->state(fn (array $attributes) => [
            'livraison_necessaire' => true,
            'adresse_livraison' => $this->faker->address(),
            'contact_livraison' => $this->faker->name(),
            'creneau_livraison' => $this->faker->randomElement(['Matin (9h-12h)', 'Après-midi (14h-18h)']),
        ]);
    }
}
