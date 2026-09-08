<?php

namespace Tests\Feature\Api;

use App\Enums\QuoteStatus;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuoteStatusUpdateTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_accepts_quote_to_order_status(): void
    {
        $quote = Quote::factory()->create(['status' => QuoteStatus::Validated]);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/quotes/{$quote->id}/status", ['status' => 'quote_to_order'])
            ->assertStatus(200);

        $this->assertSame('quote_to_order', $response->json('status'));
        $this->assertSame(QuoteStatus::QuoteToOrder, $quote->fresh()->status);
    }

    #[Test]
    public function it_accepts_quote_ordered_status(): void
    {
        $quote = Quote::factory()->create(['status' => QuoteStatus::QuoteToOrder]);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/quotes/{$quote->id}/status", ['status' => 'quote_ordered'])
            ->assertStatus(200);

        $this->assertSame('quote_ordered', $response->json('status'));
        $this->assertSame(QuoteStatus::QuoteOrdered, $quote->fresh()->status);
    }

    #[Test]
    public function it_accepts_quote_received_status(): void
    {
        $quote = Quote::factory()->create(['status' => QuoteStatus::QuoteOrdered]);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/quotes/{$quote->id}/status", ['status' => 'quote_received'])
            ->assertStatus(200);

        $this->assertSame('quote_received', $response->json('status'));
        $this->assertSame(QuoteStatus::QuoteReceived, $quote->fresh()->status);
    }

    #[Test]
    public function it_lists_the_new_statuses_in_quote_statuses(): void
    {
        $values = collect(QuoteStatus::quoteStatuses())->map(fn (QuoteStatus $s) => $s->value);

        $this->assertTrue($values->contains('quote_to_order'));
        $this->assertTrue($values->contains('quote_ordered'));
        $this->assertTrue($values->contains('quote_received'));
    }
}
