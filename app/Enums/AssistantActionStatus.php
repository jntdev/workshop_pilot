<?php

namespace App\Enums;

enum AssistantActionStatus: string
{
    case Proposed = 'proposed';
    case Confirming = 'confirming';
    case Executed = 'executed';
    case Cancelled = 'cancelled';
    case Undone = 'undone';
    case Failed = 'failed';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Proposed => 'À confirmer',
            self::Confirming => 'En cours',
            self::Executed => 'Fait',
            self::Cancelled => 'Annulé',
            self::Undone => 'Défait',
            self::Failed => 'Échec',
            self::Expired => 'Expiré',
        };
    }
}
