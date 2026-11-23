<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssistantAction;
use App\Models\AssistantMessage;
use App\Services\Assistant\Actions\AssistantActionService;
use Illuminate\Http\JsonResponse;

/**
 * Journal de l'assistant : toutes les questions, réponses, consultations et actions proposées
 * (avec qui les a confirmées), du plus récent au plus ancien.
 */
class AssistantJournalController extends Controller
{
    private const PER_PAGE = 30;

    public function __invoke(AssistantActionService $actions): JsonResponse
    {
        AssistantAction::expireStale();

        $page = AssistantMessage::query()
            ->with(['user:id,name', 'actions.confirmer:id,name'])
            ->orderByDesc('id')
            ->paginate(self::PER_PAGE);

        return response()->json([
            'data' => $page->getCollection()->map(fn (AssistantMessage $message) => [
                'id' => $message->id,
                'user' => $message->user?->name,
                'question' => $message->question,
                'status' => $message->status->value,
                'status_label' => $message->status->label(),
                'answer' => $message->answer,
                'error' => $message->error,
                'consultations' => $message->consultations ?? [],
                'actions' => $message->actions->map(fn (AssistantAction $action) => $actions->present($action))->values(),
                'created_at' => $message->created_at->toISOString(),
                'duration_seconds' => $message->claimed_at && $message->answered_at
                    ? $message->claimed_at->diffInSeconds($message->answered_at)
                    : null,
            ])->values(),
            'current_page' => $page->currentPage(),
            'last_page' => $page->lastPage(),
            'total' => $page->total(),
        ]);
    }
}
