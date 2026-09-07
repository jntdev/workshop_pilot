<?php

namespace App\Http\Controllers\Api;

use App\Enums\StockMovementType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreStockMovementRequest;
use App\Models\Article;
use App\Models\StockMovement;
use Illuminate\Http\JsonResponse;

class StockMovementController extends Controller
{
    public function index(int $articleId): JsonResponse
    {
        $article = Article::findOrFail($articleId);

        $movements = $article->stockMovements()
            ->latest()
            ->get()
            ->map(fn (StockMovement $m) => [
                'id' => $m->id,
                'quantity' => (float) $m->quantity,
                'type' => $m->type->value,
                'type_label' => $m->type->label(),
                'is_manual' => $m->type->isManual(),
                'source_type' => $m->source_type,
                'source_id' => $m->source_id,
                'unit_price_ht' => $m->unit_price_ht,
                'note' => $m->note,
                'created_at' => $m->created_at,
            ]);

        return response()->json([
            'movements' => $movements,
            'stock_quantity' => $article->stock_quantity,
        ]);
    }

    public function store(StoreStockMovementRequest $request, int $articleId): JsonResponse
    {
        $article = Article::findOrFail($articleId);
        $validated = $request->validated();

        $type = StockMovementType::from($validated['type']);
        $quantity = $type === StockMovementType::ManualOut
            ? -abs($validated['quantity'])
            : abs($validated['quantity']);

        $movement = $article->stockMovements()->create([
            'quantity' => $quantity,
            'type' => $type,
            'unit_price_ht' => $validated['unit_price_ht'] ?? null,
            'note' => $validated['note'] ?? null,
        ]);

        return response()->json([
            'movement' => [
                'id' => $movement->id,
                'quantity' => (float) $movement->quantity,
                'type' => $movement->type->value,
                'unit_price_ht' => $movement->unit_price_ht,
                'note' => $movement->note,
                'created_at' => $movement->created_at,
            ],
            'stock_quantity' => $article->fresh()->stock_quantity,
        ], 201);
    }

    public function destroy(int $id): JsonResponse
    {
        $movement = StockMovement::findOrFail($id);

        if (! $movement->type->isManual()) {
            return response()->json(['message' => 'Seuls les mouvements manuels peuvent être supprimés.'], 422);
        }

        $movement->delete();

        return response()->json([
            'message' => 'Mouvement supprimé.',
            'stock_quantity' => $movement->article->stock_quantity,
        ]);
    }
}
