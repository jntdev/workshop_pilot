<?php

namespace App\Enums;

enum Metier: string
{
    case Atelier = 'atelier';
    case Vente = 'vente';
    case Location = 'location';

    public function label(): string
    {
        return match ($this) {
            self::Atelier => 'Atelier',
            self::Vente => 'Vente',
            self::Location => 'Location',
        };
    }
}
