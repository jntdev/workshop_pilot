<?php

namespace Tests\Feature\Catalogue;

use App\Enums\ArticleCategorySource;
use App\Models\ArticleCategory;
use App\Models\ArticleSubcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ArticleCategoryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_lists_categories_with_subcategories(): void
    {
        $category = ArticleCategory::factory()->create(['name' => 'Pneus']);
        ArticleSubcategory::factory()->create(['article_category_id' => $category->id, 'name' => 'Chambre à air']);

        $response = $this->actingAs($this->user)->getJson('/api/article-categories');

        $response->assertOk()
            ->assertJsonPath('categories.0.name', 'Pneus')
            ->assertJsonPath('categories.0.subcategories.0.name', 'Chambre à air');
    }

    #[Test]
    public function it_creates_a_category(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/article-categories', [
            'name' => 'Transmission',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('article_categories', ['name' => 'Transmission']);
    }

    #[Test]
    public function it_rejects_duplicate_category_name(): void
    {
        ArticleCategory::factory()->create(['name' => 'Freins']);

        $response = $this->actingAs($this->user)->postJson('/api/article-categories', [
            'name' => 'Freins',
        ]);

        $response->assertUnprocessable();
    }

    #[Test]
    public function it_updates_a_category(): void
    {
        $category = ArticleCategory::factory()->create(['name' => 'Ancienne']);

        $response = $this->actingAs($this->user)->putJson("/api/article-categories/{$category->id}", [
            'name' => 'Nouvelle',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('article_categories', ['id' => $category->id, 'name' => 'Nouvelle']);
    }

    #[Test]
    public function it_deletes_a_category_without_subcategories(): void
    {
        $category = ArticleCategory::factory()->create();

        $response = $this->actingAs($this->user)->deleteJson("/api/article-categories/{$category->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('article_categories', ['id' => $category->id]);
    }

    #[Test]
    public function it_refuses_to_delete_category_with_subcategories(): void
    {
        $category = ArticleCategory::factory()->create();
        ArticleSubcategory::factory()->create(['article_category_id' => $category->id]);

        $response = $this->actingAs($this->user)->deleteJson("/api/article-categories/{$category->id}");

        $response->assertUnprocessable();
        $this->assertDatabaseHas('article_categories', ['id' => $category->id]);
    }

    #[Test]
    public function it_always_marks_a_created_category_as_manual(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/article-categories', [
            'name' => 'Transmission',
            'source' => ArticleCategorySource::Catalogue->value,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('article_categories', [
            'name' => 'Transmission',
            'source' => ArticleCategorySource::Manual->value,
        ]);
    }

    #[Test]
    public function it_filters_categories_by_manual_source(): void
    {
        ArticleCategory::factory()->manual()->create(['name' => 'Freinage']);
        ArticleCategory::factory()->create(['name' => 'Pieces Cycles']);

        $response = $this->actingAs($this->user)->getJson('/api/article-categories?source=manual');

        $response->assertOk()
            ->assertJsonCount(1, 'categories')
            ->assertJsonPath('categories.0.name', 'Freinage');
    }

    #[Test]
    public function it_filters_categories_by_catalogue_source(): void
    {
        ArticleCategory::factory()->manual()->create(['name' => 'Freinage']);
        ArticleCategory::factory()->create(['name' => 'Pieces Cycles']);

        $response = $this->actingAs($this->user)->getJson('/api/article-categories?source=catalogue');

        $response->assertOk()
            ->assertJsonCount(1, 'categories')
            ->assertJsonPath('categories.0.name', 'Pieces Cycles');
    }

    #[Test]
    public function it_returns_all_categories_when_source_is_omitted(): void
    {
        ArticleCategory::factory()->manual()->create();
        ArticleCategory::factory()->create();

        $response = $this->actingAs($this->user)->getJson('/api/article-categories');

        $response->assertOk()->assertJsonCount(2, 'categories');
    }

    #[Test]
    public function it_rejects_an_invalid_source_filter_value(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/article-categories?source=bogus');

        $response->assertUnprocessable();
    }

    #[Test]
    public function it_reorders_categories(): void
    {
        $cat1 = ArticleCategory::factory()->create(['sort_order' => 0]);
        $cat2 = ArticleCategory::factory()->create(['sort_order' => 1]);

        $response = $this->actingAs($this->user)->postJson('/api/article-categories/reorder', [
            'categories' => [
                ['id' => $cat1->id, 'sort_order' => 1],
                ['id' => $cat2->id, 'sort_order' => 0],
            ],
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('article_categories', ['id' => $cat1->id, 'sort_order' => 1]);
        $this->assertDatabaseHas('article_categories', ['id' => $cat2->id, 'sort_order' => 0]);
    }
}
