<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Agenda\AgendaVersioner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LocationApiTest extends TestCase
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
            ['email' => 'test-location@workshop-pilot.com'],
            [
                'name' => 'Test Location User',
                'password' => bcrypt('password'),
            ]
        );
    }

    #[Test]
    public function version_endpoint_returns_current_version(): void
    {
        $user = $this->getTestUser();
        $versioner = app(AgendaVersioner::class);
        $expectedVersion = $versioner->current();

        $response = $this->actingAs($user)->getJson('/api/location/version');

        $response->assertStatus(200);
        $response->assertJson([
            'version' => $expectedVersion,
        ]);
    }

    #[Test]
    public function version_endpoint_requires_authentication(): void
    {
        $response = $this->getJson('/api/location/version');

        $response->assertStatus(401);
    }

    #[Test]
    public function full_endpoint_returns_complete_payload(): void
    {
        $user = $this->getTestUser();

        $response = $this->actingAs($user)->getJson('/api/location/full');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'version',
            'bikes',
            'bikeCategories',
            'bikeSizes',
            'reservations',
        ]);
        $this->assertIsInt($response->json('version'));
        $this->assertIsArray($response->json('bikes'));
        $this->assertIsArray($response->json('bikeCategories'));
        $this->assertIsArray($response->json('bikeSizes'));
        $this->assertIsArray($response->json('reservations'));
    }

    #[Test]
    public function full_endpoint_requires_authentication(): void
    {
        $response = $this->getJson('/api/location/full');

        $response->assertStatus(401);
    }

    #[Test]
    public function full_endpoint_version_matches_version_endpoint(): void
    {
        $user = $this->getTestUser();

        $versionResponse = $this->actingAs($user)->getJson('/api/location/version');
        $fullResponse = $this->actingAs($user)->getJson('/api/location/full');

        $versionResponse->assertStatus(200);
        $fullResponse->assertStatus(200);
        $this->assertSame($versionResponse->json('version'), $fullResponse->json('version'));
    }

    #[Test]
    public function location_page_exposes_agenda_version_prop(): void
    {
        $user = $this->getTestUser();
        $version = app(AgendaVersioner::class)->current();

        $response = $this->actingAs($user)->get('/location');

        $response->assertStatus(200);
        $response->assertInertia(function ($page) use ($version) {
            $props = $page->toArray()['props'];
            $this->assertArrayHasKey('agendaVersion', $props);
            $this->assertSame($version, $props['agendaVersion']);
        });
    }
}
