<?php

namespace App\Http\Controllers\Api;

use App\Enums\AssistantMessageStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\AnswerAssistantMessageRequest;
use App\Http\Requests\FailAssistantMessageRequest;
use App\Models\AssistantMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Relais du Mac : relève la prochaine question en attente et renvoie la réponse.
 * Protégé par un jeton propre (ASSISTANT_RELAY_TOKEN), jamais donné à Claude.
 */
class AssistantRelayController extends Controller
{
    /**
     * Prend la plus ancienne question en attente (une seule à la fois) ou répond 204.
     */
    public function claim(): JsonResponse|Response
    {
        Cache::put(AssistantChatController::RELAY_HEARTBEAT_CACHE_KEY, now()->toISOString(), now()->addMinutes(10));

        AssistantMessage::failStale();

        $message = DB::transaction(function (): ?AssistantMessage {
            $next = AssistantMessage::pending()->orderBy('id')->lockForUpdate()->first();

            $next?->update([
                'status' => AssistantMessageStatus::Processing,
                'claimed_at' => now(),
            ]);

            return $next;
        });

        if ($message === null) {
            return response()->noContent();
        }

        $previousSessionId = AssistantMessage::query()
            ->where('conversation_id', $message->conversation_id)
            ->where('id', '<', $message->id)
            ->whereNotNull('claude_session_id')
            ->orderByDesc('id')
            ->value('claude_session_id');

        return response()->json([
            'id' => $message->id,
            'conversation_id' => $message->conversation_id,
            'question' => $message->question,
            'session_id' => $previousSessionId,
        ]);
    }

    public function answer(AnswerAssistantMessageRequest $request, AssistantMessage $message): JsonResponse
    {
        if ($message->status !== AssistantMessageStatus::Processing) {
            return response()->json(['message' => 'Cette question n\'est pas en cours de traitement.'], 409);
        }

        $message->update([
            'status' => AssistantMessageStatus::Answered,
            'answer' => $request->validated('answer'),
            'claude_session_id' => $request->validated('session_id'),
            'consultations' => $request->validated('consultations'),
            'answered_at' => now(),
        ]);

        return response()->json(['id' => $message->id, 'status' => $message->status->value]);
    }

    public function fail(FailAssistantMessageRequest $request, AssistantMessage $message): JsonResponse
    {
        if ($message->status !== AssistantMessageStatus::Processing) {
            return response()->json(['message' => 'Cette question n\'est pas en cours de traitement.'], 409);
        }

        $message->update([
            'status' => AssistantMessageStatus::Failed,
            'error' => $request->validated('error'),
            'answered_at' => now(),
        ]);

        return response()->json(['id' => $message->id, 'status' => $message->status->value]);
    }
}
