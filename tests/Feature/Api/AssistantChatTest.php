<?php

namespace Tests\Feature\Api;

use App\Models\AssistantMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AssistantChatTest extends TestCase
{
    use RefreshDatabase;

    private const RELAY_TOKEN = 'secret-relay-token';

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        config(['assistant.relay_token' => self::RELAY_TOKEN]);
        Cache::flush();
        $this->user = User::factory()->create();
    }

    /**
     * @return array<string, string>
     */
    private function relay(): array
    {
        return ['Authorization' => 'Bearer '.self::RELAY_TOKEN];
    }

    #[Test]
    public function the_chat_routes_require_a_logged_in_user(): void
    {
        $this->getJson('/api/assistant/status')->assertUnauthorized();
        $this->getJson('/api/assistant/messages?conversation_id='.Str::uuid())->assertUnauthorized();
        $this->postJson('/api/assistant/messages', ['question' => 'Salut'])->assertUnauthorized();
    }

    #[Test]
    public function a_question_is_stored_as_pending_with_a_conversation(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/assistant/messages', ['question' => 'Combien coûte une purge hydraulique ?'])
            ->assertCreated()
            ->assertJsonPath('status', 'pending')
            ->assertJsonPath('answer', null);

        $this->assertTrue(Str::isUuid($response->json('conversation_id')));
        $this->assertDatabaseHas('assistant_messages', [
            'user_id' => $this->user->id,
            'question' => 'Combien coûte une purge hydraulique ?',
            'status' => 'pending',
        ]);
    }

    #[Test]
    public function it_validates_the_question(): void
    {
        $this->actingAs($this->user)->postJson('/api/assistant/messages', [])->assertUnprocessable()->assertJsonValidationErrors('question');
        $this->actingAs($this->user)->postJson('/api/assistant/messages', ['question' => str_repeat('a', 2001)])->assertUnprocessable();
        $this->actingAs($this->user)->postJson('/api/assistant/messages', ['question' => 'Salut', 'conversation_id' => 'pas-un-uuid'])->assertUnprocessable();
    }

    #[Test]
    public function the_number_of_unfinished_questions_is_limited_per_user(): void
    {
        AssistantMessage::factory()->count(3)->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)->postJson('/api/assistant/messages', ['question' => 'Encore une'])->assertStatus(429);

        $other = User::factory()->create();
        $this->actingAs($other)->postJson('/api/assistant/messages', ['question' => 'Moi je peux'])->assertCreated();
    }

    #[Test]
    public function a_finished_question_does_not_count_in_the_limit(): void
    {
        AssistantMessage::factory()->count(3)->answered()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)->postJson('/api/assistant/messages', ['question' => 'Encore une'])->assertCreated();
    }

    #[Test]
    public function a_user_only_sees_their_own_messages(): void
    {
        $conversation = (string) Str::uuid();
        $mine = AssistantMessage::factory()->answered('Ma réponse')->create(['user_id' => $this->user->id, 'conversation_id' => $conversation]);
        $theirs = AssistantMessage::factory()->answered('Leur réponse')->create(['conversation_id' => $conversation]);

        $this->actingAs($this->user)->getJson("/api/assistant/messages?conversation_id={$conversation}")
            ->assertOk()->assertJsonCount(1)->assertJsonPath('0.id', $mine->id);

        $this->actingAs($this->user)->getJson("/api/assistant/messages/{$mine->id}")->assertOk()->assertJsonPath('answer', 'Ma réponse');
        $this->actingAs($this->user)->getJson("/api/assistant/messages/{$theirs->id}")->assertNotFound();
    }

    #[Test]
    public function the_relay_claims_the_oldest_pending_question_once(): void
    {
        $first = AssistantMessage::factory()->create(['question' => 'Première']);
        $second = AssistantMessage::factory()->create(['question' => 'Deuxième']);

        $this->postJson('/api/assistant-relay/claim', [], $this->relay())
            ->assertOk()
            ->assertJsonPath('id', $first->id)
            ->assertJsonPath('question', 'Première')
            ->assertJsonPath('session_id', null);

        $this->assertSame('processing', $first->refresh()->status->value);
        $this->assertNotNull($first->claimed_at);

        $this->postJson('/api/assistant-relay/claim', [], $this->relay())->assertOk()->assertJsonPath('id', $second->id);
        $this->postJson('/api/assistant-relay/claim', [], $this->relay())->assertNoContent();
    }

    #[Test]
    public function the_claim_returns_the_previous_session_of_the_conversation(): void
    {
        $conversation = (string) Str::uuid();
        AssistantMessage::factory()->answered()->create(['conversation_id' => $conversation, 'claude_session_id' => 'session-1']);
        $next = AssistantMessage::factory()->create(['conversation_id' => $conversation]);
        AssistantMessage::factory()->answered()->create(['claude_session_id' => 'autre-conversation']);

        $this->postJson('/api/assistant-relay/claim', [], $this->relay())
            ->assertOk()
            ->assertJsonPath('id', $next->id)
            ->assertJsonPath('session_id', 'session-1');
    }

    #[Test]
    public function the_relay_answers_a_claimed_question(): void
    {
        $message = AssistantMessage::factory()->processing()->create();

        $this->postJson("/api/assistant-relay/messages/{$message->id}/answer", [
            'answer' => 'La purge hydraulique coûte 33 €.',
            'session_id' => 'session-42',
            'consultations' => ['Fiche forfaits', 'Devis DEV-1234'],
        ], $this->relay())->assertOk()->assertJsonPath('status', 'answered');

        $message->refresh();
        $this->assertSame('answered', $message->status->value);
        $this->assertSame('La purge hydraulique coûte 33 €.', $message->answer);
        $this->assertSame('session-42', $message->claude_session_id);
        $this->assertSame(['Fiche forfaits', 'Devis DEV-1234'], $message->consultations);
        $this->assertNotNull($message->answered_at);
    }

    #[Test]
    public function an_answer_is_refused_for_a_question_not_being_processed(): void
    {
        $pending = AssistantMessage::factory()->create();
        $done = AssistantMessage::factory()->answered('Déjà répondu')->create();

        $this->postJson("/api/assistant-relay/messages/{$pending->id}/answer", ['answer' => 'x'], $this->relay())->assertStatus(409);
        $this->postJson("/api/assistant-relay/messages/{$done->id}/answer", ['answer' => 'écrasé'], $this->relay())->assertStatus(409);

        $this->assertSame('Déjà répondu', $done->refresh()->answer);
    }

    #[Test]
    public function the_relay_can_report_a_failure(): void
    {
        $message = AssistantMessage::factory()->processing()->create();

        $this->postJson("/api/assistant-relay/messages/{$message->id}/fail", ['error' => 'Claude indisponible'], $this->relay())
            ->assertOk()->assertJsonPath('status', 'failed');

        $this->assertSame('Claude indisponible', $message->refresh()->error);
    }

    #[Test]
    public function the_answer_must_not_be_empty(): void
    {
        $message = AssistantMessage::factory()->processing()->create();

        $this->postJson("/api/assistant-relay/messages/{$message->id}/answer", ['answer' => ''], $this->relay())->assertUnprocessable();
    }

    #[Test]
    public function a_question_left_processing_too_long_is_marked_failed(): void
    {
        config(['assistant.processing_timeout_seconds' => 300]);
        $stale = AssistantMessage::factory()->processing()->create(['claimed_at' => now()->subMinutes(10)]);
        $recent = AssistantMessage::factory()->processing()->create(['claimed_at' => now()->subMinute()]);

        $this->actingAs($this->user)->getJson("/api/assistant/messages/{$stale->id}")->assertNotFound();

        $stale->update(['user_id' => $this->user->id]);
        $this->actingAs($this->user)->getJson("/api/assistant/messages/{$stale->id}")
            ->assertOk()->assertJsonPath('status', 'failed');

        $this->assertSame('processing', $recent->refresh()->status->value);
    }

    #[Test]
    public function the_status_reports_whether_the_relay_polled_recently(): void
    {
        $this->actingAs($this->user)->getJson('/api/assistant/status')->assertOk()->assertJsonPath('online', false);

        $this->postJson('/api/assistant-relay/claim', [], $this->relay())->assertNoContent();

        $this->actingAs($this->user)->getJson('/api/assistant/status')->assertOk()->assertJsonPath('online', true);

        $this->travel(2)->minutes();

        $this->actingAs($this->user)->getJson('/api/assistant/status')->assertOk()->assertJsonPath('online', false);
    }

    #[Test]
    public function the_answer_is_returned_as_plain_text_without_html_interpretation(): void
    {
        $message = AssistantMessage::factory()->answered('<script>alert(1)</script>')->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)->getJson("/api/assistant/messages/{$message->id}")
            ->assertOk()
            ->assertJsonPath('answer', '<script>alert(1)</script>')
            ->assertHeader('Content-Type', 'application/json');
    }
}
