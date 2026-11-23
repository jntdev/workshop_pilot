<?php

namespace App\Services\Assistant\Actions;

use App\Enums\AssistantActionStatus;
use App\Enums\AssistantActionType;
use App\Http\Controllers\Api\OrderLineController;
use App\Models\AssistantAction;
use App\Models\QuoteLine;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Marquer une pièce d'un devis comme commandée, reçue, ou de nouveau à commander
 * (page « Pièces à commander »). Une pièce reçue ne se modifie plus, comme dans le workshop.
 */
class OrderLineHandler implements AssistantActionHandler
{
    use CallsExistingControllers;

    private const STATES = ['to_order' => 'à commander', 'ordered' => 'commandée', 'received' => 'reçue'];

    public function propose(array $params): ActionProposal
    {
        $validator = Validator::make($params, [
            'line_id' => ['required', 'integer'],
            'action' => ['required', 'string', Rule::in(array_keys(self::STATES))],
        ], [
            'line_id.required' => 'Il manque le numéro de la ligne de devis.',
            'line_id.integer' => 'Le numéro de ligne doit être un nombre.',
            'action.required' => 'Il manque l\'état voulu (to_order, ordered ou received).',
            'action.in' => 'État inconnu. États possibles : to_order (à commander), ordered (commandée), received (reçue).',
        ]);

        if ($validator->fails()) {
            throw new AssistantActionException((string) $validator->errors()->first());
        }

        $line = QuoteLine::query()->with('quote.client')->find((int) $params['line_id']);

        if ($line === null) {
            throw new AssistantActionException('Ligne de devis introuvable.');
        }

        if (! $line->needs_order) {
            throw new AssistantActionException('Cette ligne n\'est pas une pièce à commander.');
        }

        $current = $this->state($line);
        $target = $params['action'];

        if ($current === 'received') {
            throw new AssistantActionException('Une pièce déjà reçue ne peut plus être modifiée.');
        }

        if ($current === $target) {
            throw new AssistantActionException('Cette pièce est déjà « '.self::STATES[$target].' ».');
        }

        $quote = $line->quote;
        $client = $quote?->client ? trim($quote->client->prenom.' '.$quote->client->nom) : 'client inconnu';

        return new ActionProposal(
            type: AssistantActionType::OrderLine,
            subjectType: 'quote_line',
            subjectId: $line->id,
            payload: ['action' => $target],
            before: [
                'state' => $current,
                'ordered_at' => $line->ordered_at?->toISOString(),
                'received_at' => $line->received_at?->toISOString(),
                'quote_id' => $line->quote_id,
            ],
            summary: sprintf(
                'Pièce « %s » (devis %s, %s) : %s → %s',
                $line->title,
                $quote?->reference ?? '?',
                $client,
                self::STATES[$current],
                self::STATES[$target],
            ),
            warnings: $target === 'received' ? ['La réception est définitive : elle ne pourra pas être défaite.'] : [],
        );
    }

    public function execute(AssistantAction $action): array
    {
        $line = QuoteLine::query()->find($action->subject_id);

        if ($line === null) {
            throw new AssistantActionException('Ligne de devis introuvable.');
        }

        if ($this->state($line) !== ($action->before['state'] ?? null)) {
            throw new AssistantActionException('La pièce a changé d\'état depuis la proposition. Redemande à l\'assistant.');
        }

        $flags = match ($action->payload['action']) {
            'ordered' => ['mark_as_ordered' => true],
            'received' => ['mark_as_received' => true],
            default => ['unmark' => true],
        };

        $this->callController(fn () => app(OrderLineController::class)->updateOrderStatus(
            Request::create('/', 'PATCH', $flags),
            $line,
        ));

        return $this->lineState($line->refresh());
    }

    public function canUndo(AssistantAction $action): bool
    {
        if ($action->status !== AssistantActionStatus::Executed || ($action->payload['action'] ?? null) === 'received') {
            return false;
        }

        $line = QuoteLine::query()->find($action->subject_id);

        return $line !== null && $this->state($line) === ($action->payload['action'] ?? null);
    }

    public function undo(AssistantAction $action): array
    {
        $line = QuoteLine::query()->find($action->subject_id);

        if ($line === null || ! $this->canUndo($action)) {
            throw new AssistantActionException('La pièce a encore changé depuis : impossible de défaire sans risque.');
        }

        $line->update([
            'ordered_at' => $action->before['ordered_at'] ? Carbon::parse($action->before['ordered_at']) : null,
        ]);

        return $this->lineState($line->refresh());
    }

    public function link(AssistantAction $action): ?string
    {
        return '/atelier/pieces-a-commander';
    }

    public function effect(AssistantAction $action): ?array
    {
        if (! in_array($action->status, [AssistantActionStatus::Executed, AssistantActionStatus::Undone], true) || $action->after === null) {
            return null;
        }

        return [
            'type' => 'order_line',
            'line_id' => $action->subject_id,
            'quote_id' => $action->before['quote_id'] ?? null,
            'supply_status' => $action->after['state'] ?? null,
            'ordered_at' => $action->after['ordered_at'] ?? null,
            'received_at' => $action->after['received_at'] ?? null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function lineState(QuoteLine $line): array
    {
        return [
            'state' => $this->state($line),
            'ordered_at' => $line->ordered_at?->toISOString(),
            'received_at' => $line->received_at?->toISOString(),
        ];
    }

    private function state(QuoteLine $line): string
    {
        if ($line->received_at !== null) {
            return 'received';
        }

        return $line->ordered_at !== null ? 'ordered' : 'to_order';
    }
}
