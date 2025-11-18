<?php

namespace Tests\Feature\Feature\Quotes;

use App\Enums\QuoteStatus;
use App\Models\Quote;
use App\Models\QuoteLine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuoteStatusTransitionsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_allows_transition_from_draft_to_ready(): void
    {
        $user = User::factory()->create();
        $quote = Quote::factory()->create(['status' => QuoteStatus::Draft]);

        QuoteLine::factory()->create([
            'quote_id' => $quote->id,
            'purchase_price_ht' => '100.00',
        ]);

        $this->assertTrue($quote->status->canTransitionTo(QuoteStatus::Ready));

        $quote->markAsReady();
        $this->assertEquals(QuoteStatus::Ready, $quote->fresh()->status);
    }

    #[Test]
    public function it_allows_transition_from_ready_to_editable(): void
    {
        $quote = Quote::factory()->create(['status' => QuoteStatus::Ready]);

        $this->assertTrue($quote->status->canTransitionTo(QuoteStatus::Editable));

        $quote->markAsModifiable();
        $this->assertEquals(QuoteStatus::Editable, $quote->fresh()->status);
    }

    #[Test]
    public function it_allows_transition_from_ready_to_invoiced(): void
    {
        $quote = Quote::factory()->create(['status' => QuoteStatus::Ready]);

        QuoteLine::factory()->create([
            'quote_id' => $quote->id,
            'purchase_price_ht' => '100.00',
        ]);

        $this->assertTrue($quote->status->canTransitionTo(QuoteStatus::Invoiced));

        $quote->markAsInvoiced();
        $this->assertEquals(QuoteStatus::Invoiced, $quote->fresh()->status);
    }

    #[Test]
    public function it_allows_transition_from_editable_to_draft(): void
    {
        $quote = Quote::factory()->create(['status' => QuoteStatus::Editable]);

        $this->assertTrue($quote->status->canTransitionTo(QuoteStatus::Draft));

        $quote->update(['status' => QuoteStatus::Draft]);
        $this->assertEquals(QuoteStatus::Draft, $quote->fresh()->status);
    }

    #[Test]
    public function it_allows_transition_from_editable_to_invoiced(): void
    {
        $quote = Quote::factory()->create(['status' => QuoteStatus::Editable]);

        QuoteLine::factory()->create([
            'quote_id' => $quote->id,
            'purchase_price_ht' => '100.00',
        ]);

        $this->assertTrue($quote->status->canTransitionTo(QuoteStatus::Invoiced));

        $quote->markAsInvoiced();
        $this->assertEquals(QuoteStatus::Invoiced, $quote->fresh()->status);
    }

    #[Test]
    public function it_blocks_invoicing_with_incomplete_lines(): void
    {
        $quote = Quote::factory()->create(['status' => QuoteStatus::Ready]);

        QuoteLine::factory()->create([
            'quote_id' => $quote->id,
            'purchase_price_ht' => null,
        ]);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("Impossible de facturer : 1 ligne(s) sans prix d'achat");

        $quote->markAsInvoiced();
    }

    #[Test]
    public function it_prevents_transitions_from_invoiced_status(): void
    {
        $quote = Quote::factory()->create(['status' => QuoteStatus::Invoiced]);

        $this->assertFalse($quote->status->canTransitionTo(QuoteStatus::Draft));
        $this->assertFalse($quote->status->canTransitionTo(QuoteStatus::Ready));
        $this->assertFalse($quote->status->canTransitionTo(QuoteStatus::Editable));
        $this->assertEquals([], $quote->status->allowedTransitions());
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
    public function it_allows_invoicing_with_all_complete_lines(): void
    {
        $quote = Quote::factory()->create(['status' => QuoteStatus::Ready]);

        QuoteLine::factory()->count(3)->create([
            'quote_id' => $quote->id,
            'purchase_price_ht' => '100.00',
        ]);

        $this->assertFalse($quote->hasIncompleteLines());
        $this->assertEquals(0, $quote->getIncompleteLinesCount());
        $this->assertTrue($quote->canBeInvoiced());

        $quote->markAsInvoiced();
        $this->assertEquals(QuoteStatus::Invoiced, $quote->fresh()->status);
    }
}
