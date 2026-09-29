<?php

namespace Tests\Feature\Api;

use App\Models\AgendaEvent;
use App\Models\Quote;
use App\Models\QuoteAppointment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AgendaEventTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_creates_an_agenda_event(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/agenda-events', [
            'title' => 'Pause déjeuner',
            'detail' => 'Fermeture atelier',
            'starts_at' => '2026-10-01 12:00:00',
            'ends_at' => '2026-10-01 12:30:00',
        ]);

        $response->assertCreated();
        $this->assertSame('event', $response->json('kind'));
        $this->assertSame('Pause déjeuner', $response->json('title'));
        $this->assertDatabaseHas('agenda_events', ['title' => 'Pause déjeuner']);
    }

    #[Test]
    public function it_creates_an_agenda_event_without_detail(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/agenda-events', [
            'title' => 'Livraison fournisseur',
            'starts_at' => '2026-10-01 09:00:00',
            'ends_at' => '2026-10-01 09:30:00',
        ]);

        $response->assertCreated();
        $this->assertNull($response->json('detail'));
    }

    #[Test]
    public function it_rejects_an_agenda_event_without_title(): void
    {
        $this->actingAs($this->user)->postJson('/api/agenda-events', [
            'starts_at' => '2026-10-01 09:00:00',
            'ends_at' => '2026-10-01 09:30:00',
        ])->assertStatus(422);
    }

    #[Test]
    public function it_rejects_an_end_before_start(): void
    {
        $this->actingAs($this->user)->postJson('/api/agenda-events', [
            'title' => 'Test',
            'starts_at' => '2026-10-01 09:00:00',
            'ends_at' => '2026-10-01 08:00:00',
        ])->assertStatus(422);
    }

    #[Test]
    public function it_updates_an_agenda_event_time_without_requiring_the_title(): void
    {
        $event = AgendaEvent::factory()->create([
            'starts_at' => '2026-10-01 09:00:00',
            'ends_at' => '2026-10-01 09:30:00',
        ]);

        $response = $this->actingAs($this->user)->putJson("/api/agenda-events/{$event->id}", [
            'starts_at' => '2026-10-02 14:00:00',
            'ends_at' => '2026-10-02 15:00:00',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('agenda_events', [
            'id' => $event->id,
            'starts_at' => '2026-10-02 14:00:00',
        ]);
    }

    #[Test]
    public function it_updates_an_agenda_event_title_and_detail(): void
    {
        $event = AgendaEvent::factory()->create(['title' => 'Ancien titre']);

        $response = $this->actingAs($this->user)->putJson("/api/agenda-events/{$event->id}", [
            'title' => 'Nouveau titre',
            'detail' => 'Nouveau détail',
            'starts_at' => $event->starts_at,
            'ends_at' => $event->ends_at,
        ]);

        $response->assertOk();
        $this->assertSame('Nouveau titre', $response->json('title'));
        $this->assertSame('Nouveau détail', $response->json('detail'));
    }

    #[Test]
    public function it_deletes_an_agenda_event(): void
    {
        $event = AgendaEvent::factory()->create();

        $this->actingAs($this->user)->deleteJson("/api/agenda-events/{$event->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('agenda_events', ['id' => $event->id]);
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $this->postJson('/api/agenda-events', [
            'title' => 'Test',
            'starts_at' => '2026-10-01 09:00:00',
            'ends_at' => '2026-10-01 09:30:00',
        ])->assertStatus(401);
    }

    #[Test]
    public function it_merges_quote_appointments_and_agenda_events_in_a_single_list(): void
    {
        $quote = Quote::factory()->create();
        QuoteAppointment::factory()->create([
            'quote_id' => $quote->id,
            'starts_at' => '2026-10-01 09:00:00',
            'ends_at' => '2026-10-01 09:30:00',
        ]);
        AgendaEvent::factory()->create([
            'title' => 'Pause',
            'starts_at' => '2026-10-01 10:00:00',
            'ends_at' => '2026-10-01 10:30:00',
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/agenda/items?start=2026-10-01T00:00:00&end=2026-10-01T23:59:59');

        $response->assertOk();
        $kinds = collect($response->json())->pluck('kind');
        $this->assertCount(2, $response->json());
        $this->assertTrue($kinds->contains('quote'));
        $this->assertTrue($kinds->contains('event'));
    }

    #[Test]
    public function it_excludes_items_outside_the_requested_range_from_the_merged_list(): void
    {
        AgendaEvent::factory()->create([
            'title' => 'Hors plage',
            'starts_at' => '2026-10-05 09:00:00',
            'ends_at' => '2026-10-05 09:30:00',
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/agenda/items?start=2026-10-01T00:00:00&end=2026-10-01T23:59:59');

        $response->assertOk();
        $this->assertCount(0, $response->json());
    }

    #[Test]
    public function it_marks_an_agenda_event_as_completed(): void
    {
        $event = AgendaEvent::factory()->create();

        $response = $this->actingAs($this->user)->patchJson("/api/agenda-events/{$event->id}/flags", [
            'completed' => true,
        ]);

        $response->assertOk();
        $this->assertTrue($response->json('completed'));
        $this->assertDatabaseHas('agenda_events', ['id' => $event->id, 'completed' => true]);
    }

    #[Test]
    public function it_unmarks_an_agenda_event_as_completed(): void
    {
        $event = AgendaEvent::factory()->completed()->create();

        $response = $this->actingAs($this->user)->patchJson("/api/agenda-events/{$event->id}/flags", [
            'completed' => false,
        ]);

        $response->assertOk();
        $this->assertFalse($response->json('completed'));
    }

    #[Test]
    public function it_defaults_new_agenda_events_to_not_completed(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/agenda-events', [
            'title' => 'Test',
            'starts_at' => '2026-10-01 09:00:00',
            'ends_at' => '2026-10-01 09:30:00',
        ]);

        $response->assertCreated();
        $this->assertFalse($response->json('completed'));
        $this->assertFalse($response->json('is_appointment'));
        $this->assertFalse($response->json('is_commitment'));
    }

    #[Test]
    public function it_marks_an_agenda_event_as_a_physical_appointment(): void
    {
        $event = AgendaEvent::factory()->create();

        $response = $this->actingAs($this->user)->patchJson("/api/agenda-events/{$event->id}/flags", [
            'is_appointment' => true,
        ]);

        $response->assertOk();
        $this->assertTrue($response->json('is_appointment'));
    }

    #[Test]
    public function it_marks_an_agenda_event_as_a_commitment(): void
    {
        $event = AgendaEvent::factory()->create();

        $response = $this->actingAs($this->user)->patchJson("/api/agenda-events/{$event->id}/flags", [
            'is_commitment' => true,
        ]);

        $response->assertOk();
        $this->assertTrue($response->json('is_commitment'));
        $this->assertDatabaseHas('agenda_events', ['id' => $event->id, 'is_commitment' => true]);
    }

    #[Test]
    public function it_includes_the_completed_flag_in_the_merged_agenda_list(): void
    {
        AgendaEvent::factory()->completed()->create([
            'starts_at' => '2026-10-01 09:00:00',
            'ends_at' => '2026-10-01 09:30:00',
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/agenda/items?start=2026-10-01T00:00:00&end=2026-10-01T23:59:59');

        $response->assertOk();
        $this->assertTrue($response->json('0.completed'));
    }

    #[Test]
    public function it_includes_the_appointment_and_commitment_flags_in_the_merged_agenda_list(): void
    {
        AgendaEvent::factory()->create([
            'is_appointment' => true,
            'is_commitment' => true,
            'starts_at' => '2026-10-01 09:00:00',
            'ends_at' => '2026-10-01 09:30:00',
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/agenda/items?start=2026-10-01T00:00:00&end=2026-10-01T23:59:59');

        $response->assertOk();
        $this->assertTrue($response->json('0.is_appointment'));
        $this->assertTrue($response->json('0.is_commitment'));
    }

    #[Test]
    public function it_disables_commitment_when_marking_an_agenda_event_as_appointment(): void
    {
        $event = AgendaEvent::factory()->create(['is_commitment' => true]);

        $response = $this->actingAs($this->user)->patchJson("/api/agenda-events/{$event->id}/flags", [
            'is_appointment' => true,
        ]);

        $response->assertOk();
        $this->assertTrue($response->json('is_appointment'));
        $this->assertFalse($response->json('is_commitment'));
    }

    #[Test]
    public function it_disables_appointment_when_marking_an_agenda_event_as_commitment(): void
    {
        $event = AgendaEvent::factory()->create(['is_appointment' => true]);

        $response = $this->actingAs($this->user)->patchJson("/api/agenda-events/{$event->id}/flags", [
            'is_commitment' => true,
        ]);

        $response->assertOk();
        $this->assertTrue($response->json('is_commitment'));
        $this->assertFalse($response->json('is_appointment'));
    }

    #[Test]
    public function it_ignores_client_attempt_to_set_both_flags_on_an_agenda_event(): void
    {
        $event = AgendaEvent::factory()->create();

        $response = $this->actingAs($this->user)->patchJson("/api/agenda-events/{$event->id}/flags", [
            'is_appointment' => true,
            'is_commitment' => true,
        ]);

        $response->assertOk();
        $this->assertTrue($response->json('is_appointment'));
        $this->assertFalse($response->json('is_commitment'));
    }
}
