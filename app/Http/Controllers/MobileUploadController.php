<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageReply;
use App\Models\UploadToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MobileUploadController extends Controller
{
    public function show(string $token): View|JsonResponse
    {
        $uploadToken = UploadToken::where('token', $token)->first();

        if (! $uploadToken) {
            return view('upload.expired', ['reason' => 'invalid']);
        }

        if (! $uploadToken->isValid()) {
            $reason = $uploadToken->expires_at->isPast() ? 'expired' : 'limit_reached';

            return view('upload.expired', ['reason' => $reason]);
        }

        return view('upload.mobile', [
            'token' => $token,
            'remainingUses' => $uploadToken->remainingUses(),
            'expiresAt' => $uploadToken->expires_at->toIso8601String(),
        ]);
    }

    public function upload(Request $request, string $token): JsonResponse
    {
        $uploadToken = UploadToken::consume($token);

        if (! $uploadToken) {
            return response()->json([
                'error' => 'Token invalide ou expiré',
            ], 400);
        }

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,webp,heic|max:5120',
        ]);

        $model = $this->getOrCreateModel($uploadToken);

        if (! $model) {
            return response()->json([
                'error' => 'Impossible de trouver le contexte',
            ], 400);
        }

        $media = $model->addMediaFromRequest('photo')
            ->toMediaCollection('photos');

        return response()->json([
            'success' => true,
            'photo' => [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'thumb_url' => $media->getUrl('thumb'),
                'name' => $media->file_name,
            ],
            'remaining_uses' => $uploadToken->remainingUses(),
        ]);
    }

    private function getOrCreateModel(UploadToken $uploadToken): Message|MessageReply|null
    {
        if ($uploadToken->context_id) {
            $modelClass = $uploadToken->context_type === 'message'
                ? Message::class
                : MessageReply::class;

            return $modelClass::find($uploadToken->context_id);
        }

        return null;
    }
}
