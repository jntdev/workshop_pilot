<?php

namespace App\Enums;

enum QuoteStatus: string
{
    case Draft = 'brouillon';
    case Ready = 'prêt';
    case Editable = 'modifiable';
    case Invoiced = 'facturé';

    public function canTransitionTo(self $newStatus): bool
    {
        return match ($this) {
            self::Draft => $newStatus === self::Ready,
            self::Ready => in_array($newStatus, [self::Editable, self::Invoiced]),
            self::Editable => in_array($newStatus, [self::Ready, self::Invoiced]),
            self::Invoiced => false,
        };
    }

    public function showMargins(): bool
    {
        return $this === self::Draft;
    }

    public function isEditable(): bool
    {
        return in_array($this, [self::Draft, self::Editable]);
    }

    public function canShowPurchasePrice(): bool
    {
        return $this === self::Draft;
    }

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Ready => 'Prêt',
            self::Editable => 'Modifiable',
            self::Invoiced => 'Facturé',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'badge-draft',
            self::Ready => 'badge-ready',
            self::Editable => 'badge-editable',
            self::Invoiced => 'badge-invoiced',
        };
    }
}
