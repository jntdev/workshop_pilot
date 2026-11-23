<?php

namespace App\Services\Assistant\Actions;

use App\Models\AssistantAction;

interface AssistantActionHandler
{
    /**
     * Valide les paramètres envoyés par l'assistant et prépare la proposition.
     *
     * @param  array<string, mixed>  $params
     *
     * @throws AssistantActionException
     */
    public function propose(array $params): ActionProposal;

    /**
     * Applique la proposition confirmée, après avoir revérifié que l'état n'a pas changé.
     *
     * @return array<string, mixed> l'état après l'action
     *
     * @throws AssistantActionException
     */
    public function execute(AssistantAction $action): array;

    public function canUndo(AssistantAction $action): bool;

    /**
     * @return array<string, mixed> l'état après le retour en arrière
     *
     * @throws AssistantActionException
     */
    public function undo(AssistantAction $action): array;

    /**
     * Page du workshop à consulter pour cette action (adresse relative), si elle existe.
     */
    public function link(AssistantAction $action): ?string;

    /**
     * État de l'élément après l'action (faite ou défaite), dans le format que les pages ouvertes du workshop
     * appliquent directement à leur état. Renvoie null tant que l'action n'a rien changé.
     *
     * @return array<string, mixed>|null
     */
    public function effect(AssistantAction $action): ?array;
}
