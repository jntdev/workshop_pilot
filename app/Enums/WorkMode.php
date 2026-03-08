<?php

namespace App\Enums;

enum WorkMode: string
{
    case Comptoir = 'comptoir';
    case Atelier = 'atelier';

    public function label(): string
    {
        return match ($this) {
            self::Comptoir => 'Nicolas',
            self::Atelier => 'Jonathan',
        };
    }

    public static function fromLabel(string $label): ?self
    {
        return match (strtolower($label)) {
            'nicolas' => self::Comptoir,
            'jonathan' => self::Atelier,
            default => null,
        };
    }
}
