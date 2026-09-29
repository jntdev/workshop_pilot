<?php

namespace App\Services\Agenda\Reschedule;

use App\Services\Agenda\OpeningHours\OpeningHours;
use Carbon\CarbonImmutable;

/**
 * Algorithme pur de report des travaux non terminés vers le prochain jour
 * ouvré, en cascade si nécessaire. Ne touche jamais à la base de données :
 * voir EloquentRescheduleItemSource pour la lecture, et
 * AgendaRescheduleController pour la persistance des résultats.
 */
class AgendaRescheduler
{
    /** Cascade maximale (en jours ouvrés) avant d'abandonner l'opération. */
    private const MAX_CASCADE_DEPTH = 60;

    public function __construct(
        private readonly OpeningHours $hours,
        private readonly RescheduleItemSource $source,
    ) {}

    /**
     * Point d'entrée : reporte au prochain jour ouvré tous les créneaux non
     * terminés (hors Rendez-vous) du jour donné.
     *
     * @throws RescheduleCascadeLimitExceededException
     */
    public function rescheduleDay(CarbonImmutable $day): RescheduleResult
    {
        $items = $this->source->itemsOnDay($day);

        [$incomingEngagements, $incomingRegular] = $this->incomingQueuesFor($items);

        if ($incomingEngagements === [] && $incomingRegular === []) {
            return RescheduleResult::empty();
        }

        $targetDay = $this->hours->nextOpenDay($day);

        [$moves, $daysTouched, $maxDepth] = $this->applyCascade($targetDay, $incomingEngagements, $incomingRegular, 1, [], []);

        return new RescheduleResult($moves, $daysTouched, $maxDepth);
    }

    /**
     * Sépare les créneaux déplaçables d'un jour en deux files (Engagements
     * non terminés, autres créneaux non terminés), chacune triée par heure
     * d'origine. Les Rendez-vous et tout ce qui est déjà terminé sont exclus.
     *
     * @param  array<int, RescheduleItem>  $items
     * @return array{0: array<int, RescheduleItem>, 1: array<int, RescheduleItem>}
     */
    private function incomingQueuesFor(array $items): array
    {
        $engagements = [];
        $others = [];

        foreach ($items as $item) {
            if (! $item->isDisplaceable()) {
                continue;
            }

            if ($item->isCommitment) {
                $engagements[] = $item;
            } else {
                $others[] = $item;
            }
        }

        $byStartsAt = fn (RescheduleItem $a, RescheduleItem $b) => $a->startsAt <=> $b->startsAt;
        usort($engagements, $byStartsAt);
        usort($others, $byStartsAt);

        return [$engagements, $others];
    }

