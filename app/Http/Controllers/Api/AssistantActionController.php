<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssistantAction;
use App\Services\Assistant\Actions\AssistantActionException;
use App\Services\Assistant\Actions\AssistantActionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Confirmer, annuler ou défaire une action proposée par l'assistant : côté utilisateur connecté.
 * Seul l'utilisateur qui a posé la question peut agir sur ses propositions.
 */
class AssistantActionController extends Controller
{
    public function __construct(private readonly AssistantActionService $service) {}

    public function confirm(Request $request, AssistantAction $assistantAction): JsonResponse
    {
        $this->authorizeOwner($request, $assistantAction);

        return $this->respond($assistantAction, fn () => $this->service->confirm($assistantAction, $request->user()));
    }

    public function cancel(Request $request, AssistantAction $assistantAction): JsonResponse
    {
        $this->authorizeOwner($request, $assistantAction);

        return $this->respond($assistantAction, fn () => $this->service->cancel($assistantAction));
    }

    public function undo(Request $request, AssistantAction $assistantAction): JsonResponse
    {
        $this->authorizeOwner($request, $assistantAction);

        return $this->respond($assistantAction, fn () => $this->service->undo($assistantAction, $request->user()));
    }

    private function authorizeOwner(Request $request, AssistantAction $action): void
    {
        abort_unless($action->user_id !== null && $action->user_id === $request->user()->id, 404);
    }

    /**
     * @param  callable(): AssistantAction  $operation
     */
    private function respond(AssistantAction $action, callable $operation): JsonResponse
    {
        try {
            $updated = $operation();
        } catch (AssistantActionException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'action' => $this->service->present($action->refresh()->load('confirmer')),
            ], 422);
        }

        return response()->json($this->service->present($updated->load('confirmer')));
    }
}
