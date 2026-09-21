<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuoteCommentRequest;
use App\Models\Quote;
use App\Models\QuoteComment;
use Illuminate\Http\JsonResponse;

class QuoteCommentController extends Controller
{
    public function index(Quote $quote): JsonResponse
    {
        return response()->json([
            'is_resolved' => ! $quote->hasOpenComments(),
            'comments' => $quote->comments->map(fn (QuoteComment $comment) => $this->formatComment($comment)),
        ]);
    }

    public function store(StoreQuoteCommentRequest $request, Quote $quote): JsonResponse
    {
        $validated = $request->validated();

        $comment = $quote->comments()->create([
            'recipient_label' => $validated['recipient_label'],
            'content' => $validated['content'],
        ]);

        return response()->json($this->formatComment($comment), 201);
    }

    public function resolve(Quote $quote): JsonResponse
    {
        $quote->markCommentsAsResolved();

        return response()->json(['success' => true]);
    }

    public function reopen(Quote $quote): JsonResponse
    {
        $quote->reopenComments();

        return response()->json(['success' => true]);
    }

    public function destroy(QuoteComment $comment): JsonResponse
    {
        $comment->delete();

        return response()->json(null, 204);
    }

    protected function formatComment(QuoteComment $comment): array
    {
        return [
            'id' => $comment->id,
            'quote_id' => $comment->quote_id,
            'recipient_label' => $comment->recipient_label->value,
            'recipient_display' => $comment->recipient_label->label(),
            'content' => $comment->content,
            'created_at' => $comment->created_at->toISOString(),
        ];
    }
}
