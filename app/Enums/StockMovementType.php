<?php

namespace App\Enums;

enum StockMovementType: string
{
    case ManualIn = 'manual_in';
    case ManualOut = 'manual_out';
    case QuoteConsumption = 'quote_consumption';
    case MaintenanceConsumption = 'maintenance_consumption';
    case SaleConsumption = 'sale_consumption';
    case SaleReturn = 'sale_return';

    public function label(): string
    {
        return match ($this) {
            self::ManualIn => 'Entrée manuelle',
            self::ManualOut => 'Sortie manuelle',
            self::QuoteConsumption => 'Consommation devis',
            self::MaintenanceConsumption => 'Consommation maintenance',
            self::SaleConsumption => 'Consommation vente caisse',
            self::SaleReturn => 'Réintégration suite annulation vente',
        };
    }

    public function isManual(): bool
    {
        return in_array($this, [self::ManualIn, self::ManualOut]);
    }
}
