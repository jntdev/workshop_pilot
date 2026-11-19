<?php

namespace Tests\Feature\Api;

use App\Models\AssistantMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AssistantTokenTest extends TestCase
{
    use RefreshDatabase;

    private const READ_TOKEN = 'secret-read-token';

    private const RELAY_TOKEN = 'secret-relay-token';

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'assistant.read_token' => self::READ_TOKEN,
            'assistant.relay_token' => self::RELAY_TOKEN,
            'ai.token' => 'secret-ai-token',
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function bearer(string $token): array
    {
        return ['Authorization' => 'Bearer '.$token];
    }

    #[Test]
    public function read_routes_reject_requests_without_token(): void
    {
        $this->getJson('/api/assistant-read/clients?search=du')->assertUnauthorized();
    }

    #[Test]
    public function read_routes_reject_a_wrong_token(): void
    {
        $this->getJson('/api/assistant-read/clients?search=du', $this->bearer('nope'))->assertUnauthorized();
    }

    #[Test]
    public function read_routes_reject_everything_when_no_token_is_configured(): void
    {
        config(['assistant.read_token' => '']);

        $this->getJson('/api/assistant-read/clients?search=du', $this->bearer(''))->assertUnauthorized();
        $this->getJson('/api/assistant-read/clients?search=du')->assertUnauthorized();
    }

    #[Test]
    public function the_relay_token_does_not_open_read_routes(): void
    {
        $this->getJson('/api/assistant-read/clients?search=du', $this->bearer(self::RELAY_TOKEN))->assertUnauthorized();
    }

    #[Test]
    public function the_read_token_does_not_open_relay_routes(): void
    {
        $this->postJson('/api/assistant-relay/claim', [], $this->bearer(self::READ_TOKEN))->assertUnauthorized();
    }

    #[Test]
    public function the_ai_api_token_opens_neither_assistant_scope(): void
    {
        $this->getJson('/api/assistant-read/clients?search=du', $this->bearer('secret-ai-token'))->assertUnauthorized();
        $this->postJson('/api/assistant-relay/claim', [], $this->bearer('secret-ai-token'))->assertUnauthorized();
    }

    #[Test]
    public function the_assistant_tokens_do_not_open_the_ai_api(): void
    {
        $this->getJson('/api/ai/clients', $this->bearer(self::READ_TOKEN))->assertUnauthorized();
        $this->getJson('/api/ai/clients', $this->bearer(self::RELAY_TOKEN))->assertUnauthorized();
    }

    #[Test]
    public function the_read_token_never_opens_session_protected_routes(): void
    {
        $this->getJson('/api/assistant/status', $this->bearer(self::READ_TOKEN))->assertUnauthorized();
    }

    #[Test]
    public function read_routes_accept_get_only(): void
    {
        $headers = $this->bearer(self::READ_TOKEN);

        foreach (['/api/assistant-read/clients', '/api/assistant-read/quotes', '/api/assistant-read/agenda', '/api/assistant-read/articles'] as $path) {
            $this->postJson($path, [], $headers)->assertStatus(405);
            $this->putJson($path, [], $headers)->assertStatus(405);
            $this->patchJson($path, [], $headers)->assertStatus(405);
            $this->deleteJson($path, [], $headers)->assertStatus(405);
        }

        foreach (['/api/assistant-read/clients/1', '/api/assistant-read/quotes/1'] as $path) {
            $this->postJson($path, [], $headers)->assertStatus(405);
            $this->putJson($path, [], $headers)->assertStatus(405);
            $this->patchJson($path, [], $headers)->assertStatus(405);
            $this->deleteJson($path, [], $headers)->assertStatus(405);
        }
    }

    #[Test]
    public function the_read_token_cannot_change_a_message_through_the_relay_routes(): void
    {
        $message = AssistantMessage::factory()->processing()->create();

        $this->postJson("/api/assistant-relay/messages/{$message->id}/answer", ['answer' => 'x'], $this->bearer(self::READ_TOKEN))
            ->assertUnauthorized();

        $this->assertSame('processing', $message->refresh()->status->value);
    }
}
