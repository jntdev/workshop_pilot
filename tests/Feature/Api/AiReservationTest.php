<?php

namespace Tests\Feature\Api;

use App\Models\Bike;
use App\Models\BikeType;
use App\Models\Client;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\ReservationPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AiReservationTest extends TestCase
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

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function reservation(Client $client, string $du, string $au, array $attributes = []): Reservation
    {
        return Reservation::factory()->create(array_merge([
            'client_id' => $client->id,
            'date_reservation' => $du,
            'date_retour' => $au,
            'commentaires' => null,
        ], $attributes));
    }

    #[Test]
    public function it_rejects_requests_without_token(): void
    {
        $this->getJson('/api/ai/reservations')->assertUnauthorized();
        $this->getJson('/api/ai/reservations/1')->assertUnauthorized();
    }

    #[Test]
    public function it_lists_the_reservations_of_a_client_overlapping_a_period(): void
    {
        $hotel = Client::factory()->create(['prenom' => 'Hotel', 'nom' => 'Les Hauts de Kerano']);
        $autre = Client::factory()->create(['prenom' => 'Anne', 'nom' => 'Martin']);

        $fin = $this->reservation($hotel, '2026-08-20', '2026-08-25', ['commentaires' => 'Tarif groupe']);
        $chevauche = $this->reservation($hotel, '2026-07-29', '2026-08-02');
        $this->reservation($hotel, '2026-07-01', '2026-07-05');
        $this->reservation($hotel, '2026-09-01', '2026-09-03');
        $this->reservation($autre, '2026-08-10', '2026-08-12');

        $response = $this->getJson('/api/ai/reservations?search=kerano&from=2026-08-01&to=2026-08-31&order=asc', $this->auth())
            ->assertOk()
            ->assertJsonCount(2);

        $this->assertSame([$chevauche->id, $fin->id], array_column($response->json(), 'id'));
        $this->assertSame('Hotel Les Hauts de Kerano', $response->json('1.client_name'));
        $this->assertSame('Tarif groupe', $response->json('1.commentaires'));
    }

    #[Test]
    public function it_filters_by_client_id_and_status(): void
    {
        $client = Client::factory()->create();
        $paye = $this->reservation($client, '2026-08-01', '2026-08-02', ['statut' => 'paye']);
        $this->reservation($client, '2026-08-03', '2026-08-04', ['statut' => 'reserve']);
        $this->reservation(Client::factory()->create(), '2026-08-05', '2026-08-06', ['statut' => 'paye']);

        $this->getJson("/api/ai/reservations?client_id={$client->id}&statut=paye", $this->auth())
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $paye->id);
    }

    #[Test]
    public function it_rejects_an_invalid_period(): void
    {
        $this->getJson('/api/ai/reservations?from=2026-08-31&to=2026-08-01', $this->auth())
            ->assertUnprocessable()
            ->assertJsonValidationErrors('to');
    }

    #[Test]
    public function it_shows_a_reservation_with_bikes_payments_and_delivery(): void
    {
        $client = Client::factory()->create(['prenom' => 'Hotel', 'nom' => 'Les Hauts de Kerano']);
        $type = BikeType::first() ?? BikeType::create(['id' => 'VAE_mb', 'category' => 'VAE', 'size' => 'M', 'frame_type' => 'b', 'label' => 'VAE M cadre bas', 'stock' => 3]);
        $velo = Bike::create(['name' => 'VAE 12']);
        $reservation = $this->reservation($client, '2026-08-10', '2026-08-14', [
            'commentaires' => 'Livré à l\'hôtel, 2 jours offerts',
            'prix_total_ttc' => 300,
            'livraison_necessaire' => true,
            'adresse_livraison' => 'Hôtel Les Hauts de Kerano',
            'selection' => [['bike_id' => (string) $velo->id, 'start_date' => '2026-08-10', 'end_date' => '2026-08-14', 'dates' => []]],
        ]);
        ReservationItem::create(['reservation_id' => $reservation->id, 'bike_type_id' => $type->id, 'quantite' => 2]);
        ReservationPayment::factory()->create(['reservation_id' => $reservation->id, 'amount' => 100]);

        $this->getJson("/api/ai/reservations/{$reservation->id}", $this->auth())
            ->assertOk()
            ->assertJsonPath('commentaires', 'Livré à l\'hôtel, 2 jours offerts')
            ->assertJsonPath('velos.0.type', $type->label)
            ->assertJsonPath('velos.0.quantite', 2)
            ->assertJsonPath('velos_attribues.0.nom', 'VAE 12')
            ->assertJsonPath('livraison.adresse', 'Hôtel Les Hauts de Kerano')
            ->assertJsonPath('recuperation', null)
            ->assertJsonPath('paiements.0.montant', '100.00')
            ->assertJsonPath('total_paye', 100)
            ->assertJsonPath('reste_a_payer', 200);
    }

    #[Test]
    public function it_returns_404_for_an_unknown_reservation(): void
    {
        $this->getJson('/api/ai/reservations/999999', $this->auth())->assertNotFound();
    }
}
