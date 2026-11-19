<?php

namespace App\Http\Controllers\Api;

use App\Enums\AssistantActionType;
use App\Http\Controllers\Controller;
use App\Models\AssistantMessage;
use App\Services\Assistant\Actions\AssistantActionException;
use App\Services\Assistant\Actions\AssistantActionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Propositions d'actions de l'assistant (jeton ASSISTANT_PROPOSE_TOKEN, utilisé par l'outil de Claude sur le Mac).
 * Cette route ne modifie aucune donnée du workshop : elle enregistre une proposition que l'utilisateur
 * doit confirmer dans la fenêtre de chat. Elle n'accepte que les questions en cours de traitement.
 */
class AssistantProposeController extends Controller
{
    public function __invoke(Request $request, AssistantActionService $service): JsonResponse
    {
        $validated = $request->validate([
            'assistant_message_id' => ['required', 'integer'],
            'type' => ['required', 'string', Rule::enum(AssistantActionType::class)],
            'params' => ['required', 'array'],
        ]);

        $message = AssistantMessage::query()->find($validated['assistant_message_id']);

        if ($message === null) {
            return response()->json(['message' => 'Question introuvable.'], 404);
        }

        try {
            $action = $service->propose($message, AssistantActionType::from($validated['type']), $validated['params']);
        } catch (AssistantActionException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json([
            'id' => $action->id,
            'summary' => $action->summary,
            'warnings' => $action->warnings ?? [],
            'expires_in_minutes' => (int) config('assistant.action_ttl_minutes'),
        ], 201);
    }
}
