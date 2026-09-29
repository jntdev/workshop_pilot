<?php

namespace Tests\Feature\Api;

use App\Models\Quote;
use App\Models\QuoteAppointment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuoteAppointmentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_creates_an_appointment_for_a_quote(): void
    {
        $quote = Quote::factory()->create(['status' => 'reception']);

        $response = $this->actingAs($this->user)->postJson("/api/quotes/{$quote->id}/appointments", [
            'starts_at' => '2026-10-01 09:00:00',
            'ends_at' => '2026-10-01 09:30:00',
        ]);

        $response->assertCreated();
        $this->assertSame($quote->reference, $response->json('quote_reference'));
        $this->assertSame('reception', $response->json('status'));
        $this->assertDatabaseHas('quote_appointments', ['quote_id' => $quote->id]);
    }

    #[Test]
    public function it_allows_scheduling_regardless_of_status(): void
    {
        foreach (['reception', 'to_complete', 'to_quote', 'pending_validation', 'validated', 'quote_to_order', 'quote_ordered', 'quote_received', 'in_progress', 'done'] as $status) {
            $quote = Quote::factory()->create(['status' => $status]);

            $this->actingAs($this->user)->postJson("/api/quotes/{$quote->id}/appointments", [
                'starts_at' => '2026-10-01 09:00:00',
                'ends_at' => '2026-10-01 09:30:00',
            ])->assertCreated();
        }
    }

    #[Test]
    public function it_rejects_an_end_before_start(): void
    {
        $quote = Quote::factory()->create();

        $this->actingAs($this->user)->postJson("/api/quotes/{$quote->id}/appointments", [
            'starts_at' => '2026-10-01 09:00:00',
            'ends_at' => '2026-10-01 08:00:00',
        ])->assertStatus(422);
    }

    #[Test]
    public function it_lists_appointments_within_a_date_range(): void
    {
        $quote = Quote::factory()->create();
        QuoteAppointment::factory()->create([
            'quote_id' => $quote->id,
            'starts_at' => '2026-10-01 09:00:00',
            'ends_at' => '2026-10-01 09:30:00',
        ]);
        QuoteAppointment::factory()->create([
            'quote_id' => $quote->id,
            'starts_at' => '2026-10-02 09:00:00',
            'ends_at' => '2026-10-02 09:30:00',
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/quote-appointments?start=2026-10-01T00:00:00&end=2026-10-01T23:59:59');

        $response->assertOk();
        $this->assertCount(1, $response->json());
    }

    #[Test]
    public function it_updates_an_appointment_time(): void
    {
        $quote = Quote::factory()->create();
        $appointment = QuoteAppointment::factory()->create([
            'quote_id' => $quote->id,
            'starts_at' => '2026-10-01 09:00:00',
            'ends_at' => '2026-10-01 09:30:00',
        ]);

        $response = $this->actingAs($this->user)->putJson("/api/quote-appointments/{$appointment->id}", [
            'starts_at' => '2026-10-02 14:00:00',
            'ends_at' => '2026-10-02 15:00:00',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('quote_appointments', [
            'id' => $appointment->id,
            'starts_at' => '2026-10-02 14:00:00',
        ]);
    }

    #[Test]
    public function it_deletes_an_appointment(): void
    {
        $quote = Quote::factory()->create();
        $appointment = QuoteAppointment::factory()->create(['quote_id' => $quote->id]);

        $this->actingAs($this->user)->deleteJson("/api/quote-appointments/{$appointment->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('quote_appointments', ['id' => $appointment->id]);
    }

    #[Test]
    public function it_deletes_appointments_when_the_quote_is_permanently_deleted(): void
    {
        $quote = Quote::factory()->create();
        $appointment = QuoteAppointment::factory()->create(['quote_id' => $quote->id]);

        $quote->forceDelete();

        $this->assertDatabaseMissing('quote_appointments', ['id' => $appointment->id]);
    }

    #[Test]
    public function it_lists_unscheduled_quotes_of_any_active_status(): void
    {
        $scheduled = Quote::factory()->create(['status' => 'validated']);
        QuoteAppointment::factory()->create(['quote_id' => $scheduled->id]);

        $toComplete = Quote::factory()->create(['status' => 'to_complete']);
        $done = Quote::factory()->create(['status' => 'done']);
        $invoiced = Quote::factory()->invoiced()->create();

        $response = $this->actingAs($this->user)->getJson('/api/quote-appointments/unscheduled');

        $response->assertOk();
        $ids = collect($response->json())->pluck('id');
        $this->assertTrue($ids->contains($toComplete->id));
        $this->assertTrue($ids->contains($done->id));
        $this->assertFalse($ids->contains($scheduled->id));
        $this->assertFalse($ids->contains($invoiced->id));
    }

    #[Test]
    public function it_uses_estimated_time_as_default_duration_when_available(): void
    {
        $quote = Quote::factory()->create(['status' => 'validated', 'total_estimated_time_minutes' => 90]);

        $response = $this->actingAs($this->user)->getJson('/api/quote-appointments/unscheduled');

        $response->assertOk();
        $byId = collect($response->json())->keyBy('id');
        $this->assertSame(90, $byId->get($quote->id)['default_duration_minutes']);
    }

    #[Test]
    public function it_falls_back_to_a_fixed_default_duration_without_estimated_time(): void
    {
        $quote = Quote::factory()->create(['status' => 'reception', 'total_estimated_time_minutes' => null]);

        $response = $this->actingAs($this->user)->getJson('/api/quote-appointments/unscheduled');

        $response->assertOk();
        $byId = collect($response->json())->keyBy('id');
        $this->assertSame(30, $byId->get($quote->id)['default_duration_minutes']);
    }

    #[Test]
    public function it_excludes_archived_quotes_from_unscheduled(): void
    {
        $archived = Quote::factory()->create(['status' => 'reception', 'is_archived' => true]);

        $response = $this->actingAs($this->user)->getJson('/api/quote-appointments/unscheduled');

        $response->assertOk();
        $ids = collect($response->json())->pluck('id');
        $this->assertFalse($ids->contains($archived->id));
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $quote = Quote::factory()->create();

        $this->postJson("/api/quotes/{$quote->id}/appointments", [
            'starts_at' => '2026-10-01 09:00:00',
            'ends_at' => '2026-10-01 09:30:00',
        ])->assertStatus(401);
    }

    #[Test]
    public function it_marks_an_appointment_as_completed(): void
    {
        $quote = Quote::factory()->create();
        $appointment = QuoteAppointment::factory()->create(['quote_id' => $quote->id]);

        $response = $this->actingAs($this->user)->patchJson("/api/quote-appointments/{$appointment->id}/flags", [
            'completed' => true,
        ]);

        $response->assertOk();
        $this->assertTrue($response->json('completed'));
        $this->assertDatabaseHas('quote_appointments', ['id' => $appointment->id, 'completed' => true]);
    }

    #[Test]
    public function it_unmarks_an_appointment_as_completed(): void
    {
        $quote = Quote::factory()->create();
        $appointment = QuoteAppointment::factory()->completed()->create(['quote_id' => $quote->id]);

        $response = $this->actingAs($this->user)->patchJson("/api/quote-appointments/{$appointment->id}/flags", [
            'completed' => false,
        ]);

        $response->assertOk();
        $this->assertFalse($response->json('completed'));
    }

    #[Test]
    public function it_defaults_new_appointments_to_not_completed(): void
    {
        $quote = Quote::factory()->create(['status' => 'reception']);

        $response = $this->actingAs($this->user)->postJson("/api/quotes/{$quote->id}/appointments", [
            'starts_at' => '2026-10-01 09:00:00',
            'ends_at' => '2026-10-01 09:30:00',
        ]);

        $response->assertCreated();
        $this->assertFalse($response->json('completed'));
        $this->assertFalse($response->json('is_appointment'));
        $this->assertFalse($response->json('is_commitment'));
    }

    #[Test]
    public function it_marks_an_appointment_as_a_physical_appointment(): void
    {
        $quote = Quote::factory()->create();
        $appointment = QuoteAppointment::factory()->create(['quote_id' => $quote->id]);

        $response = $this->actingAs($this->user)->patchJson("/api/quote-appointments/{$appointment->id}/flags", [
            'is_appointment' => true,
        ]);

        $response->assertOk();
        $this->assertTrue($response->json('is_appointment'));
        $this->assertFalse($response->json('is_commitment'));
        $this->assertFalse($response->json('completed'));
    }

    #[Test]
    public function it_marks_an_appointment_as_a_commitment(): void
    {
        $quote = Quote::factory()->create();
        $appointment = QuoteAppointment::factory()->create(['quote_id' => $quote->id]);

        $response = $this->actingAs($this->user)->patchJson("/api/quote-appointments/{$appointment->id}/flags", [
            'is_commitment' => true,
        ]);

        $response->assertOk();
        $this->assertTrue($response->json('is_commitment'));
        $this->assertDatabaseHas('quote_appointments', ['id' => $appointment->id, 'is_commitment' => true]);
    }

    #[Test]
    public function it_updates_flags_independently_without_affecting_others(): void
    {
        $quote = Quote::factory()->create();
        $appointment = QuoteAppointment::factory()->create(['quote_id' => $quote->id, 'is_appointment' => true]);

        $response = $this->actingAs($this->user)->patchJson("/api/quote-appointments/{$appointment->id}/flags", [
            'completed' => true,
        ]);

        $response->assertOk();
        $this->assertTrue($response->json('completed'));
        $this->assertTrue($response->json('is_appointment'));
    }

    #[Test]
    public function it_disables_commitment_when_marking_as_appointment(): void
    {
        $quote = Quote::factory()->create();
        $appointment = QuoteAppointment::factory()->create(['quote_id' => $quote->id, 'is_commitment' => true]);

        $response = $this->actingAs($this->user)->patchJson("/api/quote-appointments/{$appointment->id}/flags", [
            'is_appointment' => true,
        ]);

        $response->assertOk();
        $this->assertTrue($response->json('is_appointment'));
        $this->assertFalse($response->json('is_commitment'));
    }

    #[Test]
    public function it_disables_appointment_when_marking_as_commitment(): void
    {
        $quote = Quote::factory()->create();
        $appointment = QuoteAppointment::factory()->create(['quote_id' => $quote->id, 'is_appointment' => true]);

        $response = $this->actingAs($this->user)->patchJson("/api/quote-appointments/{$appointment->id}/flags", [
            'is_commitment' => true,
        ]);

        $response->assertOk();
        $this->assertTrue($response->json('is_commitment'));
        $this->assertFalse($response->json('is_appointment'));
    }

    #[Test]
    public function it_ignores_client_attempt_to_set_both_appointment_and_commitment(): void
    {
        $quote = Quote::factory()->create();
        $appointment = QuoteAppointment::factory()->create(['quote_id' => $quote->id]);

        $response = $this->actingAs($this->user)->patchJson("/api/quote-appointments/{$appointment->id}/flags", [
            'is_appointment' => true,
            'is_commitment' => true,
        ]);

        $response->assertOk();
        $this->assertTrue($response->json('is_appointment'));
        $this->assertFalse($response->json('is_commitment'));
    }
}
