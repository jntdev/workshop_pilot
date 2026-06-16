<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(): JsonResponse
    {
        $suppliers = Supplier::ordered()->get();

        return response()->json(['suppliers' => $suppliers]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:suppliers,name'],
        ]);

        $supplier = Supplier::create([
            'name' => $validated['name'],
            'sort_order' => Supplier::max('sort_order') + 1,
        ]);

        return response()->json(['supplier' => $supplier], 201);
    }

    public function update(Request $request, Supplier $supplier): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:suppliers,name,'.$supplier->id],
        ]);

        $supplier->update($validated);

        return response()->json(['supplier' => $supplier]);
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        $supplier->articles()->update(['supplier_id' => null]);
        $supplier->delete();

        return response()->json(null, 204);
    }
}
