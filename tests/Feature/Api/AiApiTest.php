<?php

namespace Tests\Feature\Api;

use App\Models\Article;
use App\Models\Client;
use App\Models\Quote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AiApiTest extends TestCase
{
    use RefreshDatabase;

    private const TOKEN = 'secret-ai-token';

    protected function setUp(): void
    {
        parent::setUp();
        config(['ai.token' => self::TOKEN]);
    }

    /**
     * @return array<string, string>
     */
    private function auth(): array
    {
        return ['Authorization' => 'Bearer '.self::TOKEN];
    }

    #[Test]
    public function it_rejects_requests_without_token(): void
    {
        $this->getJson('/api/ai/clients')->assertUnauthorized();
    }

    #[Test]
    public function it_rejects_a_wrong_token(): void
    {
        $this->getJson('/api/ai/clients', ['Authorization' => 'Bearer nope'])->assertUnauthorized();
    }

    #[Test]
    public function it_rejects_everything_when_no_token_is_configured(): void
    {
        config(['ai.token' => null]);

        $this->getJson('/api/ai/clients', ['Authorization' => 'Bearer '])->assertUnauthorized();
    }

    #[Test]
    public function it_exposes_no_delete_routes(): void
    {
        $client = Client::factory()->create();

        $this->deleteJson("/api/ai/clients/{$client->id}", [], $this->auth())->assertStatus(405);
        $this->assertDatabaseHas('clients', ['id' => $client->id]);
    }

    #[Test]
    public function it_searches_and_creates_clients(): void
    {
        Client::factory()->create(['prenom' => 'Marie', 'nom' => 'Le Gall']);
        Client::factory()->create(['prenom' => 'Yann', 'nom' => 'Kerbrat']);

        $search = $this->getJson('/api/ai/clients?search=gall', $this->auth());
        $search->assertOk();
        $this->assertCount(1, $search->json());
        $this->assertSame('Marie', $search->json('0.prenom'));

        $this->postJson('/api/ai/clients', [
            'prenom' => 'Anne',
            'nom' => 'Morvan',
            'telephone' => '0600000000',
            'avantage_type' => 'aucun',
            'avantage_valeur' => 0,
        ], $this->auth())->assertCreated();

        $this->assertDatabaseHas('clients', ['nom' => 'Morvan']);
    }

    #[Test]
    public function it_creates_a_quote_with_computed_totals_for_a_new_client(): void
    {
        $response = $this->postJson('/api/ai/quotes', [
            'client' => ['prenom' => 'Anne', 'nom' => 'Morvan', 'telephone' => '0600000000'],
            'bike_description' => 'VTT Rockrider rouge',
            'reception_comment' => 'Freins à purger',
            'lines' => [
                ['title' => 'Purge freins', 'quantity' => 2, 'purchase_price_ht' => 5, 'sale_price_ttc' => 24, 'estimated_time_minutes' => 20],
                ['title' => 'Plaquettes', 'quantity' => 1, 'purchase_price_ht' => 10, 'sale_price_ht' => 20],
            ],
        ], $this->auth());

        $response->assertCreated();
        $this->assertSame('reception', $response->json('status'));
        $this->assertCount(2, $response->json('lines'));
        $this->assertSame(20, $response->json('total_estimated_time_minutes'));
        $this->assertEquals(72.0, (float) $response->json('total_ttc'));
        $this->assertDatabaseHas('clients', ['nom' => 'Morvan']);
        $this->assertDatabaseHas('quotes', ['bike_description' => 'VTT Rockrider rouge']);
    }

    #[Test]
    public function it_creates_a_quote_without_lines_for_an_existing_client(): void
    {
        $client = Client::factory()->create();

        $response = $this->postJson('/api/ai/quotes', [
            'client_id' => $client->id,
            'bike_description' => 'Vélo ville',
            'reception_comment' => 'Bruit de chaîne',
        ], $this->auth());

        $response->assertCreated();
        $this->assertSame($client->id, $response->json('client_id'));
        $this->assertSame([], $response->json('lines'));
    }

    #[Test]
    public function it_requires_a_client_to_create_a_quote(): void
    {
        $this->postJson('/api/ai/quotes', [
            'bike_description' => 'Vélo ville',
            'reception_comment' => 'Bruit de chaîne',
        ], $this->auth())->assertUnprocessable();
    }

    #[Test]
    public function it_searches_quotes_by_client_name_and_updates_status(): void
    {
        $client = Client::factory()->create(['prenom' => 'Marie', 'nom' => 'Le Gall']);
        $created = $this->postJson('/api/ai/quotes', [
            'client_id' => $client->id,
            'bike_description' => 'Gravel bleu',
            'reception_comment' => 'Révision',
        ], $this->auth())->json();

        $search = $this->getJson('/api/ai/quotes?search=le gall', $this->auth());
        $search->assertOk();
        $this->assertSame($created['id'], $search->json('0.id'));

        $this->patchJson("/api/ai/quotes/{$created['id']}/status", ['status' => 'in_progress'], $this->auth())
            ->assertOk();
        $this->assertSame('in_progress', Quote::find($created['id'])->status->value);
    }

    #[Test]
    public function it_plans_a_quote_and_lists_agenda_items(): void
    {
        $client = Client::factory()->create();
        $quote = $this->postJson('/api/ai/quotes', [
            'client_id' => $client->id,
            'bike_description' => 'Gravel bleu',
            'reception_comment' => 'Révision',
        ], $this->auth())->json();

        $this->postJson("/api/ai/quotes/{$quote['id']}/appointments", [
            'starts_at' => '2026-10-20 09:00:00',
            'ends_at' => '2026-10-20 10:00:00',
        ], $this->auth())->assertCreated();

        $this->postJson('/api/ai/agenda-events', [
            'title' => 'Livraison fournisseur',
            'starts_at' => '2026-10-20 14:00:00',
            'ends_at' => '2026-10-20 14:30:00',
        ], $this->auth())->assertCreated();

        $items = $this->getJson('/api/ai/agenda/items?start=2026-10-19&end=2026-10-26', $this->auth());
        $items->assertOk();
        $this->assertSame(['quote', 'event'], collect($items->json())->pluck('kind')->all());
    }

    #[Test]
    public function it_creates_and_updates_a_stock_article_with_its_references(): void
    {
        $category = $this->postJson('/api/ai/article-categories', ['name' => 'Catégorie de test API IA'], $this->auth());
        $category->assertCreated();

        $subcategory = $this->postJson('/api/ai/article-subcategories', [
            'article_category_id' => $category->json('id'),
            'name' => 'Sous-catégorie de test API IA',
        ], $this->auth());
        $subcategory->assertCreated();

        $brand = $this->postJson('/api/ai/brands', ['name' => 'KMC'], $this->auth());
        $brand->assertCreated();

        $supplier = $this->postJson('/api/ai/suppliers', ['name' => 'CGN'], $this->auth());
        $supplier->assertCreated();

        $article = $this->postJson('/api/ai/articles', [
            'article_subcategory_id' => $subcategory->json('id'),
            'brand_id' => $brand->json('id'),
            'supplier_id' => $supplier->json('id'),
            'reference' => 'KMC-X8-ROULEAU',
            'designation' => 'Chaîne 8V KMC X8 (touret 50 m)',
            'purchase_price_ht' => 28500,
            'sale_price_ttc' => 39900,
            'tva_rate' => 20,
            'unit' => 'pièce',
        ], $this->auth());

        $article->assertCreated();
        $this->assertDatabaseHas('articles', ['reference' => 'KMC-X8-ROULEAU', 'purchase_price_ht' => 28500]);

        $this->putJson('/api/ai/articles/'.$article->json('id'), ['sale_price_ttc' => 42000], $this->auth())
            ->assertOk();
        $this->assertDatabaseHas('articles', ['reference' => 'KMC-X8-ROULEAU', 'sale_price_ttc' => 42000]);

        $this->getJson('/api/ai/articles?search=KMC', $this->auth())->assertOk();
    }

    #[Test]
    public function it_rejects_a_duplicate_article_reference(): void
    {
        $payload = [
            'reference' => 'REF-1',
            'designation' => 'Article',
            'purchase_price_ht' => 100,
            'sale_price_ttc' => 200,
            'tva_rate' => 20,
            'unit' => 'pièce',
        ];

        $this->postJson('/api/ai/articles', $payload, $this->auth())->assertCreated();
        $this->postJson('/api/ai/articles', $payload, $this->auth())->assertUnprocessable();
    }

    #[Test]
    public function it_adds_stock_to_an_article_and_exposes_no_article_delete(): void
    {
        $article = $this->postJson('/api/ai/articles', [
            'reference' => 'REF-2',
            'designation' => 'Article',
            'purchase_price_ht' => 100,
            'sale_price_ttc' => 200,
            'tva_rate' => 20,
            'unit' => 'pièce',
        ], $this->auth())->json();

        $this->postJson("/api/ai/articles/{$article['id']}/stock-movements", [
            'type' => 'manual_in',
            'quantity' => 3,
        ], $this->auth())->assertCreated();

        $this->assertEquals(3.0, (float) $this->getJson("/api/ai/articles/{$article['id']}", $this->auth())->json('stock_quantity'));
        $this->deleteJson("/api/ai/articles/{$article['id']}", [], $this->auth())->assertStatus(405);
    }

    #[Test]
    public function a_unit_article_inherits_the_photo_of_its_lot(): void
    {
        $lot = Article::factory()->create(['image_url' => 'https://exemple.test/lot.jpg']);
        $unit = Article::factory()->create(['lot_article_id' => $lot->id, 'image_url' => null]);
        $ownPhoto = Article::factory()->create(['lot_article_id' => $lot->id, 'image_url' => 'https://exemple.test/propre.jpg']);

        $this->getJson("/api/ai/articles/{$unit->id}", $this->auth())
            ->assertOk()
            ->assertJsonPath('image_url', 'https://exemple.test/lot.jpg');

        $this->assertSame('https://exemple.test/propre.jpg', $ownPhoto->fresh()->image_url);
        $this->assertNull(Article::factory()->create(['image_url' => null])->image_url);
    }
}
