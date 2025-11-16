<?php

namespace Tests\Feature\Livewire\Clients;

use App\Livewire\Clients\Index;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_renders_successfully(): void
    {
        Livewire::test(Index::class)
            ->assertStatus(200);
    }

    public function test_displays_all_clients(): void
    {
        $clients = Client::factory()->count(3)->create([
            'prenom' => 'Jean',
            'nom' => 'Dupont',
        ]);

        Livewire::test(Index::class)
            ->assertSee('Jean')
            ->assertSee('Dupont');
    }

    public function test_clients_are_ordered_alphabetically(): void
    {
        Client::factory()->create(['nom' => 'Zorro', 'prenom' => 'Diego']);
        Client::factory()->create(['nom' => 'Martin', 'prenom' => 'Marie']);
        Client::factory()->create(['nom' => 'Dupont', 'prenom' => 'Jean']);

        $component = Livewire::test(Index::class);

        $clients = $component->get('filteredClients');

        $this->assertEquals('Dupont', $clients[0]->nom);
        $this->assertEquals('Martin', $clients[1]->nom);
        $this->assertEquals('Zorro', $clients[2]->nom);
    }

    public function test_search_filters_clients_by_name(): void
    {
        Client::factory()->create(['prenom' => 'Jean', 'nom' => 'Dupont']);
        Client::factory()->create(['prenom' => 'Marie', 'nom' => 'Martin']);

        Livewire::test(Index::class)
            ->set('search', 'Jean')
            ->assertSee('Jean')
            ->assertSee('Dupont')
            ->assertDontSee('Marie');
    }

    public function test_search_filters_clients_by_email(): void
    {
        Client::factory()->create(['prenom' => 'Jean', 'nom' => 'Dupont', 'email' => 'jean@example.com']);
        Client::factory()->create(['prenom' => 'Marie', 'nom' => 'Martin', 'email' => 'marie@example.com']);

        Livewire::test(Index::class)
            ->set('search', 'jean@example.com')
            ->assertSee('jean@example.com')
            ->assertDontSee('marie@example.com');
    }

    public function test_search_filters_clients_by_phone(): void
    {
        Client::factory()->create(['prenom' => 'Jean', 'nom' => 'Dupont', 'telephone' => '0612345678']);
        Client::factory()->create(['prenom' => 'Marie', 'nom' => 'Martin', 'telephone' => '0698765432']);

        Livewire::test(Index::class)
            ->set('search', '0612345678')
            ->assertSee('0612345678')
            ->assertDontSee('0698765432');
    }

    public function test_search_is_case_insensitive(): void
    {
        Client::factory()->create(['prenom' => 'Jean', 'nom' => 'Dupont']);

        Livewire::test(Index::class)
            ->set('search', 'JEAN')
            ->assertSee('Jean');
    }

    public function test_empty_search_shows_all_clients(): void
    {
        Client::factory()->create(['prenom' => 'Jean', 'nom' => 'Dupont']);
        Client::factory()->create(['prenom' => 'Marie', 'nom' => 'Martin']);

        Livewire::test(Index::class)
            ->set('search', '')
            ->assertSee('Jean')
            ->assertSee('Marie');
    }
}
