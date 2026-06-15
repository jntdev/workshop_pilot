<?php

namespace Tests\Feature\Catalogue;

use App\Models\Article;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StockMovementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_adds_a_manual_in_movement_and_increases_stock(): void
    {
        $article = Article::factory()->create();

        $response = $this->actingAs($this->user)->postJson("/api/articles/{$article->id}/stock-movements", [
            'type' => 'manual_in',
            'quantity' => 5,
            'note' => 'Réception commande',
        ]);

        $response->assertCreated()
            ->assertJsonPath('stock_quantity', 5);
        $this->assertDatabaseHas('stock_movements', ['article_id' => $article->id, 'quantity' => 5]);
    }

    #[Test]
    public function it_adds_a_manual_out_movement_and_decreases_stock(): void
    {
        $article = Article::factory()->create();
        $article->stockMovements()->create(['quantity' => 10, 'type' => 'manual_in']);

        $response = $this->actingAs($this->user)->postJson("/api/articles/{$article->id}/stock-movements", [
            'type' => 'manual_out',
            'quantity' => 3,
        ]);

        $response->assertCreated()
            ->assertJsonPath('stock_quantity', 7);
        $this->assertDatabaseHas('stock_movements', ['article_id' => $article->id, 'quantity' => -3]);
    }

    #[Test]
    public function it_lists_movements_with_stock_quantity(): void
    {
        $article = Article::factory()->create();
        $article->stockMovements()->create(['quantity' => 10, 'type' => 'manual_in']);
        $article->stockMovements()->create(['quantity' => -2, 'type' => 'manual_out']);

        $response = $this->actingAs($this->user)->getJson("/api/articles/{$article->id}/stock-movements");

        $response->assertOk()
            ->assertJsonPath('stock_quantity', 8)
            ->assertJsonCount(2, 'movements');
    }

    #[Test]
    public function it_deletes_a_manual_movement(): void
    {
        $article = Article::factory()->create();
        $movement = $article->stockMovements()->create(['quantity' => 5, 'type' => 'manual_in']);

        $response = $this->actingAs($this->user)->deleteJson("/api/stock-movements/{$movement->id}");

        $response->assertOk()->assertJsonPath('stock_quantity', 0);
        $this->assertDatabaseMissing('stock_movements', ['id' => $movement->id]);
    }

    #[Test]
    public function it_refuses_to_delete_a_non_manual_movement(): void
    {
        $article = Article::factory()->create();
        $movement = StockMovement::factory()->create([
            'article_id' => $article->id,
            'quantity' => -1,
            'type' => 'quote_consumption',
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/api/stock-movements/{$movement->id}");

        $response->assertUnprocessable();
        $this->assertDatabaseHas('stock_movements', ['id' => $movement->id]);
    }

    #[Test]
    public function it_stock_quantity_equals_sum_of_all_movements(): void
    {
        $article = Article::factory()->create();
        $article->stockMovements()->create(['quantity' => 10, 'type' => 'manual_in']);
        $article->stockMovements()->create(['quantity' => -3, 'type' => 'manual_out']);
        $article->stockMovements()->create(['quantity' => -1, 'type' => 'quote_consumption']);
        $article->stockMovements()->create(['quantity' => 5, 'type' => 'manual_in']);

        $this->assertSame(11, $article->fresh()->stock_quantity);
    }

    #[Test]
    public function it_rejects_movement_with_quantity_zero(): void
    {
        $article = Article::factory()->create();

        $response = $this->actingAs($this->user)->postJson("/api/articles/{$article->id}/stock-movements", [
            'type' => 'manual_in',
            'quantity' => 0,
        ]);

        $response->assertUnprocessable();
    }
}
