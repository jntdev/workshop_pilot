<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bike;
use App\Models\BikeCategory;
use App\Services\Agenda\AgendaVersioner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BikeController extends Controller
{
    public function __construct(
        private AgendaVersioner $agendaVersioner
    ) {}

    public function index(): JsonResponse
    {
        $bikes = Bike::with(['category', 'size'])->ordered()->get();

        return response()->json([
            'bikes' => $bikes,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $category = BikeCategory::findOrFail($request->input('bike_category_id'));

        $sizeRule = $category->has_size ? 'required|exists:bike_sizes,id' : 'nullable';
        $frameRule = $category->has_frame_type ? 'required|in:b,h' : 'nullable';

        $validated = $request->validate([
            'bike_category_id' => 'required|exists:bike_categories,id',
            'bike_size_id' => $sizeRule,
            'frame_type' => $frameRule,
            'model' => 'nullable|string|max:50',
            'battery_type' => 'nullable|in:rack,gourde,rail',
            'name' => 'required|string|max:100',
            'status' => 'required|in:OK,HS',
            'notes' => 'nullable|string',
        ]);

        if (! $category->has_size) {
            $validated['bike_size_id'] = null;
        }
        if (! $category->has_frame_type) {
            $validated['frame_type'] = null;
        }

        $query = Bike::where('bike_category_id', $validated['bike_category_id']);
        if ($validated['bike_size_id']) {
            $query->where('bike_size_id', $validated['bike_size_id']);
        } else {
            $query->whereNull('bike_size_id');
        }
        if ($validated['frame_type']) {
            $query->where('frame_type', $validated['frame_type']);
        } else {
            $query->whereNull('frame_type');
        }
        $validated['sort_order'] = ($query->max('sort_order') ?? -1) + 1;

        $bike = Bike::create($validated);
        $bike->load(['category', 'size']);
        $bike->syncBikeType();

        $this->agendaVersioner->bump();

        return response()->json($bike, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $bike = Bike::with(['category', 'size'])->findOrFail($id);

        $oldContext = [
            'type_id' => $bike->bike_type_id,
            'category_id' => $bike->bike_category_id,
            'size_id' => $bike->bike_size_id,
            'frame_type' => $bike->frame_type,
        ];

        $validated = $request->validate([
            'bike_category_id' => 'sometimes|exists:bike_categories,id',
            'bike_size_id' => 'nullable|exists:bike_sizes,id',
            'frame_type' => 'nullable|in:b,h',
            'model' => 'nullable|string|max:50',
            'battery_type' => 'nullable|in:rack,gourde,rail',
            'name' => 'sometimes|string|max:100',
            'status' => 'sometimes|in:OK,HS',
            'notes' => 'nullable|string',
        ]);

        $bike->update($validated);
        $bike->load(['category', 'size']);

        // Sync l'ancien type (décrémente ou supprime) puis le nouveau (crée ou incrémente)
        Bike::syncBikeTypeAfterDelete($oldContext);
        $bike->syncBikeType();

        $this->agendaVersioner->bump();

        return response()->json($bike);
    }

    public function destroy(int $id): JsonResponse
    {
        $bike = Bike::with(['category', 'size'])->findOrFail($id);

        $context = [
            'type_id' => $bike->bike_type_id,
            'category_id' => $bike->bike_category_id,
            'size_id' => $bike->bike_size_id,
            'frame_type' => $bike->frame_type,
        ];

        $bike->delete();

        Bike::syncBikeTypeAfterDelete($context);

        $this->agendaVersioner->bump();

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

        $this->agendaVersioner->bump();

        return response()->json(['message' => 'Ordre mis à jour']);
    }
}
