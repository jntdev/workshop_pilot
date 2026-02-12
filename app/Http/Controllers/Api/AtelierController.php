<?php

namespace App\Http\Controllers\Api;

use App\Enums\Metier;
use App\Http\Controllers\Controller;
use App\Models\MonthlyKpi;
use App\Models\Quote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AtelierController extends Controller
{
    public function stats(Request $request): JsonResponse
    {
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);

        return response()->json([
            'stats' => $this->getStatsForMonth($year, $month),
            'comparisonStats' => $this->getStatsForMonth($year - 1, $month),
        ]);
    }

    public function invoices(Request $request): JsonResponse
    {
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);

        $startDate = now()->setYear($year)->setMonth($month)->startOfMonth();
        $endDate = now()->setYear($year)->setMonth($month)->endOfMonth();

        $invoices = Quote::with('client')
            ->whereNotNull('invoiced_at')
            ->whereBetween('invoiced_at', [$startDate, $endDate])
            ->latest('invoiced_at')
            ->get()
            ->map(fn (Quote $quote) => $this->formatQuote($quote));

        return response()->json($invoices);
    }

    public function searchClients(Request $request): JsonResponse
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $quotes = Quote::with('client')
            ->whereHas('client', function ($q) use ($query) {
                $q->where('prenom', 'like', '%'.$query.'%')
                    ->orWhere('nom', 'like', '%'.$query.'%')
                    ->orWhere('email', 'like', '%'.$query.'%');
            })
            ->latest()
            ->get()
            ->map(fn (Quote $quote) => $this->formatQuote($quote));

        return response()->json($quotes);
    }

    protected function getStatsForMonth(int $year, int $month): array
    {
        $kpi = MonthlyKpi::where('metier', Metier::Atelier)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        if (! $kpi) {
            return [
                'revenue' => 0,
                'margin' => 0,
                'count' => 0,
                'margin_rate' => 0,
            ];
        }

        $revenue = (float) $kpi->revenue_ht;
        $margin = (float) $kpi->margin_ht;

        return [
            'revenue' => $revenue,
            'margin' => $margin,
            'count' => $kpi->invoice_count,
            'margin_rate' => $revenue > 0 ? ($margin / $revenue) * 100 : 0,
        ];
    }

    public function rebuildStats(): JsonResponse
    {
        $metier = Metier::Atelier->value;

        // Supprimer les KPIs existants pour ce métier
        MonthlyKpi::where('metier', $metier)->delete();

        // Agréger les factures par année/mois
        $aggregates = Quote::whereNotNull('invoiced_at')
            ->where('metier', $metier)
            ->select(
                DB::raw('YEAR(invoiced_at) as year'),
                DB::raw('MONTH(invoiced_at) as month'),
                DB::raw('COUNT(*) as invoice_count'),
                DB::raw('SUM(total_ht) as revenue_ht'),
                DB::raw('SUM(margin_total_ht) as margin_ht')
            )
            ->groupBy(DB::raw('YEAR(invoiced_at)'), DB::raw('MONTH(invoiced_at)'))
            ->get();

        // Créer les nouvelles lignes
        foreach ($aggregates as $aggregate) {
            MonthlyKpi::create([
                'metier' => $metier,
                'year' => $aggregate->year,
                'month' => $aggregate->month,
                'invoice_count' => $aggregate->invoice_count,
                'revenue_ht' => $aggregate->revenue_ht ?? 0,
                'margin_ht' => $aggregate->margin_ht ?? 0,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'KPIs recalculés avec succès',
            'count' => $aggregates->count(),
        ]);
    }

    protected function formatQuote(Quote $quote): array
    {
        return [
            'id' => $quote->id,
            'reference' => $quote->reference,
            'client_id' => $quote->client_id,
            'client' => [
                'id' => $quote->client->id,
                'prenom' => $quote->client->prenom,
                'nom' => $quote->client->nom,
                'email' => $quote->client->email,
                'telephone' => $quote->client->telephone,
                'adresse' => $quote->client->adresse,
            ],
            'bike_description' => $quote->bike_description,
            'total_ht' => $quote->total_ht,
            'total_tva' => $quote->total_tva,
            'total_ttc' => $quote->total_ttc,
            'margin_total_ht' => $quote->margin_total_ht,
            'invoiced_at' => $quote->invoiced_at?->toISOString(),
            'created_at' => $quote->created_at->toISOString(),
            'can_delete' => $quote->canDelete(),
            'is_invoice' => $quote->isInvoice(),
        ];
    }
}
