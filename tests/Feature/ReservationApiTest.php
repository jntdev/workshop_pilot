<?php

namespace Tests\Feature;

use App\Models\BikeType;
use App\Models\Client;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReservationApiTest extends TestCase
{
    protected function getTestUser(): User
    {
        return User::firstOrCreate(
            ['email' => 'test-reservation@workshop-pilot.com'],
            [
                'name' => 'Test Reservation User',
                'password' => bcrypt('password'),
            ]
        );
    }

    protected function makeValidReservationPayload(Client $client, array $overrides = []): array
    {
        $bikeType = BikeType::first() ?? BikeType::create([
            'id' => 'VAE_mb',
            'category' => 'VAE',
            'size' => 'M',
            'frame_type' => 'b',
            'label' => 'VAE M cadre bas',
            'stock' => 3,
        ]);

        return array_merge([
            'client_id' => $client->id,
            'date_contact' => now()->format('Y-m-d H:i:s'),
            'date_reservation' => now()->addDays(7)->format('Y-m-d'),
            'date_retour' => now()->addDays(14)->format('Y-m-d'),
            'livraison_necessaire' => false,
            'adresse_livraison' => null,
            'contact_livraison' => null,
            'creneau_livraison' => null,
            'recuperation_necessaire' => false,
            'adresse_recuperation' => null,
            'contact_recuperation' => null,
            'creneau_recuperation' => null,
            'prix_total_ttc' => 250.00,
            'acompte_demande' => false,
            'acompte_montant' => null,
            'acompte_paye_le' => null,
            'paiement_final_le' => null,
            'statut' => 'reserve',
            'raison_annulation' => null,
            'commentaires' => 'Test reservation',
            'items' => [
                [
                    'bike_type_id' => $bikeType->id,
                    'quantite' => 2,
                ],
            ],
        ], $overrides);
    }

    #[Test]
    public function it_can_create_a_reservation(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();

        $payload = $this->makeValidReservationPayload($client);

        $response = $this->actingAs($user)->postJson('/api/reservations', $payload);

        $response->assertStatus(201);
        $response->assertJsonPath('data.client_id', $client->id);
        $response->assertJsonPath('data.statut', 'reserve');
        $response->assertJsonPath('data.prix_total_ttc', '250.00');
        $this->assertCount(1, $response->json('data.items'));
        $response->assertJsonPath('data.items.0.quantite', 2);
    }

    #[Test]
    public function it_requires_client_id_or_new_client(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();

        $payload = $this->makeValidReservationPayload($client);
        unset($payload['client_id']);

        $response = $this->actingAs($user)->postJson('/api/reservations', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['new_client']);
    }

    #[Test]
    public function it_can_create_reservation_with_new_client(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create(); // Juste pour générer un payload valide

        $bikeType = BikeType::first() ?? BikeType::create([
            'id' => 'VAE_mb',
            'category' => 'VAE',
            'size' => 'M',
            'frame_type' => 'b',
            'label' => 'VAE M cadre bas',
            'stock' => 3,
        ]);

        $payload = [
            'client_id' => null,
            'new_client' => [
                'prenom' => 'Jean',
                'nom' => 'Nouveau',
                'telephone' => '0612345678',
                'email' => 'jean.nouveau-'.time().'@example.com',
                'adresse' => '123 Rue Test',
                'origine_contact' => 'Site web',
                'avantage_type' => 'aucun',
                'avantage_valeur' => 0,
            ],
            'date_contact' => now()->format('Y-m-d H:i:s'),
            'date_reservation' => now()->addDays(7)->format('Y-m-d'),
            'date_retour' => now()->addDays(14)->format('Y-m-d'),
            'livraison_necessaire' => false,
            'recuperation_necessaire' => false,
            'prix_total_ttc' => 180.00,
            'acompte_demande' => false,
            'statut' => 'reserve',
            'commentaires' => 'Test avec nouveau client',
            'items' => [
                [
                    'bike_type_id' => $bikeType->id,
                    'quantite' => 1,
                ],
            ],
        ];

        $response = $this->actingAs($user)->postJson('/api/reservations', $payload);

        $response->assertStatus(201);
        $response->assertJsonPath('data.client.prenom', 'Jean');
        $response->assertJsonPath('data.client.nom', 'Nouveau');
        $response->assertJsonPath('data.client.telephone', '0612345678');

        // Vérifier que le client a bien été créé en base
        $this->assertDatabaseHas('clients', [
            'prenom' => 'Jean',
            'nom' => 'Nouveau',
        ]);
    }

    #[Test]
    public function it_validates_new_client_required_fields(): void
    {
        $user = $this->getTestUser();

        $bikeType = BikeType::first() ?? BikeType::create([
            'id' => 'VAE_mb',
            'category' => 'VAE',
            'size' => 'M',
            'frame_type' => 'b',
            'label' => 'VAE M cadre bas',
            'stock' => 3,
        ]);

        $payload = [
            'client_id' => null,
            'new_client' => [
                'prenom' => '',
                'nom' => 'Test',
                'telephone' => '0612345678',
            ],
            'date_contact' => now()->format('Y-m-d H:i:s'),
            'date_reservation' => now()->addDays(7)->format('Y-m-d'),
            'date_retour' => now()->addDays(14)->format('Y-m-d'),
            'livraison_necessaire' => false,
            'recuperation_necessaire' => false,
            'prix_total_ttc' => 100.00,
            'acompte_demande' => false,
            'statut' => 'reserve',
            'items' => [
                [
                    'bike_type_id' => $bikeType->id,
                    'quantite' => 1,
                ],
            ],
        ];

        $response = $this->actingAs($user)->postJson('/api/reservations', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['new_client.prenom']);
    }

    #[Test]
    public function it_requires_at_least_one_bike(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();

        $payload = $this->makeValidReservationPayload($client, [
            'items' => [],
        ]);

        $response = $this->actingAs($user)->postJson('/api/reservations', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['items']);
    }

    #[Test]
    public function it_validates_date_retour_after_date_reservation(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();

        $payload = $this->makeValidReservationPayload($client, [
            'date_reservation' => now()->addDays(14)->format('Y-m-d'),
            'date_retour' => now()->addDays(7)->format('Y-m-d'),
        ]);

        $response = $this->actingAs($user)->postJson('/api/reservations', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['date_retour']);
    }

    #[Test]
    public function it_requires_adresse_livraison_when_livraison_is_needed(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();

        $payload = $this->makeValidReservationPayload($client, [
            'livraison_necessaire' => true,
            'adresse_livraison' => null,
        ]);

        $response = $this->actingAs($user)->postJson('/api/reservations', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['adresse_livraison']);
    }

    #[Test]
    public function it_requires_raison_annulation_when_statut_is_annule(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();

        $payload = $this->makeValidReservationPayload($client, [
            'statut' => 'annule',
            'raison_annulation' => null,
        ]);

        $response = $this->actingAs($user)->postJson('/api/reservations', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['raison_annulation']);
    }

    #[Test]
    public function it_requires_paiement_final_when_statut_is_paye(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();

        $payload = $this->makeValidReservationPayload($client, [
            'statut' => 'paye',
            'paiement_final_le' => null,
        ]);

        $response = $this->actingAs($user)->postJson('/api/reservations', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['paiement_final_le']);
    }

    #[Test]
    public function it_requires_acompte_demande_when_statut_is_en_attente_acompte(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();

        $payload = $this->makeValidReservationPayload($client, [
            'statut' => 'en_attente_acompte',
            'acompte_demande' => false,
        ]);

        $response = $this->actingAs($user)->postJson('/api/reservations', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['acompte_demande']);
    }

    #[Test]
    public function it_can_update_a_reservation(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();

        // Créer une réservation
        $payload = $this->makeValidReservationPayload($client);
        $createResponse = $this->actingAs($user)->postJson('/api/reservations', $payload);
        $reservationId = $createResponse->json('data.id');

        // Mettre à jour
        $updatePayload = [
            'prix_total_ttc' => 300.00,
            'commentaires' => 'Mise à jour du commentaire',
        ];

        $response = $this->actingAs($user)->putJson("/api/reservations/{$reservationId}", $updatePayload);

        $response->assertStatus(200);
        $response->assertJsonPath('data.prix_total_ttc', '300.00');
        $response->assertJsonPath('data.commentaires', 'Mise à jour du commentaire');
    }

    #[Test]
    public function it_can_delete_a_reservation(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();

        // Créer une réservation
        $payload = $this->makeValidReservationPayload($client);
        $createResponse = $this->actingAs($user)->postJson('/api/reservations', $payload);
        $reservationId = $createResponse->json('data.id');

        // Supprimer
        $response = $this->actingAs($user)->deleteJson("/api/reservations/{$reservationId}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('reservations', ['id' => $reservationId]);
    }

    #[Test]
    public function it_can_list_reservations(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();

        // Créer quelques réservations
        $payload = $this->makeValidReservationPayload($client);
        $this->actingAs($user)->postJson('/api/reservations', $payload);
        $this->actingAs($user)->postJson('/api/reservations', $payload);

        $response = $this->actingAs($user)->getJson('/api/reservations');

        $response->assertStatus(200);
        $this->assertGreaterThanOrEqual(2, count($response->json()));
    }

    #[Test]
    public function it_can_filter_reservations_by_statut(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();

        // Créer une réservation avec statut "reserve"
        $payload = $this->makeValidReservationPayload($client, ['statut' => 'reserve']);
        $this->actingAs($user)->postJson('/api/reservations', $payload);

        // Créer une réservation avec statut "en_cours"
        $payload2 = $this->makeValidReservationPayload($client, ['statut' => 'en_cours']);
        $this->actingAs($user)->postJson('/api/reservations', $payload2);

        $response = $this->actingAs($user)->getJson('/api/reservations?statut=reserve');

        $response->assertStatus(200);
        foreach ($response->json() as $reservation) {
            $this->assertEquals('reserve', $reservation['statut']);
        }
    }

    #[Test]
    public function it_can_show_a_single_reservation(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();

        $payload = $this->makeValidReservationPayload($client);
        $createResponse = $this->actingAs($user)->postJson('/api/reservations', $payload);
        $reservationId = $createResponse->json('data.id');

        $response = $this->actingAs($user)->getJson("/api/reservations/{$reservationId}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $reservationId);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'client_id',
                'client',
                'date_contact',
                'date_reservation',
                'date_retour',
                'statut',
                'items',
            ],
        ]);
    }

    #[Test]
    public function it_returns_404_for_non_existent_reservation(): void
    {
        $user = $this->getTestUser();

        $response = $this->actingAs($user)->getJson('/api/reservations/99999');

        $response->assertStatus(404);
    }

    #[Test]
    public function it_validates_bike_type_exists(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();

        $payload = $this->makeValidReservationPayload($client, [
            'items' => [
                [
                    'bike_type_id' => 'INVALID_TYPE',
                    'quantite' => 1,
                ],
            ],
        ]);

        $response = $this->actingAs($user)->postJson('/api/reservations', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['items.0.bike_type_id']);
    }

    #[Test]
    public function it_can_create_reservation_with_livraison(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();

        $payload = $this->makeValidReservationPayload($client, [
            'livraison_necessaire' => true,
            'adresse_livraison' => '123 Rue de Test, 75001 Paris',
            'contact_livraison' => 'Jean Dupont',
            'creneau_livraison' => 'Matin (9h-12h)',
        ]);

        $response = $this->actingAs($user)->postJson('/api/reservations', $payload);

        $response->assertStatus(201);
        $response->assertJsonPath('data.livraison_necessaire', true);
        $response->assertJsonPath('data.adresse_livraison', '123 Rue de Test, 75001 Paris');
    }

    #[Test]
    public function it_can_create_reservation_with_acompte(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();

        $payload = $this->makeValidReservationPayload($client, [
            'statut' => 'en_attente_acompte',
            'acompte_demande' => true,
            'acompte_montant' => 75.00,
        ]);

        $response = $this->actingAs($user)->postJson('/api/reservations', $payload);

        $response->assertStatus(201);
        $response->assertJsonPath('data.acompte_demande', true);
        $response->assertJsonPath('data.acompte_montant', '75.00');
        $response->assertJsonPath('data.statut', 'en_attente_acompte');
    }

    #[Test]
    public function it_can_update_client_when_creating_reservation(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create([
            'prenom' => 'Ancien',
            'nom' => 'Nom',
            'telephone' => '0600000000',
        ]);

        $payload = $this->makeValidReservationPayload($client, [
            'update_client' => [
                'prenom' => 'Nouveau',
                'nom' => 'NomModifié',
                'telephone' => '0699999999',
                'email' => 'nouveau@example.com',
                'adresse' => 'Nouvelle adresse',
            ],
        ]);

        $response = $this->actingAs($user)->postJson('/api/reservations', $payload);

        $response->assertStatus(201);
        $response->assertJsonPath('data.client.prenom', 'Nouveau');
        $response->assertJsonPath('data.client.nom', 'NomModifié');
        $response->assertJsonPath('data.client.telephone', '0699999999');

        // Vérifier que le client a bien été mis à jour en base
        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'prenom' => 'Nouveau',
            'nom' => 'NomModifié',
            'telephone' => '0699999999',
            'email' => 'nouveau@example.com',
            'adresse' => 'Nouvelle adresse',
        ]);
    }
}
