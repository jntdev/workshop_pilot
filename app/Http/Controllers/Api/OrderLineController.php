<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuoteLine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderLineController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'include_received' => 'boolean',
        ]);

        $query = QuoteLine::query()
            ->with(['quote.client'])
            ->where('needs_order', true);

        if (empty($validated['include_received'])) {
            $query->whereNull('received_at');
        }

        $lines = $query
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($lines->map(fn (QuoteLine $line) => [
            'quote_line_id' => $line->id,
            'quote_id' => $line->quote_id,
            'client_id' => $line->quote->client_id,
            'client_nom_complet' => trim($line->quote->client->prenom.' '.$line->quote->client->nom),
            'bike_description' => $line->quote->bike_description,
            'line_title' => $line->title,
            'line_reference' => $line->reference,
            'quantity' => $line->quantity,
            'needs_order' => $line->needs_order,
            'ordered_at' => $line->ordered_at?->toISOString(),
            'received_at' => $line->received_at?->toISOString(),
            'supply_status' => $line->supply_status,
        ])->values());
    }

    public function updateOrderStatus(Request $request, QuoteLine $quoteLine): JsonResponse
    {
        $validated = $request->validate([
            'mark_as_ordered' => 'boolean',
            'mark_as_received' => 'boolean',
            'unmark' => 'boolean',
        ]);

        $markAsOrdered = $validated['mark_as_ordered'] ?? false;
        $markAsReceived = $validated['mark_as_received'] ?? false;
        $unmark = $validated['unmark'] ?? false;

        if ($quoteLine->received_at !== null && ! $markAsReceived) {
            throw ValidationException::withMessages([
                'status' => ['Une pièce déjà reçue ne peut plus être modifiée.'],
            ]);
        }

        if ($unmark) {
            if ($quoteLine->received_at !== null) {
                throw ValidationException::withMessages([
                    'status' => ['Impossible de revenir en arrière sur une pièce déjà reçue.'],
                ]);
            }
            $quoteLine->update(['ordered_at' => null]);
        } elseif ($markAsReceived) {
            $quoteLine->update([
                'ordered_at' => $quoteLine->ordered_at ?? now(),
                'received_at' => now(),
            ]);
        } elseif ($markAsOrdered) {
            if ($quoteLine->ordered_at === null) {
                $quoteLine->update(['ordered_at' => now()]);
            }
        }

        $quoteLine->refresh();

        return response()->json([
            'quote_line_id' => $quoteLine->id,
            'needs_order' => $quoteLine->needs_order,
            'ordered_at' => $quoteLine->ordered_at?->toISOString(),
            'received_at' => $quoteLine->received_at?->toISOString(),
            'supply_status' => $quoteLine->supply_status,
        ]);
    }
}
