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
        $bikes = Bike::ordered()->get();

        return response()->json([
            'bikes' => $bikes,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => 'required|in:VAE,VTC',
            'size' => 'required|in:S,M,L,XL',
            'frame_type' => 'required|in:b,h',
            'name' => 'required|string|max:100',
            'status' => 'required|in:OK,HS',
            'notes' => 'nullable|string',
        ]);

        // Déterminer le sort_order (à la fin du groupe)
        $maxOrder = Bike::where('category', $validated['category'])
            ->where('size', $validated['size'])
            ->where('frame_type', $validated['frame_type'])
            ->max('sort_order') ?? -1;
        $validated['sort_order'] = $maxOrder + 1;

        $bike = Bike::create($validated);

        return response()->json($bike, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $bike = Bike::findOrFail($id);

        $validated = $request->validate([
            'category' => 'sometimes|in:VAE,VTC',
            'size' => 'sometimes|in:S,M,L,XL',
            'frame_type' => 'sometimes|in:b,h',
            'name' => 'sometimes|string|max:100',
            'status' => 'sometimes|in:OK,HS',
            'notes' => 'nullable|string',
        ]);

        $bike->update($validated);

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
