<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CaisseRouteTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_caisse_page_loads_via_inertia(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/vente/caisse');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('Vente/Caisse'));
    }

    #[Test]
    public function it_redirects_guests_to_login(): void
    {
        $response = $this->get('/vente/caisse');

        $response->assertRedirect();
    }
}
