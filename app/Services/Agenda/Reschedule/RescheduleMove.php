<?php

namespace App\Services\Agenda\Reschedule;

use Carbon\CarbonImmutable;

/**
 * Décrit le déplacement effectif d'un créneau (ancienne et nouvelle position),
 * calculé par AgendaRescheduler avant toute écriture en base.
 */
final class RescheduleMove
{
    public function __construct(
        public readonly string $kind,
        public readonly int $id,
        public readonly CarbonImmutable $oldStartsAt,
        public readonly CarbonImmutable $oldEndsAt,
        public readonly CarbonImmutable $newStartsAt,
        public readonly CarbonImmutable $newEndsAt,
    ) {}

    public function dayChanged(): bool
    {
        return ! $this->oldStartsAt->isSameDay($this->newStartsAt);
    }
}
