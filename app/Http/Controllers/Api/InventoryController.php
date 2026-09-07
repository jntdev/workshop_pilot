<?php

namespace App\Http\Controllers\Api;

use App\Enums\StockMovementType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreInventoryArticleRequest;
use App\Models\Article;
use App\Models\ArticleAttribute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Union des clés brutes filtrables (ArticleController::FILTERABLE_KEYS) et des
     * clés virtuelles traduisibles (feature 26). Toute clé hors de cette liste est
     * ignorée silencieusement, jamais persistée dans article_attributes.
     *
     * @var list<string>
     */
    private const ACCEPTED_ATTRIBUTE_KEYS = [
        'practice_type', 'valve_type', 'chainring_diameter_mm', 'tooth_count',
        'speed_count', 'crank_length_mm', 'side', 'speed_compat', 'axle_type',
        'position', 'power_source',
        'wheel_diameter', 'wheel_width_mm', 'wheel_width_inches',
        'tooth_range_min', 'tooth_range_max',
    ];

    public function lookupBarcode(Request $request): JsonResponse
    {
        $code = $request->string('code')->toString();

        if ($code === '') {
            return response()->json(['result' => 'not_found', 'article' => null]);
        }

        $articles = Article::byBarcode($code)->with(['subcategory.category', 'brand', 'supplier'])->get();

        if ($articles->count() === 1) {
            return response()->json(['result' => 'found', 'article' => $articles->first()]);
        }

        if ($articles->count() > 1) {
            return response()->json(['result' => 'ambiguous', 'articles' => $articles]);
        }

        return response()->json(['result' => 'not_found', 'article' => null]);
    }

    public function storeArticle(StoreInventoryArticleRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $article = DB::transaction(function () use ($validated) {
            $article = Article::create([
                'reference' => $validated['barcode'],
                'barcode' => $validated['barcode'],
                'designation' => $validated['designation'],
                'article_subcategory_id' => $validated['article_subcategory_id'],
                'brand_id' => $validated['brand_id'],
                'purchase_price_ht' => $validated['purchase_price_ht'],
                'sale_price_ttc' => $validated['sale_price_ttc'],
                'tva_rate' => 20.00,
                'unit' => 'pièce',
                'image_url' => $validated['image_url'],
                'sort_order' => 0,
            ]);

            $this->persistFilteredAttributes($article, $validated['attributes'] ?? []);

            $article->stockMovements()->create([
                'quantity' => $validated['quantity'],
                'type' => StockMovementType::ManualIn,
            ]);

            return $article;
        });

        return response()->json($article->load('subcategory.category', 'brand', 'supplier'), 201);
    }

    /**
     * @param  array<string, string>  $attributes
     */
    private function persistFilteredAttributes(Article $article, array $attributes): void
    {
        $accepted = array_intersect_key($attributes, array_flip(self::ACCEPTED_ATTRIBUTE_KEYS));
        $accepted = array_filter($accepted, fn ($value) => $value !== null && $value !== '');

        $wheelDiameter = $accepted['wheel_diameter'] ?? null;
        $wheelWidthMm = $accepted['wheel_width_mm'] ?? null;
        $wheelWidthInches = $accepted['wheel_width_inches'] ?? null;
        $toothRangeMin = $accepted['tooth_range_min'] ?? null;
        $toothRangeMax = $accepted['tooth_range_max'] ?? null;

        if ($wheelWidthMm !== null && $wheelDiameter !== null) {
            $this->createAttribute($article, 'etrto_size', "{$wheelWidthMm}-{$wheelDiameter}");
        }

        if ($wheelWidthInches !== null && $wheelDiameter !== null) {
            $this->createAttribute($article, 'size_inches', "{$wheelDiameter}X{$wheelWidthInches}");
        }

        if ($toothRangeMin !== null && $toothRangeMax !== null) {
            $this->createAttribute($article, 'tooth_range', "{$toothRangeMin}-{$toothRangeMax}");
        }

        $virtualKeys = ['wheel_diameter', 'wheel_width_mm', 'wheel_width_inches', 'tooth_range_min', 'tooth_range_max'];

        foreach ($accepted as $key => $value) {
            if (in_array($key, $virtualKeys, true)) {
                continue;
            }

            $this->createAttribute($article, $key, $value);
        }
    }

    private function createAttribute(Article $article, string $key, string $value): void
    {
        ArticleAttribute::create([
            'article_id' => $article->id,
            'key' => $key,
            'value' => $value,
        ]);
    }
}
