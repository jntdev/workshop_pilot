<?php

namespace App\Enums;

enum WorkMode: string
{
    case Comptoir = 'comptoir';
    case Atelier = 'atelier';
    case Julien = 'julien';

    public function label(): string
    {
        return match ($this) {
            self::Comptoir => 'Nicolas',
            self::Atelier => 'Jonathan',
            self::Julien => 'Julien',
        };
    }

    public static function fromLabel(string $label): ?self
    {
        return match (strtolower($label)) {
            'nicolas' => self::Comptoir,
            'jonathan' => self::Atelier,
            'julien' => self::Julien,
            default => null,
        };
    }
}
