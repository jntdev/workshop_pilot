<?php

namespace Tests\Feature\Api;

use App\Models\AssistantMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AssistantJournalTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_journal_requires_a_logged_in_user(): void
    {
        $this->getJson('/api/assistant/journal')->assertUnauthorized();
        $this->get('/assistant/journal')->assertRedirect();
    }

    #[Test]
    public function it_lists_every_exchange_newest_first_with_who_asked_and_what_was_consulted(): void
    {
        $mechanic = User::factory()->create(['name' => 'Le mécanicien']);
        $first = AssistantMessage::factory()->answered('Réponse 1')->create(['user_id' => $mechanic->id, 'question' => 'Première ?']);
        $second = AssistantMessage::factory()->answered('Réponse 2')->create([
            'question' => 'Deuxième ?',
            'consultations' => ['devis n°12', 'fichier connaissances/forfaits-et-prix.md'],
        ]);

        $this->actingAs(User::factory()->create())->getJson('/api/assistant/journal')
            ->assertOk()
            ->assertJsonPath('total', 2)
            ->assertJsonPath('data.0.id', $second->id)
            ->assertJsonPath('data.0.consultations.0', 'devis n°12')
            ->assertJsonPath('data.1.id', $first->id)
            ->assertJsonPath('data.1.user', 'Le mécanicien')
            ->assertJsonPath('data.1.answer', 'Réponse 1')
            ->assertJsonPath('data.1.status_label', 'Répondu');
    }

    #[Test]
    public function it_paginates(): void
    {
        AssistantMessage::factory()->count(35)->answered()->create();

        $this->actingAs(User::factory()->create())->getJson('/api/assistant/journal')
            ->assertOk()->assertJsonCount(30, 'data')->assertJsonPath('last_page', 2);

        $this->actingAs(User::factory()->create())->getJson('/api/assistant/journal?page=2')
            ->assertOk()->assertJsonCount(5, 'data');
    }
}
