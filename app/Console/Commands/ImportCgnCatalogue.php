<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\ArticleAttribute;
use App\Models\ArticleCategory;
use App\Models\ArticleSubcategory;
use App\Models\Brand;
use App\Models\Supplier;
use App\Services\Catalogue\AttributeExtractorRegistry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImportCgnCatalogue extends Command
{
    protected $signature = 'catalogue:import-cgn {path=StockNouveautesCgn022252.csv : Chemin du fichier CSV fournisseur CGN}';

    protected $description = "Importe/actualise le catalogue d'articles depuis un export CSV fournisseur CGN";

    private const CHUNK_SIZE = 500;

    private const CGN_SUPPLIER_NAME = 'CGN';

    private int $cgnSupplierId;

    /**
     * @var array<string, int>
     */
    private array $brandCache = [];

    /**
     * @var array<string, int>
     */
    private array $categoryCache = [];

    /**
     * @var array<string, int>
     */
    private array $subcategoryCache = [];

    private AttributeExtractorRegistry $extractorRegistry;

    private int $created = 0;

    private int $updated = 0;

    private int $duplicateEansSkipped = 0;

    private int $referenceCollisions = 0;

    private int $withoutEan = 0;

    private int $withoutBrand = 0;

    private int $zeroPurchasePrice = 0;

    private int $zeroSalePrice = 0;

    private int $discontinued = 0;

    private int $brandsCreated = 0;

    private int $subcategoriesCreated = 0;

    /**
     * @var array<string, int>
     */
    private array $attributesExtractedByKey = [];

    private const ETRTO_TRACKED_SUBCATEGORIES = ['PNEUS VELO', 'CHAMBRES VELO'];

    /**
     * @var array<string, int>
     */
    private array $etrtoEligibleBySubcategory = ['PNEUS VELO' => 0, 'CHAMBRES VELO' => 0];

    /**
     * @var array<string, int>
     */
    private array $etrtoExtractedBySubcategory = ['PNEUS VELO' => 0, 'CHAMBRES VELO' => 0];

    public function handle(): int
    {
        $this->extractorRegistry = new AttributeExtractorRegistry;

        $path = $this->resolvePath($this->argument('path'));

        if (! File::exists($path)) {
            $this->error("Fichier introuvable : {$path}");

            return Command::FAILURE;
        }

        $this->loadCaches();

        $rows = $this->parseFile($path);
        $rowsToImport = $this->deduplicateByEan($rows);

        $this->output->progressStart(count($rowsToImport));

        foreach (array_chunk($rowsToImport, self::CHUNK_SIZE) as $chunk) {
            DB::transaction(function () use ($chunk): void {
                foreach ($chunk as $row) {
                    $this->importRow($row);
                    $this->output->progressAdvance();
                }
            });
        }

        $this->output->progressFinish();

        $this->printReport(count($rows));

        return Command::SUCCESS;
    }

    private function resolvePath(string $path): string
    {
        return str_starts_with($path, '/') ? $path : base_path($path);
    }

    /**
     * @return array<int, array<int, string>>
     */
    private function parseFile(string $path): array
    {
        $content = File::get($path);
        $content = mb_convert_encoding($content, 'UTF-8', 'Windows-1252');

        $lines = preg_split('/\r\n|\r|\n/', $content);

        $rows = [];
        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }

            $columns = str_getcsv($line, ';');

            if (count($columns) < 13) {
                continue;
            }

            $rows[] = $columns;
        }

        return $rows;
    }

    /**
     * @param  array<int, array<int, string>>  $rows
     * @return array<int, array<int, string>>
     */
    private function deduplicateByEan(array $rows): array
    {
        $bestRowByEan = [];
        $rowsWithoutEan = [];

        foreach ($rows as $row) {
            $ean = trim($row[7] ?? '');

            if ($ean === '') {
                $rowsWithoutEan[] = $row;

                continue;
            }

            if (! isset($bestRowByEan[$ean])) {
                $bestRowByEan[$ean] = $row;

                continue;
            }

            $existingIsDiscontinued = $this->isDiscontinued($bestRowByEan[$ean][1] ?? '');
            $candidateIsDiscontinued = $this->isDiscontinued($row[1] ?? '');

            if ($existingIsDiscontinued && ! $candidateIsDiscontinued) {
                $bestRowByEan[$ean] = $row;
            }

            $this->duplicateEansSkipped++;
        }

        return [...array_values($bestRowByEan), ...$rowsWithoutEan];
    }

    private function isDiscontinued(string $designation): bool
    {
        return str_contains(strtoupper($designation), 'EPUISE');
    }

    private function loadCaches(): void
    {
        $supplier = Supplier::firstOrCreate(
            ['name' => self::CGN_SUPPLIER_NAME],
            ['sort_order' => (Supplier::max('sort_order') ?? -1) + 1],
        );
        $this->cgnSupplierId = $supplier->id;

        $this->brandCache = Brand::pluck('id', 'name')->all();
        $this->categoryCache = ArticleCategory::pluck('id', 'name')->all();

        foreach (ArticleSubcategory::query()->get(['id', 'article_category_id', 'name']) as $subcategory) {
            $this->subcategoryCache["{$subcategory->article_category_id}:{$subcategory->name}"] = $subcategory->id;
        }
    }

    /**
     * @param  array<int, string>  $row
     */
    private function importRow(array $row): void
    {
        $referenceCgn = trim($row[0] ?? '');
        $rawDesignation = trim($row[1] ?? '');
        $purchasePrice = $this->parseDecimal($row[2] ?? null);
        $rawCategory = trim($row[3] ?? '');
        $rawSubcategory = trim($row[4] ?? '');
        $ean = trim($row[7] ?? '');
        $rawBrand = trim($row[9] ?? '');
        $salePrice = $this->parseDecimal($row[10] ?? null);
        $weight = $this->parseDecimal($row[11] ?? null);
        $imageUrl = trim($row[12] ?? '');

        if ($referenceCgn === '') {
            return;
        }

        $isDiscontinued = $this->isDiscontinued($rawDesignation);
        $designation = $this->cleanDesignation($rawDesignation);

        if (($purchasePrice ?? 0) <= 0.0) {
            $this->zeroPurchasePrice++;
        }

        if (($salePrice ?? 0) <= 0.0) {
            $this->zeroSalePrice++;
        }

        if ($ean === '') {
            $this->withoutEan++;
        }

        $brandId = $this->resolveBrand($rawBrand);
        if ($brandId === null) {
            $this->withoutBrand++;
        }

        $subcategoryId = $this->resolveSubcategory($rawCategory, $rawSubcategory);

        $existing = Article::where('reference', $referenceCgn)->first();

        if ($existing !== null && $existing->supplier_id !== $this->cgnSupplierId) {
            $this->referenceCollisions++;

            return;
        }

        $attributes = [
            'purchase_price_ht' => (int) round(($purchasePrice ?? 0) * 100),
            'sale_price_ttc' => (int) round(($salePrice ?? 0) * 100),
            'barcode' => $ean !== '' ? $ean : null,
            'image_url' => $imageUrl !== '' ? $imageUrl : null,
            'weight_kg' => $weight,
            'designation' => $designation,
            'brand_id' => $brandId,
            'supplier_id' => $this->cgnSupplierId,
            'article_subcategory_id' => $subcategoryId,
        ];

        $article = Article::updateOrCreate(['reference' => $referenceCgn], $attributes);

        if ($article->wasRecentlyCreated) {
            $article->update(['tva_rate' => 20.00, 'unit' => 'pièce']);
            $this->created++;
        } else {
            $this->updated++;
        }

        $this->applyDiscontinuedStatus($article, $isDiscontinued);
        $this->applyExtractedAttributes($article, $rawSubcategory, $designation);
    }

    private function parseDecimal(?string $value): ?float
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return (float) str_replace(',', '.', $value);
    }

    private function cleanDesignation(string $designation): string
    {
        $designation = preg_replace('/^µ\s*/u', '', $designation) ?? $designation;
        $designation = preg_replace('/\s*-\s*EPUISE\s*-\s*$/i', '', $designation) ?? $designation;

        return trim($designation);
    }

    private function resolveBrand(string $raw): ?int
    {
        $name = trim($raw);

        if ($name === '') {
            return null;
        }

        if (isset($this->brandCache[$name])) {
            return $this->brandCache[$name];
        }

        $sortOrder = (Brand::max('sort_order') ?? -1) + 1;
        $brand = Brand::create(['name' => $name, 'sort_order' => $sortOrder]);
        $this->brandCache[$name] = $brand->id;
        $this->brandsCreated++;

        return $brand->id;
    }

    private function resolveCategory(string $raw): int
    {
        $name = Str::title(strtolower(trim($raw)));

        if (isset($this->categoryCache[$name])) {
            return $this->categoryCache[$name];
        }

        $sortOrder = (ArticleCategory::max('sort_order') ?? -1) + 1;
        $category = ArticleCategory::create(['name' => $name, 'sort_order' => $sortOrder]);
        $this->categoryCache[$name] = $category->id;

        return $category->id;
    }

    private function resolveSubcategory(string $rawCategory, string $rawSubcategory): int
    {
        $categoryId = $this->resolveCategory($rawCategory);
        $name = Str::title(strtolower(trim($rawSubcategory)));
        $cacheKey = "{$categoryId}:{$name}";

        if (isset($this->subcategoryCache[$cacheKey])) {
            return $this->subcategoryCache[$cacheKey];
        }

        $sortOrder = (ArticleSubcategory::where('article_category_id', $categoryId)->max('sort_order') ?? -1) + 1;
        $subcategory = ArticleSubcategory::create([
            'article_category_id' => $categoryId,
            'name' => $name,
            'sort_order' => $sortOrder,
        ]);
        $this->subcategoryCache[$cacheKey] = $subcategory->id;
        $this->subcategoriesCreated++;

        return $subcategory->id;
    }

    private function applyDiscontinuedStatus(Article $article, bool $isDiscontinued): void
    {
        if ($isDiscontinued) {
            ArticleAttribute::updateOrCreate(
                ['article_id' => $article->id, 'key' => 'supplier_status'],
                ['value' => 'discontinued']
            );
            $this->discontinued++;

            return;
        }

        ArticleAttribute::where('article_id', $article->id)
            ->where('key', 'supplier_status')
            ->delete();
    }

    private function applyExtractedAttributes(Article $article, string $rawSubcategory, string $designation): void
    {
        $normalizedSubcategory = strtoupper(trim($rawSubcategory));
        $isTrackedForEtrto = in_array($normalizedSubcategory, self::ETRTO_TRACKED_SUBCATEGORIES, true);

        if ($isTrackedForEtrto) {
            $this->etrtoEligibleBySubcategory[$normalizedSubcategory]++;
        }

        $extractor = $this->extractorRegistry->findFor($rawSubcategory, $designation);

        if ($extractor === null) {
            return;
        }

        ArticleAttribute::where('article_id', $article->id)
            ->whereIn('key', $extractor->possibleKeys())
            ->delete();

        foreach ($extractor->extract($designation) as $key => $value) {
            ArticleAttribute::create([
                'article_id' => $article->id,
                'key' => $key,
                'value' => $value,
            ]);
            $this->attributesExtractedByKey[$key] = ($this->attributesExtractedByKey[$key] ?? 0) + 1;

            if ($isTrackedForEtrto && $key === 'etrto_size') {
                $this->etrtoExtractedBySubcategory[$normalizedSubcategory]++;
            }
        }
    }

    private function printReport(int $totalRows): void
    {
        $this->newLine();
        $this->info('Import terminé.');
        $this->table(['Indicateur', 'Valeur'], [
            ['Lignes lues', $totalRows],
            ['Doublons EAN ignorés', $this->duplicateEansSkipped],
            ['Collisions de référence ignorées (article manuel existant)', $this->referenceCollisions],
            ['Articles créés', $this->created],
            ['Articles mis à jour', $this->updated],
            ['Marques créées', $this->brandsCreated],
            ['Sous-catégories créées', $this->subcategoriesCreated],
            ['Lignes sans EAN', $this->withoutEan],
            ['Lignes sans marque', $this->withoutBrand],
            ['Prix d\'achat à zéro', $this->zeroPurchasePrice],
            ['Prix de vente à zéro', $this->zeroSalePrice],
            ['Fiches épuisées (supplier_status)', $this->discontinued],
        ]);

        if ($this->attributesExtractedByKey !== []) {
            $this->info('Attributs structurés extraits :');
            $this->table(
                ['Clé', 'Nombre'],
                collect($this->attributesExtractedByKey)->map(fn ($count, $key) => [$key, $count])->values()->all()
            );
        }

        $this->info('Couverture ETRTO réelle :');
        $this->table(
            ['Sous-catégorie', 'Lignes éligibles', 'ETRTO extrait', 'Taux'],
            collect(self::ETRTO_TRACKED_SUBCATEGORIES)->map(function (string $subcategory) {
                $eligible = $this->etrtoEligibleBySubcategory[$subcategory];
                $extracted = $this->etrtoExtractedBySubcategory[$subcategory];
                $rate = $eligible > 0 ? round(($extracted / $eligible) * 100, 1).'%' : 'n/a';

                return [$subcategory, $eligible, $extracted, $rate];
            })->all()
        );
    }
}
