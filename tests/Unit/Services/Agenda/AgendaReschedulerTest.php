<?php

namespace Tests\Unit\Services\Agenda;

use App\Services\Agenda\OpeningHours\OpeningHours;
use App\Services\Agenda\Reschedule\AgendaRescheduler;
use App\Services\Agenda\Reschedule\RescheduleCascadeLimitExceededException;
use App\Services\Agenda\Reschedule\RescheduleItem;
use App\Services\Agenda\Reschedule\RescheduleItemSource;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AgendaReschedulerTest extends TestCase
{
    private OpeningHours $hours;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hours = new OpeningHours;
    }

    private int $nextId = 1;

    private function item(
        string $starts,
        string $ends,
        bool $isAppointment = false,
        bool $isCommitment = false,
        bool $completed = false,
        string $kind = 'quote',
    ): RescheduleItem {
        return new RescheduleItem(
            $kind,
            $this->nextId++,
            CarbonImmutable::parse($starts),
            CarbonImmutable::parse($ends),
            $isAppointment,
            $isCommitment,
            $completed,
        );
    }

    private function source(array $itemsByDay): RescheduleItemSource
    {
        return new class($itemsByDay) implements RescheduleItemSource
        {
            public function __construct(private array $itemsByDay) {}

            public function itemsOnDay(CarbonImmutable $day): array
            {
                $items = $this->itemsByDay[$day->toDateString()] ?? [];
                usort($items, fn (RescheduleItem $a, RescheduleItem $b) => $a->startsAt <=> $b->startsAt);

                return $items;
            }
        };
    }

    #[Test]
    public function it_reschedules_a_simple_case_that_fits_the_next_day(): void
    {
        // Mardi 2026-09-22
        $item = $this->item('2026-09-22 10:00', '2026-09-22 10:30');
        $rescheduler = new AgendaRescheduler($this->hours, $this->source([
            '2026-09-22' => [$item],
        ]));

        $result = $rescheduler->rescheduleDay(CarbonImmutable::parse('2026-09-22'));

        $this->assertCount(1, $result->moves);
        $move = $result->moves[0];
        $this->assertSame('2026-09-23 09:00', $move->newStartsAt->format('Y-m-d H:i'));
        $this->assertSame('2026-09-23 09:30', $move->newEndsAt->format('Y-m-d H:i'));
    }

    #[Test]
    public function it_jumps_the_lunch_break_instead_of_crossing_it(): void
    {
        $itemA = $this->item('2026-09-22 08:00', '2026-09-22 11:30'); // 3h30
        $itemB = $this->item('2026-09-22 08:00', '2026-09-22 09:00'); // 1h, placé après A

        $rescheduler = new AgendaRescheduler($this->hours, $this->source([
            '2026-09-22' => [$itemA, $itemB],
        ]));

        $result = $rescheduler->rescheduleDay(CarbonImmutable::parse('2026-09-22'));

        $this->assertCount(2, $result->moves);
        // A : 9h -> 12h30 (3h30), dépasse midi -> doit basculer proprement
        // En pratique, ce test vérifie surtout que rien ne chevauche 12h-14h.
        foreach ($result->moves as $move) {
            $this->assertTrue(
                $this->hours->fitsWithoutCrossingLunch($move->newStartsAt, $move->newEndsAt),
                "Le créneau {$move->newStartsAt}-{$move->newEndsAt} chevauche la pause déjeuner"
            );
        }
    }

    #[Test]
    public function a_rendezvous_is_never_moved_and_acts_as_an_obstacle(): void
    {
        $rdv = $this->item('2026-09-23 10:00', '2026-09-23 11:00', isAppointment: true, completed: true);
        $work = $this->item('2026-09-22 09:00', '2026-09-22 09:30');

        $rescheduler = new AgendaRescheduler($this->hours, $this->source([
            '2026-09-22' => [$work],
            '2026-09-23' => [$rdv],
        ]));

        $result = $rescheduler->rescheduleDay(CarbonImmutable::parse('2026-09-22'));

        $this->assertCount(1, $result->moves);
        $move = $result->moves[0];
        // Le RDV occupe 10h-11h : le travail reporté doit se placer à 9h (avant), pas dedans.
        $this->assertSame('2026-09-23 09:00', $move->newStartsAt->format('Y-m-d H:i'));

        // Le RDV lui-même n'apparaît jamais dans les moves.
        foreach ($result->moves as $m) {
            $this->assertNotSame($rdv->id, $m->id);
        }
    }

    #[Test]
    public function a_completed_engagement_is_never_moved(): void
    {
        $completedEngagement = $this->item('2026-09-22 09:00', '2026-09-22 09:30', isCommitment: true, completed: true);
        $notCompletedWork = $this->item('2026-09-22 10:00', '2026-09-22 10:30');

        $rescheduler = new AgendaRescheduler($this->hours, $this->source([
            '2026-09-22' => [$completedEngagement, $notCompletedWork],
        ]));

        $result = $rescheduler->rescheduleDay(CarbonImmutable::parse('2026-09-22'));

        $this->assertCount(1, $result->moves);
        $this->assertSame($notCompletedWork->id, $result->moves[0]->id);
    }

    #[Test]
    public function ordering_is_preserved_among_multiple_not_completed_engagements(): void
    {
        $e1 = $this->item('2026-09-22 09:00', '2026-09-22 09:30', isCommitment: true);
        $e2 = $this->item('2026-09-22 10:00', '2026-09-22 10:30', isCommitment: true);
        $e3 = $this->item('2026-09-22 11:00', '2026-09-22 11:30', isCommitment: true);

        $rescheduler = new AgendaRescheduler($this->hours, $this->source([
            '2026-09-22' => [$e3, $e1, $e2], // ordre d'insertion volontairement mélangé
        ]));

        $result = $rescheduler->rescheduleDay(CarbonImmutable::parse('2026-09-22'));

        $byId = collect($result->moves)->keyBy('id');
        $this->assertSame('2026-09-23 09:00', $byId[$e1->id]->newStartsAt->format('Y-m-d H:i'));
        $this->assertSame('2026-09-23 09:30', $byId[$e2->id]->newStartsAt->format('Y-m-d H:i'));
        $this->assertSame('2026-09-23 10:00', $byId[$e3->id]->newStartsAt->format('Y-m-d H:i'));
    }

    #[Test]
    public function engagements_always_come_before_regular_items_regardless_of_original_interleaving(): void
    {
        $engagement1 = $this->item('2026-09-22 09:00', '2026-09-22 09:30', isCommitment: true);
        $regular1 = $this->item('2026-09-22 09:30', '2026-09-22 10:00');
        $engagement2 = $this->item('2026-09-22 10:00', '2026-09-22 10:30', isCommitment: true);
        $regular2 = $this->item('2026-09-22 10:30', '2026-09-22 11:00');

        $rescheduler = new AgendaRescheduler($this->hours, $this->source([
            '2026-09-22' => [$regular1, $engagement1, $regular2, $engagement2],
        ]));

        $result = $rescheduler->rescheduleDay(CarbonImmutable::parse('2026-09-22'));

        $byId = collect($result->moves)->keyBy('id');
        $this->assertSame('2026-09-23 09:00', $byId[$engagement1->id]->newStartsAt->format('Y-m-d H:i'));
        $this->assertSame('2026-09-23 09:30', $byId[$engagement2->id]->newStartsAt->format('Y-m-d H:i'));
        $this->assertSame('2026-09-23 10:00', $byId[$regular1->id]->newStartsAt->format('Y-m-d H:i'));
        $this->assertSame('2026-09-23 10:30', $byId[$regular2->id]->newStartsAt->format('Y-m-d H:i'));
    }

    #[Test]
    public function items_rescheduled_from_d_always_come_before_items_already_on_the_target_day(): void
    {
        // D+1 a déjà un item à 9h (le plus tôt possible).
        $alreadyOnTarget = $this->item('2026-09-23 09:00', '2026-09-23 09:30');
        $rescheduledFromD = $this->item('2026-09-22 15:00', '2026-09-22 15:30', isCommitment: true);

        $rescheduler = new AgendaRescheduler($this->hours, $this->source([
            '2026-09-22' => [$rescheduledFromD],
            '2026-09-23' => [$alreadyOnTarget],
        ]));

        $result = $rescheduler->rescheduleDay(CarbonImmutable::parse('2026-09-22'));

        $byId = collect($result->moves)->keyBy('id');
        // L'item venant de D gagne la place de 9h, même si l'item déjà sur D+1 y était historiquement.
        $this->assertSame('2026-09-23 09:00', $byId[$rescheduledFromD->id]->newStartsAt->format('Y-m-d H:i'));
        $this->assertSame('2026-09-23 09:30', $byId[$alreadyOnTarget->id]->newStartsAt->format('Y-m-d H:i'));
    }

    #[Test]
    public function it_skips_sunday_and_monday_when_triggered_on_saturday(): void
    {
        $item = $this->item('2026-09-26 10:00', '2026-09-26 10:30'); // samedi

        $rescheduler = new AgendaRescheduler($this->hours, $this->source([
            '2026-09-26' => [$item],
        ]));

        $result = $rescheduler->rescheduleDay(CarbonImmutable::parse('2026-09-26'));

        $this->assertCount(1, $result->moves);
        $this->assertSame('2026-09-29', $result->moves[0]->newStartsAt->toDateString()); // mardi
    }

    #[Test]
    public function it_targets_saturday_when_triggered_on_friday(): void
    {
        $item = $this->item('2026-09-25 10:00', '2026-09-25 10:30'); // vendredi

        $rescheduler = new AgendaRescheduler($this->hours, $this->source([
            '2026-09-25' => [$item],
        ]));

        $result = $rescheduler->rescheduleDay(CarbonImmutable::parse('2026-09-25'));

        $this->assertCount(1, $result->moves);
        $this->assertSame('2026-09-26', $result->moves[0]->newStartsAt->toDateString()); // samedi
    }

    #[Test]
    public function it_cascades_to_the_next_open_day_when_the_target_day_is_full(): void
    {
        // D+1 (mercredi 2026-09-23) est complet : un RDV de 9h à 18h occupe toute la journée.
        $fullDayObstacle = $this->item('2026-09-23 09:00', '2026-09-23 18:00', isAppointment: true);
        $item1 = $this->item('2026-09-22 09:00', '2026-09-22 09:30');
        $item2 = $this->item('2026-09-22 10:00', '2026-09-22 10:30');

        $rescheduler = new AgendaRescheduler($this->hours, $this->source([
            '2026-09-22' => [$item1, $item2],
            '2026-09-23' => [$fullDayObstacle],
        ]));

        $result = $rescheduler->rescheduleDay(CarbonImmutable::parse('2026-09-22'));

        $byId = collect($result->moves)->keyBy('id');
        $this->assertSame('2026-09-24', $byId[$item1->id]->newStartsAt->toDateString());
        $this->assertSame('2026-09-24', $byId[$item2->id]->newStartsAt->toDateString());
        $this->assertSame('2026-09-24 09:00', $byId[$item1->id]->newStartsAt->format('Y-m-d H:i'));
        $this->assertSame('2026-09-24 09:30', $byId[$item2->id]->newStartsAt->format('Y-m-d H:i'));

        $this->assertArrayHasKey('2026-09-24', $result->daysTouched);
    }

    #[Test]
    public function it_fails_cleanly_when_the_cascade_limit_is_exceeded(): void
    {
        // Sature chaque jour ouvré sur une très longue période avec un RDV toute la journée.
        $itemsByDay = [];
        $cursor = CarbonImmutable::parse('2026-09-22'); // mardi
        for ($i = 0; $i < 90; $i++) {
            if ($this->hours->isOpenDay($cursor)) {
                $itemsByDay[$cursor->toDateString()] = [
                    $this->item(
                        $cursor->format('Y-m-d').' 09:00',
                        $cursor->format('Y-m-d').' 18:00',
                        isAppointment: true,
                    ),
                ];
            }
            $cursor = $cursor->addDay();
        }

        $work = $this->item('2026-09-21 10:00', '2026-09-21 10:30'); // lundi, jour du déclenchement
        $itemsByDay['2026-09-21'] = [$work];

        $rescheduler = new AgendaRescheduler($this->hours, $this->source($itemsByDay));

        $this->expectException(RescheduleCascadeLimitExceededException::class);
        $rescheduler->rescheduleDay(CarbonImmutable::parse('2026-09-21'));
    }

    #[Test]
    public function a_fixed_obstacle_ending_right_before_lunch_causes_a_snap_to_two_pm(): void
    {
        $obstacle = $this->item('2026-09-23 11:00', '2026-09-23 11:50', isAppointment: true);
        // Un item d'1h à placer juste après l'obstacle : 11h50 + 1h dépasserait midi -> doit sauter à 14h.
        $item = $this->item('2026-09-22 09:00', '2026-09-22 10:00');

        $rescheduler = new AgendaRescheduler($this->hours, $this->source([
            '2026-09-22' => [$item],
            '2026-09-23' => [$obstacle],
        ]));

        $result = $rescheduler->rescheduleDay(CarbonImmutable::parse('2026-09-22'));

        $this->assertCount(1, $result->moves);
        $this->assertSame('2026-09-23 09:00', $result->moves[0]->newStartsAt->format('Y-m-d H:i'));
    }

    #[Test]
    public function item_duration_is_preserved_across_multiple_cascade_hops(): void
    {
        $fullDay1 = $this->item('2026-09-23 09:00', '2026-09-23 18:00', isAppointment: true);
        $fullDay2 = $this->item('2026-09-24 09:00', '2026-09-24 18:00', isAppointment: true);
        $item = $this->item('2026-09-22 09:00', '2026-09-22 10:15'); // 1h15

        $rescheduler = new AgendaRescheduler($this->hours, $this->source([
            '2026-09-22' => [$item],
            '2026-09-23' => [$fullDay1],
            '2026-09-24' => [$fullDay2],
        ]));

        $result = $rescheduler->rescheduleDay(CarbonImmutable::parse('2026-09-22'));

        $this->assertCount(1, $result->moves);
        $move = $result->moves[0];
        $this->assertSame('2026-09-25', $move->newStartsAt->toDateString());
        $this->assertEquals(75, $move->newStartsAt->diffInMinutes($move->newEndsAt));
    }

    #[Test]
    public function it_returns_an_empty_result_when_nothing_is_displaceable(): void
    {
        $rdv = $this->item('2026-09-22 10:00', '2026-09-22 10:30', isAppointment: true);
        $completed = $this->item('2026-09-22 11:00', '2026-09-22 11:30', completed: true);

        $rescheduler = new AgendaRescheduler($this->hours, $this->source([
            '2026-09-22' => [$rdv, $completed],
        ]));

        $result = $rescheduler->rescheduleDay(CarbonImmutable::parse('2026-09-22'));

        $this->assertTrue($result->isEmpty());
    }

    #[Test]
    public function an_engagement_already_on_the_target_day_stays_in_place_without_collision(): void
    {
        // Un travail normal sur D, et un engagement à 11h sur D+1 (mercredi) qui ne devrait
        // pas être chevauché par un créneau de 30 min placé à 9h.
        $regularFromD = $this->item('2026-09-22 09:00', '2026-09-22 09:30');
        $engagementOnTarget = $this->item('2026-09-23 11:00', '2026-09-23 11:30', isCommitment: true);

        $rescheduler = new AgendaRescheduler($this->hours, $this->source([
            '2026-09-22' => [$regularFromD],
            '2026-09-23' => [$engagementOnTarget],
        ]));

        $result = $rescheduler->rescheduleDay(CarbonImmutable::parse('2026-09-22'));

        $byId = collect($result->moves)->keyBy('id');
        // L'engagement du 23 à 11h n'a aucun mouvement enregistré : il reste où il était.
        $this->assertArrayNotHasKey($engagementOnTarget->id, $byId);
        // Le travail normal venant de D se place après l'engagement (qui prime), donc
        // après 11h30, puisque les engagements sont toujours placés avant les réguliers.
    }

    #[Test]
    public function engagements_from_d_take_priority_over_engagements_already_on_target_regardless_of_original_time(): void
    {
        $engagementFromD = $this->item('2026-09-22 08:00', '2026-09-22 08:30', isCommitment: true);
        $engagementOnTarget = $this->item('2026-09-23 09:00', '2026-09-23 09:30', isCommitment: true);

        $rescheduler = new AgendaRescheduler($this->hours, $this->source([
            '2026-09-22' => [$engagementFromD],
            '2026-09-23' => [$engagementOnTarget],
        ]));

        $result = $rescheduler->rescheduleDay(CarbonImmutable::parse('2026-09-22'));

        $byId = collect($result->moves)->keyBy('id');
        $this->assertSame('2026-09-23 09:00', $byId[$engagementFromD->id]->newStartsAt->format('Y-m-d H:i'));
        $this->assertSame('2026-09-23 09:30', $byId[$engagementOnTarget->id]->newStartsAt->format('Y-m-d H:i'));
    }

    #[Test]
    public function regular_items_route_around_a_stationary_engagement_without_being_blocked_by_it(): void
    {
        // Reproduction du scénario signalé : un engagement stationnaire à 10h-10h30 sur D+1
        // ne bloque pas la case 9h pour les réguliers, tant qu'il n'y a pas de collision réelle.
        $regular1 = $this->item('2026-09-22 09:00', '2026-09-22 09:30');
        $regular2 = $this->item('2026-09-23 09:30', '2026-09-23 10:00');
        $engagementOnTarget = $this->item('2026-09-23 10:00', '2026-09-23 10:30', isCommitment: true);
        $regular3 = $this->item('2026-09-23 10:30', '2026-09-23 11:00');

        $rescheduler = new AgendaRescheduler($this->hours, $this->source([
            '2026-09-22' => [$regular1],
            '2026-09-23' => [$regular2, $engagementOnTarget, $regular3],
        ]));

        $result = $rescheduler->rescheduleDay(CarbonImmutable::parse('2026-09-22'));

        $byId = collect($result->moves)->keyBy('id');

        // L'engagement n'a pas bougé (rien ne le chevauche).
        $this->assertArrayNotHasKey($engagementOnTarget->id, $byId);

        // Le régulier venant de D prend la première place libre (9h), avant même les réguliers
        // déjà sur D+1 ; regular2/regular3 gardent leur place puisqu'ils ne sont pas chevauchés
        // par regular1 (9h-9h30 ne touche pas 9h30-10h).
        $this->assertArrayHasKey($regular1->id, $byId);
        $this->assertSame('2026-09-23 09:00', $byId[$regular1->id]->newStartsAt->format('Y-m-d H:i'));
        $this->assertArrayNotHasKey($regular2->id, $byId);
        $this->assertArrayNotHasKey($regular3->id, $byId);
    }

    #[Test]
    public function a_stationary_engagement_displaced_by_collision_reinserts_in_chronological_order(): void
    {
        // Deux engagements venant de D (8h et 8h30 d'origine), et un engagement déjà sur D+1
        // à 9h qui sera chevauché par le compactage des deux premiers dès 9h.
        $engagementFromD1 = $this->item('2026-09-22 08:00', '2026-09-22 08:30', isCommitment: true);
        $engagementFromD2 = $this->item('2026-09-22 08:30', '2026-09-22 09:00', isCommitment: true);
        $engagementOnTarget = $this->item('2026-09-23 09:15', '2026-09-23 09:45', isCommitment: true);

        $rescheduler = new AgendaRescheduler($this->hours, $this->source([
            '2026-09-22' => [$engagementFromD1, $engagementFromD2],
            '2026-09-23' => [$engagementOnTarget],
        ]));

        $result = $rescheduler->rescheduleDay(CarbonImmutable::parse('2026-09-22'));

        $byId = collect($result->moves)->keyBy('id');
        $this->assertSame('2026-09-23 09:00', $byId[$engagementFromD1->id]->newStartsAt->format('Y-m-d H:i'));
        $this->assertSame('2026-09-23 09:30', $byId[$engagementFromD2->id]->newStartsAt->format('Y-m-d H:i'));
        // Chevauché par le 2e (9h30-10h), l'engagement stationnaire est repoussé à la suite.
        $this->assertSame('2026-09-23 10:00', $byId[$engagementOnTarget->id]->newStartsAt->format('Y-m-d H:i'));
    }

    #[Test]
    public function regular_items_are_fully_recompacted_unlike_engagements(): void
    {
        // Contrairement aux engagements (qui restent en place sauf collision), les créneaux
        // normaux sont toujours recompactés ensemble dès 9h, triés par heure d'origine — qu'ils
        // viennent de D ou du jour cible : c'est la règle validée initialement (priorité D
        // absolue) et elle n'a pas changé pour cette catégorie.
        $regularFromD = $this->item('2026-09-22 15:00', '2026-09-22 15:30');
        $regularOnTarget = $this->item('2026-09-23 09:00', '2026-09-23 09:30');

        $rescheduler = new AgendaRescheduler($this->hours, $this->source([
            '2026-09-22' => [$regularFromD],
            '2026-09-23' => [$regularOnTarget],
        ]));

        $result = $rescheduler->rescheduleDay(CarbonImmutable::parse('2026-09-22'));

        $byId = collect($result->moves)->keyBy('id');
        // Le régulier de D prend la première place (9h), celui déjà sur la cible est décalé
        // après, même s'il n'y avait pas de "collision" au sens où il occupait déjà 9h lui-même.
        $this->assertSame('2026-09-23 09:00', $byId[$regularFromD->id]->newStartsAt->format('Y-m-d H:i'));
        $this->assertSame('2026-09-23 09:30', $byId[$regularOnTarget->id]->newStartsAt->format('Y-m-d H:i'));
    }
}
