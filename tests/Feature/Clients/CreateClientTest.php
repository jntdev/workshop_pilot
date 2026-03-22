<?php

namespace Tests\Feature\Clients;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_be_created_with_valid_data(): void
    {
        $user = User::factory()->create();

        $clientData = [
            'prenom' => 'Jean',
            'nom' => 'Dupont',
            'telephone' => '0123456789',
            'email' => 'jean.dupont@example.com',
            'adresse' => '123 Rue de la Paix, Paris',
            'origine_contact' => 'Recommandation',
            'commentaires' => 'Client préféré',
            'avantage_type' => 'pourcentage',
            'avantage_valeur' => 10.00,
            'avantage_expiration' => '2025-12-31',
        ];

        $response = $this->actingAs($user)->postJson('/api/clients', $clientData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'prenom',
                    'nom',
                    'telephone',
                    'email',
                    'adresse',
                    'origine_contact',
                    'commentaires',
                    'avantage_type',
                    'avantage_valeur',
                    'avantage_expiration',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJson([
                'data' => [
                    'prenom' => 'Jean',
                    'nom' => 'Dupont',
                    'telephone' => '0123456789',
                    'email' => 'jean.dupont@example.com',
                ],
            ]);

        $this->assertDatabaseHas('clients', [
            'prenom' => 'Jean',
            'nom' => 'Dupont',
            'email' => 'jean.dupont@example.com',
        ]);
    }

    public function test_client_creation_requires_prenom(): void
    {
        $user = User::factory()->create();

        $clientData = [
            'nom' => 'Dupont',
            'telephone' => '0123456789',
            'avantage_type' => 'aucun',
            'avantage_valeur' => 0,
        ];

        $response = $this->actingAs($user)->postJson('/api/clients', $clientData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['prenom']);
    }

    public function test_client_creation_requires_nom(): void
    {
        $user = User::factory()->create();

        $clientData = [
            'prenom' => 'Jean',
            'telephone' => '0123456789',
            'avantage_type' => 'aucun',
            'avantage_valeur' => 0,
        ];

        $response = $this->actingAs($user)->postJson('/api/clients', $clientData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nom']);
    }

    public function test_client_creation_requires_telephone(): void
    {
        $user = User::factory()->create();

        $clientData = [
            'prenom' => 'Jean',
            'nom' => 'Dupont',
            'avantage_type' => 'aucun',
            'avantage_valeur' => 0,
        ];

        $response = $this->actingAs($user)->postJson('/api/clients', $clientData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['telephone']);
    }

    public function test_client_email_must_be_unique(): void
    {
        $user = User::factory()->create();
        Client::factory()->create(['email' => 'test@example.com']);

        $clientData = [
            'prenom' => 'Jean',
            'nom' => 'Dupont',
            'telephone' => '0123456789',
            'email' => 'test@example.com',
            'avantage_type' => 'aucun',
            'avantage_valeur' => 0,
        ];

        $response = $this->actingAs($user)->postJson('/api/clients', $clientData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_avantage_pourcentage_validates_value_range(): void
    {
        $user = User::factory()->create();

        $clientData = [
            'prenom' => 'Jean',
            'nom' => 'Dupont',
            'telephone' => '0123456789',
            'avantage_type' => 'pourcentage',
            'avantage_valeur' => 150,
        ];

        $response = $this->actingAs($user)->postJson('/api/clients', $clientData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['avantage_valeur']);
    }

    public function test_avantage_montant_validates_positive_value(): void
    {
        $user = User::factory()->create();

        $clientData = [
            'prenom' => 'Jean',
            'nom' => 'Dupont',
            'telephone' => '0123456789',
            'avantage_type' => 'montant',
            'avantage_valeur' => 0,
        ];

        $response = $this->actingAs($user)->postJson('/api/clients', $clientData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['avantage_valeur']);
    }

    public function test_avantage_aucun_requires_zero_value(): void
    {
        $user = User::factory()->create();

        $clientData = [
            'prenom' => 'Jean',
            'nom' => 'Dupont',
            'telephone' => '0123456789',
            'avantage_type' => 'aucun',
            'avantage_valeur' => 10,
        ];

        $response = $this->actingAs($user)->postJson('/api/clients', $clientData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['avantage_valeur']);
    }
}
