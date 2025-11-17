<?php

namespace Tests\Feature\Feedback;

use Tests\TestCase;

class FeedbackBannerTest extends TestCase
{
    public function test_feedback_banner_displays_success_message(): void
    {
        session()->flash('feedback', [
            'type' => 'success',
            'message' => 'Operation reussie',
        ]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('window.feedbackBannerData', false);
        $response->assertSee('"type":"success"', false);
        $response->assertSee('Operation reussie', false);
    }

    public function test_feedback_banner_displays_error_message(): void
    {
        session()->flash('feedback', [
            'type' => 'error',
            'message' => 'Une erreur est survenue',
        ]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('window.feedbackBannerData', false);
        $response->assertSee('"type":"error"', false);
        $response->assertSee('Une erreur est survenue', false);
    }

    public function test_feedback_banner_not_visible_without_message(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertDontSee('window.feedbackBannerData', false);
    }
}
