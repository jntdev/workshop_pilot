<?php

namespace App\Services\Assistant\Actions;

use App\Enums\AssistantActionType;

/**
 * Proposition validée par le serveur : le texte affiché à l'utilisateur (summary) est écrit ici,
 * à partir des données réelles, jamais repris tel quel de ce que l'assistant a dit.
 */
final class ActionProposal
{
    /**
     * @param  array<string, mixed>  $payload  ce qui sera appliqué
     * @param  array<string, mixed>  $before  état observé au moment de la proposition
     * @param  list<string>  $warnings
     */
    public function __construct(
        public readonly AssistantActionType $type,
        public readonly string $subjectType,
        public readonly int $subjectId,
        public readonly array $payload,
        public readonly array $before,
        public readonly string $summary,
        public readonly array $warnings = [],
    ) {}
}
