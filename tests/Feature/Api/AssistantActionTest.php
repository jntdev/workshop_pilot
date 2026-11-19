<?php

namespace Tests\Feature\Api;

use App\Enums\AssistantActionStatus;
use App\Enums\AssistantMessageStatus;
use App\Enums\QuoteStatus;
use App\Models\AgendaEvent;
use App\Models\AssistantAction;
use App\Models\AssistantMessage;
use App\Models\Client;
use App\Models\Quote;
use App\Models\QuoteAppointment;
use App\Models\QuoteLine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AssistantActionTest extends TestCase
{
    use RefreshDatabase;

    private const READ_TOKEN = 'secret-read-token';

    private const PROPOSE_TOKEN = 'secret-propose-token';

    private const RELAY_TOKEN = 'secret-relay-token';

    private User $user;

    private AssistantMessage $message;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'assistant.read_token' => self::READ_TOKEN,
            'assistant.propose_token' => self::PROPOSE_TOKEN,
            'assistant.relay_token' => self::RELAY_TOKEN,
        ]);

        $this->user = User::factory()->create();
        $this->message = $this->processingMessage($this->user);
    }

    private function processingMessage(User $user): AssistantMessage
    {
        return AssistantMessage::factory()->create([
            'user_id' => $user->id,
            'status' => AssistantMessageStatus::Processing,
            'claimed_at' => now(),
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function propose(): array
    {
        return ['Authorization' => 'Bearer '.self::PROPOSE_TOKEN];
    }

    /**
     * @param  array<string, mixed>  $params
     */
    private function proposeAction(string $type, array $params, ?AssistantMessage $message = null)
    {
        return $this->postJson('/api/assistant-propose/actions', [
            'assistant_message_id' => ($message ?? $this->message)->id,
            'type' => $type,
            'params' => $params,
        ], $this->propose());
    }

    private function quote(QuoteStatus $status = QuoteStatus::PendingValidation, array $attributes = []): Quote
    {
        return Quote::factory()->for(Client::factory()->create(['prenom' => 'Yann', 'nom' => 'Le Gall']))->create([
            'status' => $status,
            'reference' => 'DEV-2001',
            'bike_description' => 'VTT rouge',
            ...$attributes,
        ]);
    }

    private function nextWeekday(int $dayOfWeek, string $time): Carbon
    {
        return Carbon::parse('next week', 'Europe/Paris')->startOfWeek(Carbon::MONDAY)
            ->addWeeks(1)
            ->addDays(($dayOfWeek + 6) % 7)
            ->setTimeFromTimeString($time);
    }

    // ---------------------------------------------------------------- jetons

    #[Test]
    public function the_propose_route_only_accepts_the_propose_token(): void
    {
        $payload = ['assistant_message_id' => $this->message->id, 'type' => 'quote_status', 'params' => []];

        $this->postJson('/api/assistant-propose/actions', $payload)->assertUnauthorized();
        $this->postJson('/api/assistant-propose/actions', $payload, ['Authorization' => 'Bearer '.self::READ_TOKEN])->assertUnauthorized();
        $this->postJson('/api/assistant-propose/actions', $payload, ['Authorization' => 'Bearer '.self::RELAY_TOKEN])->assertUnauthorized();
    }

    #[Test]
    public function the_propose_token_opens_nothing_else(): void
    {
        $this->getJson('/api/assistant-read/quotes', $this->propose())->assertUnauthorized();
        $this->getJson('/api/assistant-read/order-lines', $this->propose())->assertUnauthorized();
        $this->postJson('/api/assistant-relay/claim', [], $this->propose())->assertUnauthorized();
        $this->getJson('/api/ai/quotes', $this->propose())->assertUnauthorized();
    }

    #[Test]
    public function without_a_configured_propose_token_every_request_is_refused(): void
    {
        config(['assistant.propose_token' => null]);

        $this->postJson('/api/assistant-propose/actions', [], ['Authorization' => 'Bearer '])->assertUnauthorized();
        $this->postJson('/api/assistant-propose/actions', [])->assertUnauthorized();
    }

    // ---------------------------------------------------------------- statut de devis

    #[Test]
    public function proposing_a_quote_status_changes_nothing(): void
    {
        $quote = $this->quote();

        $this->proposeAction('quote_status', ['quote_id' => $quote->id, 'status' => 'quote_to_order'])
            ->assertCreated()
            ->assertJsonPath('summary', 'Devis DEV-2001 (Yann Le Gall, VTT rouge) : « Attente validation » → « À commander »');

        $this->assertSame(QuoteStatus::PendingValidation, $quote->fresh()->status);

        $action = AssistantAction::first();
        $this->assertSame(AssistantActionStatus::Proposed, $action->status);
        $this->assertSame($this->user->id, $action->user_id);
        $this->assertSame($this->message->conversation_id, $action->conversation_id);
    }

    #[Test]
    public function it_refuses_invalid_quote_status_proposals(): void
    {
        $quote = $this->quote();

        $this->proposeAction('quote_status', ['quote_id' => $quote->id, 'status' => 'nimporte_quoi'])
            ->assertUnprocessable()->assertJsonPath('message', fn (string $m) => str_contains($m, 'Statut inconnu'));

        $this->proposeAction('quote_status', ['quote_id' => $quote->id, 'status' => 'invoiced'])->assertUnprocessable();
        $this->proposeAction('quote_status', ['quote_id' => $quote->id, 'status' => 'pending_validation'])
            ->assertUnprocessable()->assertJsonPath('message', 'Ce devis est déjà « Attente validation ».');
        $this->proposeAction('quote_status', ['quote_id' => 999999, 'status' => 'validated'])
            ->assertUnprocessable()->assertJsonPath('message', 'Devis introuvable.');

        $archived = $this->quote(attributes: ['is_archived' => true, 'reference' => 'DEV-2002']);
        $this->proposeAction('quote_status', ['quote_id' => $archived->id, 'status' => 'validated'])->assertUnprocessable();

        $invoice = $this->quote(attributes: ['invoiced_at' => now(), 'reference' => 'FAC-1']);
        $this->proposeAction('quote_status', ['quote_id' => $invoice->id, 'status' => 'validated'])->assertUnprocessable();

        $this->assertSame(0, AssistantAction::count());
    }

    #[Test]
    public function proposals_are_only_accepted_while_the_question_is_being_processed(): void
    {
        $quote = $this->quote();

        foreach ([AssistantMessageStatus::Pending, AssistantMessageStatus::Answered, AssistantMessageStatus::Failed] as $status) {
            $message = AssistantMessage::factory()->create(['user_id' => $this->user->id, 'status' => $status]);

            $this->proposeAction('quote_status', ['quote_id' => $quote->id, 'status' => 'validated'], $message)
                ->assertUnprocessable();
        }

        $this->postJson('/api/assistant-propose/actions', [
            'assistant_message_id' => 999999, 'type' => 'quote_status', 'params' => ['quote_id' => $quote->id, 'status' => 'validated'],
        ], $this->propose())->assertNotFound();
    }

    #[Test]
    public function a_question_cannot_flood_the_chat_with_proposals(): void
    {
        config(['assistant.max_actions_per_message' => 2]);

        foreach ([QuoteStatus::Validated, QuoteStatus::InProgress, QuoteStatus::Done] as $index => $status) {
            $quote = $this->quote(attributes: ['reference' => 'DEV-30'.$index]);
            $response = $this->proposeAction('quote_status', ['quote_id' => $quote->id, 'status' => $status->value]);

            $index < 2 ? $response->assertCreated() : $response->assertUnprocessable();
        }
    }

    #[Test]
    public function a_new_proposal_on_the_same_quote_replaces_the_previous_one(): void
    {
        $quote = $this->quote();

        $this->proposeAction('quote_status', ['quote_id' => $quote->id, 'status' => 'validated'])->assertCreated();
        $this->proposeAction('quote_status', ['quote_id' => $quote->id, 'status' => 'quote_to_order'])->assertCreated();

        $this->assertSame(1, AssistantAction::where('status', AssistantActionStatus::Proposed->value)->count());
        $this->assertSame(1, AssistantAction::where('status', AssistantActionStatus::Cancelled->value)->count());
    }

    #[Test]
    public function the_requester_can_confirm_and_the_quote_changes_like_with_the_workshop_button(): void
    {
        $quote = $this->quote(QuoteStatus::Done, ['work_completed_notified' => true, 'work_completed_notified_at' => '2026-11-10']);
        $this->proposeAction('quote_status', ['quote_id' => $quote->id, 'status' => 'in_progress'])
            ->assertCreated()->assertJsonCount(1, 'warnings');
        $action = AssistantAction::first();

        $this->actingAs($this->user)->postJson("/api/assistant/actions/{$action->id}/confirm")
            ->assertOk()
            ->assertJsonPath('status', 'executed')
            ->assertJsonPath('can_undo', true)
            ->assertJsonPath('link', "/atelier/devis/{$quote->id}");

        $quote->refresh();
        $this->assertSame(QuoteStatus::InProgress, $quote->status);
        $this->assertFalse($quote->work_completed_notified);
        $this->assertNull($quote->work_completed_notified_at);

        $action->refresh();
        $this->assertSame($this->user->id, $action->confirmed_by);
        $this->assertNotNull($action->confirmed_at);
        $this->assertSame('in_progress', $action->after['status']);
        $this->assertSame('done', $action->before['status']);
    }

    #[Test]
    public function only_the_person_who_asked_can_act_on_a_proposal(): void
    {
        $quote = $this->quote();
        $this->proposeAction('quote_status', ['quote_id' => $quote->id, 'status' => 'validated'])->assertCreated();
        $action = AssistantAction::first();
        $other = User::factory()->create();

        $this->postJson("/api/assistant/actions/{$action->id}/confirm")->assertUnauthorized();
        $this->actingAs($other)->postJson("/api/assistant/actions/{$action->id}/confirm")->assertNotFound();
        $this->actingAs($other)->postJson("/api/assistant/actions/{$action->id}/cancel")->assertNotFound();
        $this->actingAs($other)->postJson("/api/assistant/actions/{$action->id}/undo")->assertNotFound();

        $this->assertSame(QuoteStatus::PendingValidation, $quote->fresh()->status);
    }

    #[Test]
    public function confirming_twice_never_executes_twice(): void
    {
        $quote = $this->quote();
        $this->proposeAction('quote_status', ['quote_id' => $quote->id, 'status' => 'validated'])->assertCreated();
        $action = AssistantAction::first();

        $this->actingAs($this->user)->postJson("/api/assistant/actions/{$action->id}/confirm")->assertOk();
        $this->actingAs($this->user)->postJson("/api/assistant/actions/{$action->id}/confirm")
            ->assertUnprocessable()->assertJsonPath('message', 'Cette action a déjà été faite.');
    }

    #[Test]
    public function an_expired_proposal_cannot_be_confirmed(): void
    {
        $quote = $this->quote();
        $this->proposeAction('quote_status', ['quote_id' => $quote->id, 'status' => 'validated'])->assertCreated();
        $action = AssistantAction::first();

        $this->travel(31)->minutes();

        $this->actingAs($this->user)->postJson("/api/assistant/actions/{$action->id}/confirm")
            ->assertUnprocessable()->assertJsonPath('action.status', 'expired');

        $this->assertSame(QuoteStatus::PendingValidation, $quote->fresh()->status);
    }

    #[Test]
    public function a_quote_that_changed_meanwhile_is_not_overwritten(): void
    {
        $quote = $this->quote();
        $this->proposeAction('quote_status', ['quote_id' => $quote->id, 'status' => 'validated'])->assertCreated();
        $action = AssistantAction::first();

        $quote->update(['status' => QuoteStatus::ToComplete]);

        $this->actingAs($this->user)->postJson("/api/assistant/actions/{$action->id}/confirm")
            ->assertUnprocessable()
            ->assertJsonPath('action.status', 'failed')
            ->assertJsonPath('message', fn (string $m) => str_contains($m, 'depuis la proposition'));

        $this->assertSame(QuoteStatus::ToComplete, $quote->fresh()->status);
    }

    #[Test]
    public function a_cancelled_proposal_does_nothing(): void
    {
        $quote = $this->quote();
        $this->proposeAction('quote_status', ['quote_id' => $quote->id, 'status' => 'validated'])->assertCreated();
        $action = AssistantAction::first();

        $this->actingAs($this->user)->postJson("/api/assistant/actions/{$action->id}/cancel")
            ->assertOk()->assertJsonPath('status', 'cancelled');
        $this->actingAs($this->user)->postJson("/api/assistant/actions/{$action->id}/confirm")->assertUnprocessable();

        $this->assertSame(QuoteStatus::PendingValidation, $quote->fresh()->status);
    }

    #[Test]
    public function an_executed_status_change_can_be_undone_while_nothing_else_changed(): void
    {
        $quote = $this->quote(QuoteStatus::Done, ['work_completed_notified' => true, 'work_completed_notified_at' => '2026-11-10']);
        $this->proposeAction('quote_status', ['quote_id' => $quote->id, 'status' => 'in_progress'])->assertCreated();
        $action = AssistantAction::first();
        $this->actingAs($this->user)->postJson("/api/assistant/actions/{$action->id}/confirm")->assertOk();

        $this->actingAs($this->user)->postJson("/api/assistant/actions/{$action->id}/undo")
            ->assertOk()->assertJsonPath('status', 'undone');

        $quote->refresh();
        $this->assertSame(QuoteStatus::Done, $quote->status);
        $this->assertTrue($quote->work_completed_notified);
        $this->assertSame('2026-11-10', $quote->work_completed_notified_at->format('Y-m-d'));
    }

    #[Test]
    public function an_action_cannot_be_undone_after_the_quote_changed_again(): void
    {
        $quote = $this->quote();
        $this->proposeAction('quote_status', ['quote_id' => $quote->id, 'status' => 'validated'])->assertCreated();
        $action = AssistantAction::first();
        $this->actingAs($this->user)->postJson("/api/assistant/actions/{$action->id}/confirm")->assertOk();

        $quote->update(['status' => QuoteStatus::InProgress]);

        $this->actingAs($this->user)->postJson("/api/assistant/actions/{$action->id}/undo")->assertUnprocessable();
        $this->assertSame(QuoteStatus::InProgress, $quote->fresh()->status);
    }

    // ---------------------------------------------------------------- pièces à commander

    private function orderLine(array $attributes = []): QuoteLine
    {
        return QuoteLine::factory()->for($this->quote(QuoteStatus::QuoteToOrder))->create([
            'title' => 'Chaîne KMC',
            'needs_order' => true,
            'ordered_at' => null,
            'received_at' => null,
            ...$attributes,
        ]);
    }

    #[Test]
    public function a_part_can_be_marked_ordered_then_the_change_can_be_undone(): void
    {
        $line = $this->orderLine();

        $this->proposeAction('order_line', ['line_id' => $line->id, 'action' => 'ordered'])
            ->assertCreated()
            ->assertJsonPath('summary', 'Pièce « Chaîne KMC » (devis DEV-2001, Yann Le Gall) : à commander → commandée');

        $action = AssistantAction::first();
        $this->assertNull($line->fresh()->ordered_at);

        $this->actingAs($this->user)->postJson("/api/assistant/actions/{$action->id}/confirm")->assertOk();
        $this->assertNotNull($line->fresh()->ordered_at);
        $this->assertNull($line->fresh()->received_at);

        $this->actingAs($this->user)->postJson("/api/assistant/actions/{$action->id}/undo")->assertOk();
        $this->assertNull($line->fresh()->ordered_at);
    }

    #[Test]
    public function marking_a_part_received_is_final_like_in_the_workshop(): void
    {
        $line = $this->orderLine(['ordered_at' => now()->subDay()]);

        $this->proposeAction('order_line', ['line_id' => $line->id, 'action' => 'received'])
            ->assertCreated()->assertJsonCount(1, 'warnings');
        $action = AssistantAction::first();

        $this->actingAs($this->user)->postJson("/api/assistant/actions/{$action->id}/confirm")
            ->assertOk()->assertJsonPath('can_undo', false);
        $this->assertNotNull($line->fresh()->received_at);

        $this->actingAs($this->user)->postJson("/api/assistant/actions/{$action->id}/undo")->assertUnprocessable();
        $this->assertNotNull($line->fresh()->received_at);

        $this->proposeAction('order_line', ['line_id' => $line->id, 'action' => 'to_order'])
            ->assertUnprocessable()->assertJsonPath('message', 'Une pièce déjà reçue ne peut plus être modifiée.');
    }

    #[Test]
    public function an_ordered_part_can_go_back_to_to_order_and_undo_restores_the_order_date(): void
    {
        $orderedAt = Carbon::parse('2026-11-12 10:00:00', 'UTC');
        $line = $this->orderLine(['ordered_at' => $orderedAt]);

        $this->proposeAction('order_line', ['line_id' => $line->id, 'action' => 'to_order'])->assertCreated();
        $action = AssistantAction::first();
        $this->actingAs($this->user)->postJson("/api/assistant/actions/{$action->id}/confirm")->assertOk();
        $this->assertNull($line->fresh()->ordered_at);

        $this->actingAs($this->user)->postJson("/api/assistant/actions/{$action->id}/undo")->assertOk();
        $this->assertTrue($line->fresh()->ordered_at->equalTo($orderedAt));
    }

    #[Test]
    public function it_refuses_invalid_part_proposals(): void
    {
        $line = $this->orderLine();
        $notToOrder = QuoteLine::factory()->for($line->quote)->create(['needs_order' => false]);

        $this->proposeAction('order_line', ['line_id' => $line->id, 'action' => 'to_order'])
            ->assertUnprocessable()->assertJsonPath('message', 'Cette pièce est déjà « à commander ».');
        $this->proposeAction('order_line', ['line_id' => $line->id, 'action' => 'perdue'])->assertUnprocessable();
        $this->proposeAction('order_line', ['line_id' => 999999, 'action' => 'ordered'])->assertUnprocessable();
        $this->proposeAction('order_line', ['line_id' => $notToOrder->id, 'action' => 'ordered'])
            ->assertUnprocessable()->assertJsonPath('message', 'Cette ligne n\'est pas une pièce à commander.');
    }

    // ---------------------------------------------------------------- agenda

    #[Test]
    public function an_appointment_keeps_its_duration_and_times_are_converted_from_paris_to_utc(): void
    {
        $quote = $this->quote();
        $start = $this->nextWeekday(Carbon::TUESDAY, '14:00')->utc();
        $appointment = QuoteAppointment::factory()->for($quote)->create([
            'starts_at' => $start, 'ends_at' => $start->copy()->addHour(), 'completed' => false,
        ]);
        $newStart = $this->nextWeekday(Carbon::THURSDAY, '09:00');

        $this->proposeAction('appointment_move', [
            'kind' => 'devis', 'id' => $appointment->id, 'starts_at' => $newStart->format('Y-m-d H:i'),
        ])->assertCreated()->assertJsonCount(0, 'warnings')
            ->assertJsonPath('summary', fn (string $s) => str_contains($s, 'Rendez-vous du devis DEV-2001') && str_contains($s, 'jeudi') && str_contains($s, '09:00-10:00'));

        $action = AssistantAction::first();
        $this->assertSame($newStart->copy()->utc()->format('Y-m-d H:i:s'), $action->payload['starts_at']);
        $this->assertSame($newStart->copy()->addHour()->utc()->format('Y-m-d H:i:s'), $action->payload['ends_at']);

        $this->assertTrue($appointment->fresh()->starts_at->equalTo($start));

        $this->actingAs($this->user)->postJson("/api/assistant/actions/{$action->id}/confirm")
            ->assertOk()->assertJsonPath('link', "/atelier/devis/{$quote->id}");
        $this->assertTrue($appointment->fresh()->starts_at->equalTo($newStart));
        $this->assertTrue($appointment->fresh()->ends_at->equalTo($newStart->copy()->addHour()));
    }

    #[Test]
    public function a_moved_appointment_can_be_put_back(): void
    {
        $start = $this->nextWeekday(Carbon::TUESDAY, '14:00')->utc();
        $appointment = QuoteAppointment::factory()->for($this->quote())->create([
            'starts_at' => $start, 'ends_at' => $start->copy()->addHours(2),
        ]);

        $this->proposeAction('appointment_move', [
            'kind' => 'devis', 'id' => $appointment->id,
            'starts_at' => $this->nextWeekday(Carbon::FRIDAY, '10:00')->format('Y-m-d H:i'),
        ])->assertCreated();
        $action = AssistantAction::first();
        $this->actingAs($this->user)->postJson("/api/assistant/actions/{$action->id}/confirm")->assertOk();

        $this->actingAs($this->user)->postJson("/api/assistant/actions/{$action->id}/undo")->assertOk();

        $this->assertTrue($appointment->fresh()->starts_at->equalTo($start));
        $this->assertTrue($appointment->fresh()->ends_at->equalTo($start->copy()->addHours(2)));
    }

    #[Test]
    public function a_free_event_can_be_moved_with_an_explicit_end(): void
    {
        $start = $this->nextWeekday(Carbon::WEDNESDAY, '10:00')->utc();
        $event = AgendaEvent::factory()->create(['title' => 'Livraison Shimano', 'starts_at' => $start, 'ends_at' => $start->copy()->addHour()]);
        $newStart = $this->nextWeekday(Carbon::THURSDAY, '15:00');

        $this->proposeAction('appointment_move', [
            'kind' => 'evenement', 'id' => $event->id,
            'starts_at' => $newStart->format('Y-m-d H:i'), 'ends_at' => $newStart->copy()->addMinutes(30)->format('Y-m-d H:i'),
        ])->assertCreated()
            ->assertJsonPath('summary', fn (string $s) => str_starts_with($s, 'Événement « Livraison Shimano » :'));

        $action = AssistantAction::first();
        $this->actingAs($this->user)->postJson("/api/assistant/actions/{$action->id}/confirm")
            ->assertOk()->assertJsonPath('link', '/atelier/agenda');

        $this->assertTrue($event->fresh()->starts_at->equalTo($newStart));
        $this->assertTrue($event->fresh()->ends_at->equalTo($newStart->copy()->addMinutes(30)));
    }

    #[Test]
    public function moves_to_closed_days_or_hours_or_over_other_items_are_allowed_but_warned(): void
    {
        $start = $this->nextWeekday(Carbon::TUESDAY, '14:00')->utc();
        $appointment = QuoteAppointment::factory()->for($this->quote())->create(['starts_at' => $start, 'ends_at' => $start->copy()->addHour()]);

        $monday = $this->nextWeekday(Carbon::MONDAY, '10:00');
        $this->proposeAction('appointment_move', ['kind' => 'devis', 'id' => $appointment->id, 'starts_at' => $monday->format('Y-m-d H:i')])
            ->assertCreated()->assertJsonPath('warnings.0', 'L\'atelier est fermé ce jour-là (dimanche et lundi).');

        $lunch = $this->nextWeekday(Carbon::THURSDAY, '12:30');
        $this->proposeAction('appointment_move', ['kind' => 'devis', 'id' => $appointment->id, 'starts_at' => $lunch->format('Y-m-d H:i')])
            ->assertCreated()->assertJsonPath('warnings.0', fn (string $w) => str_contains($w, 'heures d\'ouverture'));

        $other = $this->nextWeekday(Carbon::FRIDAY, '10:00');
        AgendaEvent::factory()->create(['title' => 'Formation', 'starts_at' => $other->copy()->utc(), 'ends_at' => $other->copy()->addHours(2)->utc()]);
        $this->proposeAction('appointment_move', ['kind' => 'devis', 'id' => $appointment->id, 'starts_at' => $other->copy()->addMinutes(30)->format('Y-m-d H:i')])
            ->assertCreated()->assertJsonPath('warnings.0', fn (string $w) => str_contains($w, 'Chevauche') && str_contains($w, 'Formation'));
    }

    #[Test]
    public function it_refuses_invalid_moves(): void
    {
        $start = $this->nextWeekday(Carbon::TUESDAY, '14:00')->utc();
        $appointment = QuoteAppointment::factory()->for($this->quote())->create(['starts_at' => $start, 'ends_at' => $start->copy()->addHour()]);
        $base = ['kind' => 'devis', 'id' => $appointment->id];

        $this->proposeAction('appointment_move', [...$base, 'starts_at' => 'demain 10h'])
            ->assertUnprocessable()->assertJsonPath('message', fn (string $m) => str_contains($m, 'AAAA-MM-JJ HH:MM'));
        $this->proposeAction('appointment_move', [...$base, 'starts_at' => '2026-12-01 10:00', 'ends_at' => '2026-12-01 09:00'])
            ->assertUnprocessable()->assertJsonPath('message', 'La fin doit être après le début.');
        $this->proposeAction('appointment_move', [...$base, 'starts_at' => '2031-01-01 10:00'])
            ->assertUnprocessable()->assertJsonPath('message', fn (string $m) => str_contains($m, 'trop éloignée'));
        $this->proposeAction('appointment_move', [...$base, 'starts_at' => $start->copy()->timezone('Europe/Paris')->format('Y-m-d H:i')])
            ->assertUnprocessable()->assertJsonPath('message', 'Ce rendez-vous est déjà à ce moment-là.');
        $this->proposeAction('appointment_move', ['kind' => 'devis', 'id' => 999999, 'starts_at' => '2026-12-01 10:00'])
            ->assertUnprocessable()->assertJsonPath('message', 'Rendez-vous introuvable.');
        $this->proposeAction('appointment_move', ['kind' => 'autre', 'id' => 1, 'starts_at' => '2026-12-01 10:00'])->assertUnprocessable();
    }

    #[Test]
    public function an_appointment_moved_by_someone_else_meanwhile_is_not_overwritten(): void
    {
        $start = $this->nextWeekday(Carbon::TUESDAY, '14:00')->utc();
        $appointment = QuoteAppointment::factory()->for($this->quote())->create(['starts_at' => $start, 'ends_at' => $start->copy()->addHour()]);

        $this->proposeAction('appointment_move', [
            'kind' => 'devis', 'id' => $appointment->id,
            'starts_at' => $this->nextWeekday(Carbon::FRIDAY, '10:00')->format('Y-m-d H:i'),
        ])->assertCreated();
        $action = AssistantAction::first();

        $appointment->update(['starts_at' => $start->copy()->addDay(), 'ends_at' => $start->copy()->addDay()->addHour()]);

        $this->actingAs($this->user)->postJson("/api/assistant/actions/{$action->id}/confirm")
            ->assertUnprocessable()->assertJsonPath('action.status', 'failed');
        $this->assertTrue($appointment->fresh()->starts_at->equalTo($start->copy()->addDay()));
    }

    // ---------------------------------------------------------------- chat et journal

    #[Test]
    public function the_chat_returns_the_actions_of_each_message_to_its_owner_only(): void
    {
        $quote = $this->quote();
        $this->proposeAction('quote_status', ['quote_id' => $quote->id, 'status' => 'validated'])->assertCreated();

        $this->actingAs($this->user)
            ->getJson('/api/assistant/messages?conversation_id='.$this->message->conversation_id)
            ->assertOk()
            ->assertJsonPath('0.actions.0.status', 'proposed')
            ->assertJsonPath('0.actions.0.type', 'quote_status')
            ->assertJsonPath('0.actions.0.can_undo', false);

        $this->actingAs(User::factory()->create())
            ->getJson('/api/assistant/messages?conversation_id='.$this->message->conversation_id)
            ->assertOk()->assertJsonCount(0);
    }

    #[Test]
    public function the_journal_shows_who_confirmed_each_action(): void
    {
        $quote = $this->quote();
        $this->proposeAction('quote_status', ['quote_id' => $quote->id, 'status' => 'validated'])->assertCreated();
        $action = AssistantAction::first();
        $this->actingAs($this->user)->postJson("/api/assistant/actions/{$action->id}/confirm")->assertOk();

        $this->actingAs($this->user)->getJson('/api/assistant/journal')
            ->assertOk()
            ->assertJsonPath('data.0.actions.0.status', 'executed')
            ->assertJsonPath('data.0.actions.0.confirmed_by', $this->user->name);
    }

    // ---------------------------------------------------------------- lecture enrichie

    #[Test]
    public function the_read_api_exposes_ids_and_the_parts_to_order(): void
    {
        $line = $this->orderLine(['title' => 'Plaquettes', 'ordered_at' => now()->subDay()]);
        $start = $this->nextWeekday(Carbon::TUESDAY, '10:00')->utc();
        $appointment = QuoteAppointment::factory()->for($line->quote)->create(['starts_at' => $start, 'ends_at' => $start->copy()->addHour()]);
        $event = AgendaEvent::factory()->create(['starts_at' => $start->copy()->addHours(3), 'ends_at' => $start->copy()->addHours(4)]);
        $auth = ['Authorization' => 'Bearer '.self::READ_TOKEN];

        $this->getJson("/api/assistant-read/quotes/{$line->quote_id}", $auth)
            ->assertOk()
            ->assertJsonPath('lines.0.id', $line->id)
            ->assertJsonPath('lines.0.order_status', 'ordered')
            ->assertJsonPath('appointments.0.id', $appointment->id);

        $this->getJson('/api/assistant-read/order-lines', $auth)
            ->assertOk()->assertJsonCount(1)
            ->assertJsonPath('0.line_id', $line->id)
            ->assertJsonPath('0.quote_reference', 'DEV-2001')
            ->assertJsonPath('0.order_status', 'ordered');

        $items = $this->getJson('/api/assistant-read/agenda?start='.$start->format('Y-m-d').'&end='.$start->format('Y-m-d'), $auth)
            ->assertOk()->json();
        $this->assertEqualsCanonicalizing([$appointment->id, $event->id], array_column($items, 'id'));
    }
}
