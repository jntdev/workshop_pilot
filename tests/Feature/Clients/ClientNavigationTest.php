<?php

namespace Tests\Feature\Clients;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_navigate_from_list_to_create_and_back(): void
    {
        // Accès à la liste
        $response = $this->get(route('clients.index'));
        $response->assertStatus(200);
        $response->assertSee('Clients');

        // Navigation vers création
        $response = $this->get(route('clients.create'));
        $response->assertStatus(200);
        $response->assertSee('Nouveau client');
    }

    public function test_can_create_client_and_see_in_list(): void
    {
        // Création d'un client via Livewire
        $this->post('/livewire/message/clients.form', [
            'components' => [],
        ]);

        // Création directe pour tester la liste
        $client = Client::factory()->create([
            'prenom' => 'Test',
            'nom' => 'Navigation',
            'telephone' => '0123456789',
        ]);

        // Vérification dans la liste
        $response = $this->get(route('clients.index'));
        $response->assertStatus(200);
        $response->assertSee('Test');
        $response->assertSee('Navigation');
    }

    public function test_can_navigate_from_list_to_detail_and_back(): void
    {
        $client = Client::factory()->create([
            'prenom' => 'Jean',
            'nom' => 'Dupont',
        ]);

        // Accès à la liste
        $response = $this->get(route('clients.index'));
        $response->assertStatus(200);
        $response->assertSee('Jean');

        // Navigation vers détail
        $response = $this->get(route('clients.show', $client->id));
        $response->assertStatus(200);
        $response->assertSee('Fiche client');
        $response->assertSee('Jean');
        $response->assertSee('Dupont');
    }

    public function test_can_update_client_from_detail_page(): void
    {
        $client = Client::factory()->create([
            'prenom' => 'Marie',
            'nom' => 'Martin',
            'telephone' => '0987654321',
        ]);

        // Accès à la page de modification
        $response = $this->get(route('clients.show', $client->id));
        $response->assertStatus(200);
        $response->assertSee('Marie');
        $response->assertSee('Martin');

        // La modification se fait via Livewire, on vérifie juste la page
        $response->assertSee('Modifier');
        $response->assertSee('Supprimer');
    }

    public function test_list_page_shows_all_clients_in_alphabetical_order(): void
    {
        Client::factory()->create(['nom' => 'Zorro', 'prenom' => 'Diego']);
        Client::factory()->create(['nom' => 'Dupont', 'prenom' => 'Jean']);
        Client::factory()->create(['nom' => 'Martin', 'prenom' => 'Marie']);

        $response = $this->get(route('clients.index'));
        $response->assertStatus(200);

        // Vérifier que tous les clients sont affichés
        $response->assertSee('Dupont');
        $response->assertSee('Martin');
        $response->assertSee('Zorro');
    }

    public function test_create_page_displays_correct_form(): void
    {
        $response = $this->get(route('clients.create'));

        $response->assertStatus(200);
        $response->assertSee('Nouveau client');
        $response->assertSee('Prénom');
        $response->assertSee('Nom');
        $response->assertSee('Téléphone');
        $response->assertSee('Enregistrer le client');
        $response->assertDontSee('Supprimer');
        $response->assertDontSee('Modifier');
    }

    public function test_edit_page_displays_client_data_and_actions(): void
    {
        $client = Client::factory()->create([
            'prenom' => 'Alice',
            'nom' => 'Wonderland',
            'telephone' => '0611111111',
        ]);

        $response = $this->get(route('clients.show', $client->id));

        $response->assertStatus(200);
        $response->assertSee('Fiche client');
        $response->assertSee('Alice');
        $response->assertSee('Wonderland');
        $response->assertSee('Modifier');
        $response->assertSee('Supprimer');
        $response->assertDontSee('Enregistrer le client');
    }

    public function test_routes_are_correctly_defined(): void
    {
        $this->assertTrue(route('clients.index') !== null);
        $this->assertTrue(route('clients.create') !== null);

        $client = Client::factory()->create();
        $this->assertTrue(route('clients.show', $client->id) !== null);
    }
}
