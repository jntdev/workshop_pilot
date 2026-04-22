<?php

namespace Tests\Feature\Api;

use App\Models\Quote;
use App\Models\QuoteLine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderLineIndexTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_returns_only_needs_order_lines_excluding_received_by_default(): void
    {
        $quote = Quote::factory()->create();

        QuoteLine::factory()->create(['quote_id' => $quote->id, 'needs_order' => true, 'ordered_at' => null, 'received_at' => null]);
        QuoteLine::factory()->create(['quote_id' => $quote->id, 'needs_order' => true, 'ordered_at' => now(), 'received_at' => now()]);
        QuoteLine::factory()->create(['quote_id' => $quote->id, 'needs_order' => false]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/quotes/order-lines')
            ->assertStatus(200);

        $this->assertCount(1, $response->json());
    }

    #[Test]
    public function it_includes_received_lines_when_filter_is_set(): void
    {
        $quote = Quote::factory()->create();

        QuoteLine::factory()->create(['quote_id' => $quote->id, 'needs_order' => true, 'ordered_at' => null, 'received_at' => null]);
        QuoteLine::factory()->create(['quote_id' => $quote->id, 'needs_order' => true, 'ordered_at' => now(), 'received_at' => now()]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/quotes/order-lines?include_received=1')
            ->assertStatus(200);

        $this->assertCount(2, $response->json());
    }

    #[Test]
    public function it_returns_expected_fields(): void
    {
        $quote = Quote::factory()->create();
        QuoteLine::factory()->create([
            'quote_id' => $quote->id,
            'needs_order' => true,
            'reference' => 'REF-TEST',
            'ordered_at' => null,
            'received_at' => null,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/quotes/order-lines')
            ->assertStatus(200);

        $item = $response->json(0);

        $this->assertArrayHasKey('quote_line_id', $item);
        $this->assertArrayHasKey('quote_id', $item);
        $this->assertArrayHasKey('client_id', $item);
        $this->assertArrayHasKey('client_nom_complet', $item);
        $this->assertArrayHasKey('bike_description', $item);
        $this->assertArrayHasKey('line_title', $item);
        $this->assertArrayHasKey('line_reference', $item);
        $this->assertArrayHasKey('quantity', $item);
        $this->assertArrayHasKey('needs_order', $item);
        $this->assertArrayHasKey('ordered_at', $item);
        $this->assertArrayHasKey('received_at', $item);
        $this->assertArrayHasKey('supply_status', $item);
        $this->assertSame('to_order', $item['supply_status']);
        $this->assertSame($quote->id, $item['quote_id']);
    }

    #[Test]
    public function it_returns_correct_supply_status_for_ordered_line(): void
    {
        $quote = Quote::factory()->create();
        QuoteLine::factory()->create([
            'quote_id' => $quote->id,
            'needs_order' => true,
            'ordered_at' => now(),
            'received_at' => null,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/quotes/order-lines')
            ->assertStatus(200);

        $this->assertSame('ordered', $response->json('0.supply_status'));
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $this->getJson('/api/quotes/order-lines')
            ->assertStatus(401);
    }
}
