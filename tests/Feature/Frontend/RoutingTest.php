<?php

namespace Tests\Feature\Frontend;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class RoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_dashboard_route_is_accessible(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Dashboard')
        );
    }

    public function test_clients_index_route_is_accessible(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/clients');

        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Clients/Index')
        );
    }

    public function test_atelier_index_route_is_accessible(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/atelier');

        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Atelier/Index')
        );
    }

    public function test_location_index_route_is_accessible(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/location');

        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Location/Index')
        );
    }

    public function test_unauthenticated_user_is_redirected(): void
    {
        $response = $this->get('/');

        $response->assertStatus(302);
    }

    public function test_named_routes_are_defined(): void
    {
        $this->assertTrue(route('home') !== null);
        $this->assertTrue(route('clients.index') !== null);
        $this->assertTrue(route('atelier.index') !== null);
        $this->assertTrue(route('location.index') !== null);
    }
}
