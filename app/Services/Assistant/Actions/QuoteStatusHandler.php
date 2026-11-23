<?php

namespace App\Services\Assistant\Actions;

use App\Enums\AssistantActionStatus;
use App\Enums\AssistantActionType;
use App\Enums\QuoteStatus;
use App\Http\Controllers\Api\QuoteController;
use App\Models\AssistantAction;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Changer le statut d'un devis (À commander, En commande, Validé…).
 * L'exécution passe par QuoteController::updateStatus, comme le bouton du workshop.
 */
class QuoteStatusHandler implements AssistantActionHandler
{
    use CallsExistingControllers;

    public function propose(array $params): ActionProposal
    {
        $validator = Validator::make($params, [
            'quote_id' => ['required', 'integer'],
            'status' => ['required', 'string', Rule::in(array_map(fn (QuoteStatus $status) => $status->value, QuoteStatus::quoteStatuses()))],
        ], [
            'quote_id.required' => 'Il manque le numéro du devis.',
            'quote_id.integer' => 'Le numéro du devis doit être un nombre.',
            'status.required' => 'Il manque le statut voulu.',
            'status.in' => 'Statut inconnu. Statuts possibles : '.implode(', ', array_map(fn (QuoteStatus $status) => $status->value, QuoteStatus::quoteStatuses())).'.',
        ]);

        if ($validator->fails()) {
            throw new AssistantActionException((string) $validator->errors()->first());
        }

        $quote = Quote::query()->with('client')->find((int) $params['quote_id']);

        if ($quote === null) {
            throw new AssistantActionException('Devis introuvable.');
        }

        if ($quote->isInvoice()) {
            throw new AssistantActionException('Ce devis est déjà facturé : son statut ne se change pas ici.');
        }

        if ($quote->is_archived) {
            throw new AssistantActionException('Ce devis est archivé : son statut ne se change pas.');
        }

        $target = QuoteStatus::from($params['status']);
        $current = $quote->status;

        if ($current === $target) {
            throw new AssistantActionException("Ce devis est déjà « {$target->label()} ».");
        }

        $warnings = [];

        if ($quote->work_completed_notified && $target !== QuoteStatus::Done) {
            $warnings[] = 'Le client avait été prévenu que les travaux étaient terminés : cette information sera remise à zéro.';
        }

        return new ActionProposal(
            type: AssistantActionType::QuoteStatus,
            subjectType: 'quote',
            subjectId: $quote->id,
            payload: ['status' => $target->value],
            before: [
                'status' => $current?->value,
                'work_completed_notified' => (bool) $quote->work_completed_notified,
                'work_completed_notified_at' => $quote->work_completed_notified_at?->format('Y-m-d'),
            ],
            summary: sprintf(
                'Devis %s (%s) : « %s » → « %s »',
                $quote->reference,
                $this->describe($quote),
                $current?->label() ?? 'sans statut',
                $target->label(),
            ),
            warnings: $warnings,
        );
    }

    public function execute(AssistantAction $action): array
    {
        $quote = Quote::query()->find($action->subject_id);

        if ($quote === null) {
            throw new AssistantActionException('Devis introuvable.');
        }

        if ($quote->status?->value !== ($action->before['status'] ?? null)) {
            $now = $quote->status?->label() ?? 'sans statut';

            throw new AssistantActionException("Le devis est passé à « {$now} » depuis la proposition. Redemande à l'assistant.");
        }

        $this->callController(fn () => app(QuoteController::class)->updateStatus(
            Request::create('/', 'PATCH', ['status' => $action->payload['status']]),
            $quote,
        ));

        return $this->state($quote->refresh());
    }

    public function canUndo(AssistantAction $action): bool
    {
        if ($action->status !== AssistantActionStatus::Executed) {
            return false;
        }

        $quote = Quote::query()->find($action->subject_id);

        return $quote !== null && $quote->status?->value === ($action->payload['status'] ?? null);
    }

    public function undo(AssistantAction $action): array
    {
        $quote = Quote::query()->find($action->subject_id);

        if ($quote === null || $quote->status?->value !== ($action->payload['status'] ?? null)) {
            throw new AssistantActionException('Le devis a encore changé depuis : impossible de défaire sans risque.');
        }

        $quote->update([
            'status' => $action->before['status'],
            'work_completed_notified' => (bool) ($action->before['work_completed_notified'] ?? false),
            'work_completed_notified_at' => $action->before['work_completed_notified_at'] ?? null,
        ]);

        return $this->state($quote->refresh());
    }

    public function link(AssistantAction $action): ?string
    {
        return '/atelier/devis/'.$action->subject_id;
    }

    public function effect(AssistantAction $action): ?array
    {
        if (! in_array($action->status, [AssistantActionStatus::Executed, AssistantActionStatus::Undone], true) || $action->after === null) {
            return null;
        }

        $status = $action->after['status'] ?? null;

        return [
            'type' => 'quote_status',
            'quote_id' => $action->subject_id,
            'status' => $status,
            'status_label' => $status ? QuoteStatus::tryFrom($status)?->label() : null,
            'work_completed_notified' => (bool) ($action->after['work_completed_notified'] ?? false),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function state(Quote $quote): array
    {
        return [
            'status' => $quote->status?->value,
            'work_completed_notified' => (bool) $quote->work_completed_notified,
        ];
    }

    private function describe(Quote $quote): string
    {
        $client = $quote->client ? trim($quote->client->prenom.' '.$quote->client->nom) : 'client inconnu';

        return $quote->bike_description ? "{$client}, {$quote->bike_description}" : $client;
    }
}
