<?php

namespace Tests\Feature\Clients;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ClientNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_navigate_from_list_to_create_and_back(): void
    {
        $user = User::factory()->create();

        // Accès à la liste
        $response = $this->actingAs($user)->get(route('clients.index'));
        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Clients/Index')
        );

        // Navigation vers création
        $response = $this->actingAs($user)->get(route('clients.create'));
        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Clients/Form')
            ->missing('client')
        );
    }

    public function test_can_create_client_and_see_in_list(): void
    {
        $user = User::factory()->create();

        // Création d'un client via API
        $clientData = [
            'prenom' => 'Test',
            'nom' => 'Navigation',
            'telephone' => '0123456789',
            'avantage_type' => 'aucun',
            'avantage_valeur' => 0,
        ];

        $this->actingAs($user)->postJson('/api/clients', $clientData)
            ->assertStatus(201);

        // Vérification dans la liste
        $response = $this->actingAs($user)->get(route('clients.index'));
        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Clients/Index')
            ->has('clients', 1)
        );
    }

    public function test_can_navigate_from_list_to_detail_and_back(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create([
            'prenom' => 'Jean',
            'nom' => 'Dupont',
        ]);

        // Accès à la liste
        $response = $this->actingAs($user)->get(route('clients.index'));
        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Clients/Index')
            ->has('clients', 1)
        );

        // Navigation vers détail
        $response = $this->actingAs($user)->get(route('clients.show', $client->id));
        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Clients/Form')
            ->has('client')
            ->where('client.prenom', 'Jean')
            ->where('client.nom', 'Dupont')
        );
    }

    public function test_can_update_client_from_detail_page(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create([
            'prenom' => 'Marie',
            'nom' => 'Martin',
            'telephone' => '0987654321',
        ]);

        // Accès à la page de modification
        $response = $this->actingAs($user)->get(route('clients.show', $client->id));
        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Clients/Form')
            ->has('client')
            ->where('client.prenom', 'Marie')
            ->where('client.nom', 'Martin')
        );
    }

    public function test_list_page_shows_all_clients_in_alphabetical_order(): void
    {
        $user = User::factory()->create();
        Client::factory()->create(['nom' => 'Zorro', 'prenom' => 'Diego']);
        Client::factory()->create(['nom' => 'Dupont', 'prenom' => 'Jean']);
        Client::factory()->create(['nom' => 'Martin', 'prenom' => 'Marie']);

        $response = $this->actingAs($user)->get(route('clients.index'));
        $response->assertStatus(200);

        // Vérifier que tous les clients sont affichés et triés par nom
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Clients/Index')
            ->has('clients', 3)
            ->where('clients.0.nom', 'Dupont')
            ->where('clients.1.nom', 'Martin')
            ->where('clients.2.nom', 'Zorro')
        );
    }

    public function test_create_page_displays_form(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('clients.create'));

        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Clients/Form')
            ->missing('client')
        );
    }

    public function test_edit_page_displays_client_data(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create([
            'prenom' => 'Alice',
            'nom' => 'Wonderland',
            'telephone' => '0611111111',
        ]);

        $response = $this->actingAs($user)->get(route('clients.show', $client->id));

        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Clients/Form')
            ->has('client')
            ->where('client.prenom', 'Alice')
            ->where('client.nom', 'Wonderland')
            ->where('client.telephone', '0611111111')
        );
    }

    public function test_routes_are_correctly_defined(): void
    {
        $user = User::factory()->create();

        $this->assertTrue(route('clients.index') !== null);
        $this->assertTrue(route('clients.create') !== null);

        $client = Client::factory()->create();
        $this->assertTrue(route('clients.show', $client->id) !== null);
    }
}
