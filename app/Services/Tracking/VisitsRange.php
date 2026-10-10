<?php

namespace App\Services\Tracking;

use App\Enums\VisitsPeriod;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

/**
 * Fenêtre de dates analysée par le suivi des visites, découpée en jours ou en semaines (du lundi au dimanche).
 */
class VisitsRange
{
    private function __construct(
        public readonly CarbonImmutable $start,
        public readonly CarbonImmutable $end,
        public readonly bool $weekly,
    ) {}

    /**
     * Période se terminant aujourd'hui (inclus). Si le suivi a démarré en cours de période,
     * elle commence au premier jour suivi pour ne pas afficher de faux zéros.
     */
    public static function forPeriod(VisitsPeriod $period, CarbonImmutable $today, ?CarbonImmutable $firstTrackedDay): self
    {
        $end = $today->startOfDay();
        $weekly = $period->isWeekly();

        $start = $weekly
            ? $end->startOfWeek(CarbonInterface::MONDAY)->subWeeks($period->bucketCount() - 1)
            : $end->subDays($period->bucketCount() - 1);

        if ($firstTrackedDay !== null && $firstTrackedDay->greaterThan($start) && $firstTrackedDay->lessThanOrEqualTo($end)) {
            $start = $weekly ? $firstTrackedDay->startOfWeek(CarbonInterface::MONDAY) : $firstTrackedDay->startOfDay();
        }

        return new self($start, $end, $weekly);
    }

    /** Période de même durée qui précède immédiatement celle-ci, pour calculer les tendances. */
    public function previous(): self
    {
        return new self($this->start->subDays($this->dayCount()), $this->start->subDay(), $this->weekly);
    }

    public function dayCount(): int
    {
        return (int) round($this->start->diffInDays($this->end)) + 1;
    }

    /** @return 'day'|'week' */
    public function granularity(): string
    {
        return $this->weekly ? 'week' : 'day';
    }

    /**
     * Premier jour (Y-m-d) de chaque point de la courbe, du plus ancien au plus récent.
     *
     * @return list<string>
     */
    public function bucketStarts(): array
    {
        $step = $this->weekly ? 7 : 1;
        $starts = [];

        for ($cursor = $this->start; $cursor->lessThanOrEqualTo($this->end); $cursor = $cursor->addDays($step)) {
            $starts[] = $cursor->toDateString();
        }

        return $starts;
    }

    /** Premier jour (Y-m-d) du point de la courbe qui contient ce jour. */
    public function bucketOf(string $day): string
    {
        $date = CarbonImmutable::parse($day);

        return ($this->weekly ? $date->startOfWeek(CarbonInterface::MONDAY) : $date)->toDateString();
    }
}
