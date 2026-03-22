<?php

namespace Tests\Feature\Clients;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_all_clients(): void
    {
        $user = User::factory()->create();
        $clients = Client::factory()->count(3)->create();

        $response = $this->actingAs($user)->getJson('/api/clients');

        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => ['id', 'prenom', 'nom', 'telephone', 'email'],
            ]);
    }

    public function test_can_get_single_client(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create([
            'prenom' => 'Jean',
            'nom' => 'Dupont',
        ]);

        $response = $this->actingAs($user)->getJson("/api/clients/{$client->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $client->id,
                    'prenom' => 'Jean',
                    'nom' => 'Dupont',
                ],
            ]);
    }

    public function test_returns_404_for_nonexistent_client(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/clients/999');

        $response->assertStatus(404);
    }

    public function test_can_update_client(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create([
            'prenom' => 'Jean',
            'nom' => 'Dupont',
        ]);

        $updateData = [
            'prenom' => 'Pierre',
            'nom' => 'Martin',
            'telephone' => '0612345678',
            'avantage_type' => 'aucun',
            'avantage_valeur' => 0,
        ];

        $response = $this->actingAs($user)->putJson("/api/clients/{$client->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'prenom' => 'Pierre',
                    'nom' => 'Martin',
                ],
            ]);

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'prenom' => 'Pierre',
            'nom' => 'Martin',
        ]);
    }

    public function test_update_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();

        $response = $this->actingAs($user)->putJson("/api/clients/{$client->id}", [
            'prenom' => '',
            'nom' => 'Test',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['prenom']);
    }

    public function test_can_delete_client(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/api/clients/{$client->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('clients', [
            'id' => $client->id,
        ]);
    }

    public function test_delete_returns_404_for_nonexistent_client(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->deleteJson('/api/clients/999');

        $response->assertStatus(404);
    }

    public function test_unauthenticated_user_cannot_access_api(): void
    {
        $response = $this->getJson('/api/clients');

        $response->assertStatus(401);
    }
}
