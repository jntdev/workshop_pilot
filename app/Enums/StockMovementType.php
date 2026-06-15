<?php

namespace App\Enums;

enum StockMovementType: string
{
    case ManualIn = 'manual_in';
    case ManualOut = 'manual_out';
    case QuoteConsumption = 'quote_consumption';
    case MaintenanceConsumption = 'maintenance_consumption';

    public function label(): string
    {
        return match ($this) {
            self::ManualIn => 'Entrée manuelle',
            self::ManualOut => 'Sortie manuelle',
            self::QuoteConsumption => 'Consommation devis',
            self::MaintenanceConsumption => 'Consommation maintenance',
        };
    }

    public function isManual(): bool
    {
        return in_array($this, [self::ManualIn, self::ManualOut]);
    }
}
