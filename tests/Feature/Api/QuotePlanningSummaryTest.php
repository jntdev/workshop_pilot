<?php

namespace Tests\Feature\Api;

use App\Models\Quote;
use App\Models\QuoteLine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuotePlanningSummaryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_returns_the_reception_comment_and_tasks(): void
    {
        $quote = Quote::factory()->create(['reception_comment' => 'Freins qui frottent']);
        QuoteLine::factory()->create(['quote_id' => $quote->id, 'title' => 'Remplacement plaquettes', 'quantity' => 2]);

        $response = $this->actingAs($this->user)->getJson("/api/quotes/{$quote->id}/planning-summary");

        $response->assertOk();
        $this->assertSame('Freins qui frottent', $response->json('reception_comment'));
        $this->assertCount(1, $response->json('tasks'));
        $this->assertSame('Remplacement plaquettes', $response->json('tasks.0.title'));
        $this->assertEquals(2.0, $response->json('tasks.0.quantity'));
    }

    #[Test]
    public function it_does_not_expose_prices_or_margins(): void
    {
        $quote = Quote::factory()->create();
        QuoteLine::factory()->create(['quote_id' => $quote->id]);

        $response = $this->actingAs($this->user)->getJson("/api/quotes/{$quote->id}/planning-summary");

        $response->assertOk();
        $task = $response->json('tasks.0');
        $this->assertArrayNotHasKey('sale_price_ttc', $task);
        $this->assertArrayNotHasKey('margin_amount_ht', $task);
        $this->assertArrayNotHasKey('purchase_price_ht', $task);
    }

    #[Test]
    public function it_returns_null_when_there_is_no_reception_comment(): void
    {
        $quote = Quote::factory()->create(['reception_comment' => null]);

        $response = $this->actingAs($this->user)->getJson("/api/quotes/{$quote->id}/planning-summary");

        $response->assertOk();
        $this->assertNull($response->json('reception_comment'));
        $this->assertSame([], $response->json('tasks'));
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $quote = Quote::factory()->create();

        $this->getJson("/api/quotes/{$quote->id}/planning-summary")->assertStatus(401);
    }
}
