<?php

namespace Tests\Feature;

use App\Models\BikeType;
use App\Models\Client;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReservationWindowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Client $client;

    protected BikeType $bikeType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->client = Client::factory()->create();
        $this->bikeType = BikeType::firstOrCreate(
            ['id' => 'VAE_mb'],
            [
                'category' => 'VAE',
                'size' => 'M',
                'frame_type' => 'b',
                'label' => 'VAE M cadre bas',
                'stock' => 3,
            ]
        );
    }

    protected function createReservation(array $attributes = []): Reservation
    {
        return Reservation::factory()->create(array_merge([
            'client_id' => $this->client->id,
            'statut' => 'reserve',
        ], $attributes));
    }

    #[Test]
    public function full_endpoint_loads_non_cancelled_reservations_overlapping_current_year(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 15));

        $overlapFromPreviousYear = $this->createReservation([
            'date_reservation' => '2025-12-28',
            'date_retour' => '2026-01-03',
        ]);

        $insideYear = $this->createReservation([
            'date_reservation' => '2026-06-10',
            'date_retour' => '2026-06-12',
        ]);

        $overlapToNextYear = $this->createReservation([
            'date_reservation' => '2026-12-30',
            'date_retour' => '2027-01-02',
        ]);

        $beforeYear = $this->createReservation([
            'date_reservation' => '2025-01-10',
            'date_retour' => '2025-01-15',
        ]);

        $afterYear = $this->createReservation([
            'date_reservation' => '2027-02-10',
            'date_retour' => '2027-02-15',
        ]);

        $cancelledInsideYear = $this->createReservation([
            'date_reservation' => '2026-07-01',
            'date_retour' => '2026-07-05',
            'statut' => 'annule',
            'raison_annulation' => 'Test annulation',
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/location/full');

        $response->assertStatus(200);
        $ids = collect($response->json('reservations'))->pluck('id')->all();

        $this->assertContains($overlapFromPreviousYear->id, $ids);
        $this->assertContains($insideYear->id, $ids);
        $this->assertContains($overlapToNextYear->id, $ids);
        $this->assertNotContains($beforeYear->id, $ids);
        $this->assertNotContains($afterYear->id, $ids);
        $this->assertNotContains($cancelledInsideYear->id, $ids);

        Carbon::setTestNow();
    }

    #[Test]
    public function full_endpoint_returns_reservations_with_calendar_fields(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 15));

        $reservation = $this->createReservation([
            'date_reservation' => '2026-06-20',
            'date_retour' => '2026-06-25',
            'selection' => [
                ['bike_id' => 'bike_1', 'dates' => ['2026-06-20', '2026-06-21']],
            ],
            'color' => 5,
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/location/full');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'version',
            'bikes',
            'bikeCategories',
            'bikeSizes',
            'reservations' => [
                '*' => [
                    'id',
                    'client_id',
                    'client_name',
                    'client',
                    'date_reservation',
                    'date_retour',
                    'selection',
                    'color',
                    'statut',
                    'items',
                    'payments',
                    'total_paid',
                    'remaining',
                ],
            ],
        ]);

        $reservationData = collect($response->json('reservations'))->firstWhere('id', $reservation->id);
        $this->assertSame('2026-06-20', $reservationData['date_reservation']);
        $this->assertSame(5, $reservationData['color']);
        $this->assertNotEmpty($reservationData['selection']);

        Carbon::setTestNow();
    }

    #[Test]
    public function location_page_and_full_endpoint_use_the_same_reservation_scope_for_current_year(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 2, 16));

        $included = $this->createReservation([
            'date_reservation' => '2026-02-10',
            'date_retour' => '2026-02-20',
        ]);

        $excluded = $this->createReservation([
            'date_reservation' => '2025-12-01',
            'date_retour' => '2025-12-10',
        ]);

        $locationResponse = $this->actingAs($this->user)->get('/location');
        $fullResponse = $this->actingAs($this->user)->getJson('/api/location/full');

        $locationResponse->assertStatus(200);
        $fullResponse->assertStatus(200);

        $locationResponse->assertInertia(function ($page) use ($fullResponse, $included, $excluded) {
            $props = $page->toArray()['props'];
            $locationIds = collect($props['reservations'])->pluck('id')->sort()->values()->all();
            $fullIds = collect($fullResponse->json('reservations'))->pluck('id')->sort()->values()->all();

            $this->assertSame($fullIds, $locationIds);
            $this->assertContains($included->id, $locationIds);
            $this->assertNotContains($excluded->id, $locationIds);
            $this->assertArrayHasKey('agendaVersion', $props);
        });

        Carbon::setTestNow();
    }
}
