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
            self::Editable => in_array($newStatus, [self::Draft, self::Invoiced]),
            self::Invoiced => false,
        };
    }

    /**
     * Retourne les transitions autorisées depuis le statut actuel.
     *
     * @return array<self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Draft => [self::Ready],
            self::Ready => [self::Editable, self::Invoiced],
            self::Editable => [self::Draft, self::Invoiced],
            self::Invoiced => [],
        };
    }

    /**
     * Détermine si le statut peut être modifié.
     */
    public function canChangeStatus(): bool
    {
        return $this !== self::Invoiced;
    }

    /**
     * Détermine si le devis peut être modifié.
     */
    public function canEdit(): bool
    {
        return in_array($this, [self::Draft, self::Editable]);
    }

    public function showMargins(): bool
    {
        return $this === self::Draft;
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
            self::Draft => 'quote-form__status-badge--brouillon',
            self::Ready => 'quote-form__status-badge--prêt',
            self::Editable => 'quote-form__status-badge--modifiable',
            self::Invoiced => 'quote-form__status-badge--facturé',
        };
    }
}
