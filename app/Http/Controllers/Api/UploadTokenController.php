<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UploadToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UploadTokenController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'context_type' => 'required|in:message,message_reply',
            'context_id' => 'nullable|integer',
        ]);

        $token = UploadToken::generate(
            $validated['context_type'],
            $validated['context_id'] ?? null
        );

        return response()->json([
            'token' => $token->token,
            'url' => url('/upload/'.$token->token),
            'expires_at' => $token->expires_at->toIso8601String(),
            'max_uses' => $token->max_uses,
        ]);
    }

    public function photos(string $token): JsonResponse
    {
        $uploadToken = UploadToken::where('token', $token)->first();

        if (! $uploadToken) {
            return response()->json(['error' => 'Token invalide'], 404);
        }

        $photos = $this->getPhotosForContext($uploadToken);

        return response()->json([
            'photos' => $photos,
            'remaining_uses' => $uploadToken->remainingUses(),
            'is_valid' => $uploadToken->isValid(),
        ]);
    }

    private function getPhotosForContext(UploadToken $uploadToken): array
    {
        if (! $uploadToken->context_id) {
            return [];
        }

        $modelClass = $uploadToken->context_type === 'message'
            ? \App\Models\Message::class
            : \App\Models\MessageReply::class;

        $model = $modelClass::find($uploadToken->context_id);

        if (! $model) {
            return [];
        }

        return $model->getMedia('photos')->map(fn ($media) => [
            'id' => $media->id,
            'url' => $media->getUrl(),
            'thumb_url' => $media->getUrl('thumb'),
            'name' => $media->file_name,
            'size' => $media->size,
        ])->toArray();
    }
}
