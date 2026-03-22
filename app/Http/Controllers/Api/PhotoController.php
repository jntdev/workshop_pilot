<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PhotoController extends Controller
{
    public function destroy(int $id): JsonResponse
    {
        $media = Media::find($id);

        if (! $media) {
            return response()->json(['error' => 'Photo non trouvée'], 404);
        }

        $media->delete();

        return response()->json(['success' => true]);
    }
}
