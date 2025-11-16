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
            ->assertSee('Formulaire Client');
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

    public function test_form_fields_are_reset_after_save(): void
    {
        Livewire::test(Form::class)
            ->set('prenom', 'Jean')
            ->set('nom', 'Dupont')
            ->set('telephone', '0123456789')
            ->set('avantage_type', 'aucun')
            ->set('avantage_valeur', 0)
            ->call('save')
            ->assertSet('prenom', '')
            ->assertSet('nom', '')
            ->assertSet('telephone', '')
            ->assertSet('email', '')
            ->assertSet('avantage_type', 'aucun')
            ->assertSet('avantage_valeur', 0);
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
}
