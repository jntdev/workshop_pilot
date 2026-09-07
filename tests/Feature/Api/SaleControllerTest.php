<?php

namespace Tests\Feature\Api;

use App\Enums\PaymentMethod;
use App\Enums\StockMovementType;
use App\Models\Article;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SaleControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_looks_up_an_article_by_exact_barcode(): void
    {
        $article = Article::factory()->create(['barcode' => '1234567890123']);

        $response = $this->actingAs($this->user)->getJson('/api/sales/lookup-article?q=1234567890123');

        $response->assertOk()
            ->assertJsonPath('result', 'found')
            ->assertJsonPath('article.id', $article->id);
    }

    #[Test]
    public function it_looks_up_an_article_by_exact_reference_when_no_barcode_matches(): void
    {
        $article = Article::factory()->create(['reference' => 'CGN-999999', 'barcode' => null]);

        $response = $this->actingAs($this->user)->getJson('/api/sales/lookup-article?q=CGN-999999');

        $response->assertOk()
            ->assertJsonPath('result', 'found')
            ->assertJsonPath('article.id', $article->id);
    }

    #[Test]
    public function it_returns_ambiguous_when_multiple_articles_share_the_same_barcode(): void
    {
        Article::factory()->create(['barcode' => 'DUP123']);
        Article::factory()->create(['barcode' => 'DUP123']);

        $response = $this->actingAs($this->user)->getJson('/api/sales/lookup-article?q=DUP123');

        $response->assertOk()->assertJsonPath('result', 'ambiguous');
        $this->assertCount(2, $response->json('articles'));
    }

    #[Test]
    public function it_returns_choices_for_text_search_matching_multiple_articles(): void
    {
        Article::factory()->create(['designation' => 'PNEU MICHELIN ROUTE', 'barcode' => null]);
        Article::factory()->create(['designation' => 'PNEU MICHELIN VTT', 'barcode' => null]);

        $response = $this->actingAs($this->user)->getJson('/api/sales/lookup-article?q=MICHELIN');

        $response->assertOk()->assertJsonPath('result', 'choices');
        $this->assertGreaterThanOrEqual(2, count($response->json('articles')));
    }

    #[Test]
    public function it_persists_article_prices_when_adding_a_line(): void
    {
        $article = Article::factory()->create(['purchase_price_ht' => 500, 'sale_price_ttc' => 1200]);
        $sale = Sale::factory()->create();

        $response = $this->actingAs($this->user)->postJson("/api/sales/{$sale->id}/lines", [
            'article_id' => $article->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('sale_lines', [
            'sale_id' => $sale->id,
            'article_id' => $article->id,
            'purchase_price_ht' => 500,
            'unit_price_ttc' => 1200,
        ]);
    }

    #[Test]
    public function it_requires_designation_and_purchase_price_for_a_free_form_line(): void
    {
        $sale = Sale::factory()->create();

        $response = $this->actingAs($this->user)->postJson("/api/sales/{$sale->id}/lines", []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['designation', 'purchase_price_ht', 'unit_price_ttc']);
    }

    #[Test]
    public function it_increments_quantity_instead_of_duplicating_the_line_on_second_scan(): void
    {
        $article = Article::factory()->create(['sale_price_ttc' => 1000]);
        $sale = Sale::factory()->create();

        $this->actingAs($this->user)->postJson("/api/sales/{$sale->id}/lines", ['article_id' => $article->id]);
        $this->actingAs($this->user)->postJson("/api/sales/{$sale->id}/lines", ['article_id' => $article->id]);

        $this->assertSame(1, $sale->lines()->count());
        $this->assertSame('2.00', $sale->lines()->first()->quantity);
    }

    #[Test]
    public function it_recalculates_line_and_sale_totals_on_quantity_update(): void
    {
        $article = Article::factory()->create();
        $sale = Sale::factory()->create();
        $line = $sale->lines()->create([
            'article_id' => $article->id,
            'designation' => 'x',
            'quantity' => 1,
            'purchase_price_ht' => 100,
            'unit_price_ttc' => 200,
            'tva_rate' => 20,
            'line_total_ttc' => 200,
            'position' => 0,
        ]);

        $response = $this->actingAs($this->user)->putJson("/api/sales/{$sale->id}/lines/{$line->id}", [
            'quantity' => 3,
            'unit_price_ttc' => 200,
        ]);

        $response->assertOk()->assertJsonPath('total_ttc', 600);
    }

    #[Test]
    public function it_recalculates_totals_on_line_removal(): void
    {
        $sale = Sale::factory()->create();
        $line = $sale->lines()->create([
            'designation' => 'x',
            'quantity' => 1,
            'purchase_price_ht' => 100,
            'unit_price_ttc' => 200,
            'tva_rate' => 20,
            'line_total_ttc' => 200,
            'position' => 0,
        ]);
        $sale->recalculateTotals();

        $response = $this->actingAs($this->user)->deleteJson("/api/sales/{$sale->id}/lines/{$line->id}");

        $response->assertOk()->assertJsonPath('total_ttc', 0);
    }

    #[Test]
    public function it_returns_404_when_updating_a_line_from_another_sale(): void
    {
        $saleA = Sale::factory()->create();
        $saleB = Sale::factory()->create();
        $line = $saleA->lines()->create([
            'designation' => 'x', 'quantity' => 1, 'purchase_price_ht' => 100,
            'unit_price_ttc' => 200, 'tva_rate' => 20, 'line_total_ttc' => 200, 'position' => 0,
        ]);

        $response = $this->actingAs($this->user)->putJson("/api/sales/{$saleB->id}/lines/{$line->id}", [
            'quantity' => 2, 'unit_price_ttc' => 200,
        ]);

        $response->assertNotFound();
    }

    #[Test]
    public function it_returns_404_when_deleting_a_line_from_another_sale(): void
    {
        $saleA = Sale::factory()->create();
        $saleB = Sale::factory()->create();
        $line = $saleA->lines()->create([
            'designation' => 'x', 'quantity' => 1, 'purchase_price_ht' => 100,
            'unit_price_ttc' => 200, 'tva_rate' => 20, 'line_total_ttc' => 200, 'position' => 0,
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/api/sales/{$saleB->id}/lines/{$line->id}");

        $response->assertNotFound();
    }

    #[Test]
    public function it_refuses_mutations_on_a_completed_sale(): void
    {
        $article = Article::factory()->create();
        $sale = Sale::factory()->create();
        $sale->lines()->create([
            'article_id' => $article->id, 'designation' => 'x', 'quantity' => 1,
            'purchase_price_ht' => 100, 'unit_price_ttc' => 200, 'tva_rate' => 20,
            'line_total_ttc' => 200, 'position' => 0,
        ]);
        $sale->recalculateTotals();
        $sale->complete(PaymentMethod::Cb);

        $response = $this->actingAs($this->user)->postJson("/api/sales/{$sale->id}/lines", ['article_id' => $article->id]);

        $response->assertForbidden();
    }

    #[Test]
    public function it_does_not_change_payment_method_when_completing_an_already_completed_sale(): void
    {
        $article = Article::factory()->create();
        $article->stockMovements()->create(['quantity' => 10, 'type' => 'manual_in']);
        $sale = Sale::factory()->create();
        $sale->lines()->create([
            'article_id' => $article->id, 'designation' => 'x', 'quantity' => 1,
            'purchase_price_ht' => 100, 'unit_price_ttc' => 200, 'tva_rate' => 20,
            'line_total_ttc' => 200, 'position' => 0,
        ]);
        $sale->recalculateTotals();
        $sale->complete(PaymentMethod::Cb);

        $response = $this->actingAs($this->user)->postJson("/api/sales/{$sale->id}/complete", ['payment_method' => 'liquide']);

        $response->assertForbidden();
        $this->assertSame('cb', $sale->fresh()->payment_method->value);
    }

    #[Test]
    public function it_refuses_completing_an_empty_cart(): void
    {
        $sale = Sale::factory()->create();

        $response = $this->actingAs($this->user)->postJson("/api/sales/{$sale->id}/complete", ['payment_method' => 'cb']);

        $response->assertForbidden();
    }

    #[Test]
    public function it_creates_sale_consumption_movement_and_decreases_stock_on_complete(): void
    {
        $article = Article::factory()->create(['purchase_price_ht' => 500]);
        $article->stockMovements()->create(['quantity' => 10, 'type' => 'manual_in']);
        $sale = Sale::factory()->create();
        $sale->lines()->create([
            'article_id' => $article->id, 'designation' => 'x', 'quantity' => 3,
            'purchase_price_ht' => 500, 'unit_price_ttc' => 1000, 'tva_rate' => 20,
            'line_total_ttc' => 3000, 'position' => 0,
        ]);
        $sale->recalculateTotals();

        $response = $this->actingAs($this->user)->postJson("/api/sales/{$sale->id}/complete", ['payment_method' => 'cb']);

        $response->assertOk();
        $this->assertSame(7.0, $article->fresh()->stock_quantity);
        $this->assertDatabaseHas('stock_movements', [
            'article_id' => $article->id,
            'type' => StockMovementType::SaleConsumption->value,
            'quantity' => -3,
        ]);
    }

    #[Test]
    public function it_fails_completing_without_payment_method(): void
    {
        $sale = Sale::factory()->create();
        $sale->lines()->create([
            'designation' => 'x', 'quantity' => 1, 'purchase_price_ht' => 100,
            'unit_price_ttc' => 200, 'tva_rate' => 20, 'line_total_ttc' => 200, 'position' => 0,
        ]);

        $response = $this->actingAs($this->user)->postJson("/api/sales/{$sale->id}/complete", []);

        $response->assertUnprocessable()->assertJsonValidationErrors(['payment_method']);
    }

    #[Test]
    public function it_does_not_double_process_two_immediate_complete_calls(): void
    {
        $article = Article::factory()->create();
        $article->stockMovements()->create(['quantity' => 10, 'type' => 'manual_in']);
        $sale = Sale::factory()->create();
        $sale->lines()->create([
            'article_id' => $article->id, 'designation' => 'x', 'quantity' => 1,
            'purchase_price_ht' => 100, 'unit_price_ttc' => 200, 'tva_rate' => 20,
            'line_total_ttc' => 200, 'position' => 0,
        ]);
        $sale->recalculateTotals();

        // payment_method volontairement différent entre les deux appels : reproduit le scénario de course
        // où deux requêtes concurrentes tenteraient de finaliser avec des moyens de paiement différents.
        // payment_method est désormais écrit dans la même transaction verrouillée que le changement de statut
        // (Sale::complete()), donc le second appel ne peut plus l'écraser après coup.
        $r1 = $this->actingAs($this->user)->postJson("/api/sales/{$sale->id}/complete", ['payment_method' => 'cb']);
        $r2 = $this->actingAs($this->user)->postJson("/api/sales/{$sale->id}/complete", ['payment_method' => 'liquide']);

        $r1->assertOk();
        $r2->assertForbidden();
        $this->assertSame(1, StockMovement::where('article_id', $article->id)->where('type', StockMovementType::SaleConsumption->value)->count());
        $this->assertSame('cb', $sale->fresh()->payment_method->value);
    }

    #[Test]
    public function it_creates_sale_return_movements_and_restores_stock_on_cancel(): void
    {
        $article = Article::factory()->create();
        $article->stockMovements()->create(['quantity' => 10, 'type' => 'manual_in']);
        $sale = Sale::factory()->create();
        $sale->lines()->create([
            'article_id' => $article->id, 'designation' => 'x', 'quantity' => 4,
            'purchase_price_ht' => 100, 'unit_price_ttc' => 200, 'tva_rate' => 20,
            'line_total_ttc' => 800, 'position' => 0,
        ]);
        $sale->recalculateTotals();
        $sale->complete(PaymentMethod::Cb);

        $this->assertSame(6.0, $article->fresh()->stock_quantity);

        $response = $this->actingAs($this->user)->postJson("/api/sales/{$sale->id}/cancel");

        $response->assertOk();
        $this->assertSame(10.0, $article->fresh()->stock_quantity);
        $this->assertDatabaseHas('stock_movements', [
            'article_id' => $article->id,
            'type' => StockMovementType::SaleReturn->value,
            'quantity' => 4,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'article_id' => $article->id,
            'type' => StockMovementType::SaleConsumption->value,
            'quantity' => -4,
        ]);
    }

    #[Test]
    public function it_is_idempotent_when_cancelled_twice(): void
    {
        $article = Article::factory()->create();
        $article->stockMovements()->create(['quantity' => 10, 'type' => 'manual_in']);
        $sale = Sale::factory()->create();
        $sale->lines()->create([
            'article_id' => $article->id, 'designation' => 'x', 'quantity' => 2,
            'purchase_price_ht' => 100, 'unit_price_ttc' => 200, 'tva_rate' => 20,
            'line_total_ttc' => 400, 'position' => 0,
        ]);
        $sale->recalculateTotals();
        $sale->complete(PaymentMethod::Cb);

        $this->actingAs($this->user)->postJson("/api/sales/{$sale->id}/cancel");
        $this->actingAs($this->user)->postJson("/api/sales/{$sale->id}/cancel");

        $this->assertSame(10.0, $article->fresh()->stock_quantity);
    }

    #[Test]
    public function margin_ht_is_computed_correctly_across_multiple_lines(): void
    {
        $sale = Sale::factory()->create();
        $sale->lines()->create([
            'designation' => 'a', 'quantity' => 2, 'purchase_price_ht' => 300,
            'unit_price_ttc' => 600, 'tva_rate' => 20, 'line_total_ttc' => 1200, 'position' => 0,
        ]);
        $sale->lines()->create([
            'designation' => 'b', 'quantity' => 1, 'purchase_price_ht' => 500,
            'unit_price_ttc' => 1000, 'tva_rate' => 20, 'line_total_ttc' => 1000, 'position' => 1,
        ]);
        $sale->recalculateTotals();
        $sale->refresh();

        // total_ht = round(1200/1.2) + round(1000/1.2) = 1000 + 833 = 1833
        // cost_ht = 2*300 + 1*500 = 1100
        $this->assertSame(1833 - 1100, $sale->marginHt());
    }

    #[Test]
    public function it_lists_completed_sales_of_the_day_most_recent_first(): void
    {
        $article = Article::factory()->create();
        $article->stockMovements()->create(['quantity' => 10, 'type' => 'manual_in']);

        $sale1 = Sale::factory()->create();
        $sale1->lines()->create([
            'article_id' => $article->id, 'designation' => 'x', 'quantity' => 1,
            'purchase_price_ht' => 100, 'unit_price_ttc' => 200, 'tva_rate' => 20,
            'line_total_ttc' => 200, 'position' => 0,
        ]);
        $sale1->recalculateTotals();
        $sale1->complete(PaymentMethod::Cb);
        $sale1->update(['completed_at' => now()->subMinutes(10)]);

        $sale2 = Sale::factory()->create();
        $sale2->lines()->create([
            'article_id' => $article->id, 'designation' => 'y', 'quantity' => 1,
            'purchase_price_ht' => 100, 'unit_price_ttc' => 200, 'tva_rate' => 20,
            'line_total_ttc' => 200, 'position' => 0,
        ]);
        $sale2->recalculateTotals();
        $sale2->complete(PaymentMethod::Cb);

        $response = $this->actingAs($this->user)->getJson('/api/sales/recent');

        $response->assertOk();
        $sales = $response->json('sales');
        $this->assertCount(2, $sales);
        $this->assertSame($sale2->id, $sales[0]['id']);
        $this->assertSame($sale1->id, $sales[1]['id']);
        $this->assertSame(1, $sales[0]['lines_count']);
    }

    #[Test]
    public function it_excludes_sales_completed_on_a_different_day(): void
    {
        $sale = Sale::factory()->create();
        $sale->lines()->create([
            'designation' => 'x', 'quantity' => 1, 'purchase_price_ht' => 100,
            'unit_price_ttc' => 200, 'tva_rate' => 20, 'line_total_ttc' => 200, 'position' => 0,
        ]);
        $sale->recalculateTotals();
        $sale->complete(PaymentMethod::Cb);
        $sale->update(['completed_at' => now()->subDay()]);

        $response = $this->actingAs($this->user)->getJson('/api/sales/recent');

        $response->assertOk();
        $this->assertCount(0, $response->json('sales'));
    }

    #[Test]
    public function it_includes_cancelled_sales_in_the_recent_list(): void
    {
        $article = Article::factory()->create();
        $article->stockMovements()->create(['quantity' => 10, 'type' => 'manual_in']);
        $sale = Sale::factory()->create();
        $sale->lines()->create([
            'article_id' => $article->id, 'designation' => 'x', 'quantity' => 1,
            'purchase_price_ht' => 100, 'unit_price_ttc' => 200, 'tva_rate' => 20,
            'line_total_ttc' => 200, 'position' => 0,
        ]);
        $sale->recalculateTotals();
        $sale->complete(PaymentMethod::Cb);
        $sale->cancel();

        $response = $this->actingAs($this->user)->getJson('/api/sales/recent');

        $response->assertOk();
        $this->assertSame('cancelled', $response->json('sales.0.status'));
    }
}
