<?php

namespace App\Services\Agenda\Reschedule;

use App\Models\AgendaEvent;
use App\Models\QuoteAppointment;
use Carbon\CarbonImmutable;

/**
 * Charge, jour par jour et à la demande, les créneaux devis et événements
 * libres pour l'algorithme de report (voir AgendaRescheduler). Seul point
 * de contact entre le calcul pur et Eloquent, côté lecture.
 */
class EloquentRescheduleItemSource implements RescheduleItemSource
{
    public function itemsOnDay(CarbonImmutable $day): array
    {
        $start = $day->startOfDay();
        $end = $day->endOfDay();

        $appointments = QuoteAppointment::query()
            ->whereBetween('starts_at', [$start, $end])
            ->get()
            ->map(fn (QuoteAppointment $appointment) => new RescheduleItem(
                'quote',
                $appointment->id,
                CarbonImmutable::parse($appointment->starts_at),
                CarbonImmutable::parse($appointment->ends_at),
                $appointment->is_appointment,
                $appointment->is_commitment,
                $appointment->completed,
            ));

        $events = AgendaEvent::query()
            ->whereBetween('starts_at', [$start, $end])
            ->get()
            ->map(fn (AgendaEvent $event) => new RescheduleItem(
                'event',
                $event->id,
                CarbonImmutable::parse($event->starts_at),
                CarbonImmutable::parse($event->ends_at),
                $event->is_appointment,
                $event->is_commitment,
                $event->completed,
            ));

        return $appointments->concat($events)
            ->sortBy(fn (RescheduleItem $item) => $item->startsAt)
            ->values()
            ->all();
    }
}
