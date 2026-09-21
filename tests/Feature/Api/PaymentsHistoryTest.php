<?php

namespace Tests\Feature\Api;

use App\Enums\SaleStatus;
use App\Models\Quote;
use App\Models\QuotePayment;
use App\Models\Reservation;
use App\Models\ReservationPayment;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaymentsHistoryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_groups_quote_payments_by_day_and_method(): void
    {
        $quote = Quote::factory()->create();
        QuotePayment::factory()->create(['quote_id' => $quote->id, 'method' => 'cb', 'amount' => 50, 'paid_at' => '2026-09-15 10:00']);
        QuotePayment::factory()->create(['quote_id' => $quote->id, 'method' => 'cb', 'amount' => 30, 'paid_at' => '2026-09-15 14:00']);
        QuotePayment::factory()->create(['quote_id' => $quote->id, 'method' => 'liquide', 'amount' => 20, 'paid_at' => '2026-09-15 09:00']);
        QuotePayment::factory()->create(['quote_id' => $quote->id, 'method' => 'cb', 'amount' => 999, 'paid_at' => '2026-09-16 10:00']);

        $response = $this->actingAs($this->user)->getJson('/api/dashboard/payments-history');

        $response->assertOk();
        $this->assertEquals(80, $response->json('days.2026-09-15.by_method.cb'));
        $this->assertEquals(20, $response->json('days.2026-09-15.by_method.liquide'));
        $this->assertEquals(100, $response->json('days.2026-09-15.total'));
        $this->assertEquals(999, $response->json('days.2026-09-16.by_method.cb'));
        $this->assertEquals(80, $response->json('days.2026-09-15.by_source.atelier.cb'));
        $this->assertEquals(0, $response->json('days.2026-09-15.by_source.location.cb'));
    }

    #[Test]
    public function it_includes_reservation_payments(): void
    {
        $reservation = Reservation::factory()->create();
        ReservationPayment::factory()->create(['reservation_id' => $reservation->id, 'method' => 'virement', 'amount' => 75, 'paid_at' => '2026-09-15 11:00']);

        $response = $this->actingAs($this->user)->getJson('/api/dashboard/payments-history');

        $response->assertOk();
        $this->assertEquals(75, $response->json('days.2026-09-15.by_method.virement'));
        $this->assertEquals(75, $response->json('days.2026-09-15.by_source.location.virement'));
    }

    #[Test]
    public function it_includes_completed_sales_converted_from_cents(): void
    {
        Sale::factory()->completed()->create([
            'payment_method' => 'cheque',
            'total_ttc' => 4500,
            'completed_at' => '2026-09-15 12:00',
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/dashboard/payments-history');

        $response->assertOk();
        $this->assertEquals(45, $response->json('days.2026-09-15.by_method.cheque'));
        $this->assertEquals(45, $response->json('days.2026-09-15.by_source.caisse.cheque'));
    }

    #[Test]
    public function it_excludes_cancelled_sales(): void
    {
        Sale::factory()->completed()->create([
            'payment_method' => 'cb',
            'total_ttc' => 5000,
            'completed_at' => '2026-09-15 12:00',
            'status' => SaleStatus::Cancelled,
            'cancelled_at' => '2026-09-15 13:00',
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/dashboard/payments-history');

        $response->assertOk();
        $this->assertNull($response->json('days.2026-09-15'));
    }

    #[Test]
    public function it_excludes_draft_sales(): void
    {
        Sale::factory()->create([
            'payment_method' => null,
            'total_ttc' => 5000,
            'status' => SaleStatus::Draft,
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/dashboard/payments-history');

        $response->assertOk();
        $this->assertSame([], $response->json('days'));
    }

    #[Test]
    public function it_combines_all_three_sources_on_the_same_day(): void
    {
        $quote = Quote::factory()->create();
        $reservation = Reservation::factory()->create();

        QuotePayment::factory()->create(['quote_id' => $quote->id, 'method' => 'cb', 'amount' => 10, 'paid_at' => '2026-09-15 10:00']);
        ReservationPayment::factory()->create(['reservation_id' => $reservation->id, 'method' => 'cb', 'amount' => 20, 'paid_at' => '2026-09-15 11:00']);
        Sale::factory()->completed()->create(['payment_method' => 'cb', 'total_ttc' => 3000, 'completed_at' => '2026-09-15 12:00']);

        $response = $this->actingAs($this->user)->getJson('/api/dashboard/payments-history');

        $response->assertOk();
        $this->assertEquals(60, $response->json('days.2026-09-15.by_method.cb'));
        $this->assertEquals(10, $response->json('days.2026-09-15.by_source.atelier.cb'));
        $this->assertEquals(20, $response->json('days.2026-09-15.by_source.location.cb'));
        $this->assertEquals(30, $response->json('days.2026-09-15.by_source.caisse.cb'));
    }

    #[Test]
    public function it_returns_an_empty_days_object_when_there_is_no_payment(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/dashboard/payments-history');

        $response->assertOk();
        $this->assertSame([], $response->json('days'));
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $this->getJson('/api/dashboard/payments-history')->assertStatus(401);
    }
}
