<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BikeSize;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BikeSizeController extends Controller
{
    public function index(): JsonResponse
    {
        $sizes = BikeSize::ordered()->get();

        return response()->json([
            'sizes' => $sizes,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:10|unique:bike_sizes,name',
            'color' => 'required|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if (! isset($validated['sort_order'])) {
            $validated['sort_order'] = (BikeSize::max('sort_order') ?? -1) + 1;
        }

        $size = BikeSize::create($validated);

        return response()->json($size, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $size = BikeSize::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:10|unique:bike_sizes,name,'.$id,
            'color' => 'sometimes|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'sort_order' => 'sometimes|integer|min:0',
        ]);

        $size->update($validated);

        return response()->json($size);
    }

    public function destroy(int $id): JsonResponse
    {
        $size = BikeSize::findOrFail($id);

        if ($size->bikes()->exists()) {
            return response()->json(['message' => 'Impossible de supprimer une taille utilisée par des vélos'], 422);
        }

        $size->delete();

        return response()->json(['message' => 'Taille supprimée']);
    }
}
