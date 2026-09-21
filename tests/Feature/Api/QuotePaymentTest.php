<?php

namespace Tests\Feature\Api;

use App\Models\Quote;
use App\Models\QuotePayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuotePaymentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_creates_a_payment_for_a_quote(): void
    {
        $quote = Quote::factory()->create(['total_ttc' => 100]);

        $response = $this->actingAs($this->user)->postJson("/api/quotes/{$quote->id}/payments", [
            'amount' => 50,
            'method' => 'cb',
            'paid_at' => '2026-09-10 10:00',
        ]);

        $response->assertCreated();
        $this->assertSame('cb', $response->json('payment.method'));
        $this->assertDatabaseHas('quote_payments', [
            'quote_id' => $quote->id,
            'method' => 'cb',
        ]);
    }

    #[Test]
    public function it_rejects_an_invalid_method(): void
    {
        $quote = Quote::factory()->create();

        $this->actingAs($this->user)->postJson("/api/quotes/{$quote->id}/payments", [
            'amount' => 50,
            'method' => 'bitcoin',
            'paid_at' => now()->toDateTimeString(),
        ])->assertStatus(422);
    }

    #[Test]
    public function it_rejects_a_zero_amount(): void
    {
        $quote = Quote::factory()->create();

        $this->actingAs($this->user)->postJson("/api/quotes/{$quote->id}/payments", [
            'amount' => 0,
            'method' => 'cb',
            'paid_at' => now()->toDateTimeString(),
        ])->assertStatus(422);
    }

    #[Test]
    public function it_lists_payments_for_a_quote(): void
    {
        $quote = Quote::factory()->create();
        $other = Quote::factory()->create();
        QuotePayment::factory()->create(['quote_id' => $quote->id, 'paid_at' => '2026-09-01']);
        QuotePayment::factory()->create(['quote_id' => $quote->id, 'paid_at' => '2026-09-05']);
        QuotePayment::factory()->create(['quote_id' => $other->id]);

        $response = $this->actingAs($this->user)->getJson("/api/quotes/{$quote->id}/payments");

        $response->assertOk();
        $this->assertCount(2, $response->json());
    }

    #[Test]
    public function it_sets_paid_at_when_cumulative_payments_reach_the_total(): void
    {
        $quote = Quote::factory()->create(['total_ttc' => 100, 'paid_at' => null]);

        $this->actingAs($this->user)->postJson("/api/quotes/{$quote->id}/payments", [
            'amount' => 60,
            'method' => 'cb',
            'paid_at' => '2026-09-10 10:00',
        ]);
        $this->assertNull($quote->fresh()->paid_at);

        $response = $this->actingAs($this->user)->postJson("/api/quotes/{$quote->id}/payments", [
            'amount' => 40,
            'method' => 'liquide',
            'paid_at' => '2026-09-12 10:00',
        ]);

        $this->assertSame('2026-09-12', $response->json('paid_at'));
        $this->assertSame('2026-09-12', $quote->fresh()->paid_at->format('Y-m-d'));
    }

    #[Test]
    public function it_picks_the_earliest_payment_date_that_reaches_the_total_regardless_of_entry_order(): void
    {
        $quote = Quote::factory()->create(['total_ttc' => 100, 'paid_at' => null]);

        // Saisi en second mais daté avant : la date retenue doit suivre l'ordre chronologique des paiements, pas l'ordre de saisie.
        $this->actingAs($this->user)->postJson("/api/quotes/{$quote->id}/payments", [
            'amount' => 40,
            'method' => 'liquide',
            'paid_at' => '2026-09-05 10:00',
        ]);
        $response = $this->actingAs($this->user)->postJson("/api/quotes/{$quote->id}/payments", [
            'amount' => 60,
            'method' => 'cb',
            'paid_at' => '2026-09-01 10:00',
        ]);

        $this->assertSame('2026-09-05', $response->json('paid_at'));
    }

    #[Test]
    public function it_clears_paid_at_when_a_payment_is_removed_and_total_is_no_longer_reached(): void
    {
        $quote = Quote::factory()->create(['total_ttc' => 100]);
        $payment = QuotePayment::factory()->create(['quote_id' => $quote->id, 'amount' => 100, 'paid_at' => '2026-09-10']);
        $quote->recalculatePaidAt();
        $this->assertNotNull($quote->fresh()->paid_at);

        $response = $this->actingAs($this->user)->deleteJson("/api/quote-payments/{$payment->id}");

        $response->assertOk();
        $this->assertNull($quote->fresh()->paid_at);
        $this->assertEquals(0, $response->json('total_paid'));
    }

    #[Test]
    public function it_deletes_a_payment(): void
    {
        $quote = Quote::factory()->create();
        $payment = QuotePayment::factory()->create(['quote_id' => $quote->id]);

        $this->actingAs($this->user)->deleteJson("/api/quote-payments/{$payment->id}")
            ->assertOk();

        $this->assertDatabaseMissing('quote_payments', ['id' => $payment->id]);
    }

    #[Test]
    public function it_deletes_payments_when_the_quote_is_permanently_deleted(): void
    {
        $quote = Quote::factory()->create();
        $payment = QuotePayment::factory()->create(['quote_id' => $quote->id]);

        $quote->forceDelete();

        $this->assertDatabaseMissing('quote_payments', ['id' => $payment->id]);
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $quote = Quote::factory()->create();

        $this->postJson("/api/quotes/{$quote->id}/payments", [
            'amount' => 50,
            'method' => 'cb',
            'paid_at' => now()->toDateTimeString(),
        ])->assertStatus(401);
    }
}
