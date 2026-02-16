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
    public function window_endpoint_requires_start_and_end_dates(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/reservations/window');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['start', 'end']);
    }

    #[Test]
    public function window_endpoint_validates_end_after_start(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/reservations/window?start=2026-03-15&end=2026-03-01');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['end']);
    }

    #[Test]
    public function window_returns_reservations_within_date_range(): void
    {
        // Réservation dans la fenêtre
        $inWindow = $this->createReservation([
            'date_reservation' => '2026-02-20',
            'date_retour' => '2026-02-25',
        ]);

        // Réservation hors fenêtre (avant)
        $beforeWindow = $this->createReservation([
            'date_reservation' => '2026-01-01',
            'date_retour' => '2026-01-10',
        ]);

        // Réservation hors fenêtre (après)
        $afterWindow = $this->createReservation([
            'date_reservation' => '2026-04-01',
            'date_retour' => '2026-04-10',
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/reservations/window?start=2026-02-01&end=2026-03-15');

        $response->assertStatus(200);
        $ids = collect($response->json())->pluck('id')->toArray();

        $this->assertContains($inWindow->id, $ids);
        $this->assertNotContains($beforeWindow->id, $ids);
        $this->assertNotContains($afterWindow->id, $ids);
    }

    #[Test]
    public function window_returns_active_reservations_started_before_window(): void
    {
        // Réservation commencée avant la fenêtre mais toujours active
        $activeBeforeWindow = $this->createReservation([
            'date_reservation' => '2026-01-15',
            'date_retour' => '2026-02-20', // Se termine dans la fenêtre
        ]);

        // Réservation commencée et terminée avant la fenêtre
        $completedBeforeWindow = $this->createReservation([
            'date_reservation' => '2026-01-01',
            'date_retour' => '2026-01-15', // Terminée avant la fenêtre
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/reservations/window?start=2026-02-01&end=2026-03-15');

        $response->assertStatus(200);
        $ids = collect($response->json())->pluck('id')->toArray();

        $this->assertContains($activeBeforeWindow->id, $ids);
        $this->assertNotContains($completedBeforeWindow->id, $ids);
    }

    #[Test]
    public function window_excludes_cancelled_reservations(): void
    {
        // Réservation annulée dans la fenêtre
        $cancelled = $this->createReservation([
            'date_reservation' => '2026-02-20',
            'date_retour' => '2026-02-25',
            'statut' => 'annule',
            'raison_annulation' => 'Test annulation',
        ]);

        // Réservation active dans la fenêtre
        $active = $this->createReservation([
            'date_reservation' => '2026-02-20',
            'date_retour' => '2026-02-25',
            'statut' => 'reserve',
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/reservations/window?start=2026-02-01&end=2026-03-15');

        $response->assertStatus(200);
        $ids = collect($response->json())->pluck('id')->toArray();

        $this->assertNotContains($cancelled->id, $ids);
        $this->assertContains($active->id, $ids);
    }

    #[Test]
    public function window_returns_correct_format_for_calendar(): void
    {
        $reservation = $this->createReservation([
            'date_reservation' => '2026-02-20',
            'date_retour' => '2026-02-25',
            'selection' => [
                ['bike_id' => 'VAE_mb_1', 'dates' => ['2026-02-20', '2026-02-21']],
            ],
            'color' => 5,
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/reservations/window?start=2026-02-01&end=2026-03-15');

        $response->assertStatus(200);
        $response->assertJsonStructure([
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
            ],
        ]);

        $reservationData = collect($response->json())->firstWhere('id', $reservation->id);
        $this->assertEquals('2026-02-20', $reservationData['date_reservation']);
        $this->assertEquals(5, $reservationData['color']);
        $this->assertNotEmpty($reservationData['selection']);
    }

    #[Test]
    public function location_page_loads_reservations_in_sliding_window(): void
    {
        // Freeze time to 2026-02-16
        Carbon::setTestNow(Carbon::create(2026, 2, 16));

        // J-15 = 2026-02-01, J+30 = 2026-03-18

        // Réservation du 1er janvier (terminée >15 jours) - NE DOIT PAS être chargée
        $oldReservation = $this->createReservation([
            'date_reservation' => '2026-01-01',
            'date_retour' => '2026-01-10',
        ]);

        // Réservation du 10-20 février (dans la fenêtre) - DOIT être chargée
        $inWindowReservation = $this->createReservation([
            'date_reservation' => '2026-02-10',
            'date_retour' => '2026-02-20',
        ]);

        // Réservation du 5 janvier au 25 février (commencée avant mais active) - DOIT être chargée
        $activeReservation = $this->createReservation([
            'date_reservation' => '2026-01-05',
            'date_retour' => '2026-02-25',
        ]);

        // Réservation du 25 mars (au-delà de +30 jours) - NE DOIT PAS être chargée
        $futureReservation = $this->createReservation([
            'date_reservation' => '2026-03-25',
            'date_retour' => '2026-04-01',
        ]);

        $response = $this->actingAs($this->user)->get('/location');

        $response->assertStatus(200);
        $response->assertInertia(function ($page) use ($oldReservation, $inWindowReservation, $activeReservation, $futureReservation) {
            $reservations = collect($page->toArray()['props']['reservations']);
            $ids = $reservations->pluck('id')->toArray();

            // Vérifier les critères d'acceptance
            $this->assertNotContains($oldReservation->id, $ids, 'Réservation du 1er janvier NE devrait PAS être chargée');
            $this->assertContains($inWindowReservation->id, $ids, 'Réservation du 10-20 février DEVRAIT être chargée');
            $this->assertContains($activeReservation->id, $ids, 'Réservation du 5 jan - 25 fév DEVRAIT être chargée (active)');
            $this->assertNotContains($futureReservation->id, $ids, 'Réservation du 25 mars NE devrait PAS être chargée');
        });

        Carbon::setTestNow(); // Reset time
    }

    #[Test]
    public function location_page_excludes_cancelled_from_sliding_window(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 2, 16));

        // Réservation annulée dans la fenêtre
        $cancelledReservation = $this->createReservation([
            'date_reservation' => '2026-02-10',
            'date_retour' => '2026-02-20',
            'statut' => 'annule',
            'raison_annulation' => 'Test',
        ]);

        $response = $this->actingAs($this->user)->get('/location');

        $response->assertStatus(200);
        $response->assertInertia(function ($page) use ($cancelledReservation) {
            $reservations = collect($page->toArray()['props']['reservations']);
            $ids = $reservations->pluck('id')->toArray();

            $this->assertNotContains($cancelledReservation->id, $ids, 'Réservation annulée NE devrait PAS être chargée');
        });

        Carbon::setTestNow();
    }
}
