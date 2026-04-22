<?php

namespace Tests\Feature\Api;

use App\Models\Quote;
use App\Models\QuoteLine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuoteLineSyncTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_preserves_quote_line_id_on_update(): void
    {
        $quote = Quote::factory()->create();
        $line = QuoteLine::factory()->create(['quote_id' => $quote->id]);

        $payload = $this->quotePayloadWithLines($quote, [
            array_merge($this->defaultLine(), ['id' => $line->id]),
        ]);

        $this->actingAs($this->user)
            ->putJson("/api/quotes/{$quote->id}", $payload)
            ->assertStatus(200);

        $this->assertDatabaseHas('quote_lines', ['id' => $line->id, 'quote_id' => $quote->id]);
    }

    #[Test]
    public function it_creates_a_new_line_when_no_id_is_provided(): void
    {
        $quote = Quote::factory()->create();

        $this->assertDatabaseCount('quote_lines', 0);

        $payload = $this->quotePayloadWithLines($quote, [
            $this->defaultLine(),
        ]);

        $this->actingAs($this->user)
            ->putJson("/api/quotes/{$quote->id}", $payload)
            ->assertStatus(200);

        $this->assertDatabaseCount('quote_lines', 1);
    }

    #[Test]
    public function it_deletes_lines_absent_from_payload(): void
    {
        $quote = Quote::factory()->create();
        $lineToKeep = QuoteLine::factory()->create(['quote_id' => $quote->id]);
        $lineToDelete = QuoteLine::factory()->create(['quote_id' => $quote->id]);

        $payload = $this->quotePayloadWithLines($quote, [
            array_merge($this->defaultLine(), ['id' => $lineToKeep->id]),
        ]);

        $this->actingAs($this->user)
            ->putJson("/api/quotes/{$quote->id}", $payload)
            ->assertStatus(200);

        $this->assertDatabaseHas('quote_lines', ['id' => $lineToKeep->id]);
        $this->assertDatabaseMissing('quote_lines', ['id' => $lineToDelete->id]);
    }

    #[Test]
    public function it_rejects_a_line_id_that_belongs_to_another_quote(): void
    {
        $quote = Quote::factory()->create();
        $otherLine = QuoteLine::factory()->create();

        $payload = $this->quotePayloadWithLines($quote, [
            array_merge($this->defaultLine(), ['id' => $otherLine->id]),
        ]);

        $this->actingAs($this->user)
            ->putJson("/api/quotes/{$quote->id}", $payload)
            ->assertStatus(422);
    }

    #[Test]
    public function it_persists_needs_order_and_order_tracking_fields(): void
    {
        $quote = Quote::factory()->create();

        $payload = $this->quotePayloadWithLines($quote, [
            array_merge($this->defaultLine(), [
                'reference' => 'REF-001',
                'needs_order' => true,
            ]),
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/quotes/{$quote->id}", $payload)
            ->assertStatus(200);

        $line = $quote->lines()->first();
        $this->assertTrue($line->needs_order);
        $this->assertNull($line->ordered_at);
        $this->assertNull($line->received_at);

        $response->assertJsonPath('lines.0.needs_order', true);
    }

    #[Test]
    public function it_rejects_needs_order_true_without_reference(): void
    {
        $quote = Quote::factory()->create();

        $payload = $this->quotePayloadWithLines($quote, [
            array_merge($this->defaultLine(), [
                'reference' => null,
                'needs_order' => true,
            ]),
        ]);

        $this->actingAs($this->user)
            ->putJson("/api/quotes/{$quote->id}", $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['lines.0.reference']);
    }

    #[Test]
    public function it_returns_supply_status_in_formatted_quote(): void
    {
        $quote = Quote::factory()->create();
        QuoteLine::factory()->create([
            'quote_id' => $quote->id,
            'needs_order' => true,
            'ordered_at' => null,
            'received_at' => null,
        ]);

        $this->actingAs($this->user)
            ->getJson("/api/quotes/{$quote->id}")
            ->assertStatus(200)
            ->assertJsonPath('lines.0.needs_order', true)
            ->assertJsonPath('lines.0.ordered_at', null)
            ->assertJsonPath('lines.0.received_at', null);
    }

    private function quotePayloadWithLines(Quote $quote, array $lines): array
    {
        return [
            'client_id' => $quote->client_id,
            'client_prenom' => $quote->client->prenom,
            'client_nom' => $quote->client->nom,
            'client_email' => $quote->client->email,
            'client_telephone' => $quote->client->telephone,
            'client_adresse' => $quote->client->adresse,
            'client_origine_contact' => null,
            'client_commentaires' => null,
            'client_avantage_type' => 'aucun',
            'client_avantage_valeur' => 0,
            'client_avantage_expiration' => null,
            'bike_description' => $quote->bike_description ?? 'VTT bleu',
            'reception_comment' => $quote->reception_comment ?? 'Révision complète',
            'valid_until' => now()->addDays(15)->format('Y-m-d'),
            'discount_type' => null,
            'discount_value' => null,
            'lines' => $lines,
            'totals' => [
                'total_ht' => 100,
                'total_tva' => 20,
                'total_ttc' => 120,
                'margin_total_ht' => 50,
            ],
        ];
    }

    private function defaultLine(): array
    {
        return [
            'title' => 'Réparation',
            'reference' => null,
            'quantity' => 1,
            'purchase_price_ht' => 50,
            'sale_price_ht' => 100,
            'sale_price_ttc' => 120,
            'margin_amount_ht' => 50,
            'margin_rate' => 50,
            'tva_rate' => 20,
            'line_purchase_ht' => 50,
            'line_margin_ht' => 50,
            'line_total_ht' => 100,
            'line_total_ttc' => 120,
            'needs_order' => false,
            'ordered_at' => null,
            'received_at' => null,
        ];
    }
}
