<?php

namespace App\Enums;

/**
 * @deprecated Utilisé uniquement pour la compatibilité avec les anciennes données.
 *             Le nouveau workflow 7.1 utilise `invoiced_at` pour distinguer devis/facture.
 */
enum QuoteStatus: string
{
    case Draft = 'brouillon';
    case Ready = 'prêt';
    case Editable = 'modifiable';
    case Invoiced = 'facturé';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Ready => 'Prêt',
            self::Editable => 'Modifiable',
            self::Invoiced => 'Facturé',
        };
    }
}
