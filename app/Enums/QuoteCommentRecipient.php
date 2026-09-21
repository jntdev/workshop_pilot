<?php

namespace App\Enums;

enum QuoteCommentRecipient: string
{
    case Nikal = 'nikal';
    case Jal = 'jal';

    public function label(): string
    {
        return match ($this) {
            self::Nikal => 'Pour Nikal',
            self::Jal => 'Pour Jal',
        };
    }
}
