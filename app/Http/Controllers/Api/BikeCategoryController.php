<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BikeCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BikeCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = BikeCategory::ordered()->get();

        return response()->json([
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:bike_categories,name',
            'color' => 'required|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'has_battery' => 'required|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if (! isset($validated['sort_order'])) {
            $validated['sort_order'] = (BikeCategory::max('sort_order') ?? -1) + 1;
        }

        $category = BikeCategory::create($validated);

        return response()->json($category, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $category = BikeCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:50|unique:bike_categories,name,'.$id,
            'color' => 'sometimes|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'has_battery' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer|min:0',
        ]);

        $category->update($validated);

        return response()->json($category);
    }

    public function destroy(int $id): JsonResponse
    {
        $category = BikeCategory::findOrFail($id);

        if ($category->bikes()->exists()) {
            return response()->json(['message' => 'Impossible de supprimer une catégorie utilisée par des vélos'], 422);
        }

        $category->delete();

        return response()->json(['message' => 'Catégorie supprimée']);
    }
}
