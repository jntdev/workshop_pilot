<?php

namespace App\Enums;

enum AssistantMessageStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Answered = 'answered';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Processing => 'En cours',
            self::Answered => 'Répondu',
            self::Failed => 'Échec',
        };
    }

    public function isFinished(): bool
    {
        return $this === self::Answered || $this === self::Failed;
    }
}
