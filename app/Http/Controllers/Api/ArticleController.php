<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreArticleRequest;
use App\Http\Requests\Api\UpdateArticleRequest;
use App\Http\Requests\Api\UploadArticlePhotoRequest;
use App\Models\Article;
use App\Models\ArticleAttribute;
use App\Models\ArticleSubcategory;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * @var list<string>
     */
    private const FILTERABLE_KEYS = [
        'practice_type', 'valve_type', 'chainring_diameter_mm', 'tooth_count',
        'speed_count', 'crank_length_mm', 'side', 'speed_compat', 'axle_type',
        'position', 'power_source',
    ];

    /**
     * @var list<string>
     */
    private const NUMERIC_KEYS = [
        'chainring_diameter_mm', 'tooth_count', 'speed_count', 'crank_length_mm',
        'wheel_diameter', 'wheel_width_mm', 'wheel_width_inches', 'tooth_range_min', 'tooth_range_max',
    ];

    /**
     * Diamètres commerciaux vélo réels observés dans le catalogue CGN (Pneus + Chambres).
     * Sert à exclure le bruit fournisseur (TR, TRAINER, URBAIN, plages multi-diamètres...)
     * du champ size_inches. Volontairement non fusionné (700 et 700C restent distincts).
     *
     * @var list<string>
     */
    private const COMMERCIAL_WHEEL_DIAMETERS = [
        '10', '12', '14', '16', '18', '20', '22', '24', '26', '27', '27.5', '28', '29',
        '350', '400', '400A', '450', '450A', '500', '500A', '550', '550A', '600A',
        '650', '650A', '650B', '700', '700C',
    ];

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'attribute' => ['sometimes', 'array'],
            'attribute.*' => ['nullable', 'string', 'max:255'],
        ]);

        $query = Article::with(['subcategory.category', 'brand', 'supplier', 'lot'])->ordered();

        if ($request->filled('subcategory_id')) {
            $query->where('article_subcategory_id', $request->integer('subcategory_id'));
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->integer('brand_id'));
        }

        if ($request->filled('search')) {
            $query->search($request->string('search'));
        }

        if ($request->filled('has_movements')) {
            $request->boolean('has_movements')
                ? $query->whereHas('stockMovements')
                : $query->whereDoesntHave('stockMovements');
        }

        $this->applyAttributeFilters($query, $request->array('attribute'));

        $articles = $query->paginate(50);

        return response()->json($articles);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<Article>  $query
     * @param  array<string, string>  $attributes
     */
    private function applyAttributeFilters($query, array $attributes): void
    {
        $virtualKeys = ['wheel_diameter', 'wheel_width_mm', 'wheel_width_inches', 'tooth_range_min', 'tooth_range_max'];

        foreach ($attributes as $key => $value) {
            if ($value === null || $value === '' || in_array($key, $virtualKeys, true)) {
                continue;
            }

            $query->whereHas('attributes', fn ($q) => $q->where('key', $key)->where('value', $value));
        }

        if (($diameter = $attributes['wheel_diameter'] ?? null) !== null && $diameter !== '') {
            $query->whereHas('attributes', function ($q) use ($diameter) {
                $q->where('key', 'size_inches')
                    ->where(fn ($sub) => $sub->where('value', 'like', "{$diameter}\"%")
                        ->orWhere('value', 'like', "{$diameter}X%")
                        ->orWhere('value', $diameter));
            });
        }

        if (($widthMm = $attributes['wheel_width_mm'] ?? null) !== null && $widthMm !== '') {
            $query->whereHas('attributes', fn ($q) => $q->where('key', 'etrto_size')->where('value', 'like', "{$widthMm}-%"));
        }

        if (($widthInches = $attributes['wheel_width_inches'] ?? null) !== null && $widthInches !== '') {
            $query->whereHas('attributes', fn ($q) => $q->where('key', 'size_inches')->where('value', 'like', "%X{$widthInches}"));
        }

        $this->applyCompositeAttributeFilter(
            $query, 'tooth_range', $attributes['tooth_range_min'] ?? null, $attributes['tooth_range_max'] ?? null,
        );
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<Article>  $query
     */
    private function applyCompositeAttributeFilter($query, string $key, ?string $first, ?string $second): void
    {
        if ($first !== null && $first !== '' && $second !== null && $second !== '') {
            $query->whereHas('attributes', fn ($q) => $q->where('key', $key)->where('value', "{$first}-{$second}"));

            return;
        }

        if ($first !== null && $first !== '') {
            $query->whereHas('attributes', fn ($q) => $q->where('key', $key)->where('value', 'like', "{$first}-%"));

            return;
        }

        if ($second !== null && $second !== '') {
            $query->whereHas('attributes', fn ($q) => $q->where('key', $key)->where('value', 'like', "%-{$second}"));
        }
    }

    public function search(Request $request): JsonResponse
    {
        $term = $request->string('q')->toString();

        if (strlen($term) < 3) {
            return response()->json([]);
        }

        $firstWord = preg_split('/\s+/', trim($term))[0] ?? $term;

        $query = Article::with(['subcategory.category', 'brand', 'supplier', 'lot'])
            ->search($term);

        if ($request->filled('has_movements')) {
            $request->boolean('has_movements')
                ? $query->whereHas('stockMovements')
                : $query->whereDoesntHave('stockMovements');
        }

        if ($request->boolean('has_stock')) {
            $query->withSum('stockMovements', 'quantity')->having('stock_movements_sum_quantity', '>', 0);
        }

        $articles = $query
            ->orderByRaw('CASE WHEN designation LIKE ? THEN 0 ELSE 1 END', ["{$firstWord}%"])
            ->ordered()
            ->limit(30)
            ->get()
            ->map(fn (Article $article) => [
                'id' => $article->id,
                'reference' => $article->reference,
                'designation' => $article->designation,
                'purchase_price_ht' => $article->purchase_price_ht,
                'sale_price_ttc' => $article->sale_price_ttc,
                'tva_rate' => $article->tva_rate,
                'unit' => $article->unit,
                'stock_quantity' => $article->stock_quantity,
                'image_url' => $article->image_url,
                'is_discontinued' => $article->is_discontinued,
                'is_editable' => $article->is_editable,
                'lot_article_id' => $article->lot_article_id,
                'lot_quantity' => $article->lot_quantity,
                'suggested_unit_price_ttc' => $article->suggested_unit_price_ttc,
                'brand' => $article->brand ? ['id' => $article->brand->id, 'name' => $article->brand->name] : null,
                'supplier' => $article->supplier ? ['id' => $article->supplier->id, 'name' => $article->supplier->name] : null,
                'subcategory' => $article->subcategory ? ['id' => $article->subcategory->id, 'name' => $article->subcategory->name] : null,
                'category' => $article->subcategory?->category ? ['id' => $article->subcategory->category->id, 'name' => $article->subcategory->category->name] : null,
            ]);

        return response()->json($articles);
    }

    public function filterOptions(int $id): JsonResponse
    {
        ArticleSubcategory::query()->findOrFail($id);

        $grouped = ArticleAttribute::query()
            ->join('articles', 'articles.id', '=', 'article_attributes.article_id')
            ->where('articles.article_subcategory_id', $id)
            ->select('article_attributes.key', 'article_attributes.value')
            ->distinct()
            ->get()
            ->groupBy('key');

        $attributes = [];

        foreach (self::FILTERABLE_KEYS as $key) {
            if (! $grouped->has($key)) {
                continue;
            }

            $values = $grouped->get($key)->pluck('value')->unique()->values()->all();

            if ($key === 'practice_type') {
                $values = $this->normalizePracticeTypeValues($values);
            }

            $attributes[$key] = $this->sortAttributeValues($key, $values);
        }

        if ($grouped->has('etrto_size')) {
            [$widthsMm] = $this->decomposeCompositeValues(
                $grouped->get('etrto_size')->pluck('value')->unique()->all(),
                'etrto_size',
            );

            if ($widthsMm !== []) {
                $attributes['wheel_width_mm'] = $this->sortAttributeValues('wheel_width_mm', $widthsMm);
            }
        }

        if ($grouped->has('size_inches')) {
            $rawValues = $grouped->get('size_inches')->pluck('value')->unique()->all();

            $diameters = $this->extractCommercialWheelDiameters($rawValues);
            if ($diameters !== []) {
                $attributes['wheel_diameter'] = $this->sortAttributeValues('wheel_diameter', $diameters);
            }

            $widthsInches = $this->extractCommercialWheelWidthsInches($rawValues);
            if ($widthsInches !== []) {
                $attributes['wheel_width_inches'] = $this->sortAttributeValues('wheel_width_inches', $widthsInches);
            }
        }

        if ($grouped->has('tooth_range')) {
            [$mins, $maxes] = $this->decomposeCompositeValues(
                $grouped->get('tooth_range')->pluck('value')->unique()->all(),
                'tooth_range',
            );

            if ($mins !== []) {
                $attributes['tooth_range_min'] = $this->sortAttributeValues('tooth_range_min', $mins);
            }

            if ($maxes !== []) {
                $attributes['tooth_range_max'] = $this->sortAttributeValues('tooth_range_max', $maxes);
            }
        }

        $brands = Brand::query()
            ->whereHas('articles', fn ($query) => $query->where('article_subcategory_id', $id))
            ->ordered()
            ->get(['id', 'name', 'sort_order']);

        return response()->json([
            'attributes' => $attributes,
            'brands' => $brands,
        ]);
    }

    /**
     * @param  list<string>  $values
     * @return array{0: list<string>, 1: list<string>}
     */
    private function decomposeCompositeValues(array $values, string $sourceKey): array
    {
        $firsts = [];
        $seconds = [];

        foreach ($values as $value) {
            $parts = explode('-', $value);

            if (count($parts) !== 2 || $parts[0] === '' || $parts[1] === '') {
                Log::warning("Valeur {$sourceKey} malformée ignorée dans filterOptions().", ['value' => $value]);

                continue;
            }

            $firsts[] = $parts[0];
            $seconds[] = $parts[1];
        }

        return [array_values(array_unique($firsts)), array_values(array_unique($seconds))];
    }

    /**
     * Extrait le diamètre commercial (ex. "700", "700C", "26", "27.5") depuis size_inches.
     * Pneus : préfixe avant le premier "X" ("700X28C" -> "700"). Chambres : valeur telle
     * quelle après nettoyage (pas de "X", ex. "26\"" -> "26"). Filtré par liste blanche
     * pour exclure le bruit fournisseur (TR, TRAINER, URBAIN, plages multi-diamètres...).
     *
     * @param  list<string>  $values
     * @return list<string>
     */
    private function extractCommercialWheelDiameters(array $values): array
    {
        $diameters = [];

        foreach ($values as $value) {
            $prefix = strtoupper(str_replace(['"', ' '], '', trim(explode('X', strtoupper($value))[0] ?? '')));

            if (in_array($prefix, self::COMMERCIAL_WHEEL_DIAMETERS, true)) {
                $diameters[] = $prefix;
            }
        }

        return array_values(array_unique($diameters));
    }

    /**
     * Extrait la largeur commerciale en pouces (ex. "28", "28C", "1.75", "35B") depuis le
     * suffixe après le "X" de size_inches. N'accepte que le format simple (un nombre avec
     * au plus une lettre de talon) ; plages ("1.50-2.40") et fractions ("1 3/8") sont
     * ignorées silencieusement (hors périmètre v1).
     *
     * @param  list<string>  $values
     * @return list<string>
     */
    private function extractCommercialWheelWidthsInches(array $values): array
    {
        $widths = [];

        foreach ($values as $value) {
            $parts = preg_split('/X/i', strtoupper(trim($value)));

            if (count($parts) !== 2 || $parts[1] === '') {
                continue;
            }

            $suffix = trim($parts[1]);

            if (preg_match('/^\d+(\.\d+)?[A-Z]?$/', $suffix)) {
                $widths[] = $suffix;
            }
        }

        return array_values(array_unique($widths));
    }

    /**
     * @param  list<string>  $values
     * @return list<string>
     */
    private function sortAttributeValues(string $key, array $values): array
    {
        if (in_array($key, self::NUMERIC_KEYS, true)) {
            usort($values, fn (string $a, string $b) => (float) $a <=> (float) $b);
        } else {
            sort($values, SORT_STRING);
        }

        return array_values($values);
    }

    /**
     * @param  list<string>  $values
     * @return list<string>
     */
    private function normalizePracticeTypeValues(array $values): array
    {
        $seenGroups = [];
        $result = [];

        foreach ($values as $value) {
            $group = str_contains($value, 'VTC') ? 'VTC/Urbain' : $value;

            if (isset($seenGroups[$group])) {
                continue;
            }

            $seenGroups[$group] = true;
            $result[] = $value;
        }

        return $result;
    }

    public function show(int $id): JsonResponse
    {
        $article = Article::with(['subcategory.category', 'brand', 'supplier', 'lot'])->findOrFail($id);

        return response()->json($article);
    }

    public function store(StoreArticleRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if (! isset($validated['sort_order'])) {
            $validated['sort_order'] = 0;
        }

        $article = Article::create($validated);

        return response()->json($article->load('subcategory.category', 'brand', 'supplier', 'lot'), 201);
    }

    public function update(UpdateArticleRequest $request, int $id): JsonResponse
    {
        $article = Article::findOrFail($id);

        if (! $article->is_editable) {
            return response()->json(['message' => 'Impossible de modifier un article du catalogue fournisseur.'], 422);
        }

        $article->update($request->validated());

        return response()->json($article->load('subcategory.category', 'brand', 'supplier', 'lot'));
    }

    public function destroy(int $id): JsonResponse
    {
        $article = Article::findOrFail($id);

        if (! $article->is_editable) {
            return response()->json(['message' => 'Impossible de supprimer un article du catalogue fournisseur.'], 422);
        }

        $article->delete();

        return response()->json(['message' => 'Article supprimé.']);
    }

    public function uploadPhoto(UploadArticlePhotoRequest $request): JsonResponse
    {
        $path = $request->file('photo')->store('articles', 'public');

        return response()->json(['image_url' => Storage::url($path)]);
    }
}
