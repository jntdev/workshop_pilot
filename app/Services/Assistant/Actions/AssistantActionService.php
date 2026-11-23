<?php

namespace App\Services\Assistant\Actions;

use App\Enums\AssistantActionStatus;
use App\Enums\AssistantActionType;
use App\Enums\AssistantMessageStatus;
use App\Models\AssistantAction;
use App\Models\AssistantMessage;
use App\Models\User;
use Throwable;

/**
 * Cycle de vie d'une action de l'assistant : proposée → confirmée (exécutée) → éventuellement défaite.
 * L'assistant ne peut que proposer ; seul un utilisateur connecté, celui qui a posé la question, confirme.
 */
class AssistantActionService
{
    public function __construct(
        private readonly QuoteStatusHandler $quoteStatus,
        private readonly OrderLineHandler $orderLine,
        private readonly AppointmentMoveHandler $appointmentMove,
    ) {}

    /**
     * @param  array<string, mixed>  $params
     *
     * @throws AssistantActionException
     */
    public function propose(AssistantMessage $message, AssistantActionType $type, array $params): AssistantAction
    {
        if ($message->status !== AssistantMessageStatus::Processing) {
            throw new AssistantActionException('Cette question n\'est plus en cours de traitement : aucune proposition possible.');
        }

        $max = (int) config('assistant.max_actions_per_message');

        if ($message->actions()->count() >= $max) {
            throw new AssistantActionException("Trop de propositions pour une seule question ({$max} au plus).");
        }

        $proposal = $this->handler($type)->propose($params);

        // Une nouvelle proposition sur le même élément remplace celle de la même question restée sans réponse.
        $message->actions()
            ->where('status', AssistantActionStatus::Proposed->value)
            ->where('subject_type', $proposal->subjectType)
            ->where('subject_id', $proposal->subjectId)
            ->update(['status' => AssistantActionStatus::Cancelled->value]);

        return AssistantAction::create([
            'assistant_message_id' => $message->id,
            'user_id' => $message->user_id,
            'conversation_id' => $message->conversation_id,
            'type' => $proposal->type,
            'subject_type' => $proposal->subjectType,
            'subject_id' => $proposal->subjectId,
            'payload' => $proposal->payload,
            'before' => $proposal->before,
            'summary' => $proposal->summary,
            'warnings' => $proposal->warnings,
            'status' => AssistantActionStatus::Proposed,
            'expires_at' => now()->addMinutes((int) config('assistant.action_ttl_minutes')),
        ]);
    }

    /**
     * @throws AssistantActionException
     */
    public function confirm(AssistantAction $action, User $user): AssistantAction
    {
        // Prise atomique : un double clic ou deux onglets ne peuvent pas exécuter deux fois.
        $claimed = AssistantAction::query()
            ->whereKey($action->id)
            ->where('status', AssistantActionStatus::Proposed->value)
            ->where('expires_at', '>=', now())
            ->update(['status' => AssistantActionStatus::Confirming->value]);

        if ($claimed === 0) {
            $action->refresh();

            if ($action->status === AssistantActionStatus::Proposed) {
                $action->update(['status' => AssistantActionStatus::Expired]);
            }

            throw new AssistantActionException(match ($action->status) {
                AssistantActionStatus::Expired => 'Cette proposition a expiré. Redemande à l\'assistant.',
                AssistantActionStatus::Executed => 'Cette action a déjà été faite.',
                AssistantActionStatus::Cancelled => 'Cette proposition a été annulée.',
                default => 'Cette proposition n\'est plus valable.',
            });
        }

        $action->refresh();

        try {
            $after = $this->handler($action->type)->execute($action);
        } catch (AssistantActionException $exception) {
            $action->update(['status' => AssistantActionStatus::Failed, 'error' => mb_substr($exception->getMessage(), 0, 250)]);

            throw $exception;
        } catch (Throwable $throwable) {
            report($throwable);
            $message = 'Erreur inattendue pendant l\'action : vérifie l\'état dans le workshop.';
            $action->update(['status' => AssistantActionStatus::Failed, 'error' => $message]);

            throw new AssistantActionException($message, 0, $throwable);
        }

        $action->update([
            'status' => AssistantActionStatus::Executed,
            'after' => $after,
            'error' => null,
            'confirmed_at' => now(),
            'confirmed_by' => $user->id,
        ]);

        return $action->refresh();
    }

    /**
     * @throws AssistantActionException
     */
    public function cancel(AssistantAction $action): AssistantAction
    {
        $cancelled = AssistantAction::query()
            ->whereKey($action->id)
            ->where('status', AssistantActionStatus::Proposed->value)
            ->update(['status' => AssistantActionStatus::Cancelled->value]);

        if ($cancelled === 0) {
            throw new AssistantActionException('Cette proposition ne peut plus être annulée.');
        }

        return $action->refresh();
    }

    /**
     * @throws AssistantActionException
     */
    public function undo(AssistantAction $action, User $user): AssistantAction
    {
        if ($action->status !== AssistantActionStatus::Executed) {
            throw new AssistantActionException('Seule une action faite peut être défaite.');
        }

        $handler = $this->handler($action->type);

        if (! $handler->canUndo($action)) {
            throw new AssistantActionException('Cette action ne peut plus être défaite (l\'élément a changé depuis, ou elle est définitive).');
        }

        $after = $handler->undo($action);

        $action->update([
            'status' => AssistantActionStatus::Undone,
            'after' => $after,
            'undone_at' => now(),
        ]);

        return $action->refresh();
    }

    /**
     * Représentation pour l'écran et le journal.
     *
     * @return array<string, mixed>
     */
    public function present(AssistantAction $action): array
    {
        $handler = $this->handler($action->type);

        return [
            'id' => $action->id,
            'type' => $action->type->value,
            'type_label' => $action->type->label(),
            'summary' => $action->summary,
            'warnings' => $action->warnings ?? [],
            'status' => $action->status->value,
            'status_label' => $action->status->label(),
            'error' => $action->error,
            'expires_at' => $action->expires_at->toISOString(),
            'confirmed_at' => $action->confirmed_at?->toISOString(),
            'confirmed_by' => $action->relationLoaded('confirmer') ? $action->confirmer?->name : null,
            'can_undo' => $action->status === AssistantActionStatus::Executed && $handler->canUndo($action),
            'link' => $handler->link($action),
            // Ce qui a changé, au format que les pages ouvertes du workshop appliquent à leur état (voir assistantBus.ts).
            'effect' => $handler->effect($action),
        ];
    }

    private function handler(AssistantActionType $type): AssistantActionHandler
    {
        return match ($type) {
            AssistantActionType::QuoteStatus => $this->quoteStatus,
            AssistantActionType::OrderLine => $this->orderLine,
            AssistantActionType::AppointmentMove => $this->appointmentMove,
        };
    }
}
