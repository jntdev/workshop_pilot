<?php

namespace App\Enums;

enum QuoteStatus: string
{
    case Reception = 'reception';
    case ToComplete = 'to_complete';
    case ToQuote = 'to_quote';
    case PendingValidation = 'pending_validation';
    case Validated = 'validated';
    case QuoteToOrder = 'quote_to_order';
    case QuoteOrdered = 'quote_ordered';
    case QuoteReceived = 'quote_received';
    case InProgress = 'in_progress';
    case Done = 'done';
    case Invoiced = 'invoiced';

    public function label(): string
    {
        return match ($this) {
            self::Reception => 'Bon de réception',
            self::ToComplete => 'À compléter',
            self::ToQuote => 'À chiffrer',
            self::PendingValidation => 'Attente validation',
            self::Validated => 'Validé',
            self::QuoteToOrder => 'À commander',
            self::QuoteOrdered => 'En commande',
            self::QuoteReceived => 'Commande reçue',
            self::InProgress => 'En cours',
            self::Done => 'Terminé',
            self::Invoiced => 'Facturé',
        };
    }

    /** Statuts visibles dans l'onglet devis (hors facture). */
    public static function quoteStatuses(): array
    {
        return [
            self::Reception,
            self::ToComplete,
            self::ToQuote,
            self::PendingValidation,
            self::Validated,
            self::QuoteToOrder,
            self::QuoteOrdered,
            self::QuoteReceived,
            self::InProgress,
            self::Done,
        ];
    }
}
