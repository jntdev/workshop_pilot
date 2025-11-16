<?php

namespace Tests\Feature\Frontend;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_dashboard_route_is_accessible(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('home.dashboard');
    }

    public function test_clients_index_route_is_accessible(): void
    {
        $response = $this->get('/clients');

        $response->assertStatus(200);
        $response->assertViewIs('clients.index');
    }

    public function test_atelier_index_route_is_accessible(): void
    {
        $response = $this->get('/atelier');

        $response->assertStatus(200);
        $response->assertViewIs('atelier.index');
    }

    public function test_location_index_route_is_accessible(): void
    {
        $response = $this->get('/location');

        $response->assertStatus(200);
        $response->assertViewIs('location.index');
    }

    public function test_dashboard_displays_navigation_cards(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Clients');
        $response->assertSee('Atelier');
        $response->assertSee('Location');
    }

    public function test_named_routes_are_defined(): void
    {
        $this->assertTrue(route('home') !== null);
        $this->assertTrue(route('clients.index') !== null);
        $this->assertTrue(route('atelier.index') !== null);
        $this->assertTrue(route('location.index') !== null);
    }
}
