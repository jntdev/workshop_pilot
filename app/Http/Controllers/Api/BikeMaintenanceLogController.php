<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bike;
use App\Models\BikeMaintenanceLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BikeMaintenanceLogController extends Controller
{
    public function orderLines(Request $request): JsonResponse
    {
        $includeReceived = $request->boolean('include_received', false);

        $logs = BikeMaintenanceLog::with(['bike.category', 'bike.size'])
            ->where('needs_order', true)
            ->when(! $includeReceived, fn ($q) => $q->whereNull('received_at'))
            ->orderBy('created_at')
            ->get()
            ->map(fn (BikeMaintenanceLog $log) => [
                'id' => $log->id,
                'bike_id' => $log->bike_id,
                'bike_name' => $log->bike->name,
                'bike_type' => $log->bike->type_label,
                'description' => $log->description,
                'reference' => $log->reference,
                'cost' => $log->cost,
                'needs_order' => $log->needs_order,
                'ordered_at' => $log->ordered_at?->toISOString(),
                'received_at' => $log->received_at?->toISOString(),
                'supply_status' => $log->supply_status,
            ]);

        return response()->json($logs);
    }

    public function updateOrderStatus(Request $request, BikeMaintenanceLog $log): JsonResponse
    {
        $request->validate([
            'mark_as_ordered' => ['boolean'],
            'mark_as_received' => ['boolean'],
            'unmark' => ['boolean'],
        ]);

        if ($request->boolean('mark_as_ordered')) {
            $log->update(['ordered_at' => now()]);
        } elseif ($request->boolean('mark_as_received')) {
            $log->update([
                'ordered_at' => $log->ordered_at ?? now(),
                'received_at' => now(),
            ]);
        } elseif ($request->boolean('unmark')) {
            abort_if($log->received_at !== null, 422, 'Impossible de revenir en arrière sur une pièce déjà reçue.');
            $log->update(['ordered_at' => null]);
        }

        return response()->json([
            'id' => $log->id,
            'ordered_at' => $log->fresh()->ordered_at?->toISOString(),
            'received_at' => $log->fresh()->received_at?->toISOString(),
            'supply_status' => $log->fresh()->supply_status,
        ]);
    }

    public function store(Request $request, Bike $bike): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:500'],
            'article_id' => ['nullable', 'integer', 'exists:articles,id'],
            'reference' => ['nullable', 'string', 'max:100'],
            'cost' => ['nullable', 'integer', 'min:0'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'in:todo,done'],
            'needs_order' => ['boolean'],
        ]);

        $log = $bike->maintenanceLogs()->create($validated);

        return response()->json($log->fresh(), 201);
    }

    public function update(Request $request, Bike $bike, BikeMaintenanceLog $log): JsonResponse
    {
        abort_if($log->bike_id !== $bike->id, 404);

        $validated = $request->validate([
            'date' => ['sometimes', 'date'],
            'description' => ['sometimes', 'string', 'max:500'],
            'article_id' => ['nullable', 'integer', 'exists:articles,id'],
            'reference' => ['nullable', 'string', 'max:100'],
            'cost' => ['nullable', 'integer', 'min:0'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'status' => ['sometimes', 'in:todo,done'],
            'needs_order' => ['boolean'],
        ]);

        $log->update($validated);

        return response()->json($log->fresh());
    }

    public function destroy(Bike $bike, BikeMaintenanceLog $log): JsonResponse
    {
        abort_if($log->bike_id !== $bike->id, 404);

        $log->delete();

        return response()->json(null, 204);
    }
}
