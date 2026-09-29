<?php

namespace App\Services\Agenda\Reschedule;

use Carbon\CarbonImmutable;

/**
 * Représentation normalisée d'un créneau (devis ou événement libre) pour
 * l'algorithme de report, découplée d'Eloquent.
 */
final class RescheduleItem
{
    public function __construct(
        public readonly string $kind,
        public readonly int $id,
        public readonly CarbonImmutable $startsAt,
        public readonly CarbonImmutable $endsAt,
        public readonly bool $isAppointment,
        public readonly bool $isCommitment,
        public readonly bool $completed,
    ) {}

    public function durationMinutes(): int
    {
        return $this->startsAt->diffInMinutes($this->endsAt);
    }

    /**
     * Un Rendez-vous (quel que soit son état terminé) ou tout élément terminé
     * (Engagement ou travail normal) ne doit jamais être déplacé par le report.
     */
    public function isFixedObstacle(): bool
    {
        return $this->isAppointment || $this->completed;
    }

    public function isDisplaceable(): bool
    {
        return ! $this->isFixedObstacle();
    }

    public function withNewTimes(CarbonImmutable $start, CarbonImmutable $end): self
    {
        return new self(
            $this->kind,
            $this->id,
            $start,
            $end,
            $this->isAppointment,
            $this->isCommitment,
            $this->completed,
        );
    }
}
