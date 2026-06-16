<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(): JsonResponse
    {
        $brands = Brand::ordered()->get();

        return response()->json(['brands' => $brands]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:brands,name'],
        ]);

        $brand = Brand::create([
            'name' => $validated['name'],
            'sort_order' => Brand::max('sort_order') + 1,
        ]);

        return response()->json(['brand' => $brand], 201);
    }

    public function update(Request $request, Brand $brand): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:brands,name,'.$brand->id],
        ]);

        $brand->update($validated);

        return response()->json(['brand' => $brand]);
    }

    public function destroy(Brand $brand): JsonResponse
    {
        $brand->articles()->update(['brand_id' => null]);
        $brand->delete();

        return response()->json(null, 204);
    }
}
