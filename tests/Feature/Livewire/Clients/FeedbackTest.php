<?php

namespace Tests\Feature\Livewire\Clients;

use App\Livewire\Clients\Form;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_dispatches_feedback_event_on_client_creation(): void
    {
        Livewire::test(Form::class)
            ->set('prenom', 'Jean')
            ->set('nom', 'Dupont')
            ->set('telephone', '0123456789')
            ->set('avantage_type', 'aucun')
            ->set('avantage_valeur', 0)
            ->call('save')
            ->assertDispatched('feedback-banner', function ($event, $params) {
                return $params['type'] === 'success'
                    && $params['message'] === 'Client créé avec succès.';
            });
    }

    public function test_flashes_feedback_session_on_client_update(): void
    {
        $client = Client::factory()->create();

        Livewire::test(Form::class, ['clientId' => $client->id])
            ->set('prenom', 'Jean')
            ->set('nom', 'Dupont')
            ->set('telephone', '0123456789')
            ->set('avantage_type', 'aucun')
            ->set('avantage_valeur', 0)
            ->call('save');

        $this->assertEquals('success', session('feedback')['type']);
        $this->assertEquals('Client modifié avec succès.', session('feedback')['message']);
    }

    public function test_flashes_feedback_session_on_client_deletion(): void
    {
        $client = Client::factory()->create();

        Livewire::test(Form::class, ['clientId' => $client->id])
            ->call('delete');

        $this->assertEquals('success', session('feedback')['type']);
        $this->assertEquals('Client supprimé avec succès.', session('feedback')['message']);
    }
}
