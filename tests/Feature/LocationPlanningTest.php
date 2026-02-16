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

class LocationPlanningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer un type de vélo pour les tests
        BikeType::create([
            'id' => 'VAE_mb',
            'category' => 'VAE',
            'size' => 'M',
            'frame_type' => 'b',
            'label' => 'VAE M cadre bas',
            'stock' => 3,
        ]);
    }

    protected function getTestUser(): User
    {
        return User::factory()->create();
    }

    protected function createReservation(Client $client, array $overrides = []): Reservation
    {
        $reservation = Reservation::create(array_merge([
            'client_id' => $client->id,
            'date_contact' => now(),
            'date_reservation' => now()->addDays(7),
            'date_retour' => now()->addDays(14),
            'livraison_necessaire' => false,
            'recuperation_necessaire' => false,
            'prix_total_ttc' => 250.00,
            'acompte_demande' => false,
            'statut' => 'reserve',
        ], $overrides));

        $reservation->items()->create([
            'bike_type_id' => 'VAE_mb',
            'quantite' => 1,
        ]);

        return $reservation;
    }

    #[Test]
    public function it_can_access_planning_page(): void
    {
        $user = $this->getTestUser();

        $response = $this->actingAs($user)->get('/location/planning');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Location/Planning'));
    }

    #[Test]
    public function it_defaults_to_today_when_no_date_provided(): void
    {
        $user = $this->getTestUser();
        $today = now()->format('Y-m-d');

        $response = $this->actingAs($user)->get('/location/planning');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Location/Planning')
            ->where('date', $today)
        );
    }

    #[Test]
    public function it_accepts_date_parameter(): void
    {
        $user = $this->getTestUser();
        $targetDate = '2026-03-15';

        $response = $this->actingAs($user)->get("/location/planning?date={$targetDate}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Location/Planning')
            ->where('date', $targetDate)
        );
    }

    #[Test]
    public function it_returns_departures_for_selected_date(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();
        $targetDate = Carbon::parse('2026-03-20');

        // Réservation qui démarre le jour cible (départ)
        $this->createReservation($client, [
            'date_reservation' => $targetDate,
            'date_retour' => $targetDate->copy()->addDays(7),
        ]);

        // Réservation qui démarre un autre jour (ne doit pas apparaître dans les départs)
        $this->createReservation($client, [
            'date_reservation' => $targetDate->copy()->addDays(1),
            'date_retour' => $targetDate->copy()->addDays(8),
        ]);

        $response = $this->actingAs($user)->get('/location/planning?date=2026-03-20');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Location/Planning')
            ->has('departures', 1)
        );
    }

    #[Test]
    public function it_returns_returns_for_selected_date(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();
        $targetDate = Carbon::parse('2026-03-20');

        // Réservation qui se termine le jour cible (retour)
        $this->createReservation($client, [
            'date_reservation' => $targetDate->copy()->subDays(7),
            'date_retour' => $targetDate,
        ]);

        // Réservation qui se termine un autre jour (ne doit pas apparaître dans les retours)
        $this->createReservation($client, [
            'date_reservation' => $targetDate->copy()->subDays(6),
            'date_retour' => $targetDate->copy()->addDays(1),
        ]);

        $response = $this->actingAs($user)->get('/location/planning?date=2026-03-20');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Location/Planning')
            ->has('returns', 1)
        );
    }

    #[Test]
    public function it_excludes_cancelled_reservations(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();
        $targetDate = Carbon::parse('2026-03-20');

        // Réservation annulée qui démarrerait le jour cible
        $this->createReservation($client, [
            'date_reservation' => $targetDate,
            'date_retour' => $targetDate->copy()->addDays(7),
            'statut' => 'annule',
            'raison_annulation' => 'Test annulation',
        ]);

        // Réservation active qui démarre le jour cible
        $this->createReservation($client, [
            'date_reservation' => $targetDate,
            'date_retour' => $targetDate->copy()->addDays(7),
            'statut' => 'reserve',
        ]);

        $response = $this->actingAs($user)->get('/location/planning?date=2026-03-20');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Location/Planning')
            ->has('departures', 1)
        );
    }

    #[Test]
    public function it_includes_client_information(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create([
            'prenom' => 'Jean',
            'nom' => 'Dupont',
            'telephone' => '0612345678',
        ]);
        $targetDate = Carbon::parse('2026-03-20');

        $this->createReservation($client, [
            'date_reservation' => $targetDate,
            'date_retour' => $targetDate->copy()->addDays(7),
        ]);

        $response = $this->actingAs($user)->get('/location/planning?date=2026-03-20');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Location/Planning')
            ->where('departures.0.client_name', 'Jean Dupont')
            ->where('departures.0.client.telephone', '0612345678')
        );
    }

    #[Test]
    public function it_includes_bike_types_information(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();
        $targetDate = Carbon::parse('2026-03-20');

        $this->createReservation($client, [
            'date_reservation' => $targetDate,
            'date_retour' => $targetDate->copy()->addDays(7),
        ]);

        $response = $this->actingAs($user)->get('/location/planning?date=2026-03-20');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Location/Planning')
            ->where('departures.0.items.0.bike_type.label', 'VAE M cadre bas')
        );
    }

    #[Test]
    public function it_includes_logistics_information(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();
        $targetDate = Carbon::parse('2026-03-20');

        $this->createReservation($client, [
            'date_reservation' => $targetDate,
            'date_retour' => $targetDate->copy()->addDays(7),
            'livraison_necessaire' => true,
            'adresse_livraison' => '123 Rue Test',
            'creneau_livraison' => 'Matin (9h-12h)',
        ]);

        $response = $this->actingAs($user)->get('/location/planning?date=2026-03-20');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Location/Planning')
            ->where('departures.0.livraison_necessaire', true)
            ->where('departures.0.adresse_livraison', '123 Rue Test')
            ->where('departures.0.creneau_livraison', 'Matin (9h-12h)')
        );
    }

    #[Test]
    public function it_returns_empty_arrays_when_no_reservations(): void
    {
        $user = $this->getTestUser();

        $response = $this->actingAs($user)->get('/location/planning?date=2099-12-31');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Location/Planning')
            ->has('departures', 0)
            ->has('returns', 0)
        );
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $response = $this->get('/location/planning');

        $response->assertRedirect('/auth/google');
    }

    #[Test]
    public function it_includes_reservation_color(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();
        $targetDate = Carbon::parse('2026-03-20');

        $this->createReservation($client, [
            'date_reservation' => $targetDate,
            'date_retour' => $targetDate->copy()->addDays(7),
            'color' => 5,
        ]);

        $response = $this->actingAs($user)->get('/location/planning?date=2026-03-20');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Location/Planning')
            ->where('departures.0.color', 5)
        );
    }
}
