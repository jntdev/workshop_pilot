<?php

namespace Tests\Feature\Feedback;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class FeedbackBannerTest extends TestCase
{
    use RefreshDatabase;

    public function test_feedback_banner_displays_success_message(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['message' => 'Opération réussie'])
            ->get(route('home'));

        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->where('flash.message', 'Opération réussie')
        );
    }

    public function test_feedback_banner_displays_error_message(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['error' => 'Une erreur est survenue'])
            ->get(route('home'));

        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->where('flash.error', 'Une erreur est survenue')
        );
    }

    public function test_feedback_banner_not_visible_without_message(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->where('flash.message', null)
            ->where('flash.error', null)
        );
    }
}