    /**
     * Place, sur $targetDay, d'abord les Engagements (ceux reportés depuis le
     * jour d'origine + ceux déjà présents sur $targetDay), puis les autres
     * créneaux ; récursion sur le jour ouvré suivant pour tout ce qui déborde.
     *
     * @param  array<int, RescheduleItem>  $incomingEngagements
     * @param  array<int, RescheduleItem>  $incomingRegular
     * @param  array<int, RescheduleMove>  $movesAcc
     * @param  array<string, int>  $daysTouchedAcc
     * @return array{0: array<int, RescheduleMove>, 1: array<string, int>, 2: int}
     *
     * @throws RescheduleCascadeLimitExceededException
     */
    private function applyCascade(
        CarbonImmutable $targetDay,
        array $incomingEngagements,
        array $incomingRegular,
        int $depth,
        array $movesAcc,
        array $daysTouchedAcc,
    ): array {
        if ($depth > self::MAX_CASCADE_DEPTH) {
            throw new RescheduleCascadeLimitExceededException(self::MAX_CASCADE_DEPTH);
        }

        $existingOnTarget = $this->source->itemsOnDay($targetDay);

        $fixedIntervals = [];
        $existingEngagements = [];
        $existingRegular = [];

        foreach ($existingOnTarget as $item) {
            if ($item->isFixedObstacle()) {
                $fixedIntervals[] = [$item->startsAt, $item->endsAt];
            } elseif ($item->isCommitment) {
                $existingEngagements[] = $item;
            } else {
                $existingRegular[] = $item;
            }
        }

        usort($existingRegular, fn (RescheduleItem $a, RescheduleItem $b) => $a->startsAt <=> $b->startsAt);

        $placedCount = 0;

        // --- Phase 1 : Engagements. Ceux déjà présents sur $targetDay restent à leur
        // place tant qu'aucune collision ne les touche ; en cas de collision, ils
        // repartent dans la file d'attente, réinsérés à leur position chronologique
        // d'origine parmi les engagements restant à placer (jamais de mutation en place).
        [$engagementIntervals, $engagementOverflow, $engagementMoves, $engagementPlaced] = $this->placeEngagements(
            $targetDay,
            $incomingEngagements,
            $existingEngagements,
            $fixedIntervals,
        );
        $movesAcc = [...$movesAcc, ...$engagementMoves];
        $placedCount += $engagementPlaced;

        // --- Phase 2 : créneaux normaux, casés après les Engagements et tous les obstacles.
        $regularQueue = [...$incomingRegular, ...$existingRegular];
        usort($regularQueue, fn (RescheduleItem $a, RescheduleItem $b) => $a->startsAt <=> $b->startsAt);

        $occupied = [...$fixedIntervals, ...$engagementIntervals];
        $cursor = $this->hours->dayOpeningStart($targetDay);
        $regularOverflow = [];

        foreach ($regularQueue as $item) {
            $candidateStart = $this->findSlotOnDay($targetDay, $cursor, $occupied, $item->durationMinutes());

            if ($candidateStart === null) {
                $regularOverflow[] = $item;

                continue;
            }

            $candidateEnd = $candidateStart->addMinutes($item->durationMinutes());

            if (! $candidateStart->equalTo($item->startsAt) || ! $candidateEnd->equalTo($item->endsAt)) {
                $movesAcc[] = new RescheduleMove($item->kind, $item->id, $item->startsAt, $item->endsAt, $candidateStart, $candidateEnd);
            }

            $occupied[] = [$candidateStart, $candidateEnd];
            $cursor = $candidateEnd;
            $placedCount++;
        }

        if ($placedCount > 0) {
            $daysTouchedAcc[$targetDay->toDateString()] = ($daysTouchedAcc[$targetDay->toDateString()] ?? 0) + $placedCount;
        }

        if ($engagementOverflow === [] && $regularOverflow === []) {
            return [$movesAcc, $daysTouchedAcc, $depth];
        }

        return $this->applyCascade(
            $this->hours->nextOpenDay($targetDay),
            $engagementOverflow,
            $regularOverflow,
            $depth + 1,
            $movesAcc,
            $daysTouchedAcc,
        );
    }

    /**
     * Place les Engagements entrants sur $targetDay. Les engagements venant de D ont
     * la priorité absolue : ils se casent consécutivement dès 9h sans jamais éviter
     * les engagements déjà présents sur $targetDay. Un engagement du jour cible qui
     * se retrouve chevauché par ce placement repart dans la file, réinséré à sa
     * position chronologique d'origine ; un engagement du jour cible jamais chevauché
     * reste exactement où il était (aucun move généré).
     *
     * @param  array<int, RescheduleItem>  $incoming
     * @param  array<int, RescheduleItem>  $existing
     * @param  array<int, array{0: CarbonImmutable, 1: CarbonImmutable}>  $fixedIntervals
     * @return array{0: array<int, array{0: CarbonImmutable, 1: CarbonImmutable}>, 1: array<int, RescheduleItem>, 2: array<int, RescheduleMove>, 3: int}
     */
    private function placeEngagements(CarbonImmutable $targetDay, array $incoming, array $existing, array $fixedIntervals): array
    {
        // File des engagements encore à traiter, triée par heure d'origine. Chaque entrée
        // précise si l'item vient de D (priorité absolue, ignore les stationnaires) ou s'il
        // a été délogé d'une position stationnaire (doit, lui, éviter les stationnaires restants).
        $pending = array_map(fn (RescheduleItem $item) => ['item' => $item, 'fromD' => true], $incoming);
        usort($pending, fn (array $a, array $b) => $a['item']->startsAt <=> $b['item']->startsAt);

        // Engagements du jour cible pas encore délogés : restent tels quels tant
        // qu'aucune collision ne les touche.
        $stationary = $existing;

        $placedIntervals = [];
        $moves = [];
        $placedCount = 0;
        $cursor = $this->hours->dayOpeningStart($targetDay);
        $overflow = [];

        while ($pending !== []) {
            $entry = array_shift($pending);
            $item = $entry['item'];

            // Un item venant de D ignore les stationnaires (priorité absolue) ; un item
            // délogé doit lui-même éviter ceux qui restent encore en place.
            $obstacles = $entry['fromD']
                ? [...$fixedIntervals, ...$placedIntervals]
                : [...$fixedIntervals, ...$placedIntervals, ...array_map(
                    fn (RescheduleItem $s) => [$s->startsAt, $s->endsAt],
                    $stationary,
                )];

            $candidateStart = $this->findSlotOnDay($targetDay, $cursor, $obstacles, $item->durationMinutes());

            if ($candidateStart === null) {
                $overflow[] = $item;

                continue;
            }

            $candidateEnd = $candidateStart->addMinutes($item->durationMinutes());

            if (! $candidateStart->equalTo($item->startsAt) || ! $candidateEnd->equalTo($item->endsAt)) {
                $moves[] = new RescheduleMove($item->kind, $item->id, $item->startsAt, $item->endsAt, $candidateStart, $candidateEnd);
            }

            $placedIntervals[] = [$candidateStart, $candidateEnd];
            $cursor = $candidateEnd;
            $placedCount++;

            // Un engagement stationnaire chevauché par ce placement repart dans la file,
            // réinséré à sa position chronologique d'origine parmi ceux restant à traiter.
            foreach ($stationary as $key => $stationaryItem) {
                if ($this->overlaps($candidateStart, $candidateEnd, $stationaryItem->startsAt, $stationaryItem->endsAt)) {
                    unset($stationary[$key]);
                    $pending[] = ['item' => $stationaryItem, 'fromD' => false];
                    usort($pending, fn (array $a, array $b) => $a['item']->startsAt <=> $b['item']->startsAt);
                }
            }
        }

        $stationaryIntervals = array_map(
            fn (RescheduleItem $s) => [$s->startsAt, $s->endsAt],
            $stationary,
        );

        return [[...$placedIntervals, ...$stationaryIntervals], $overflow, $moves, $placedCount];
    }

