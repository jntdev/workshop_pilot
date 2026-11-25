<?php

namespace Tests\Feature\Api;

use App\Models\Quote;
use App\Models\QuoteLine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AiQuoteEditTest extends TestCase
{
    use RefreshDatabase;

    private const TOKEN = 'secret-ai-token';

    private const INTUVIA = [
        'title' => 'Bosch Intuvia Control Unit',
        'reference' => '2100601',
        'purchase_price_ht' => 33.25,
        'sale_price_ttc' => 52.33,
    ];

    protected function setUp(): void
    {
        parent::setUp();
        config(['ai.token' => self::TOKEN]);
        $this->withHeader('Authorization', 'Bearer '.self::TOKEN);
    }

    private function emptyQuote(array $attributes = []): Quote
    {
        return Quote::factory()->create(array_merge([
            'total_ht' => 0, 'total_tva' => 0, 'total_ttc' => 0, 'margin_total_ht' => 0,
            'discount_type' => null, 'discount_value' => null,
        ], $attributes));
    }

    #[Test]
    public function it_requires_the_ai_token(): void
    {
        $quote = $this->emptyQuote();

        $this->withHeader('Authorization', 'Bearer mauvais')
            ->postJson("/api/ai/quotes/{$quote->id}/lines", self::INTUVIA)
            ->assertUnauthorized();
    }

    #[Test]
    public function a_dry_run_shows_the_line_and_totals_without_saving(): void
    {
        $quote = $this->emptyQuote();

        $this->postJson("/api/ai/quotes/{$quote->id}/lines", self::INTUVIA + ['dry_run' => true])
            ->assertOk()
            ->assertJsonPath('dry_run', true)
            ->assertJsonPath('line.sale_price_ht', '43.60')
            ->assertJsonPath('line.margin_amount_ht', '10.35')
            ->assertJsonPath('totals_before.total_ttc', '0.00')
            ->assertJsonPath('totals_after.total_ttc', '52.33')
            ->assertJsonMissingPath('quote');

        $this->assertSame(0, $quote->lines()->count());
        $this->assertSame('0.00', $quote->fresh()->total_ttc);
    }

    #[Test]
    public function it_adds_lines_and_recalculates_the_totals(): void
    {
        $quote = $this->emptyQuote();

        $this->postJson("/api/ai/quotes/{$quote->id}/lines", self::INTUVIA)
            ->assertCreated()
            ->assertJsonPath('dry_run', false)
            ->assertJsonPath('line.title', 'Bosch Intuvia Control Unit')
            ->assertJsonPath('line.position', 0)
            ->assertJsonPath('totals_after.total_ttc', '52.33')
            ->assertJsonPath('quote.lines.0.reference', '2100601');

        $quote->refresh();
        $this->assertSame(['43.60', '8.73', '52.33', '10.35'], [$quote->total_ht, $quote->total_tva, $quote->total_ttc, $quote->margin_total_ht]);

        $this->postJson("/api/ai/quotes/{$quote->id}/lines", ['title' => 'Main-d\'œuvre', 'sale_price_ttc' => 30, 'estimated_time_minutes' => 30])
            ->assertCreated()
            ->assertJsonPath('line.position', 1)
            ->assertJsonPath('totals_after.total_ttc', '82.33')
            ->assertJsonPath('totals_after.total_estimated_time_minutes', 30);
    }

    #[Test]
    public function it_updates_a_line_keeping_the_sale_price_unless_a_new_one_is_given(): void
    {
        $quote = $this->emptyQuote();
        $lineId = $this->postJson("/api/ai/quotes/{$quote->id}/lines", self::INTUVIA)->json('line.id');

        $this->patchJson("/api/ai/quotes/{$quote->id}/lines/{$lineId}", ['quantity' => 2])
            ->assertOk()
            ->assertJsonPath('line.sale_price_ttc', '52.33')
            ->assertJsonPath('line.line_total_ttc', '104.66')
            ->assertJsonPath('totals_after.total_ttc', '104.66');

        $this->patchJson("/api/ai/quotes/{$quote->id}/lines/{$lineId}", ['quantity' => 1, 'purchase_price_ht' => 30])
            ->assertOk()
            ->assertJsonPath('line.sale_price_ttc', '52.33')
            ->assertJsonPath('line.margin_amount_ht', '13.60');

        $this->patchJson("/api/ai/quotes/{$quote->id}/lines/{$lineId}", ['sale_price_ht' => 50])
            ->assertOk()
            ->assertJsonPath('line.sale_price_ttc', '60.00')
            ->assertJsonPath('line.title', 'Bosch Intuvia Control Unit')
            ->assertJsonPath('line.reference', '2100601');

        $this->assertSame('60.00', $quote->fresh()->total_ttc);
    }

    #[Test]
    public function it_deletes_a_line(): void
    {
        $quote = $this->emptyQuote();
        $lineId = $this->postJson("/api/ai/quotes/{$quote->id}/lines", self::INTUVIA)->json('line.id');

        $this->deleteJson("/api/ai/quotes/{$quote->id}/lines/{$lineId}", ['dry_run' => true])
            ->assertOk()
            ->assertJsonPath('totals_after.total_ttc', '0.00');
        $this->assertSame(1, $quote->lines()->count());

        $this->deleteJson("/api/ai/quotes/{$quote->id}/lines/{$lineId}")
            ->assertOk()
            ->assertJsonPath('line.id', $lineId)
            ->assertJsonPath('quote.total_ttc', '0.00');

        $this->assertSame(0, $quote->lines()->count());
        $this->assertSoftDeleted('quote_lines', ['id' => $lineId]);
    }

    #[Test]
    public function it_updates_the_header_and_applies_the_discount(): void
    {
        $quote = $this->emptyQuote();
        $this->postJson("/api/ai/quotes/{$quote->id}/lines", self::INTUVIA)->assertCreated();

        $this->patchJson("/api/ai/quotes/{$quote->id}", ['email_note' => 'Pièce posée, vélo prêt.', 'discount_value' => 10])
            ->assertOk()
            ->assertJsonPath('fields.discount_type', 'percent')
            ->assertJsonPath('quote.email_note', 'Pièce posée, vélo prêt.')
            ->assertJsonPath('totals_after.total_ht', '39.24');

        $this->patchJson("/api/ai/quotes/{$quote->id}", ['discount_value' => 0])
            ->assertOk()
            ->assertJsonPath('fields.discount_type', null)
            ->assertJsonPath('totals_after.total_ttc', '52.33');

        $this->patchJson("/api/ai/quotes/{$quote->id}", [])->assertUnprocessable();
    }

    #[Test]
    public function it_refuses_to_change_an_invoice(): void
    {
        $quote = $this->emptyQuote(['invoiced_at' => now(), 'total_ttc' => 52.33]);
        $line = QuoteLine::factory()->create(['quote_id' => $quote->id]);

        $this->postJson("/api/ai/quotes/{$quote->id}/lines", self::INTUVIA)->assertUnprocessable();
        $this->patchJson("/api/ai/quotes/{$quote->id}/lines/{$line->id}", ['quantity' => 3])->assertUnprocessable();
        $this->deleteJson("/api/ai/quotes/{$quote->id}/lines/{$line->id}")->assertUnprocessable();
        $this->patchJson("/api/ai/quotes/{$quote->id}", ['email_note' => 'x'])->assertUnprocessable();

        $this->assertSame(1, $quote->lines()->count());
        $this->assertSame('52.33', $quote->fresh()->total_ttc);
    }

    #[Test]
    public function it_validates_lines(): void
    {
        $quote = $this->emptyQuote();
        $other = QuoteLine::factory()->create();

        $this->patchJson("/api/ai/quotes/{$quote->id}/lines/{$other->id}", ['quantity' => 2])->assertNotFound();
        $this->postJson("/api/ai/quotes/{$quote->id}/lines", ['title' => 'Sans prix'])->assertUnprocessable();
        $this->postJson("/api/ai/quotes/{$quote->id}/lines", ['title' => 'À commander', 'sale_price_ttc' => 10, 'needs_order' => true])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('reference');

        $this->assertSame(0, $quote->lines()->count());
    }
}
