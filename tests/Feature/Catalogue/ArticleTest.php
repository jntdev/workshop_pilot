<?php

namespace Tests\Feature\Catalogue;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleSubcategory;
use App\Models\QuoteLine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_creates_an_article(): void
    {
        $subcategory = ArticleSubcategory::factory()->create();

        $response = $this->actingAs($this->user)->postJson('/api/articles', [
            'article_subcategory_id' => $subcategory->id,
            'reference' => 'CH-700-PR',
            'designation' => 'Chambre à air 700x23-25 Presta',
            'purchase_price_ht' => 280,
            'sale_price_ht' => 590,
            'tva_rate' => 20.00,
            'unit' => 'pièce',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('articles', ['reference' => 'CH-700-PR']);
    }

    #[Test]
    public function it_rejects_duplicate_reference(): void
    {
        Article::factory()->create(['reference' => 'CH-700-PR']);

        $response = $this->actingAs($this->user)->postJson('/api/articles', [
            'reference' => 'CH-700-PR',
            'designation' => 'Autre désignation',
            'purchase_price_ht' => 100,
            'sale_price_ht' => 200,
            'tva_rate' => 20,
            'unit' => 'pièce',
        ]);

        $response->assertUnprocessable();
    }

    #[Test]
    public function it_searches_articles_by_reference(): void
    {
        Article::factory()->create(['reference' => 'CH-700-PR', 'designation' => 'Chambre Presta']);
        Article::factory()->create(['reference' => 'PN-700-28', 'designation' => 'Pneu 700x28']);

        $response = $this->actingAs($this->user)->getJson('/api/articles/search?q=CH-7');

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.reference', 'CH-700-PR');
    }

    #[Test]
    public function it_searches_articles_by_designation(): void
    {
        Article::factory()->create(['reference' => 'CH-700-PR', 'designation' => 'Chambre Presta 700']);
        Article::factory()->create(['reference' => 'PN-700', 'designation' => 'Pneu 700x28']);

        $response = $this->actingAs($this->user)->getJson('/api/articles/search?q=Chambre');

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.reference', 'CH-700-PR');
    }

    #[Test]
    public function it_returns_empty_results_for_short_query(): void
    {
        Article::factory()->create(['reference' => 'CH-700-PR']);

        $response = $this->actingAs($this->user)->getJson('/api/articles/search?q=CH');

        $response->assertOk()->assertJsonCount(0);
    }

    #[Test]
    public function it_returns_stock_quantity_from_movements(): void
    {
        $article = Article::factory()->create();
        $article->stockMovements()->create(['quantity' => 10, 'type' => 'manual_in']);
        $article->stockMovements()->create(['quantity' => -3, 'type' => 'manual_out']);

        $this->assertSame(7, $article->stock_quantity);
    }

    #[Test]
    public function it_updates_an_article(): void
    {
        $article = Article::factory()->create(['designation' => 'Ancienne désignation']);

        $response = $this->actingAs($this->user)->putJson("/api/articles/{$article->id}", [
            'designation' => 'Nouvelle désignation',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('articles', ['id' => $article->id, 'designation' => 'Nouvelle désignation']);
    }

    #[Test]
    public function it_deletes_an_article_and_nullifies_quote_line_article_id(): void
    {
        $article = Article::factory()->create();
        $quoteLine = QuoteLine::factory()->create(['article_id' => $article->id]);

        $response = $this->actingAs($this->user)->deleteJson("/api/articles/{$article->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
        $this->assertDatabaseHas('quote_lines', ['id' => $quoteLine->id, 'article_id' => null]);
    }

    #[Test]
    public function it_filters_articles_by_subcategory(): void
    {
        $cat = ArticleCategory::factory()->create();
        $sub1 = ArticleSubcategory::factory()->create(['article_category_id' => $cat->id]);
        $sub2 = ArticleSubcategory::factory()->create(['article_category_id' => $cat->id]);
        Article::factory()->create(['article_subcategory_id' => $sub1->id, 'reference' => 'AAA-001']);
        Article::factory()->create(['article_subcategory_id' => $sub2->id, 'reference' => 'BBB-001']);

        $response = $this->actingAs($this->user)->getJson("/api/articles?subcategory_id={$sub1->id}");

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertSame('AAA-001', $data[0]['reference']);
    }
}
