<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bike;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BikeController extends Controller
{
    public function index(): JsonResponse
    {
        $bikes = Bike::with(['category', 'size'])->ordered()->get();

        return response()->json([
            'bikes' => $bikes,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bike_category_id' => 'required|exists:bike_categories,id',
            'bike_size_id' => 'required|exists:bike_sizes,id',
            'frame_type' => 'required|in:b,h',
            'model' => 'nullable|string|max:50',
            'battery_type' => 'nullable|in:rack,gourde,rail',
            'name' => 'required|string|max:100',
            'status' => 'required|in:OK,HS',
            'notes' => 'nullable|string',
        ]);

        // Déterminer le sort_order (à la fin du groupe)
        $maxOrder = Bike::where('bike_category_id', $validated['bike_category_id'])
            ->where('bike_size_id', $validated['bike_size_id'])
            ->where('frame_type', $validated['frame_type'])
            ->max('sort_order') ?? -1;
        $validated['sort_order'] = $maxOrder + 1;

        $bike = Bike::create($validated);
        $bike->load(['category', 'size']);

        return response()->json($bike, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $bike = Bike::findOrFail($id);

        $validated = $request->validate([
            'bike_category_id' => 'sometimes|exists:bike_categories,id',
            'bike_size_id' => 'sometimes|exists:bike_sizes,id',
            'frame_type' => 'sometimes|in:b,h',
            'model' => 'nullable|string|max:50',
            'battery_type' => 'nullable|in:rack,gourde,rail',
            'name' => 'sometimes|string|max:100',
            'status' => 'sometimes|in:OK,HS',
            'notes' => 'nullable|string',
        ]);

        $bike->update($validated);
        $bike->load(['category', 'size']);

        return response()->json($bike);
    }

    public function destroy(int $id): JsonResponse
    {
        $bike = Bike::findOrFail($id);
        $bike->delete();

        return response()->json(['message' => 'Vélo supprimé']);
    }

    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bikes' => 'required|array',
            'bikes.*.id' => 'required|exists:bikes,id',
            'bikes.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['bikes'] as $bikeData) {
            Bike::where('id', $bikeData['id'])->update(['sort_order' => $bikeData['sort_order']]);
        }

        return response()->json(['message' => 'Ordre mis à jour']);
    }
}
