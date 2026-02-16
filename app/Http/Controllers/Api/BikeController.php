<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bike;
use App\Models\BikeType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BikeController extends Controller
{
    public function index(): JsonResponse
    {
        $bikes = Bike::with('bikeType')
            ->ordered()
            ->get();

        $bikeTypes = BikeType::orderBy('category')
            ->orderByRaw("FIELD(size, 'S', 'M', 'L', 'XL')")
            ->orderBy('frame_type')
            ->get();

        return response()->json([
            'bikes' => $bikes,
            'bikeTypes' => $bikeTypes,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bike_type_id' => 'required|exists:bike_types,id',
            'label' => 'required|string|max:255',
            'status' => 'required|in:OK,HS',
            'notes' => 'nullable|string',
        ]);

        // Déterminer le sort_order (à la fin du type)
        $maxOrder = Bike::where('bike_type_id', $validated['bike_type_id'])->max('sort_order') ?? -1;
        $validated['sort_order'] = $maxOrder + 1;

        $bike = Bike::create($validated);

        // Mettre à jour le stock du BikeType
        $this->updateBikeTypeStock($validated['bike_type_id']);

        return response()->json($bike->load('bikeType'), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $bike = Bike::findOrFail($id);

        $validated = $request->validate([
            'bike_type_id' => 'sometimes|exists:bike_types,id',
            'label' => 'sometimes|string|max:255',
            'status' => 'sometimes|in:OK,HS',
            'notes' => 'nullable|string',
        ]);

        $oldBikeTypeId = $bike->bike_type_id;
        $bike->update($validated);

        // Mettre à jour le stock si le type a changé
        if (isset($validated['bike_type_id']) && $validated['bike_type_id'] !== $oldBikeTypeId) {
            $this->updateBikeTypeStock($oldBikeTypeId);
            $this->updateBikeTypeStock($validated['bike_type_id']);
        } else {
            $this->updateBikeTypeStock($bike->bike_type_id);
        }

        return response()->json($bike->load('bikeType'));
    }

    public function destroy(int $id): JsonResponse
    {
        $bike = Bike::findOrFail($id);
        $bikeTypeId = $bike->bike_type_id;

        $bike->delete();

        // Mettre à jour le stock du BikeType
        $this->updateBikeTypeStock($bikeTypeId);

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

    private function updateBikeTypeStock(string $bikeTypeId): void
    {
        $count = Bike::where('bike_type_id', $bikeTypeId)->count();
        BikeType::where('id', $bikeTypeId)->update(['stock' => $count]);
    }
}
