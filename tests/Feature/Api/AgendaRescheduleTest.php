<?php

namespace Tests\Feature\Api;

use App\Models\AgendaEvent;
use App\Models\Quote;
use App\Models\QuoteAppointment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AgendaRescheduleTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_reschedules_not_completed_items_to_the_next_open_day(): void
    {
        // 2026-09-22 = mardi
        $quote = Quote::factory()->create();
        $appointment = QuoteAppointment::factory()->create([
            'quote_id' => $quote->id,
            'starts_at' => '2026-09-22 09:00:00',
            'ends_at' => '2026-09-22 09:30:00',
        ]);
        $event = AgendaEvent::factory()->create([
            'starts_at' => '2026-09-22 10:00:00',
            'ends_at' => '2026-09-22 10:30:00',
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/agenda/reschedule', ['day' => '2026-09-22']);

        $response->assertOk();
        $response->assertJsonStructure(['moved_count', 'days_touched', 'max_cascade_depth']);
        $this->assertSame(2, $response->json('moved_count'));

        $this->assertDatabaseHas('quote_appointments', [
            'id' => $appointment->id,
            'starts_at' => '2026-09-23 09:00:00',
        ]);
        $this->assertDatabaseHas('agenda_events', [
            'id' => $event->id,
            'starts_at' => '2026-09-23 09:30:00',
        ]);
    }

    #[Test]
    public function engagements_are_prioritized_over_regular_items_across_both_kinds(): void
    {
        $quote = Quote::factory()->create();
        $regularEvent = AgendaEvent::factory()->create([
            'starts_at' => '2026-09-22 09:00:00',
            'ends_at' => '2026-09-22 09:30:00',
        ]);
        $engagementAppointment = QuoteAppointment::factory()->create([
            'quote_id' => $quote->id,
            'is_commitment' => true,
            'starts_at' => '2026-09-22 10:00:00',
            'ends_at' => '2026-09-22 10:30:00',
        ]);

        $this->actingAs($this->user)->postJson('/api/agenda/reschedule', ['day' => '2026-09-22'])->assertOk();

        // L'engagement (devis) passe devant l'événement libre malgré son heure d'origine plus tardive.
        $this->assertDatabaseHas('quote_appointments', [
            'id' => $engagementAppointment->id,
            'starts_at' => '2026-09-23 09:00:00',
        ]);
        $this->assertDatabaseHas('agenda_events', [
            'id' => $regularEvent->id,
            'starts_at' => '2026-09-23 09:30:00',
        ]);
    }

    #[Test]
    public function a_rendezvous_on_the_target_day_is_never_moved(): void
    {
        $quote = Quote::factory()->create();
        $work = QuoteAppointment::factory()->create([
            'quote_id' => $quote->id,
            'starts_at' => '2026-09-22 09:00:00',
            'ends_at' => '2026-09-22 09:30:00',
        ]);
        $rdv = QuoteAppointment::factory()->create([
            'quote_id' => $quote->id,
            'is_appointment' => true,
            'starts_at' => '2026-09-23 09:00:00',
            'ends_at' => '2026-09-23 09:30:00',
        ]);

        $this->actingAs($this->user)->postJson('/api/agenda/reschedule', ['day' => '2026-09-22'])->assertOk();

        $this->assertDatabaseHas('quote_appointments', [
            'id' => $rdv->id,
            'starts_at' => '2026-09-23 09:00:00',
        ]);
        $this->assertDatabaseHas('quote_appointments', [
            'id' => $work->id,
            'starts_at' => '2026-09-23 09:30:00',
        ]);
    }

    #[Test]
    public function it_rejects_a_missing_day(): void
    {
        $this->actingAs($this->user)->postJson('/api/agenda/reschedule', [])->assertStatus(422);
    }

    #[Test]
    public function it_rejects_an_invalid_day(): void
    {
        $this->actingAs($this->user)->postJson('/api/agenda/reschedule', ['day' => 'not-a-date'])->assertStatus(422);
    }

    #[Test]
    public function it_does_not_write_anything_when_there_is_nothing_displaceable(): void
    {
        $quote = Quote::factory()->create();
        QuoteAppointment::factory()->create([
            'quote_id' => $quote->id,
            'is_appointment' => true,
            'starts_at' => '2026-09-22 09:00:00',
            'ends_at' => '2026-09-22 09:30:00',
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/agenda/reschedule', ['day' => '2026-09-22']);

        $response->assertOk();
        $this->assertSame(0, $response->json('moved_count'));
        $this->assertDatabaseHas('quote_appointments', ['starts_at' => '2026-09-22 09:00:00']);
    }

    #[Test]
    public function it_cascades_correctly_across_multiple_days_end_to_end(): void
    {
        $quote = Quote::factory()->create();

        // D+1 (2026-09-23) est complet toute la journée.
        QuoteAppointment::factory()->create([
            'quote_id' => $quote->id,
            'is_appointment' => true,
            'starts_at' => '2026-09-23 09:00:00',
            'ends_at' => '2026-09-23 18:00:00',
        ]);

        $work = QuoteAppointment::factory()->create([
            'quote_id' => $quote->id,
            'starts_at' => '2026-09-22 09:00:00',
            'ends_at' => '2026-09-22 09:30:00',
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/agenda/reschedule', ['day' => '2026-09-22']);

        $response->assertOk();
        $this->assertSame(1, $response->json('moved_count'));
        $this->assertSame(1, $response->json('days_touched'));

        $this->assertDatabaseHas('quote_appointments', [
            'id' => $work->id,
            'starts_at' => '2026-09-24 09:00:00',
        ]);
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $this->postJson('/api/agenda/reschedule', ['day' => '2026-09-22'])->assertStatus(401);
    }
}
