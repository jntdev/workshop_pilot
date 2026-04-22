<?php

namespace Tests\Feature\Api;

use App\Models\QuoteLine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuoteLineOrderStatusTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_transitions_from_to_order_to_ordered(): void
    {
        $line = QuoteLine::factory()->create([
            'needs_order' => true,
            'ordered_at' => null,
            'received_at' => null,
        ]);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/quote-lines/{$line->id}/order-status", [
                'mark_as_ordered' => true,
            ])
            ->assertStatus(200);

        $this->assertSame('ordered', $response->json('supply_status'));
        $this->assertNotNull($response->json('ordered_at'));
        $this->assertNull($response->json('received_at'));
    }

    #[Test]
    public function it_transitions_from_ordered_to_received(): void
    {
        $line = QuoteLine::factory()->create([
            'needs_order' => true,
            'ordered_at' => now()->subDay(),
            'received_at' => null,
        ]);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/quote-lines/{$line->id}/order-status", [
                'mark_as_received' => true,
            ])
            ->assertStatus(200);

        $this->assertSame('received', $response->json('supply_status'));
        $this->assertNotNull($response->json('received_at'));
    }

    #[Test]
    public function it_forces_ordered_at_when_marking_received_without_it(): void
    {
        $line = QuoteLine::factory()->create([
            'needs_order' => true,
            'ordered_at' => null,
            'received_at' => null,
        ]);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/quote-lines/{$line->id}/order-status", [
                'mark_as_received' => true,
            ])
            ->assertStatus(200);

        $this->assertSame('received', $response->json('supply_status'));
        $this->assertNotNull($response->json('ordered_at'));
        $this->assertNotNull($response->json('received_at'));
    }

    #[Test]
    public function it_allows_unmarking_ordered_before_received(): void
    {
        $line = QuoteLine::factory()->create([
            'needs_order' => true,
            'ordered_at' => now(),
            'received_at' => null,
        ]);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/quote-lines/{$line->id}/order-status", [
                'unmark' => true,
            ])
            ->assertStatus(200);

        $this->assertSame('to_order', $response->json('supply_status'));
        $this->assertNull($response->json('ordered_at'));
    }

    #[Test]
    public function it_blocks_unmark_once_received(): void
    {
        $line = QuoteLine::factory()->create([
            'needs_order' => true,
            'ordered_at' => now()->subDay(),
            'received_at' => now(),
        ]);

        $this->actingAs($this->user)
            ->patchJson("/api/quote-lines/{$line->id}/order-status", [
                'unmark' => true,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    #[Test]
    public function it_returns_supply_status_from_model(): void
    {
        $line = QuoteLine::factory()->create([
            'needs_order' => true,
            'ordered_at' => null,
            'received_at' => null,
        ]);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/quote-lines/{$line->id}/order-status", [
                'mark_as_ordered' => true,
            ])
            ->assertStatus(200);

        $this->assertArrayHasKey('supply_status', $response->json());
        $this->assertArrayHasKey('quote_line_id', $response->json());
        $this->assertArrayHasKey('ordered_at', $response->json());
        $this->assertArrayHasKey('received_at', $response->json());
    }
}
