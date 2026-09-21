<?php

namespace Tests\Feature\Api;

use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuoteWorkCompletedNotifiedTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_marks_the_client_as_notified_when_status_is_done(): void
    {
        $quote = Quote::factory()->create(['status' => 'done', 'work_completed_notified' => false]);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/quotes/{$quote->id}/work-completed-notified", ['work_completed_notified' => true])
            ->assertStatus(200);

        $this->assertTrue($response->json('work_completed_notified'));
        $this->assertSame(now()->format('Y-m-d'), $response->json('work_completed_notified_at'));

        $quote->refresh();
        $this->assertTrue($quote->work_completed_notified);
        $this->assertSame(now()->format('Y-m-d'), $quote->work_completed_notified_at->format('Y-m-d'));
    }

    #[Test]
    public function it_rejects_marking_notified_when_status_is_not_done(): void
    {
        $quote = Quote::factory()->create(['status' => 'in_progress']);

        $this->actingAs($this->user)
            ->patchJson("/api/quotes/{$quote->id}/work-completed-notified", ['work_completed_notified' => true])
            ->assertStatus(422);
    }

    #[Test]
    public function it_clears_the_date_when_unmarking(): void
    {
        $quote = Quote::factory()->create([
            'status' => 'done',
            'work_completed_notified' => true,
            'work_completed_notified_at' => '2026-09-10',
        ]);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/quotes/{$quote->id}/work-completed-notified", ['work_completed_notified' => false])
            ->assertStatus(200);

        $this->assertFalse($response->json('work_completed_notified'));
        $this->assertNull($response->json('work_completed_notified_at'));
    }

    #[Test]
    public function it_resets_to_false_when_status_moves_away_from_done(): void
    {
        $quote = Quote::factory()->create([
            'status' => 'done',
            'work_completed_notified' => true,
            'work_completed_notified_at' => now(),
        ]);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/quotes/{$quote->id}/status", ['status' => 'in_progress'])
            ->assertStatus(200);

        $this->assertFalse($response->json('work_completed_notified'));

        $quote->refresh();
        $this->assertFalse($quote->work_completed_notified);
        $this->assertNull($quote->work_completed_notified_at);
    }

    #[Test]
    public function it_keeps_the_flag_when_status_stays_done(): void
    {
        $quote = Quote::factory()->create([
            'status' => 'in_progress',
            'work_completed_notified' => false,
        ]);

        $this->actingAs($this->user)
            ->patchJson("/api/quotes/{$quote->id}/status", ['status' => 'done'])
            ->assertStatus(200);

        $quote->refresh();
        $this->assertSame('done', $quote->status->value);
        $this->assertFalse($quote->work_completed_notified);
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $quote = Quote::factory()->create(['status' => 'done']);

        $this->patchJson("/api/quotes/{$quote->id}/work-completed-notified", ['work_completed_notified' => true])
            ->assertStatus(401);
    }
}
