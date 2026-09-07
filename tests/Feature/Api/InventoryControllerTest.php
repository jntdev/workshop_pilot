<?php

namespace Tests\Feature\Api;

use App\Models\Article;
use App\Models\ArticleAttribute;
use App\Models\ArticleSubcategory;
use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InventoryControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_finds_a_single_article_by_exact_barcode(): void
    {
        $article = Article::factory()->create(['barcode' => '1234567890123']);

        $response = $this->actingAs($this->user)->getJson('/api/inventory/lookup-barcode?code=1234567890123');

        $response->assertOk()
            ->assertJsonPath('result', 'found')
            ->assertJsonPath('article.id', $article->id);
    }

    #[Test]
    public function it_returns_ambiguous_when_multiple_articles_share_the_same_barcode(): void
    {
        Article::factory()->create(['barcode' => '1234567890123']);
        Article::factory()->create(['barcode' => '1234567890123']);

        $response = $this->actingAs($this->user)->getJson('/api/inventory/lookup-barcode?code=1234567890123');

        $response->assertOk()
            ->assertJsonPath('result', 'ambiguous')
            ->assertJsonCount(2, 'articles');
    }

    #[Test]
    public function it_returns_not_found_for_an_unknown_barcode(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/inventory/lookup-barcode?code=DOES-NOT-EXIST');

        $response->assertOk()
            ->assertJsonPath('result', 'not_found')
            ->assertJsonPath('article', null);
    }

    #[Test]
    public function it_returns_not_found_for_an_empty_code_without_error(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/inventory/lookup-barcode?code=');

        $response->assertOk()->assertJsonPath('result', 'not_found');
    }

    #[Test]
    public function it_uploads_a_valid_photo_and_returns_its_url(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('produit.jpg');

        $response = $this->actingAs($this->user)->postJson('/api/articles/upload-photo', ['photo' => $file]);

        $response->assertOk();
        $imageUrl = $response->json('image_url');
        $this->assertStringStartsWith('/storage/articles/', $imageUrl);

        $storedPath = str_replace('/storage/', '', $imageUrl);
        Storage::disk('public')->assertExists($storedPath);
    }

    #[Test]
    public function it_rejects_a_non_image_file(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->user)->postJson('/api/articles/upload-photo', ['photo' => $file]);

        $response->assertUnprocessable();
    }

    #[Test]
    public function it_rejects_a_file_exceeding_the_size_limit(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('trop-gros.jpg', 6000, 'image/jpeg');

        $response = $this->actingAs($this->user)->postJson('/api/articles/upload-photo', ['photo' => $file]);

        $response->assertUnprocessable();
    }

    #[Test]
    public function it_rejects_an_upload_without_any_file(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/articles/upload-photo', []);

        $response->assertUnprocessable();
    }

    /**
     * @return array<string, mixed>
     */
    private function validInventoryArticlePayload(array $overrides = []): array
    {
        $subcategory = $overrides['article_subcategory_id'] ?? ArticleSubcategory::factory()->create()->id;
        $brand = $overrides['brand_id'] ?? Brand::factory()->create(['name' => 'MARQUE TEST '.uniqid()])->id;

        return array_merge([
            'barcode' => 'SCAN-BARCODE-'.uniqid(),
            'designation' => 'ARTICLE CREE DEPUIS LE SCAN',
            'article_subcategory_id' => $subcategory,
            'brand_id' => $brand,
            'purchase_price_ht' => 1000,
            'sale_price_ttc' => 2000,
            'quantity' => 3,
            'image_url' => '/storage/articles/photo-test.jpg',
        ], $overrides);
    }

    #[Test]
    public function it_creates_an_article_with_reference_equal_to_barcode_and_an_initial_stock_movement(): void
    {
        $payload = $this->validInventoryArticlePayload(['barcode' => 'SCAN-BARCODE-001', 'quantity' => 3]);

        $response = $this->actingAs($this->user)->postJson('/api/inventory/articles', $payload);

        $response->assertCreated();
        $article = Article::where('barcode', 'SCAN-BARCODE-001')->first();
        $this->assertNotNull($article);
        $this->assertSame('SCAN-BARCODE-001', $article->reference);
        $this->assertSame(3.0, $article->fresh()->stock_quantity);
    }

    #[Test]
    public function it_ignores_a_falsified_reference_field_in_the_raw_request_payload(): void
    {
        $payload = $this->validInventoryArticlePayload(['barcode' => 'SCAN-BARCODE-002']);
        $payload['reference'] = 'FALSIFIED-REFERENCE-VALUE';

        $response = $this->actingAs($this->user)->postJson('/api/inventory/articles', $payload);

        $response->assertCreated();
        $article = Article::where('barcode', 'SCAN-BARCODE-002')->first();
        $this->assertSame('SCAN-BARCODE-002', $article->reference);
        $this->assertNotEquals('FALSIFIED-REFERENCE-VALUE', $article->reference);
    }

    #[Test]
    public function it_requires_each_mandatory_field_individually(): void
    {
        foreach (['barcode', 'designation', 'article_subcategory_id', 'brand_id', 'purchase_price_ht', 'sale_price_ttc', 'quantity', 'image_url'] as $missingField) {
            $payload = $this->validInventoryArticlePayload();
            unset($payload[$missingField]);

            $response = $this->actingAs($this->user)->postJson('/api/inventory/articles', $payload);

            $response->assertUnprocessable();
        }
    }

    #[Test]
    public function it_rejects_a_second_article_with_the_same_barcode(): void
    {
        $payload = $this->validInventoryArticlePayload(['barcode' => 'SCAN-BARCODE-DUP']);
        $this->actingAs($this->user)->postJson('/api/inventory/articles', $payload)->assertCreated();

        $response = $this->actingAs($this->user)->postJson('/api/inventory/articles', $this->validInventoryArticlePayload(['barcode' => 'SCAN-BARCODE-DUP']));

        $response->assertUnprocessable();
        $this->assertSame(1, Article::where('barcode', 'SCAN-BARCODE-DUP')->count());
    }

    #[Test]
    public function it_rejects_an_image_url_not_coming_from_the_dedicated_upload(): void
    {
        $response = $this->actingAs($this->user)->postJson(
            '/api/inventory/articles',
            $this->validInventoryArticlePayload(['image_url' => 'https://example.com/photo.jpg']),
        );

        $response->assertUnprocessable();
    }

    #[Test]
    public function it_accepts_an_image_url_starting_with_the_expected_prefix(): void
    {
        $response = $this->actingAs($this->user)->postJson(
            '/api/inventory/articles',
            $this->validInventoryArticlePayload(['image_url' => '/storage/articles/valide.jpg']),
        );

        $response->assertCreated();
    }

    #[Test]
    public function it_persists_a_simple_attribute_as_is(): void
    {
        $payload = $this->validInventoryArticlePayload([
            'barcode' => 'SCAN-BARCODE-ATTR-SIMPLE',
            'attributes' => ['practice_type' => 'ROUTE'],
        ]);

        $this->actingAs($this->user)->postJson('/api/inventory/articles', $payload)->assertCreated();

        $article = Article::where('barcode', 'SCAN-BARCODE-ATTR-SIMPLE')->first();
        $this->assertSame('ROUTE', $article->attributeValue('practice_type'));
    }

    #[Test]
    public function it_translates_virtual_wheel_attributes_into_the_real_composite_key(): void
    {
        $payload = $this->validInventoryArticlePayload([
            'barcode' => 'SCAN-BARCODE-ATTR-VIRTUAL',
            'attributes' => ['wheel_diameter' => '700', 'wheel_width_mm' => '28'],
        ]);

        $this->actingAs($this->user)->postJson('/api/inventory/articles', $payload)->assertCreated();

        $article = Article::where('barcode', 'SCAN-BARCODE-ATTR-VIRTUAL')->first();
        $this->assertSame('28-700', $article->attributeValue('etrto_size'));
        $this->assertNull($article->attributeValue('wheel_diameter'));
        $this->assertNull($article->attributeValue('wheel_width_mm'));
    }

    #[Test]
    public function it_does_not_persist_an_invented_value_when_only_half_of_a_composite_attribute_is_given(): void
    {
        $payload = $this->validInventoryArticlePayload([
            'barcode' => 'SCAN-BARCODE-ATTR-PARTIAL',
            'attributes' => ['wheel_diameter' => '700'],
        ]);

        $this->actingAs($this->user)->postJson('/api/inventory/articles', $payload)->assertCreated();

        $article = Article::where('barcode', 'SCAN-BARCODE-ATTR-PARTIAL')->first();
        $this->assertSame(0, $article->attributes()->count());
    }

    #[Test]
    public function it_silently_ignores_attribute_keys_outside_the_whitelist(): void
    {
        $payload = $this->validInventoryArticlePayload([
            'barcode' => 'SCAN-BARCODE-ATTR-REJECTED',
            'attributes' => ['supplier_status' => 'discontinued', 'foo_arbitrary' => 'bar'],
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/inventory/articles', $payload);

        $response->assertCreated();
        $article = Article::where('barcode', 'SCAN-BARCODE-ATTR-REJECTED')->first();
        $this->assertSame(0, ArticleAttribute::where('article_id', $article->id)->count());
    }

    #[Test]
    public function it_makes_the_created_article_immediately_visible_as_having_stock_movements(): void
    {
        $payload = $this->validInventoryArticlePayload(['barcode' => 'SCAN-BARCODE-HASMOV', 'quantity' => 2]);
        $this->actingAs($this->user)->postJson('/api/inventory/articles', $payload)->assertCreated();

        $response = $this->actingAs($this->user)->getJson('/api/articles?has_movements=1');

        $response->assertOk();
        $references = collect($response->json('data'))->pluck('barcode');
        $this->assertTrue($references->contains('SCAN-BARCODE-HASMOV'));
    }

    #[Test]
    public function it_completes_a_sale_the_same_way_whether_the_article_came_from_a_direct_lookup_or_an_ambiguous_choice(): void
    {
        $sharedBarcode = 'SHARED-BARCODE-AMBIGUOUS';
        $first = Article::factory()->create(['barcode' => $sharedBarcode]);
        Article::factory()->create(['barcode' => $sharedBarcode]);

        $lookup = $this->actingAs($this->user)->getJson("/api/inventory/lookup-barcode?code={$sharedBarcode}");
        $lookup->assertJsonPath('result', 'ambiguous');
        $chosenId = $lookup->json('articles.0.id');

        $response = $this->actingAs($this->user)->postJson("/api/articles/{$chosenId}/stock-movements", [
            'type' => 'manual_in',
            'quantity' => 5,
        ]);

        $response->assertCreated();
        $this->assertSame(5.0, Article::find($chosenId)->fresh()->stock_quantity);
    }
}
