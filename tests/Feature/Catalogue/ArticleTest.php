<?php

namespace Tests\Feature\Catalogue;

use App\Models\Article;
use App\Models\ArticleAttribute;
use App\Models\ArticleCategory;
use App\Models\ArticleSubcategory;
use App\Models\Brand;
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
            'sale_price_ttc' => 708,
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
            'sale_price_ttc' => 240,
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
    public function it_ranks_designations_starting_with_the_search_term_first(): void
    {
        Article::factory()->create(['reference' => 'ACC-001', 'designation' => 'CLIQUET PNEUMATIQUE']);
        Article::factory()->create(['reference' => 'TIRE-001', 'designation' => 'PNEU ROUTE 700X28C']);

        $response = $this->actingAs($this->user)->getJson('/api/articles/search?q=pneu');

        $response->assertOk();
        $this->assertSame('TIRE-001', $response->json('0.reference'));
    }

    #[Test]
    public function it_searches_by_multiple_words_combined_with_and(): void
    {
        $matching = Article::factory()->create(['reference' => 'MULTI-MATCH', 'designation' => 'PNEU GRAVEL 700X35C NOIR', 'barcode' => null]);
        $partial = Article::factory()->create(['reference' => 'MULTI-PARTIAL', 'designation' => 'PNEU GRAVEL 700X40C NOIR', 'barcode' => null]);

        $response = $this->actingAs($this->user)->getJson('/api/articles/search?q='.urlencode('pneu 700 35'));

        $response->assertOk();
        $references = collect($response->json())->pluck('reference');
        $this->assertTrue($references->contains('MULTI-MATCH'));
        $this->assertFalse($references->contains('MULTI-PARTIAL'));
    }

    #[Test]
    public function it_searches_articles_by_subcategory_name(): void
    {
        $tireSubcategory = ArticleSubcategory::factory()->create(['name' => 'Pneus Velo']);
        $otherSubcategory = ArticleSubcategory::factory()->create(['name' => 'Eclairage']);
        $tire = Article::factory()->create(['article_subcategory_id' => $tireSubcategory->id, 'reference' => 'SUBCAT-TIRE', 'designation' => 'BOYAU CYCLOCROSS']);
        Article::factory()->create(['article_subcategory_id' => $otherSubcategory->id, 'reference' => 'SUBCAT-LIGHT', 'designation' => 'LAMPE AVANT']);

        $response = $this->actingAs($this->user)->getJson('/api/articles/search?q=pneus');

        $response->assertOk();
        $references = collect($response->json())->pluck('reference');
        $this->assertTrue($references->contains('SUBCAT-TIRE'));
        $this->assertFalse($references->contains('SUBCAT-LIGHT'));
    }

    #[Test]
    public function it_searches_articles_by_category_name(): void
    {
        $category = ArticleCategory::factory()->create(['name' => 'Pieces Cycles']);
        $subcategory = ArticleSubcategory::factory()->create(['article_category_id' => $category->id]);
        $article = Article::factory()->create(['article_subcategory_id' => $subcategory->id, 'reference' => 'CAT-MATCH', 'designation' => 'ARTICLE GENERIQUE']);

        $otherCategory = ArticleCategory::factory()->create(['name' => 'Motorisation']);
        $otherSubcategory = ArticleSubcategory::factory()->create(['article_category_id' => $otherCategory->id]);
        Article::factory()->create(['article_subcategory_id' => $otherSubcategory->id, 'reference' => 'CAT-OTHER', 'designation' => 'AUTRE ARTICLE']);

        $response = $this->actingAs($this->user)->getJson('/api/articles/search?q=cycles');

        $response->assertOk();
        $references = collect($response->json())->pluck('reference');
        $this->assertTrue($references->contains('CAT-MATCH'));
        $this->assertFalse($references->contains('CAT-OTHER'));
    }

    #[Test]
    public function it_returns_stock_quantity_from_movements(): void
    {
        $article = Article::factory()->create();
        $article->stockMovements()->create(['quantity' => 10, 'type' => 'manual_in']);
        $article->stockMovements()->create(['quantity' => -3, 'type' => 'manual_out']);

        $this->assertSame(7.0, $article->stock_quantity);
    }

    #[Test]
    public function it_reports_is_discontinued_when_supplier_status_attribute_is_set(): void
    {
        $article = Article::factory()->create();
        ArticleAttribute::create(['article_id' => $article->id, 'key' => 'supplier_status', 'value' => 'discontinued']);

        $this->assertTrue($article->fresh()->is_discontinued);
    }

    #[Test]
    public function it_reports_not_discontinued_when_supplier_status_attribute_is_absent(): void
    {
        $article = Article::factory()->create();

        $this->assertFalse($article->is_discontinued);
    }

    #[Test]
    public function it_exposes_purchase_price_image_url_and_discontinued_status_in_search_results(): void
    {
        $article = Article::factory()->create([
            'reference' => 'CARD-INFO',
            'designation' => 'ARTICLE AVEC PHOTO',
            'purchase_price_ht' => 1500,
            'image_url' => 'https://example.com/photo.jpg',
        ]);
        ArticleAttribute::create(['article_id' => $article->id, 'key' => 'supplier_status', 'value' => 'discontinued']);

        $response = $this->actingAs($this->user)->getJson('/api/articles/search?q=CARD-INFO');

        $response->assertOk();
        $this->assertSame(1500, $response->json('0.purchase_price_ht'));
        $this->assertSame('https://example.com/photo.jpg', $response->json('0.image_url'));
        $this->assertTrue($response->json('0.is_discontinued'));
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

    #[Test]
    public function it_filters_articles_with_at_least_one_stock_movement_when_has_movements_is_true(): void
    {
        $withMovement = Article::factory()->create(['reference' => 'HAS-MOV']);
        $withMovement->stockMovements()->create(['quantity' => 5, 'type' => 'manual_in']);
        Article::factory()->create(['reference' => 'NO-MOV']);

        $response = $this->actingAs($this->user)->getJson('/api/articles?has_movements=1');

        $response->assertOk();
        $references = collect($response->json('data'))->pluck('reference');
        $this->assertTrue($references->contains('HAS-MOV'));
        $this->assertFalse($references->contains('NO-MOV'));
    }

    #[Test]
    public function it_filters_articles_without_any_stock_movement_when_has_movements_is_false(): void
    {
        $withMovement = Article::factory()->create(['reference' => 'HAS-MOV-2']);
        $withMovement->stockMovements()->create(['quantity' => 5, 'type' => 'manual_in']);
        Article::factory()->create(['reference' => 'NO-MOV-2']);

        $response = $this->actingAs($this->user)->getJson('/api/articles?has_movements=0');

        $response->assertOk();
        $references = collect($response->json('data'))->pluck('reference');
        $this->assertFalse($references->contains('HAS-MOV-2'));
        $this->assertTrue($references->contains('NO-MOV-2'));
    }

    #[Test]
    public function it_returns_all_articles_when_has_movements_is_not_provided(): void
    {
        $withMovement = Article::factory()->create(['reference' => 'HAS-MOV-3']);
        $withMovement->stockMovements()->create(['quantity' => 5, 'type' => 'manual_in']);
        Article::factory()->create(['reference' => 'NO-MOV-3']);

        $response = $this->actingAs($this->user)->getJson('/api/articles');

        $response->assertOk();
        $references = collect($response->json('data'))->pluck('reference');
        $this->assertTrue($references->contains('HAS-MOV-3'));
        $this->assertTrue($references->contains('NO-MOV-3'));
    }

    #[Test]
    public function it_filters_articles_by_single_attribute(): void
    {
        $sub = ArticleSubcategory::factory()->create();
        $matching = Article::factory()->create(['article_subcategory_id' => $sub->id, 'reference' => 'VTT-001']);
        $other = Article::factory()->create(['article_subcategory_id' => $sub->id, 'reference' => 'ROUTE-001']);
        ArticleAttribute::create(['article_id' => $matching->id, 'key' => 'practice_type', 'value' => 'VTT']);
        ArticleAttribute::create(['article_id' => $other->id, 'key' => 'practice_type', 'value' => 'ROUTE']);

        $response = $this->actingAs($this->user)->getJson('/api/articles?'.http_build_query(['attribute' => ['practice_type' => 'VTT']]));

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertSame('VTT-001', $data[0]['reference']);
    }

    #[Test]
    public function it_applies_and_logic_between_different_attributes(): void
    {
        $sub = ArticleSubcategory::factory()->create();
        $matching = Article::factory()->create(['article_subcategory_id' => $sub->id, 'reference' => 'MATCH-001']);
        ArticleAttribute::create(['article_id' => $matching->id, 'key' => 'practice_type', 'value' => 'VTT']);
        ArticleAttribute::create(['article_id' => $matching->id, 'key' => 'speed_count', 'value' => '9']);

        $partial = Article::factory()->create(['article_subcategory_id' => $sub->id, 'reference' => 'PARTIAL-001']);
        ArticleAttribute::create(['article_id' => $partial->id, 'key' => 'practice_type', 'value' => 'VTT']);
        ArticleAttribute::create(['article_id' => $partial->id, 'key' => 'speed_count', 'value' => '10']);

        $response = $this->actingAs($this->user)->getJson('/api/articles?'.http_build_query([
            'attribute' => ['practice_type' => 'VTT', 'speed_count' => '9'],
        ]));

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertSame('MATCH-001', $data[0]['reference']);
    }

    #[Test]
    public function it_filters_articles_by_commercial_wheel_diameter_from_size_inches(): void
    {
        $sub = ArticleSubcategory::factory()->create();
        $matching = Article::factory()->create(['article_subcategory_id' => $sub->id, 'reference' => 'DIAM-700']);
        ArticleAttribute::create(['article_id' => $matching->id, 'key' => 'size_inches', 'value' => '700X28C']);

        $other = Article::factory()->create(['article_subcategory_id' => $sub->id, 'reference' => 'DIAM-26']);
        ArticleAttribute::create(['article_id' => $other->id, 'key' => 'size_inches', 'value' => '26X1.75']);

        $response = $this->actingAs($this->user)->getJson('/api/articles?'.http_build_query([
            'attribute' => ['wheel_diameter' => '700'],
        ]));

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertSame('DIAM-700', $data[0]['reference']);
    }

    #[Test]
    public function it_does_not_confuse_700_and_700c_when_filtering_by_wheel_diameter(): void
    {
        $sub = ArticleSubcategory::factory()->create();
        $tire700 = Article::factory()->create(['article_subcategory_id' => $sub->id, 'reference' => 'TIRE-700']);
        ArticleAttribute::create(['article_id' => $tire700->id, 'key' => 'size_inches', 'value' => '700X35C']);

        $tube700c = Article::factory()->create(['article_subcategory_id' => $sub->id, 'reference' => 'TUBE-700C']);
        ArticleAttribute::create(['article_id' => $tube700c->id, 'key' => 'size_inches', 'value' => '700C']);

        $response = $this->actingAs($this->user)->getJson('/api/articles?'.http_build_query([
            'attribute' => ['wheel_diameter' => '700'],
        ]));

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertSame('TIRE-700', $data[0]['reference']);
    }

    #[Test]
    public function it_filters_articles_by_wheel_width_mm_from_etrto_size(): void
    {
        $sub = ArticleSubcategory::factory()->create();
        $a = Article::factory()->create(['article_subcategory_id' => $sub->id, 'reference' => 'ETRTO-A']);
        ArticleAttribute::create(['article_id' => $a->id, 'key' => 'etrto_size', 'value' => '28-622']);
        $b = Article::factory()->create(['article_subcategory_id' => $sub->id, 'reference' => 'ETRTO-B']);
        ArticleAttribute::create(['article_id' => $b->id, 'key' => 'etrto_size', 'value' => '32-622']);

        $response = $this->actingAs($this->user)->getJson('/api/articles?'.http_build_query([
            'attribute' => ['wheel_width_mm' => '28'],
        ]));

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertSame('ETRTO-A', $data[0]['reference']);
    }

    #[Test]
    public function it_filters_articles_by_wheel_width_inches_from_size_inches(): void
    {
        $sub = ArticleSubcategory::factory()->create();
        $matching = Article::factory()->create(['article_subcategory_id' => $sub->id, 'reference' => 'WIDTH-175']);
        ArticleAttribute::create(['article_id' => $matching->id, 'key' => 'size_inches', 'value' => '26X1.75']);
        $other = Article::factory()->create(['article_subcategory_id' => $sub->id, 'reference' => 'WIDTH-200']);
        ArticleAttribute::create(['article_id' => $other->id, 'key' => 'size_inches', 'value' => '26X2.00']);

        $response = $this->actingAs($this->user)->getJson('/api/articles?'.http_build_query([
            'attribute' => ['wheel_width_inches' => '1.75'],
        ]));

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertSame('WIDTH-175', $data[0]['reference']);
    }

    #[Test]
    public function it_combines_wheel_diameter_and_wheel_width_mm_filters(): void
    {
        $sub = ArticleSubcategory::factory()->create();
        $matching = Article::factory()->create(['article_subcategory_id' => $sub->id, 'reference' => 'COMBO-MATCH']);
        ArticleAttribute::create(['article_id' => $matching->id, 'key' => 'size_inches', 'value' => '700X28C']);
        ArticleAttribute::create(['article_id' => $matching->id, 'key' => 'etrto_size', 'value' => '28-622']);

        $other = Article::factory()->create(['article_subcategory_id' => $sub->id, 'reference' => 'COMBO-OTHER']);
        ArticleAttribute::create(['article_id' => $other->id, 'key' => 'size_inches', 'value' => '700X32C']);
        ArticleAttribute::create(['article_id' => $other->id, 'key' => 'etrto_size', 'value' => '32-622']);

        $response = $this->actingAs($this->user)->getJson('/api/articles?'.http_build_query([
            'attribute' => ['wheel_diameter' => '700', 'wheel_width_mm' => '28'],
        ]));

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertSame('COMBO-MATCH', $data[0]['reference']);
    }

    #[Test]
    public function it_filters_articles_by_exact_tooth_range(): void
    {
        $sub = ArticleSubcategory::factory()->create();
        $matching = Article::factory()->create(['article_subcategory_id' => $sub->id, 'reference' => 'CASS-MATCH']);
        ArticleAttribute::create(['article_id' => $matching->id, 'key' => 'tooth_range', 'value' => '11-32']);
        $other = Article::factory()->create(['article_subcategory_id' => $sub->id, 'reference' => 'CASS-OTHER']);
        ArticleAttribute::create(['article_id' => $other->id, 'key' => 'tooth_range', 'value' => '11-34']);

        $response = $this->actingAs($this->user)->getJson('/api/articles?'.http_build_query([
            'attribute' => ['tooth_range_min' => '11', 'tooth_range_max' => '32'],
        ]));

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertSame('CASS-MATCH', $data[0]['reference']);
    }

    #[Test]
    public function it_returns_empty_list_for_attribute_filter_without_match(): void
    {
        Article::factory()->create();

        $response = $this->actingAs($this->user)->getJson('/api/articles?'.http_build_query([
            'attribute' => ['practice_type' => 'INEXISTANT'],
        ]));

        $response->assertOk();
        $this->assertCount(0, $response->json('data'));
    }

    #[Test]
    public function it_returns_filter_options_with_wheel_width_mm_from_etrto_size(): void
    {
        $sub = ArticleSubcategory::factory()->create();
        $a = Article::factory()->create(['article_subcategory_id' => $sub->id]);
        $b = Article::factory()->create(['article_subcategory_id' => $sub->id]);
        ArticleAttribute::create(['article_id' => $a->id, 'key' => 'etrto_size', 'value' => '28-622']);
        ArticleAttribute::create(['article_id' => $b->id, 'key' => 'etrto_size', 'value' => '32-584']);

        $response = $this->actingAs($this->user)->getJson("/api/article-subcategories/{$sub->id}/filter-options");

        $response->assertOk();
        $response->assertJsonMissingPath('attributes.etrto_size');
        $response->assertJsonMissingPath('attributes.wheel_diameter');
        $this->assertSame(['28', '32'], $response->json('attributes.wheel_width_mm'));
    }

    #[Test]
    public function it_returns_filter_options_with_commercial_wheel_diameter_and_width_inches_from_size_inches(): void
    {
        $sub = ArticleSubcategory::factory()->create();
        $a = Article::factory()->create(['article_subcategory_id' => $sub->id]);
        $b = Article::factory()->create(['article_subcategory_id' => $sub->id]);
        $noise = Article::factory()->create(['article_subcategory_id' => $sub->id]);
        ArticleAttribute::create(['article_id' => $a->id, 'key' => 'size_inches', 'value' => '700X28C']);
        ArticleAttribute::create(['article_id' => $b->id, 'key' => 'size_inches', 'value' => '26X1.75']);
        ArticleAttribute::create(['article_id' => $noise->id, 'key' => 'size_inches', 'value' => 'TRAINER']);

        $response = $this->actingAs($this->user)->getJson("/api/article-subcategories/{$sub->id}/filter-options");

        $response->assertOk();
        $this->assertSame(['26', '700'], $response->json('attributes.wheel_diameter'));
        $this->assertSame(['1.75', '28C'], $response->json('attributes.wheel_width_inches'));
    }

    #[Test]
    public function it_keeps_700_and_700c_as_distinct_wheel_diameter_options(): void
    {
        $sub = ArticleSubcategory::factory()->create();
        $a = Article::factory()->create(['article_subcategory_id' => $sub->id]);
        $b = Article::factory()->create(['article_subcategory_id' => $sub->id]);
        ArticleAttribute::create(['article_id' => $a->id, 'key' => 'size_inches', 'value' => '700X35C']);
        ArticleAttribute::create(['article_id' => $b->id, 'key' => 'size_inches', 'value' => '700C']);

        $response = $this->actingAs($this->user)->getJson("/api/article-subcategories/{$sub->id}/filter-options");

        $response->assertOk();
        $this->assertSame(['700', '700C'], $response->json('attributes.wheel_diameter'));
    }

    #[Test]
    public function it_returns_filter_options_with_decomposed_tooth_range(): void
    {
        $sub = ArticleSubcategory::factory()->create();
        $a = Article::factory()->create(['article_subcategory_id' => $sub->id]);
        $b = Article::factory()->create(['article_subcategory_id' => $sub->id]);
        ArticleAttribute::create(['article_id' => $a->id, 'key' => 'tooth_range', 'value' => '11-32']);
        ArticleAttribute::create(['article_id' => $b->id, 'key' => 'tooth_range', 'value' => '12-36']);

        $response = $this->actingAs($this->user)->getJson("/api/article-subcategories/{$sub->id}/filter-options");

        $response->assertOk();
        $response->assertJsonMissingPath('attributes.tooth_range');
        $this->assertSame(['11', '12'], $response->json('attributes.tooth_range_min'));
        $this->assertSame(['32', '36'], $response->json('attributes.tooth_range_max'));
    }

    #[Test]
    public function it_never_returns_excluded_keys_in_filter_options(): void
    {
        $sub = ArticleSubcategory::factory()->create();
        $article = Article::factory()->create(['article_subcategory_id' => $sub->id]);
        ArticleAttribute::create(['article_id' => $article->id, 'key' => 'supplier_status', 'value' => 'EPUISE']);
        ArticleAttribute::create(['article_id' => $article->id, 'key' => 'size_inches', 'value' => '28x1.75']);
        ArticleAttribute::create(['article_id' => $article->id, 'key' => 'practice_type', 'value' => 'VTT']);

        $response = $this->actingAs($this->user)->getJson("/api/article-subcategories/{$sub->id}/filter-options");

        $response->assertOk();
        $response->assertJsonMissingPath('attributes.supplier_status');
        $response->assertJsonMissingPath('attributes.size_inches');
        $this->assertSame(['VTT'], $response->json('attributes.practice_type'));
    }

    #[Test]
    public function it_returns_empty_attributes_and_populated_brands_for_subcategory_without_attributes(): void
    {
        $sub = ArticleSubcategory::factory()->create();
        $brand = Brand::factory()->create(['name' => 'Continental']);
        Article::factory()->create(['article_subcategory_id' => $sub->id, 'brand_id' => $brand->id]);

        $response = $this->actingAs($this->user)->getJson("/api/article-subcategories/{$sub->id}/filter-options");

        $response->assertOk();
        $this->assertSame([], $response->json('attributes'));
        $this->assertCount(1, $response->json('brands'));
        $this->assertSame($brand->id, $response->json('brands.0.id'));
    }

    #[Test]
    public function it_only_returns_brands_with_articles_in_the_given_subcategory(): void
    {
        $sub1 = ArticleSubcategory::factory()->create();
        $sub2 = ArticleSubcategory::factory()->create();
        $brandInSub1 = Brand::factory()->create(['name' => 'Michelin']);
        $brandInSub2 = Brand::factory()->create(['name' => 'Schwalbe']);
        Article::factory()->create(['article_subcategory_id' => $sub1->id, 'brand_id' => $brandInSub1->id]);
        Article::factory()->create(['article_subcategory_id' => $sub2->id, 'brand_id' => $brandInSub2->id]);

        $response = $this->actingAs($this->user)->getJson("/api/article-subcategories/{$sub1->id}/filter-options");

        $response->assertOk();
        $brandIds = collect($response->json('brands'))->pluck('id')->all();
        $this->assertContains($brandInSub1->id, $brandIds);
        $this->assertNotContains($brandInSub2->id, $brandIds);
    }

    #[Test]
    public function it_sorts_numeric_filter_values_ascending(): void
    {
        $sub = ArticleSubcategory::factory()->create();
        foreach (['52', '30', '44'] as $diameter) {
            $article = Article::factory()->create(['article_subcategory_id' => $sub->id]);
            ArticleAttribute::create(['article_id' => $article->id, 'key' => 'chainring_diameter_mm', 'value' => $diameter]);
        }

        $response = $this->actingAs($this->user)->getJson("/api/article-subcategories/{$sub->id}/filter-options");

        $response->assertOk();
        $this->assertSame(['30', '44', '52'], $response->json('attributes.chainring_diameter_mm'));
    }

    #[Test]
    public function it_returns_404_for_unknown_subcategory_in_filter_options(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/article-subcategories/999999/filter-options');

        $response->assertNotFound();
    }

    #[Test]
    public function it_ignores_malformed_etrto_size_value_without_failing(): void
    {
        $sub = ArticleSubcategory::factory()->create();
        $article = Article::factory()->create(['article_subcategory_id' => $sub->id]);
        ArticleAttribute::create(['article_id' => $article->id, 'key' => 'etrto_size', 'value' => 'malformed']);

        $response = $this->actingAs($this->user)->getJson("/api/article-subcategories/{$sub->id}/filter-options");

        $response->assertOk();
        $this->assertArrayNotHasKey('wheel_width_mm', $response->json('attributes'));
    }

    #[Test]
    public function it_ignores_noise_values_in_size_inches_without_failing(): void
    {
        $sub = ArticleSubcategory::factory()->create();
        $article = Article::factory()->create(['article_subcategory_id' => $sub->id]);
        ArticleAttribute::create(['article_id' => $article->id, 'key' => 'size_inches', 'value' => 'TRAINER']);

        $response = $this->actingAs($this->user)->getJson("/api/article-subcategories/{$sub->id}/filter-options");

        $response->assertOk();
        $this->assertArrayNotHasKey('wheel_diameter', $response->json('attributes'));
        $this->assertArrayNotHasKey('wheel_width_inches', $response->json('attributes'));
    }
}
