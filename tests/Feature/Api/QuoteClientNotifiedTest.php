<?php

namespace Tests\Feature\Api;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuoteClientNotifiedTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_marks_the_client_as_notified_with_todays_date_by_default(): void
    {
        $quote = Quote::factory()->create(['client_notified' => false, 'client_notified_at' => null]);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/quotes/{$quote->id}/client-notified", ['client_notified' => true])
            ->assertStatus(200);

        $this->assertTrue($response->json('client_notified'));
        $this->assertSame(now()->format('Y-m-d'), $response->json('client_notified_at'));

        $quote->refresh();
        $this->assertTrue($quote->client_notified);
        $this->assertSame(now()->format('Y-m-d'), $quote->client_notified_at->format('Y-m-d'));
    }

    #[Test]
    public function it_accepts_an_explicit_client_notified_at_date(): void
    {
        $quote = Quote::factory()->create(['client_notified' => false, 'client_notified_at' => null]);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/quotes/{$quote->id}/client-notified", [
                'client_notified' => true,
                'client_notified_at' => '2026-09-14',
            ])
            ->assertStatus(200);

        $this->assertSame('2026-09-14', $response->json('client_notified_at'));
        $this->assertSame('2026-09-14', $quote->fresh()->client_notified_at->format('Y-m-d'));
    }

    #[Test]
    public function it_clears_the_date_when_unmarking_the_client_as_notified(): void
    {
        $quote = Quote::factory()->create(['client_notified' => true, 'client_notified_at' => '2026-09-10']);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/quotes/{$quote->id}/client-notified", ['client_notified' => false])
            ->assertStatus(200);

        $this->assertFalse($response->json('client_notified'));
        $this->assertNull($response->json('client_notified_at'));

        $quote->refresh();
        $this->assertFalse($quote->client_notified);
        $this->assertNull($quote->client_notified_at);
    }

    #[Test]
    public function it_rejects_a_missing_client_notified_value(): void
    {
        $quote = Quote::factory()->create();

        $this->actingAs($this->user)
            ->patchJson("/api/quotes/{$quote->id}/client-notified", [])
            ->assertStatus(422);
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $quote = Quote::factory()->create();

        $this->patchJson("/api/quotes/{$quote->id}/client-notified", ['client_notified' => true])
            ->assertStatus(401);
    }

    /**
     * Preuve que le champ se sauvegarde de façon autonome, sans passer par un
     * enregistrement complet du devis (PUT /api/quotes/{id}) : seuls client_notified
     * et client_notified_at doivent changer, tout le reste du devis reste intact.
     */
    #[Test]
    public function it_saves_independently_without_touching_the_rest_of_the_quote(): void
    {
        $quote = Quote::factory()->create([
            'bike_description' => 'VTT rouge',
            'client_notified' => false,
            'client_notified_at' => null,
        ]);
        $originalUpdatedAt = $quote->updated_at;
        $originalLinesCount = $quote->lines()->count();

        $this->actingAs($this->user)
            ->patchJson("/api/quotes/{$quote->id}/client-notified", ['client_notified' => true])
            ->assertStatus(200);

        $quote->refresh();
        $this->assertTrue($quote->client_notified);
        $this->assertSame('VTT rouge', $quote->bike_description);
        $this->assertSame($originalLinesCount, $quote->lines()->count());
        $this->assertTrue($quote->updated_at->greaterThanOrEqualTo($originalUpdatedAt));
    }
}
