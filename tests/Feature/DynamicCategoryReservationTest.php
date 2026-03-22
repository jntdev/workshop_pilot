<?php

namespace Tests\Feature;

use App\Models\BikeCategory;
use App\Models\BikeType;
use App\Models\Client;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DynamicCategoryReservationTest extends TestCase
{
    protected function getTestUser(): User
    {
        return User::firstOrCreate(
            ['email' => 'test-dynamic-category@workshop-pilot.com'],
            [
                'name' => 'Test Dynamic Category User',
                'password' => bcrypt('password'),
            ]
        );
    }

    #[Test]
    public function it_can_create_a_category_without_size_and_frame_type(): void
    {
        $user = $this->getTestUser();
        $name = 'Accessoire_'.time();

        $response = $this->actingAs($user)->postJson('/api/bike-categories', [
            'name' => $name,
            'color' => '#FF9900',
            'has_battery' => false,
            'has_size' => false,
            'has_frame_type' => false,
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('name', $name);
        $response->assertJsonPath('has_battery', false);

        $this->assertDatabaseHas('bike_categories', [
            'name' => $name,
            'has_size' => false,
            'has_frame_type' => false,
        ]);
    }

    #[Test]
    public function it_can_create_a_category_with_size_and_frame_type(): void
    {
        $user = $this->getTestUser();
        $name = 'VTT_Elec_'.time();

        $response = $this->actingAs($user)->postJson('/api/bike-categories', [
            'name' => $name,
            'color' => '#00CC00',
            'has_battery' => true,
            'has_size' => true,
            'has_frame_type' => true,
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('has_battery', true);

        $this->assertDatabaseHas('bike_categories', [
            'name' => $name,
            'has_size' => true,
            'has_frame_type' => true,
        ]);
    }

    #[Test]
    public function it_defaults_has_size_and_has_frame_type_to_true(): void
    {
        $user = $this->getTestUser();
        $name = 'CatDefaut_'.time();

        $response = $this->actingAs($user)->postJson('/api/bike-categories', [
            'name' => $name,
            'color' => '#0000FF',
            'has_battery' => false,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('bike_categories', [
            'name' => $name,
            'has_size' => true,
            'has_frame_type' => true,
        ]);
    }

    #[Test]
    public function it_can_create_bike_type_for_dynamic_category_and_reserve(): void
    {
        $user = $this->getTestUser();

        // 1. S'assurer qu'une catégorie sans taille ni cadre existe
        BikeCategory::firstOrCreate(
            ['name' => 'Remorque'],
            [
                'color' => '#FF0000',
                'has_battery' => false,
                'has_size' => false,
                'has_frame_type' => false,
                'sort_order' => 99,
            ]
        );

        // 2. Créer un BikeType correspondant (sans size ni frame_type)
        $bikeType = BikeType::firstOrCreate(
            ['id' => 'Remorque_'],
            [
                'category' => 'Remorque',
                'size' => null,
                'frame_type' => null,
                'label' => 'Remorque',
                'stock' => 5,
            ]
        );

        $this->assertDatabaseHas('bike_types', [
            'id' => 'Remorque_',
            'category' => 'Remorque',
        ]);

        // 3. Créer un client et une réservation avec ce type
        $client = Client::factory()->create();

        $reservationResponse = $this->actingAs($user)->postJson('/api/reservations', [
            'client_id' => $client->id,
            'date_contact' => now()->format('Y-m-d H:i:s'),
            'date_reservation' => now()->addDays(7)->format('Y-m-d'),
            'date_retour' => now()->addDays(14)->format('Y-m-d'),
            'livraison_necessaire' => false,
            'recuperation_necessaire' => false,
            'prix_total_ttc' => 50.00,
            'acompte_demande' => false,
            'statut' => 'reserve',
            'commentaires' => 'Réservation avec catégorie dynamique',
            'items' => [
                [
                    'bike_type_id' => $bikeType->id,
                    'quantite' => 2,
                ],
            ],
        ]);

        $reservationResponse->assertStatus(201);
        $reservationResponse->assertJsonPath('data.statut', 'reserve');
        $reservationResponse->assertJsonPath('data.prix_total_ttc', '50.00');
        $this->assertCount(1, $reservationResponse->json('data.items'));
        $reservationResponse->assertJsonPath('data.items.0.bike_type_id', 'Remorque_');
        $reservationResponse->assertJsonPath('data.items.0.quantite', 2);
    }

    #[Test]
    public function it_can_mix_standard_and_dynamic_category_items_in_reservation(): void
    {
        $user = $this->getTestUser();

        // BikeType standard (VAE avec taille et cadre)
        $standardBikeType = BikeType::firstOrCreate(
            ['id' => 'VAE_mb'],
            [
                'category' => 'VAE',
                'size' => 'M',
                'frame_type' => 'b',
                'label' => 'VAE M cadre bas',
                'stock' => 3,
            ]
        );

        // BikeType dynamique (sans taille ni cadre)
        $dynamicBikeType = BikeType::firstOrCreate(
            ['id' => 'Accessoire_'],
            [
                'category' => 'Accessoire',
                'size' => null,
                'frame_type' => null,
                'label' => 'Accessoire',
                'stock' => 10,
            ]
        );

        $client = Client::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/reservations', [
            'client_id' => $client->id,
            'date_contact' => now()->format('Y-m-d H:i:s'),
            'date_reservation' => now()->addDays(3)->format('Y-m-d'),
            'date_retour' => now()->addDays(10)->format('Y-m-d'),
            'livraison_necessaire' => false,
            'recuperation_necessaire' => false,
            'prix_total_ttc' => 320.00,
            'acompte_demande' => false,
            'statut' => 'reserve',
            'items' => [
                [
                    'bike_type_id' => $standardBikeType->id,
                    'quantite' => 1,
                ],
                [
                    'bike_type_id' => $dynamicBikeType->id,
                    'quantite' => 3,
                ],
            ],
        ]);

        $response->assertStatus(201);
        $this->assertCount(2, $response->json('data.items'));

        $items = $response->json('data.items');
        $bikeTypeIds = array_column($items, 'bike_type_id');
        $this->assertContains('VAE_mb', $bikeTypeIds);
        $this->assertContains('Accessoire_', $bikeTypeIds);
    }

    #[Test]
    public function it_can_update_category_has_size_and_has_frame_type(): void
    {
        $user = $this->getTestUser();
        $name = 'Cargo_'.time();

        // Créer une catégorie avec taille et cadre
        $createResponse = $this->actingAs($user)->postJson('/api/bike-categories', [
            'name' => $name,
            'color' => '#663399',
            'has_battery' => true,
            'has_size' => true,
            'has_frame_type' => true,
        ]);
        $createResponse->assertStatus(201);
        $categoryId = $createResponse->json('id');

        // Mettre à jour pour retirer taille et cadre
        $updateResponse = $this->actingAs($user)->putJson("/api/bike-categories/{$categoryId}", [
            'has_size' => false,
            'has_frame_type' => false,
        ]);

        $updateResponse->assertStatus(200);

        $this->assertDatabaseHas('bike_categories', [
            'id' => $categoryId,
            'has_size' => false,
            'has_frame_type' => false,
        ]);
    }
}
