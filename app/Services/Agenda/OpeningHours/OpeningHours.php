<?php

namespace App\Services\Agenda\OpeningHours;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

/**
 * Heures d'ouverture réelles de l'atelier (le mécanicien) : 9h-12h et 14h-18h,
 * fermé le dimanche et le lundi (des rendez-vous peuvent toutefois déjà exister
 * un lundi, mais aucun travail n'y est jamais placé par cette classe).
 *
 * Primitive pure, sans accès base de données, utilisée par le mécanisme de
 * report automatique des travaux non terminés (voir AgendaRescheduler).
 */
class OpeningHours
{
    private const MORNING_START_HOUR = 9;

    private const MORNING_END_HOUR = 12;

    private const AFTERNOON_START_HOUR = 14;

    private const AFTERNOON_END_HOUR = 18;

    /** Jours fermés (constantes CarbonInterface::SUNDAY / ::MONDAY). */
    private const CLOSED_WEEKDAYS = [CarbonInterface::SUNDAY, CarbonInterface::MONDAY];

    public function isOpenDay(CarbonInterface $date): bool
    {
        return ! in_array($date->dayOfWeek, self::CLOSED_WEEKDAYS, true);
    }

    /**
     * Premier jour ouvré strictement après $date (ignore l'heure de $date).
     */
    public function nextOpenDay(CarbonInterface $date): CarbonImmutable
    {
        $candidate = CarbonImmutable::parse($date)->startOfDay()->addDay();

        while (! $this->isOpenDay($candidate)) {
            $candidate = $candidate->addDay();
        }

        return $candidate;
    }

    /**
     * 9h00 le jour de $date (ne vérifie pas que $date est un jour ouvré : à l'appelant de s'en assurer).
     */
    public function dayOpeningStart(CarbonInterface $date): CarbonImmutable
    {
        return CarbonImmutable::parse($date)->startOfDay()->setTime(self::MORNING_START_HOUR, 0);
    }

    public function isWithinLunchBreak(CarbonInterface $instant): bool
    {
        $minutes = $instant->hour * 60 + $instant->minute;

        return $minutes >= self::MORNING_END_HOUR * 60 && $minutes < self::AFTERNOON_START_HOUR * 60;
    }

    private function isBeforeMorningStart(CarbonInterface $instant): bool
    {
        $minutes = $instant->hour * 60 + $instant->minute;

        return $minutes < self::MORNING_START_HOUR * 60;
    }

    private function isAtOrAfterClosing(CarbonInterface $instant): bool
    {
        $minutes = $instant->hour * 60 + $instant->minute;

        return $minutes >= self::AFTERNOON_END_HOUR * 60;
    }

    /**
     * Premier instant valide (dans les heures d'ouverture, jour ouvré) à partir de $instant inclus.
     * - jour fermé : 9h du prochain jour ouvré
     * - avant 9h : 9h le jour même
     * - dans la pause déjeuner [12h,14h) : 14h le jour même
     * - à/après 18h : 9h du prochain jour ouvré
     * - sinon : $instant inchangé
     */
    public function nextSlotAtOrAfter(CarbonInterface $instant): CarbonImmutable
    {
        $instant = CarbonImmutable::parse($instant);

        if (! $this->isOpenDay($instant)) {
            return $this->dayOpeningStart($this->nextOpenDay($instant->subDay()));
        }

        if ($this->isBeforeMorningStart($instant)) {
            return $this->dayOpeningStart($instant);
        }

        if ($this->isWithinLunchBreak($instant)) {
            return $instant->setTime(self::AFTERNOON_START_HOUR, 0);
        }

        if ($this->isAtOrAfterClosing($instant)) {
            return $this->dayOpeningStart($this->nextOpenDay($instant));
        }

        return $instant;
    }

    /**
     * Vrai si [$start, $end) tient entièrement dans un seul bloc d'ouverture
     * (matin 9h-12h ou après-midi 14h-18h), sans jamais chevaucher la pause déjeuner.
     */
    public function fitsWithoutCrossingLunch(CarbonInterface $start, CarbonInterface $end): bool
    {
        $startMinutes = $start->hour * 60 + $start->minute;
        $endMinutes = $end->hour * 60 + $end->minute;

        $withinMorning = $startMinutes >= self::MORNING_START_HOUR * 60 && $endMinutes <= self::MORNING_END_HOUR * 60;
        $withinAfternoon = $startMinutes >= self::AFTERNOON_START_HOUR * 60 && $endMinutes <= self::AFTERNOON_END_HOUR * 60;

        return $withinMorning || $withinAfternoon;
    }

    /**
     * Vrai si un créneau de $durationMinutes démarrant à $start (déjà calé sur un instant
     * valide via nextSlotAtOrAfter) tient dans la même journée sans dépasser 18h.
     */
    public function canFitOnDay(CarbonInterface $start, int $durationMinutes): bool
    {
        $end = CarbonImmutable::parse($start)->addMinutes($durationMinutes);

        return $this->fitsWithoutCrossingLunch(CarbonImmutable::parse($start), $end);
    }
}
