<?php

namespace Tests\Feature\Console;

use App\Models\Article;
use App\Models\ArticleAttribute;
use App\Models\Brand;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ImportCgnCatalogueTest extends TestCase
{
    use RefreshDatabase;

    private string $fixturePath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fixturePath = base_path('tests/fixtures/cgn_test_import.csv');
    }

    private function runImport(): void
    {
        $this->artisan('catalogue:import-cgn', ['path' => $this->fixturePath])->assertExitCode(0);
    }

    #[Test]
    public function it_imports_a_normal_line_with_raw_reference_and_prices(): void
    {
        $this->runImport();

        $article = Article::where('reference', '435447')->first();

        $this->assertNotNull($article);
        $this->assertSame(928, $article->purchase_price_ht);
        $this->assertSame(1856, $article->sale_price_ttc);
        $this->assertSame('3528701009261', $article->barcode);
    }

    #[Test]
    public function it_assigns_the_cgn_supplier_to_imported_articles(): void
    {
        $this->runImport();

        $article = Article::where('reference', '435447')->first();

        $this->assertNotNull($article->supplier_id);
        $this->assertSame('CGN', $article->supplier->name);
    }

    #[Test]
    public function it_reuses_the_same_cgn_supplier_across_articles(): void
    {
        $this->runImport();

        $this->assertSame(1, Supplier::where('name', 'CGN')->count());
    }

    #[Test]
    public function it_does_not_overwrite_a_manually_created_article_with_the_same_reference(): void
    {
        $manualArticle = Article::factory()->create([
            'reference' => '435447',
            'designation' => 'Article saisi manuellement',
            'supplier_id' => null,
        ]);

        $this->runImport();

        $manualArticle->refresh();
        $this->assertSame('Article saisi manuellement', $manualArticle->designation);
        $this->assertNull($manualArticle->supplier_id);
    }

    #[Test]
    public function it_merges_duplicate_ean_into_a_single_article(): void
    {
        $this->runImport();

        $this->assertSame(1, Article::where('barcode', '4026495903998')->count());

        $article = Article::where('barcode', '4026495903998')->first();
        $this->assertSame('510305', $article->reference);
    }

    #[Test]
    public function it_imports_a_line_without_ean_with_null_barcode(): void
    {
        $this->runImport();

        $article = Article::where('reference', '9001')->first();

        $this->assertNull($article->barcode);
    }

    #[Test]
    public function it_does_not_duplicate_an_existing_brand(): void
    {
        Brand::create(['name' => 'MICHELIN', 'sort_order' => 0]);

        $this->runImport();

        $this->assertSame(1, Brand::where('name', 'MICHELIN')->count());
    }

    #[Test]
    public function it_creates_a_new_subcategory_on_the_fly(): void
    {
        $this->runImport();

        $article = Article::where('reference', '435447')->first();

        $this->assertNotNull($article->article_subcategory_id);
        $this->assertSame('Pneus Velo', $article->subcategory->name);
    }

    #[Test]
    public function it_converts_comma_formatted_price_to_cents(): void
    {
        $this->runImport();

        $article = Article::where('reference', '510305')->first();

        $this->assertSame(1200, $article->sale_price_ttc);
    }

    #[Test]
    public function it_converts_comma_formatted_weight_correctly(): void
    {
        $this->runImport();

        $article = Article::where('reference', '435447')->first();

        $this->assertIsFloat($article->weight_kg);
        $this->assertEqualsWithDelta(0.33, $article->weight_kg, 0.001);
    }

    #[Test]
    public function it_cleans_prefix_and_epuise_suffix_from_designation(): void
    {
        $this->runImport();

        $article = Article::where('reference', '9003')->first();

        $this->assertStringNotContainsString('EPUISE', $article->designation);
        $this->assertStringNotContainsString('µ', $article->designation);
    }

    #[Test]
    public function it_is_idempotent_on_replay(): void
    {
        $this->runImport();
        $countAfterFirst = Article::count();

        $this->artisan('catalogue:import-cgn', ['path' => $this->fixturePath])->assertExitCode(0);

        $this->assertSame($countAfterFirst, Article::count());
    }

    #[Test]
    public function it_bounds_the_number_of_queries_for_brand_category_resolution(): void
    {
        DB::enableQueryLog();
        $this->runImport();
        $log = DB::getQueryLog();

        $resolutionQueries = array_filter($log, fn ($q) => str_contains($q['query'], 'brands')
            || str_contains($q['query'], 'article_categories')
            || str_contains($q['query'], 'article_subcategories'));

        // 7 lignes de test, marques/catégories/sous-catégories distinctes limitées :
        // le nombre de requêtes doit rester proche du nombre de valeurs distinctes, pas du nombre de lignes
        $this->assertLessThan(20, count($resolutionQueries));
    }

    #[Test]
    public function it_marks_a_discontinued_article_without_ean_duplicate(): void
    {
        $this->runImport();

        $article = Article::where('reference', '9003')->first();

        $this->assertSame('discontinued', $article->attributeValue('supplier_status'));
    }

    #[Test]
    public function it_clears_supplier_status_when_article_becomes_active_again(): void
    {
        $this->runImport();
        $article = Article::where('reference', '9003')->first();
        $this->assertSame('discontinued', $article->attributeValue('supplier_status'));

        $reactivatedContent = str_replace(
            "\xb5 ARTICLE OBSOLETE SEUL - EPUISE -",
            'ARTICLE REACTIVE',
            file_get_contents($this->fixturePath)
        );
        $reactivatedPath = base_path('tests/fixtures/cgn_test_import_reactivated.csv');
        file_put_contents($reactivatedPath, $reactivatedContent);

        $this->artisan('catalogue:import-cgn', ['path' => $reactivatedPath])->assertExitCode(0);

        $article->refresh();
        $this->assertNull($article->attributeValue('supplier_status'));

        unlink($reactivatedPath);
    }

    #[Test]
    public function it_imports_zero_price_lines_without_ignoring_them(): void
    {
        $this->runImport();

        $article = Article::where('reference', '9004')->first();

        $this->assertNotNull($article);
        $this->assertSame(0, $article->purchase_price_ht);
    }

    #[Test]
    public function it_never_creates_a_brand_for_an_empty_brand_column(): void
    {
        $this->runImport();

        $article = Article::where('reference', '9002')->first();

        $this->assertNull($article->brand_id);
        $this->assertSame(0, Brand::where('name', '')->count());
    }

    #[Test]
    public function it_extracts_article_attributes_for_a_tire(): void
    {
        $this->runImport();

        $article = Article::where('reference', '435447')->first();

        $this->assertSame('ROUTE', $article->attributeValue('practice_type'));
        $this->assertSame('700X28C', $article->attributeValue('size_inches'));
        $this->assertSame('28-622', $article->attributeValue('etrto_size'));
    }

    #[Test]
    public function it_does_not_extract_attributes_for_uncovered_subcategory(): void
    {
        $this->runImport();

        $article = Article::where('reference', '9001')->first();

        $this->assertSame(0, ArticleAttribute::where('article_id', $article->id)->count());
    }

    #[Test]
    public function it_reports_the_real_etrto_coverage_rate_per_subcategory(): void
    {
        // Fixture : 3 lignes PNEUS VELO dont 2 partagent un EAN (dédoublonnées en 1),
        // soit 2 lignes éligibles au total, toutes deux avec ETRTO extractible.
        $this->artisan('catalogue:import-cgn', ['path' => $this->fixturePath])
            ->assertExitCode(0)
            ->expectsOutputToContain('PNEUS VELO')
            ->expectsOutputToContain('100%');
    }

    #[Test]
    public function it_marks_categories_created_during_import_as_catalogue_source(): void
    {
        $this->runImport();

        $article = Article::where('reference', '435447')->first();

        $this->assertSame('catalogue', $article->subcategory->category->source->value);
    }
}
