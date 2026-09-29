<?php

namespace App\Services\Agenda\Reschedule;

use Carbon\CarbonImmutable;

interface RescheduleItemSource
{
    /**
     * Tous les créneaux (devis + événements libres) dont starts_at tombe le
     * jour donné, triés par starts_at croissant.
     *
     * @return array<int, RescheduleItem>
     */
    public function itemsOnDay(CarbonImmutable $day): array;
}
