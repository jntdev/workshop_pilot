<?php

namespace App\Enums;

enum ArticleCategorySource: string
{
    case Manual = 'manual';
    case Catalogue = 'catalogue';

    public function label(): string
    {
        return match ($this) {
            self::Manual => 'Manuelle',
            self::Catalogue => 'Catalogue',
        };
    }
}
