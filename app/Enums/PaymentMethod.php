<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cb = 'cb';
    case Liquide = 'liquide';
    case Cheque = 'cheque';
    case Virement = 'virement';
    case Autre = 'autre';

    public function label(): string
    {
        return match ($this) {
            self::Cb => 'Carte bancaire',
            self::Liquide => 'Liquide',
            self::Cheque => 'Chèque',
            self::Virement => 'Virement',
            self::Autre => 'Autre',
        };
    }
}
