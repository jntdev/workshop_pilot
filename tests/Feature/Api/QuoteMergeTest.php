<?php

namespace Tests\Feature\Api;

use App\Models\Quote;
use App\Models\QuoteLine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuoteMergeTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_saves_silently_when_two_tabs_edit_different_fields(): void
    {
        $quote = Quote::factory()->create(['bike_description' => 'VTT bleu', 'remarks' => null]);
        $base = $this->basePayload($quote);

        // L'autre onglet a déjà sauvegardé un changement sur bike_description.
        $quote->update(['bike_description' => 'VTT bleu, garde-boue cassé']);

        $mine = $base;
        $mine['remarks'] = 'Roue avant voilée';

        $response = $this->actingAs($this->user)
            ->putJson("/api/quotes/{$quote->id}", ['base' => $base] + $mine)
            ->assertStatus(200);

        $response->assertJsonPath('bike_description', 'VTT bleu, garde-boue cassé');
        $response->assertJsonPath('remarks', 'Roue avant voilée');
    }

    #[Test]
    public function it_returns_conflict_when_same_field_edited_differently(): void
    {
        $quote = Quote::factory()->create(['bike_description' => 'VTT bleu']);
        $base = $this->basePayload($quote);

        $quote->update(['bike_description' => 'VTT bleu, garde-boue cassé']);

        $mine = $base;
        $mine['bike_description'] = 'VTT bleu, roue avant voilée';

        $response = $this->actingAs($this->user)
            ->putJson("/api/quotes/{$quote->id}", ['base' => $base] + $mine)
            ->assertStatus(409);

        $response->assertJsonPath('conflict', true);
        $fields = collect($response->json('fields'));
        $conflict = $fields->firstWhere('path', 'bike_description');
        $this->assertNotNull($conflict);
        $this->assertSame('VTT bleu, roue avant voilée', $conflict['mine']);
        $this->assertSame('VTT bleu, garde-boue cassé', $conflict['theirs']);
    }

    #[Test]
    public function it_saves_silently_when_same_field_edited_to_the_same_value(): void
    {
        $quote = Quote::factory()->create(['bike_description' => 'VTT bleu']);
        $base = $this->basePayload($quote);

        $quote->update(['bike_description' => 'VTT bleu, révisé']);

        $mine = $base;
        $mine['bike_description'] = 'VTT bleu, révisé';

        $this->actingAs($this->user)
            ->putJson("/api/quotes/{$quote->id}", ['base' => $base] + $mine)
            ->assertStatus(200)
            ->assertJsonPath('bike_description', 'VTT bleu, révisé');
    }

    #[Test]
    public function it_merges_a_new_line_added_by_each_tab_without_conflict(): void
    {
        $quote = Quote::factory()->create();
        $base = $this->basePayload($quote);

        // L'autre onglet ajoute une ligne et sauvegarde déjà.
        $theirsLine = QuoteLine::factory()->create($this->lineAttributes($quote, ['title' => 'Ligne des autres']));

        $mine = $base;
        $mine['lines'] = [
            array_merge($this->defaultLine(), ['title' => 'Ma ligne', 'client_key' => 'client-key-1']),
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/quotes/{$quote->id}", ['base' => $base] + $mine)
            ->assertStatus(200);

        $titles = collect($response->json('lines'))->pluck('title');
        $this->assertTrue($titles->contains('Ma ligne'));
        $this->assertTrue($titles->contains('Ligne des autres'));
        $this->assertDatabaseHas('quote_lines', ['id' => $theirsLine->id]);
    }

    #[Test]
    public function it_returns_line_conflict_when_same_line_modified_by_both(): void
    {
        $quote = Quote::factory()->create();
        $line = QuoteLine::factory()->create($this->lineAttributes($quote, ['sale_price_ttc' => '80.00']));
        $base = $this->basePayload($quote);

        $line->update(['sale_price_ttc' => '95.00']);

        $mine = $base;
        $mine['lines'] = [
            array_merge($this->defaultLine(), ['id' => $line->id, 'sale_price_ttc' => 60]),
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/quotes/{$quote->id}", ['base' => $base] + $mine)
            ->assertStatus(409);

        $lineConflicts = collect($response->json('lines'));
        $conflict = $lineConflicts->firstWhere('type', 'line_conflict');
        $this->assertNotNull($conflict);
        $this->assertSame('id:'.$line->id, $conflict['line_key']);
        $this->assertEquals(60, $conflict['mine']['sale_price_ttc']);
        $this->assertSame('95.00', $conflict['theirs']['sale_price_ttc']);
    }

    #[Test]
    public function it_returns_delete_vs_update_conflict(): void
    {
        $quote = Quote::factory()->create();
        $line = QuoteLine::factory()->create($this->lineAttributes($quote));
        $base = $this->basePayload($quote);

        $line->update(['sale_price_ttc' => '150.00']);

        $mine = $base;
        $mine['lines'] = []; // mine supprime la ligne

        $response = $this->actingAs($this->user)
            ->putJson("/api/quotes/{$quote->id}", ['base' => $base] + $mine)
            ->assertStatus(409);

        $lineConflicts = collect($response->json('lines'));
        $this->assertNotNull($lineConflicts->firstWhere('type', 'delete_vs_update'));
    }

    #[Test]
    public function it_returns_update_vs_delete_conflict(): void
    {
        $quote = Quote::factory()->create();
        $line = QuoteLine::factory()->create($this->lineAttributes($quote));
        $base = $this->basePayload($quote);

        $line->delete();

        $mine = $base;
        $mine['lines'] = [
            array_merge($this->defaultLine(), ['id' => $line->id, 'title' => 'Modifiée par moi']),
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/quotes/{$quote->id}", ['base' => $base] + $mine)
            ->assertStatus(409);

        $lineConflicts = collect($response->json('lines'));
        $this->assertNotNull($lineConflicts->firstWhere('type', 'update_vs_delete'));
    }

    #[Test]
    public function it_recalculates_totals_from_server_regardless_of_client_totals(): void
    {
        $quote = Quote::factory()->create();
        $base = $this->basePayload($quote);

        $mine = $base;
        $mine['lines'] = [$this->defaultLine()];
        $mine['totals'] = [
            'total_ht' => 999999,
            'total_tva' => 999999,
            'total_ttc' => 999999,
            'margin_total_ht' => 999999,
        ];

        $this->actingAs($this->user)
            ->putJson("/api/quotes/{$quote->id}", ['base' => $base] + $mine)
            ->assertStatus(200);

        $quote->refresh();
        $this->assertNotEquals('999999.00', $quote->total_ttc);
        $this->assertNotEquals('999999.00', $quote->total_ht);
        $this->assertNotEquals('999999.00', $quote->margin_total_ht);
    }

    #[Test]
    public function it_applies_resolved_conflicts_on_second_submission(): void
    {
        $quote = Quote::factory()->create(['bike_description' => 'VTT bleu']);
        $base = $this->basePayload($quote);

        $quote->update(['bike_description' => 'VTT bleu, garde-boue cassé']);

        $mine = $base;
        $mine['bike_description'] = 'VTT bleu, roue avant voilée';

        $this->actingAs($this->user)
            ->putJson("/api/quotes/{$quote->id}", ['base' => $base] + $mine)
            ->assertStatus(409);

        $resolved = $mine;
        $resolved['resolved_conflicts'] = [
            'fields' => ['bike_description' => 'VTT bleu, roue avant voilée'],
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/quotes/{$quote->id}", ['base' => $base] + $resolved)
            ->assertStatus(200);

        $response->assertJsonPath('bike_description', 'VTT bleu, roue avant voilée');
    }

    #[Test]
    public function it_blocks_save_when_quote_became_invoice_concurrently(): void
    {
        $quote = Quote::factory()->create();
        $base = $this->basePayload($quote);

        $quote->convertToInvoice();

        $response = $this->actingAs($this->user)
            ->putJson("/api/quotes/{$quote->id}", ['base' => $base] + $base)
            ->assertStatus(422);

        $response->assertJsonPath('became_invoice', true);
    }

    #[Test]
    public function it_returns_404_when_quote_was_deleted_concurrently(): void
    {
        $quote = Quote::factory()->create();
        $base = $this->basePayload($quote);

        $quote->delete();

        $this->actingAs($this->user)
            ->putJson("/api/quotes/{$quote->id}", ['base' => $base] + $base)
            ->assertStatus(404);
    }

    #[Test]
    public function it_does_not_conflict_on_reordering_alone(): void
    {
        $quote = Quote::factory()->create();
        $lineA = QuoteLine::factory()->create($this->lineAttributes($quote, ['title' => 'A', 'position' => 0]));
        $lineB = QuoteLine::factory()->create($this->lineAttributes($quote, ['title' => 'B', 'position' => 1]));
        $base = $this->basePayload($quote);

        $baseLinesById = collect($base['lines'])->keyBy('id');

        $mine = $base;
        $mine['lines'] = [
            $baseLinesById[$lineB->id],
            $baseLinesById[$lineA->id],
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/quotes/{$quote->id}", ['base' => $base] + $mine)
            ->assertStatus(200);

        $titles = collect($response->json('lines'))->pluck('title')->values();
        $this->assertSame(['B', 'A'], $titles->all());
    }

    /**
     * @return array<string, mixed>
     */
    private function basePayload(Quote $quote): array
    {
        $quote->refresh()->load('client', 'lines');

        return [
            'client_id' => $quote->client_id,
            'client_prenom' => $quote->client->prenom,
            'client_nom' => $quote->client->nom,
            'client_email' => $quote->client->email,
            'client_telephone' => $quote->client->telephone,
            'client_adresse' => $quote->client->adresse,
            'client_origine_contact' => $quote->client->origine_contact,
            'client_commentaires' => $quote->client->commentaires,
            'client_avantage_type' => $quote->client->avantage_type ?? 'aucun',
            'client_avantage_valeur' => $quote->client->avantage_valeur ?? 0,
            'client_avantage_expiration' => $quote->client->avantage_expiration?->format('Y-m-d'),
            'bike_description' => $quote->bike_description ?? 'VTT bleu',
            'reception_comment' => $quote->reception_comment ?? 'Révision complète',
            'remarks' => $quote->remarks,
            'email_note' => $quote->email_note,
            'valid_until' => $quote->valid_until->format('Y-m-d'),
            'discount_type' => $quote->discount_type,
            'discount_value' => $quote->discount_value,
            'actual_time_minutes' => $quote->actual_time_minutes,
            'lines' => $quote->lines->map(fn (QuoteLine $line) => array_merge($this->defaultLine(), [
                'id' => $line->id,
                'title' => $line->title,
                'sale_price_ttc' => $line->sale_price_ttc,
            ]))->toArray(),
            'totals' => [
                'total_ht' => 100,
                'total_tva' => 20,
                'total_ttc' => 120,
                'margin_total_ht' => 50,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
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
            'needs_order' => false,
            'ordered_at' => null,
            'received_at' => null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function lineAttributes(Quote $quote, array $overrides = []): array
    {
        return array_merge([
            'quote_id' => $quote->id,
            'title' => 'Réparation',
            'purchase_price_ht' => '50.00',
            'sale_price_ht' => '100.00',
            'sale_price_ttc' => '120.00',
            'margin_amount_ht' => '50.00',
            'margin_rate' => '50.00',
            'tva_rate' => '20.00',
        ], $overrides);
    }
}