    private function overlaps(CarbonImmutable $aStart, CarbonImmutable $aEnd, CarbonImmutable $bStart, CarbonImmutable $bEnd): bool
    {
        return $aStart->lt($bEnd) && $bStart->lt($aEnd);
    }

    /**
     * Cherche un créneau de $durationMinutes qui tient sur $targetDay (jamais
     * un autre jour), à partir de $cursor, en évitant tout intervalle occupé.
     * Retourne null si rien ne convient ce jour-là (l'appelant doit alors
     * faire déborder l'item vers le jour ouvré suivant).
     *
     * @param  array<int, array{0: CarbonImmutable, 1: CarbonImmutable}>  $occupiedIntervals
     */
    private function findSlotOnDay(CarbonImmutable $targetDay, CarbonImmutable $cursor, array $occupiedIntervals, int $durationMinutes): ?CarbonImmutable
    {
        $candidate = $this->settleAgainstObstacles($cursor, $occupiedIntervals);

        if ($candidate->isSameDay($targetDay) && $this->hours->canFitOnDay($candidate, $durationMinutes)) {
            return $candidate;
        }

        // Ne tient pas au premier essai (typiquement : trop long pour finir avant la
        // pause déjeuner, ou l'essai précédent a déjà débordé sur le jour suivant) :
        // on retente l'après-midi du même jour cible avant d'abandonner.
        $afternoonCursor = $targetDay->setTime(14, 0);
        if ($afternoonCursor->gt($cursor)) {
            $afternoonCandidate = $this->settleAgainstObstacles($afternoonCursor, $occupiedIntervals);

            if ($afternoonCandidate->isSameDay($targetDay) && $this->hours->canFitOnDay($afternoonCandidate, $durationMinutes)) {
                return $afternoonCandidate;
            }
        }

        return null;
    }

    /**
     * Avance $cursor au premier instant valide (heures d'ouverture, hors
     * pause déjeuner) qui ne chevauche aucun intervalle déjà occupé ce jour.
     * Peut déborder sur un jour ultérieur si les obstacles occupent toute la
     * fin de journée : c'est à l'appelant (findSlotOnDay) de détecter ce
     * débordement via isSameDay($targetDay) et de ne jamais l'utiliser tel quel.
     *
     * @param  array<int, array{0: CarbonImmutable, 1: CarbonImmutable}>  $occupiedIntervals
     */
    private function settleAgainstObstacles(CarbonImmutable $cursor, array $occupiedIntervals): CarbonImmutable
    {
        $candidate = $this->hours->nextSlotAtOrAfter($cursor);

        do {
            $moved = false;

            foreach ($occupiedIntervals as [$start, $end]) {
                if ($candidate->gte($start) && $candidate->lt($end)) {
                    $candidate = $this->hours->nextSlotAtOrAfter($end);
                    $moved = true;
                }
            }
        } while ($moved);

        return $candidate;
    }
}
