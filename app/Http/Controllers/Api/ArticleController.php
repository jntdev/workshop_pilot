<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreArticleRequest;
use App\Http\Requests\Api\UpdateArticleRequest;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Article::with(['subcategory.category', 'brand'])->ordered();

        if ($request->filled('subcategory_id')) {
            $query->where('article_subcategory_id', $request->integer('subcategory_id'));
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->integer('brand_id'));
        }

        if ($request->filled('search')) {
            $query->search($request->string('search'));
        }

        $articles = $query->paginate(50);

        return response()->json($articles);
    }

    public function search(Request $request): JsonResponse
    {
        $term = $request->string('q')->toString();

        if (strlen($term) < 3) {
            return response()->json([]);
        }

        $articles = Article::with(['subcategory.category', 'brand'])
            ->search($term)
            ->ordered()
            ->limit(10)
            ->get()
            ->map(fn (Article $article) => [
                'id' => $article->id,
                'reference' => $article->reference,
                'designation' => $article->designation,
                'purchase_price_ht' => $article->purchase_price_ht,
                'sale_price_ht' => $article->sale_price_ht,
                'tva_rate' => $article->tva_rate,
                'unit' => $article->unit,
                'stock_quantity' => $article->stock_quantity,
                'brand' => $article->brand ? ['id' => $article->brand->id, 'name' => $article->brand->name] : null,
                'subcategory' => $article->subcategory ? ['id' => $article->subcategory->id, 'name' => $article->subcategory->name] : null,
                'category' => $article->subcategory?->category ? ['id' => $article->subcategory->category->id, 'name' => $article->subcategory->category->name] : null,
            ]);

        return response()->json($articles);
    }

    public function show(int $id): JsonResponse
    {
        $article = Article::with(['subcategory.category', 'brand'])->findOrFail($id);

        return response()->json($article);
    }

    public function store(StoreArticleRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if (! isset($validated['sort_order'])) {
            $validated['sort_order'] = 0;
        }

        $article = Article::create($validated);

        return response()->json($article->load('subcategory.category', 'brand'), 201);
    }

    public function update(UpdateArticleRequest $request, int $id): JsonResponse
    {
        $article = Article::findOrFail($id);
        $article->update($request->validated());

        return response()->json($article->load('subcategory.category', 'brand'));
    }

    public function destroy(int $id): JsonResponse
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return response()->json(['message' => 'Article supprimé.']);
    }
}
