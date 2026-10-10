<?php

namespace App\Enums;

enum VisitsPeriod: string
{
    case Last7Days = '7d';
    case Last30Days = '30d';
    case Last3Months = '3m';
    case Last6Months = '6m';
    case Last12Months = '12m';

    public function isWeekly(): bool
    {
        return match ($this) {
            self::Last6Months, self::Last12Months => true,
            default => false,
        };
    }

    /** Nombre de points de la courbe : des jours, ou des semaines pour une période hebdomadaire. */
    public function bucketCount(): int
    {
        return match ($this) {
            self::Last7Days => 7,
            self::Last30Days => 30,
            self::Last3Months => 90,
            self::Last6Months => 26,
            self::Last12Months => 52,
        };
    }
}
