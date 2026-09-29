<?php

namespace App\Services\Agenda\Reschedule;

/**
 * Résultat du calcul de report — liste des déplacements à appliquer, sans
 * aucune écriture en base (voir AgendaRescheduler, EloquentRescheduleItemSource).
 */
final class RescheduleResult
{
    /**
     * @param  array<int, RescheduleMove>  $moves
     * @param  array<string, int>  $daysTouched  date (Y-m-d) => nombre de créneaux placés/déplacés ce jour-là
     */
    public function __construct(
        public readonly array $moves,
        public readonly array $daysTouched,
        public readonly int $maxCascadeDepth,
    ) {}

    public static function empty(): self
    {
        return new self([], [], 0);
    }

    public function isEmpty(): bool
    {
        return $this->moves === [];
    }

    public function movedCount(): int
    {
        return count($this->moves);
    }

    public function distinctDaysCount(): int
    {
        return count($this->daysTouched);
    }
}
