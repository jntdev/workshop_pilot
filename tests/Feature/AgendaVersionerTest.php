<?php

namespace Tests\Feature;

use App\Models\BikeType;
use App\Models\Client;
use App\Models\User;
use App\Services\Agenda\AgendaVersioner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AgendaVersionerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure agenda_meta table exists with initial version
        if (! DB::table('agenda_meta')->exists()) {
            DB::table('agenda_meta')->insert([
                'id' => 1,
                'agenda_version' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    protected function getTestUser(): User
    {
        return User::firstOrCreate(
            ['email' => 'test-agenda@workshop-pilot.com'],
            [
                'name' => 'Test Agenda User',
                'password' => bcrypt('password'),
            ]
        );
    }

    #[Test]
    public function current_returns_integer_greater_than_or_equal_to_one(): void
    {
        $versioner = app(AgendaVersioner::class);

        $version = $versioner->current();

        $this->assertIsInt($version);
        $this->assertGreaterThanOrEqual(1, $version);
    }

    #[Test]
    public function bump_increments_version_and_returns_new_value(): void
    {
        $versioner = app(AgendaVersioner::class);

        $initialVersion = $versioner->current();
        $newVersion = $versioner->bump();

        $this->assertEquals($initialVersion + 1, $newVersion);
        $this->assertEquals($newVersion, $versioner->current());
    }

    #[Test]
    public function bump_is_atomic_and_handles_concurrent_calls(): void
    {
        $versioner = app(AgendaVersioner::class);

        $initialVersion = $versioner->current();

        // Simulate multiple concurrent bumps
        $versioner->bump();
        $versioner->bump();
        $versioner->bump();

        $finalVersion = $versioner->current();

        $this->assertEquals($initialVersion + 3, $finalVersion);
    }

    #[Test]
    public function creating_reservation_increments_agenda_version(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();
        $bikeType = BikeType::firstOrCreate(
            ['id' => 'test_vae'],
            [
                'category' => 'VAE',
                'size' => 'M',
                'frame_type' => 'homme',
                'label' => 'Test VAE M',
                'stock' => 3,
            ]
        );

        $versioner = app(AgendaVersioner::class);
        $versionBefore = $versioner->current();

        $response = $this->actingAs($user)->postJson('/api/reservations', [
            'client_id' => $client->id,
            'date_contact' => now()->format('Y-m-d H:i:s'),
            'date_reservation' => now()->addDays(7)->format('Y-m-d'),
            'date_retour' => now()->addDays(14)->format('Y-m-d'),
            'livraison_necessaire' => false,
            'recuperation_necessaire' => false,
            'prix_total_ttc' => 250.00,
            'acompte_demande' => false,
            'statut' => 'reserve',
            'items' => [
                ['bike_type_id' => $bikeType->id, 'quantite' => 1],
            ],
        ]);

        $response->assertStatus(201);

        // Clear cache to get fresh value from DB
        $versioner->clearCache();
        $versionAfter = $versioner->current();

        $this->assertEquals($versionBefore + 1, $versionAfter);
    }

    #[Test]
    public function updating_reservation_increments_agenda_version(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();
        $bikeType = BikeType::firstOrCreate(
            ['id' => 'test_vae_update'],
            [
                'category' => 'VAE',
                'size' => 'L',
                'frame_type' => 'homme',
                'label' => 'Test VAE L',
                'stock' => 2,
            ]
        );

        // Create a reservation first
        $response = $this->actingAs($user)->postJson('/api/reservations', [
            'client_id' => $client->id,
            'date_contact' => now()->format('Y-m-d H:i:s'),
            'date_reservation' => now()->addDays(7)->format('Y-m-d'),
            'date_retour' => now()->addDays(14)->format('Y-m-d'),
            'livraison_necessaire' => false,
            'recuperation_necessaire' => false,
            'prix_total_ttc' => 250.00,
            'acompte_demande' => false,
            'statut' => 'reserve',
            'items' => [
                ['bike_type_id' => $bikeType->id, 'quantite' => 1],
            ],
        ]);

        $reservationId = $response->json('data.id');

        $versioner = app(AgendaVersioner::class);
        $versioner->clearCache();
        $versionBefore = $versioner->current();

        // Update the reservation
        $this->actingAs($user)->putJson("/api/reservations/{$reservationId}", [
            'prix_total_ttc' => 300.00,
        ]);

        $versioner->clearCache();
        $versionAfter = $versioner->current();

        $this->assertEquals($versionBefore + 1, $versionAfter);
    }

    #[Test]
    public function deleting_reservation_increments_agenda_version(): void
    {
        $user = $this->getTestUser();
        $client = Client::factory()->create();
        $bikeType = BikeType::firstOrCreate(
            ['id' => 'test_vae_delete'],
            [
                'category' => 'VAE',
                'size' => 'S',
                'frame_type' => 'femme',
                'label' => 'Test VAE S',
                'stock' => 1,
            ]
        );

        // Create a reservation first
        $response = $this->actingAs($user)->postJson('/api/reservations', [
            'client_id' => $client->id,
            'date_contact' => now()->format('Y-m-d H:i:s'),
            'date_reservation' => now()->addDays(7)->format('Y-m-d'),
            'date_retour' => now()->addDays(14)->format('Y-m-d'),
            'livraison_necessaire' => false,
            'recuperation_necessaire' => false,
            'prix_total_ttc' => 150.00,
            'acompte_demande' => false,
            'statut' => 'reserve',
            'items' => [
                ['bike_type_id' => $bikeType->id, 'quantite' => 1],
            ],
        ]);

        $reservationId = $response->json('data.id');

        $versioner = app(AgendaVersioner::class);
        $versioner->clearCache();
        $versionBefore = $versioner->current();

        // Delete the reservation
        $this->actingAs($user)->deleteJson("/api/reservations/{$reservationId}");

        $versioner->clearCache();
        $versionAfter = $versioner->current();

        $this->assertEquals($versionBefore + 1, $versionAfter);
    }
}
