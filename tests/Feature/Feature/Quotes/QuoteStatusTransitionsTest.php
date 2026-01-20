<?php

namespace Tests\Feature\Feature\Quotes;

use App\Models\Quote;
use App\Models\QuoteLine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests pour la détection des lignes incomplètes.
 * Note: Les tests de transitions de statut ont été supprimés avec le workflow 7.1.
 */
class QuoteStatusTransitionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    #[Test]
    public function it_detects_incomplete_lines(): void
    {
        $quote = Quote::factory()->create();

        QuoteLine::factory()->create([
            'quote_id' => $quote->id,
            'purchase_price_ht' => '100.00',
        ]);

        QuoteLine::factory()->create([
            'quote_id' => $quote->id,
            'purchase_price_ht' => null,
        ]);

        $this->assertTrue($quote->hasIncompleteLines());
        $this->assertEquals(1, $quote->getIncompleteLinesCount());
        $this->assertFalse($quote->canBeInvoiced());
    }

    #[Test]
    public function it_detects_complete_lines(): void
    {
        $quote = Quote::factory()->create();

        QuoteLine::factory()->count(3)->create([
            'quote_id' => $quote->id,
            'purchase_price_ht' => '100.00',
        ]);

        $this->assertFalse($quote->hasIncompleteLines());
        $this->assertEquals(0, $quote->getIncompleteLinesCount());
        $this->assertTrue($quote->canBeInvoiced());
    }
}
