<?php

namespace App\Http\Controllers\Api;

use App\Enums\ArticleCategorySource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreArticleCategoryRequest;
use App\Http\Requests\Api\UpdateArticleCategoryRequest;
use App\Models\ArticleCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ArticleCategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'source' => ['sometimes', Rule::enum(ArticleCategorySource::class)],
        ]);

        $query = ArticleCategory::ordered()->with('subcategories');

        if ($request->filled('source')) {
            $query->where('source', ArticleCategorySource::from($request->string('source')->toString()));
        }

        $categories = $query->get();

        return response()->json(['categories' => $categories]);
    }

    public function store(StoreArticleCategoryRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if (! isset($validated['sort_order'])) {
            $validated['sort_order'] = (ArticleCategory::max('sort_order') ?? -1) + 1;
        }

        $validated['source'] = ArticleCategorySource::Manual;

        $category = ArticleCategory::create($validated);

        return response()->json($category, 201);
    }

    public function update(UpdateArticleCategoryRequest $request, int $id): JsonResponse
    {
        $category = ArticleCategory::findOrFail($id);
        $category->update($request->validated());

        return response()->json($category);
    }

    public function destroy(int $id): JsonResponse
    {
        $category = ArticleCategory::findOrFail($id);

        if ($category->subcategories()->exists()) {
            return response()->json(['message' => 'Impossible de supprimer une catégorie qui contient des sous-catégories.'], 422);
        }

        $category->delete();

        return response()->json(['message' => 'Catégorie supprimée.']);
    }

    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'categories' => ['required', 'array'],
            'categories.*.id' => ['required', 'integer', 'exists:article_categories,id'],
            'categories.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($validated['categories'] as $item) {
            ArticleCategory::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['message' => 'Ordre mis à jour.']);
    }
}
