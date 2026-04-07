<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\StoreReplyRequest;
use App\Http\Requests\UpdateReplyRequest;
use App\Models\Message;
use App\Models\MessageReply;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $messages = Message::forUser(Auth::id())
            ->with(['author', 'recipient', 'category', 'replies.author', 'replies.recipient', 'media', 'replies.media'])
            ->get()
            ->map(fn (Message $message) => $this->formatMessage($message));

        return response()->json($messages);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $count = Message::unreadCountForUser($userId);
        $byCategory = Message::unreadCountByCategoryForUser($userId);

        return response()->json([
            'count' => $count,
            'by_category' => $byCategory,
        ]);
    }

    public function store(StoreMessageRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $message = Message::create([
            'author_user_id' => Auth::id(),
            'recipient_user_id' => $validated['recipient_user_id'] ?? null,
            'category_id' => $validated['category_id'],
            'contact_name' => $validated['contact_name'] ?? null,
            'contact_phone' => $validated['contact_phone'] ?? null,
            'contact_email' => $validated['contact_email'] ?? null,
            'content' => $validated['content'],
            'status' => 'ouvert',
        ]);

        $message->load(['author', 'recipient', 'category', 'replies.author', 'replies.recipient', 'media', 'replies.media']);

        // TODO: Broadcast MessageCreated event

        return response()->json($this->formatMessage($message), 201);
    }

    public function show(Message $message): JsonResponse
    {
        $message->load(['author', 'recipient', 'category', 'replies.author', 'replies.recipient', 'media', 'replies.media']);

        return response()->json($this->formatMessage($message));
    }

    public function updateCategory(Request $request, Message $message): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:message_categories,id',
        ]);

        $message->update(['category_id' => $validated['category_id']]);
        $message->load('category');

        return response()->json([
            'success' => true,
            'category_id' => $message->category_id,
            'category' => $message->category ? [
                'id' => $message->category->id,
                'slug' => $message->category->slug,
                'label' => $message->category->label,
                'color' => $message->category->color,
            ] : null,
        ]);
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
            'author_user_id' => Auth::id(),
            'recipient_user_id' => $validated['recipient_user_id'] ?? null,
            'content' => $validated['content'],
        ]);

        $reply->load(['author', 'recipient', 'media']);

        // TODO: Broadcast ReplyCreated event

        return response()->json($this->formatReply($reply), 201);
    }

    public function updateReply(UpdateReplyRequest $request, MessageReply $reply): JsonResponse
    {
        if ($reply->author_user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $reply->update(['content' => $request->validated()['content']]);
        $reply->load(['author', 'recipient', 'media']);

        return response()->json($this->formatReply($reply));
    }

    public function destroyReply(MessageReply $reply): JsonResponse
    {
        if ($reply->author_user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $reply->delete();

        return response()->json(null, 204);
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
            'author_user_id' => $message->author_user_id,
            'author_label' => $message->authorLabel(),
            'recipient_user_id' => $message->recipient_user_id,
            'recipient_label' => $message->recipientLabel(),
            'category_id' => $message->category_id,
            'category' => $message->category ? [
                'id' => $message->category->id,
                'slug' => $message->category->slug,
                'label' => $message->category->label,
                'color' => $message->category->color,
            ] : null,
            'contact_name' => $message->contact_name,
            'contact_phone' => $message->contact_phone,
            'contact_email' => $message->contact_email,
            'content' => $message->content,
            'status' => $message->status,
            'read_at' => $message->read_at?->toISOString(),
            'resolved_at' => $message->resolved_at?->toISOString(),
            'created_at' => $message->created_at->toISOString(),
            'replies' => $message->replies->map(fn ($reply) => $this->formatReply($reply))->toArray(),
            'photos' => $this->formatPhotos($message),
        ];
    }

    protected function formatReply(MessageReply $reply): array
    {
        return [
            'id' => $reply->id,
            'message_id' => $reply->message_id,
            'author_user_id' => $reply->author_user_id,
            'author_label' => $reply->authorLabel(),
            'recipient_user_id' => $reply->recipient_user_id,
            'recipient_label' => $reply->recipientLabel(),
            'content' => $reply->content,
            'read_at' => $reply->read_at?->toISOString(),
            'created_at' => $reply->created_at->toISOString(),
            'photos' => $this->formatPhotos($reply),
        ];
    }

    /**
     * @param  Message|MessageReply  $model
     */
    protected function formatPhotos($model): array
    {
        return $model->getMedia('photos')->map(fn ($media) => [
            'id' => $media->id,
            'url' => $media->getUrl(),
            'thumb_url' => $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl(),
            'name' => $media->file_name,
            'size' => $media->size,
        ])->toArray();
    }
}
