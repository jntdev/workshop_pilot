<?php

namespace App\Enums;

enum AssistantActionType: string
{
    case QuoteStatus = 'quote_status';
    case OrderLine = 'order_line';
    case AppointmentMove = 'appointment_move';

    public function label(): string
    {
        return match ($this) {
            self::QuoteStatus => 'Statut du devis',
            self::OrderLine => 'Pièce à commander',
            self::AppointmentMove => 'Agenda de réparation',
        };
    }
}
