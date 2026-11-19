<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssistantMessage;
use Illuminate\Http\JsonResponse;

/**
 * Journal de l'assistant : toutes les questions, réponses et consultations, du plus récent au plus ancien.
 */
class AssistantJournalController extends Controller
{
    private const PER_PAGE = 30;

    public function __invoke(): JsonResponse
    {
        $page = AssistantMessage::query()
            ->with('user:id,name')
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
