<?php

namespace App\Http\Controllers\Api;

use App\Enums\WorkMode;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\StoreReplyRequest;
use App\Models\Message;
use App\Models\MessageReply;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $mode = $request->input('mode', 'comptoir');

        $messages = Message::forMode($mode)
            ->with('replies')
            ->get()
            ->map(fn (Message $message) => $this->formatMessage($message));

        return response()->json($messages);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $mode = $request->input('mode', 'comptoir');
        $count = Message::unreadCountForMode($mode);
        $byCategory = Message::unreadCountByCategoryForMode($mode);

        return response()->json([
            'count' => $count,
            'by_category' => $byCategory,
        ]);
    }

    public function store(StoreMessageRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $message = Message::create([
            'author_mode' => $validated['author_mode'],
            'recipient_mode' => $validated['recipient_mode'] ?? null,
            'category' => $validated['category'],
            'contact_name' => $validated['contact_name'] ?? null,
            'contact_phone' => $validated['contact_phone'] ?? null,
            'contact_email' => $validated['contact_email'] ?? null,
            'content' => $validated['content'],
            'status' => 'ouvert',
        ]);

        $message->load('replies');

        // TODO: Broadcast MessageCreated event

        return response()->json($this->formatMessage($message), 201);
    }

    public function show(Message $message): JsonResponse
    {
        $message->load('replies');

        return response()->json($this->formatMessage($message));
    }

    public function markAsRead(Message $message): JsonResponse
    {
        $message->markAsRead();

        // TODO: Broadcast MessageRead event

        return response()->json([
            'success' => true,
            'read_at' => $message->read_at->toISOString(),
        ]);
    }

    public function markAsResolved(Message $message): JsonResponse
    {
        $message->markAsResolved();

        // TODO: Broadcast MessageResolved event

        return response()->json([
            'success' => true,
            'resolved_at' => $message->resolved_at->toISOString(),
        ]);
    }

    public function reopen(Message $message): JsonResponse
    {
        $message->reopen();

        return response()->json([
            'success' => true,
        ]);
    }

    public function destroy(Message $message): JsonResponse
    {
        $message->delete();

        return response()->json(null, 204);
    }

    public function storeReply(StoreReplyRequest $request, Message $message): JsonResponse
    {
        $validated = $request->validated();

        $reply = $message->replies()->create([
            'author_mode' => $validated['author_mode'],
            'recipient_mode' => $validated['recipient_mode'] ?? null,
            'content' => $validated['content'],
        ]);

        // TODO: Broadcast ReplyCreated event

        return response()->json($this->formatReply($reply), 201);
    }

    public function markReplyAsRead(MessageReply $reply): JsonResponse
    {
        $reply->markAsRead();

        // TODO: Broadcast ReplyRead event

        return response()->json([
            'success' => true,
            'read_at' => $reply->read_at->toISOString(),
        ]);
    }

    protected function formatMessage(Message $message): array
    {
        return [
            'id' => $message->id,
            'author_mode' => $message->author_mode->value,
            'author_label' => $message->authorLabel(),
            'recipient_mode' => $message->recipient_mode?->value,
            'recipient_label' => $message->recipientLabel(),
            'category' => $message->category,
            'contact_name' => $message->contact_name,
            'contact_phone' => $message->contact_phone,
            'contact_email' => $message->contact_email,
            'content' => $message->content,
            'status' => $message->status,
            'read_at' => $message->read_at?->toISOString(),
            'resolved_at' => $message->resolved_at?->toISOString(),
            'created_at' => $message->created_at->toISOString(),
            'replies' => $message->replies->map(fn ($reply) => $this->formatReply($reply))->toArray(),
        ];
    }

    protected function formatReply(MessageReply $reply): array
    {
        return [
            'id' => $reply->id,
            'message_id' => $reply->message_id,
            'author_mode' => $reply->author_mode->value,
            'author_label' => $reply->authorLabel(),
            'recipient_mode' => $reply->recipient_mode?->value,
            'recipient_label' => $reply->recipientLabel(),
            'content' => $reply->content,
            'read_at' => $reply->read_at?->toISOString(),
            'created_at' => $reply->created_at->toISOString(),
        ];
    }
}
