<?php

namespace Tests\Feature\Livewire\Clients;

use App\Livewire\Clients\Form;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FormTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_can_render(): void
    {
        Livewire::test(Form::class)
            ->assertStatus(200)
            ->assertSee('Nouveau client');
    }

    public function test_can_save_client_with_valid_data(): void
    {
        Livewire::test(Form::class)
            ->set('prenom', 'Jean')
            ->set('nom', 'Dupont')
            ->set('telephone', '0123456789')
            ->set('email', 'jean.dupont@example.com')
            ->set('adresse', '123 Rue de la Paix')
            ->set('origine_contact', 'Recommandation')
            ->set('commentaires', 'Bon client')
            ->set('avantage_type', 'pourcentage')
            ->set('avantage_valeur', 10)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('clients', [
            'prenom' => 'Jean',
            'nom' => 'Dupont',
            'email' => 'jean.dupont@example.com',
        ]);
    }

    public function test_redirects_to_list_after_create(): void
    {
        Livewire::test(Form::class)
            ->set('prenom', 'Jean')
            ->set('nom', 'Dupont')
            ->set('telephone', '0123456789')
            ->set('avantage_type', 'aucun')
            ->set('avantage_valeur', 0)
            ->call('save')
            ->assertRedirect(route('clients.index'));
    }

    public function test_prenom_is_required(): void
    {
        Livewire::test(Form::class)
            ->set('nom', 'Dupont')
            ->set('telephone', '0123456789')
            ->set('avantage_type', 'aucun')
            ->set('avantage_valeur', 0)
            ->call('save')
            ->assertHasErrors(['prenom' => 'required']);
    }

    public function test_nom_is_required(): void
    {
        Livewire::test(Form::class)
            ->set('prenom', 'Jean')
            ->set('telephone', '0123456789')
            ->set('avantage_type', 'aucun')
            ->set('avantage_valeur', 0)
            ->call('save')
            ->assertHasErrors(['nom' => 'required']);
    }

    public function test_telephone_is_required(): void
    {
        Livewire::test(Form::class)
            ->set('prenom', 'Jean')
            ->set('nom', 'Dupont')
            ->set('avantage_type', 'aucun')
            ->set('avantage_valeur', 0)
            ->call('save')
            ->assertHasErrors(['telephone' => 'required']);
    }

    public function test_email_must_be_valid(): void
    {
        Livewire::test(Form::class)
            ->set('prenom', 'Jean')
            ->set('nom', 'Dupont')
            ->set('telephone', '0123456789')
            ->set('email', 'invalid-email')
            ->set('avantage_type', 'aucun')
            ->set('avantage_valeur', 0)
            ->call('save')
            ->assertHasErrors(['email' => 'email']);
    }

    public function test_email_must_be_unique(): void
    {
        Client::factory()->create(['email' => 'test@example.com']);

        Livewire::test(Form::class)
            ->set('prenom', 'Jean')
            ->set('nom', 'Dupont')
            ->set('telephone', '0123456789')
            ->set('email', 'test@example.com')
            ->set('avantage_type', 'aucun')
            ->set('avantage_valeur', 0)
            ->call('save')
            ->assertHasErrors(['email' => 'unique']);
    }

    public function test_avantage_pourcentage_validates_range(): void
    {
        Livewire::test(Form::class)
            ->set('prenom', 'Jean')
            ->set('nom', 'Dupont')
            ->set('telephone', '0123456789')
            ->set('avantage_type', 'pourcentage')
            ->set('avantage_valeur', 150)
            ->call('save')
            ->assertHasErrors('avantage_valeur');
    }

    public function test_success_message_is_displayed_after_save(): void
    {
        Livewire::test(Form::class)
            ->set('prenom', 'Jean')
            ->set('nom', 'Dupont')
            ->set('telephone', '0123456789')
            ->set('avantage_type', 'aucun')
            ->set('avantage_valeur', 0)
            ->call('save')
            ->assertDispatched('client-saved');
    }

    // Tests en mode édition

    public function test_loads_client_data_in_edit_mode(): void
    {
        $client = Client::factory()->create([
            'prenom' => 'Marie',
            'nom' => 'Martin',
            'telephone' => '0987654321',
            'email' => 'marie@example.com',
        ]);

        Livewire::test(Form::class, ['clientId' => $client->id])
            ->assertSet('clientId', $client->id)
            ->assertSet('prenom', 'Marie')
            ->assertSet('nom', 'Martin')
            ->assertSet('telephone', '0987654321')
            ->assertSet('email', 'marie@example.com');
    }

    public function test_can_update_client_in_edit_mode(): void
    {
        $client = Client::factory()->create([
            'prenom' => 'Marie',
            'nom' => 'Martin',
            'telephone' => '0987654321',
        ]);

        Livewire::test(Form::class, ['clientId' => $client->id])
            ->set('prenom', 'Marie-Claude')
            ->set('nom', 'Dupuis')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'prenom' => 'Marie-Claude',
            'nom' => 'Dupuis',
        ]);
    }

    public function test_does_not_reset_fields_after_update(): void
    {
        $client = Client::factory()->create([
            'prenom' => 'Marie',
            'nom' => 'Martin',
            'telephone' => '0987654321',
        ]);

        Livewire::test(Form::class, ['clientId' => $client->id])
            ->set('prenom', 'Marie-Claude')
            ->call('save')
            ->assertSet('prenom', 'Marie-Claude')
            ->assertSet('nom', 'Martin');
    }

    public function test_email_unique_validation_excludes_current_client(): void
    {
        $client1 = Client::factory()->create(['email' => 'test@example.com']);
        $client2 = Client::factory()->create(['email' => 'other@example.com']);

        // Should be able to keep same email when editing
        Livewire::test(Form::class, ['clientId' => $client1->id])
            ->set('prenom', $client1->prenom)
            ->set('nom', $client1->nom)
            ->set('telephone', $client1->telephone)
            ->set('email', 'test@example.com')
            ->set('avantage_type', 'aucun')
            ->set('avantage_valeur', 0)
            ->call('save')
            ->assertHasNoErrors();

        // Should NOT be able to use another client's email
        Livewire::test(Form::class, ['clientId' => $client2->id])
            ->set('prenom', $client2->prenom)
            ->set('nom', $client2->nom)
            ->set('telephone', $client2->telephone)
            ->set('email', 'test@example.com')
            ->set('avantage_type', 'aucun')
            ->set('avantage_valeur', 0)
            ->call('save')
            ->assertHasErrors(['email' => 'unique']);
    }

    public function test_can_delete_client(): void
    {
        $client = Client::factory()->create();

        Livewire::test(Form::class, ['clientId' => $client->id])
            ->call('delete')
            ->assertDispatched('client-deleted');

        $this->assertDatabaseMissing('clients', [
            'id' => $client->id,
        ]);
    }

    public function test_delete_only_available_in_edit_mode(): void
    {
        Livewire::test(Form::class)
            ->call('delete')
            ->assertHasErrors();
    }

    public function test_displays_create_title_in_create_mode(): void
    {
        Livewire::test(Form::class)
            ->assertSee('Nouveau client');
    }

    public function test_displays_edit_title_in_edit_mode(): void
    {
        $client = Client::factory()->create([
            'prenom' => 'Jean',
            'nom' => 'Dupont',
        ]);

        Livewire::test(Form::class, ['clientId' => $client->id])
            ->assertSee('Fiche client : Jean Dupont');
    }
}
