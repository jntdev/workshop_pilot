<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CompleteSaleRequest;
use App\Http\Requests\Api\StoreSaleLineRequest;
use App\Models\Article;
use App\Models\Sale;
use App\Models\SaleLine;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function lookupArticle(Request $request): JsonResponse
    {
        $term = $request->string('q')->toString();

        if ($term === '') {
            return response()->json(['result' => 'not_found', 'articles' => []]);
        }

        $byBarcode = Article::byBarcode($term)->get();
        if ($byBarcode->isNotEmpty()) {
            return $this->respondToLookup($byBarcode, exact: true);
        }

        $byReference = Article::where('reference', $term)->get();
        if ($byReference->isNotEmpty()) {
            return $this->respondToLookup($byReference, exact: true);
        }

        $bySearch = Article::search($term)->ordered()->get();
        if ($bySearch->isNotEmpty()) {
            return response()->json([
                'result' => 'choices',
                'articles' => $bySearch->map(fn (Article $article) => $this->formatArticle($article)),
            ]);
        }

        return response()->json(['result' => 'not_found', 'articles' => []]);
    }

    public function recent(): JsonResponse
    {
        $sales = Sale::query()
            ->whereIn('status', ['completed', 'cancelled'])
            ->whereDate('completed_at', now()->toDateString())
            ->withCount('lines')
            ->orderByDesc('completed_at')
            ->get();

        return response()->json([
            'sales' => $sales->map(fn (Sale $sale) => [
                'id' => $sale->id,
                'reference' => $sale->reference,
                'status' => $sale->status->value,
                'total_ttc' => $sale->total_ttc,
                'completed_at' => $sale->completed_at,
                'lines_count' => $sale->lines_count,
            ]),
        ]);
    }

    public function store(): JsonResponse
    {
        $sale = Sale::create();

        return response()->json($this->formatSale($sale), 201);
    }

    public function show(Sale $sale): JsonResponse
    {
        return response()->json($this->formatSale($sale));
    }

    public function addLine(StoreSaleLineRequest $request, Sale $sale): JsonResponse
    {
        $this->ensureDraft($sale);

        $validated = $request->validated();
        $article = isset($validated['article_id']) ? Article::find($validated['article_id']) : null;
        $quantity = $validated['quantity'] ?? 1;

        if ($article !== null) {
            $existingLine = $sale->lines()->where('article_id', $article->id)->first();

            if ($existingLine !== null) {
                $this->updateLineTotals($existingLine, (float) $existingLine->quantity + $quantity, $existingLine->unit_price_ttc, $existingLine->tva_rate);
                $sale->recalculateTotals();

                return response()->json($this->formatSale($sale->fresh()), 201);
            }
        }

        $unitPriceTtc = $article?->sale_price_ttc ?? $validated['unit_price_ttc'];
        $tvaRate = $article?->tva_rate ?? 20.00;

        try {
            $line = $sale->lines()->create([
                'article_id' => $article?->id,
                'designation' => $article?->designation ?? $validated['designation'],
                'reference' => $article?->reference,
                'quantity' => $quantity,
                'purchase_price_ht' => $article?->purchase_price_ht ?? $validated['purchase_price_ht'],
                'unit_price_ttc' => $unitPriceTtc,
                'tva_rate' => $tvaRate,
                'line_total_ttc' => (int) round($quantity * $unitPriceTtc),
                'position' => $sale->lines()->count(),
            ]);
        } catch (QueryException $e) {
            if ($article === null) {
                throw $e;
            }

            $existingLine = $sale->lines()->where('article_id', $article->id)->first();
            $this->updateLineTotals($existingLine, (float) $existingLine->quantity + $quantity, $existingLine->unit_price_ttc, $existingLine->tva_rate);
            $sale->recalculateTotals();

            return response()->json($this->formatSale($sale->fresh()), 201);
        }

        $sale->recalculateTotals();

        return response()->json($this->formatSale($sale->fresh()), 201);
    }

    public function updateLine(Request $request, Sale $sale, SaleLine $line): JsonResponse
    {
        $this->ensureDraft($sale);
        $this->ensureLineBelongsToSale($sale, $line);

        $validated = $request->validate([
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_price_ttc' => ['required', 'integer', 'min:0'],
        ]);

        $this->updateLineTotals($line, $validated['quantity'], $validated['unit_price_ttc'], $line->tva_rate);
        $sale->recalculateTotals();

        return response()->json($this->formatSale($sale->fresh()));
    }

    public function removeLine(Sale $sale, SaleLine $line): JsonResponse
    {
        $this->ensureDraft($sale);
        $this->ensureLineBelongsToSale($sale, $line);

        $line->delete();
        $sale->recalculateTotals();

        return response()->json($this->formatSale($sale->fresh()));
    }

    public function complete(CompleteSaleRequest $request, Sale $sale): JsonResponse
    {
        $paymentMethod = PaymentMethod::from($request->validated('payment_method'));

        try {
            $sale->complete($paymentMethod);
        } catch (\DomainException $e) {
            throw new AuthorizationException($e->getMessage());
        }

        return response()->json($this->formatSale($sale->fresh()));
    }

    public function cancel(Sale $sale): JsonResponse
    {
        try {
            $sale->cancel();
        } catch (\DomainException $e) {
            throw new AuthorizationException($e->getMessage());
        }

        return response()->json($this->formatSale($sale->fresh()));
    }

    private function ensureDraft(Sale $sale): void
    {
        if (! $sale->isDraft()) {
            throw new AuthorizationException('Cette vente ne peut plus être modifiée.');
        }
    }

    private function ensureLineBelongsToSale(Sale $sale, SaleLine $line): void
    {
        if ($line->sale_id !== $sale->id) {
            abort(404);
        }
    }

    private function updateLineTotals(SaleLine $line, float $quantity, int $unitPriceTtc, float $tvaRate): void
    {
        $line->update([
            'quantity' => $quantity,
            'unit_price_ttc' => $unitPriceTtc,
            'line_total_ttc' => (int) round($quantity * $unitPriceTtc),
        ]);
    }

    private function respondToLookup($articles, bool $exact): JsonResponse
    {
        if ($articles->count() === 1) {
            return response()->json([
                'result' => 'found',
                'article' => $this->formatArticle($articles->first()),
            ]);
        }

        return response()->json([
            'result' => 'ambiguous',
            'articles' => $articles->map(fn (Article $article) => $this->formatArticle($article)),
        ]);
    }

    private function formatArticle(Article $article): array
    {
        return [
            'id' => $article->id,
            'reference' => $article->reference,
            'designation' => $article->designation,
            'barcode' => $article->barcode,
            'purchase_price_ht' => $article->purchase_price_ht,
            'sale_price_ttc' => $article->sale_price_ttc,
            'tva_rate' => $article->tva_rate,
            'stock_quantity' => $article->stock_quantity,
        ];
    }

    private function formatSale(Sale $sale): array
    {
        $sale->load('lines');

        return [
            'id' => $sale->id,
            'reference' => $sale->reference,
            'status' => $sale->status->value,
            'payment_method' => $sale->payment_method?->value,
            'total_ht' => $sale->total_ht,
            'total_tva' => $sale->total_tva,
            'total_ttc' => $sale->total_ttc,
            'completed_at' => $sale->completed_at,
            'cancelled_at' => $sale->cancelled_at,
            'lines' => $sale->lines->map(fn (SaleLine $line) => [
                'id' => $line->id,
                'article_id' => $line->article_id,
                'designation' => $line->designation,
                'reference' => $line->reference,
                'quantity' => (float) $line->quantity,
                'purchase_price_ht' => $line->purchase_price_ht,
                'unit_price_ttc' => $line->unit_price_ttc,
                'tva_rate' => $line->tva_rate,
                'line_total_ttc' => $line->line_total_ttc,
                'position' => $line->position,
            ]),
        ];
    }
}
