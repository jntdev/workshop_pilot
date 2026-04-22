<?php

namespace Tests\Feature\Quotes;

use App\Models\QuoteLine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuoteLineSupplyStatusTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_to_order_when_needs_order_is_true_and_no_dates_set(): void
    {
        $line = QuoteLine::factory()->create([
            'needs_order' => true,
            'ordered_at' => null,
            'received_at' => null,
        ]);

        $this->assertSame('to_order', $line->supply_status);
    }

    #[Test]
    public function it_returns_ordered_when_ordered_at_is_set_but_not_received(): void
    {
        $line = QuoteLine::factory()->create([
            'needs_order' => true,
            'ordered_at' => now(),
            'received_at' => null,
        ]);

        $this->assertSame('ordered', $line->supply_status);
    }

    #[Test]
    public function it_returns_received_when_received_at_is_set(): void
    {
        $line = QuoteLine::factory()->create([
            'needs_order' => true,
            'ordered_at' => now()->subDay(),
            'received_at' => now(),
        ]);

        $this->assertSame('received', $line->supply_status);
    }
}
