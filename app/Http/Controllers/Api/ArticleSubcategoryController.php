<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreArticleSubcategoryRequest;
use App\Http\Requests\Api\UpdateArticleSubcategoryRequest;
use App\Models\ArticleSubcategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleSubcategoryController extends Controller
{
    public function store(StoreArticleSubcategoryRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if (! isset($validated['sort_order'])) {
            $validated['sort_order'] = (ArticleSubcategory::where('article_category_id', $validated['article_category_id'])->max('sort_order') ?? -1) + 1;
        }

        $subcategory = ArticleSubcategory::create($validated);

        return response()->json($subcategory->load('category'), 201);
    }

    public function update(UpdateArticleSubcategoryRequest $request, int $id): JsonResponse
    {
        $subcategory = ArticleSubcategory::findOrFail($id);
        $subcategory->update($request->validated());

        return response()->json($subcategory->load('category'));
    }

    public function destroy(int $id): JsonResponse
    {
        $subcategory = ArticleSubcategory::findOrFail($id);

        if ($subcategory->articles()->exists()) {
            return response()->json(['message' => 'Impossible de supprimer une sous-catégorie qui contient des articles.'], 422);
        }

        $subcategory->delete();

        return response()->json(['message' => 'Sous-catégorie supprimée.']);
    }

    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subcategories' => ['required', 'array'],
            'subcategories.*.id' => ['required', 'integer', 'exists:article_subcategories,id'],
            'subcategories.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($validated['subcategories'] as $item) {
            ArticleSubcategory::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['message' => 'Ordre mis à jour.']);
    }
}
