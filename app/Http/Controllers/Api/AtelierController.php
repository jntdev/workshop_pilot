<?php

namespace App\Http\Controllers\Api;

use App\Enums\Metier;
use App\Http\Controllers\Controller;
use App\Models\MonthlyKpi;
use App\Models\Quote;
use App\Models\Reservation;
use App\Models\ReservationPayment;
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

    public function rebuildAllKpis(): JsonResponse
    {
        $results = [];

        // Rebuild Atelier
        $results['atelier'] = $this->rebuildMetierKpis(Metier::Atelier->value);

        // Rebuild Vente
        $results['vente'] = $this->rebuildMetierKpis(Metier::Vente->value);

        // Rebuild Location
        $results['location'] = $this->rebuildLocationKpis();

        return response()->json([
            'success' => true,
            'message' => 'Tous les KPIs ont été recalculés',
            'results' => $results,
        ]);
    }

    protected function rebuildMetierKpis(string $metier): int
    {
        MonthlyKpi::where('metier', $metier)->delete();

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

        return $aggregates->count();
    }

    protected function rebuildLocationKpis(): int
    {
        MonthlyKpi::where('metier', Metier::Location->value)->delete();

        // Taux de TVA pour conversion TTC -> HT
        $tvaRate = config('location.tva_rate', 20);

        // Agréger les paiements par année/mois
        $paymentAggregates = ReservationPayment::select(
            DB::raw('YEAR(paid_at) as year'),
            DB::raw('MONTH(paid_at) as month'),
            DB::raw('SUM(amount) as total_amount')
        )
            ->groupBy(DB::raw('YEAR(paid_at)'), DB::raw('MONTH(paid_at)'))
            ->get()
            ->keyBy(fn ($row) => $row->year.'-'.$row->month);

        // Agréger les acomptes par année/mois
        $acompteAggregates = Reservation::whereNotNull('acompte_paye_le')
            ->where('acompte_montant', '>', 0)
            ->select(
                DB::raw('YEAR(acompte_paye_le) as year'),
                DB::raw('MONTH(acompte_paye_le) as month'),
                DB::raw('SUM(acompte_montant) as total_acompte')
            )
            ->groupBy(DB::raw('YEAR(acompte_paye_le)'), DB::raw('MONTH(acompte_paye_le)'))
            ->get()
            ->keyBy(fn ($row) => $row->year.'-'.$row->month);

        $allMonths = $paymentAggregates->keys()->merge($acompteAggregates->keys())->unique();

        $created = 0;
        foreach ($allMonths as $key) {
            $payment = $paymentAggregates->get($key);
            $acompte = $acompteAggregates->get($key);

            $year = $payment?->year ?? $acompte->year;
            $month = $payment?->month ?? $acompte->month;

            // Total TTC (les paiements et acomptes sont en TTC)
            $totalTtc = ($payment->total_amount ?? 0) + ($acompte->total_acompte ?? 0);

            // Convertir TTC en HT
            $totalHt = $totalTtc / (1 + $tvaRate / 100);

            // Compter les réservations uniques
            $reservationIdsFromPayments = $payment
                ? ReservationPayment::whereYear('paid_at', $year)
                    ->whereMonth('paid_at', $month)
                    ->distinct()
                    ->pluck('reservation_id')
                : collect();

            $reservationIdsFromAcomptes = $acompte
                ? Reservation::whereYear('acompte_paye_le', $year)
                    ->whereMonth('acompte_paye_le', $month)
                    ->pluck('id')
                : collect();

            $uniqueReservations = $reservationIdsFromPayments
                ->merge($reservationIdsFromAcomptes)
                ->unique()
                ->count();

            MonthlyKpi::create([
                'metier' => Metier::Location->value,
                'year' => $year,
                'month' => $month,
                'invoice_count' => $uniqueReservations,
                'revenue_ht' => round($totalHt, 2),
                'revenue_ttc' => round($totalTtc, 2),
                'margin_ht' => 0,
            ]);
            $created++;
        }

        return $created;
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
            'status' => $quote->status?->value,
            'invoiced_at' => $quote->invoiced_at?->toISOString(),
            'created_at' => $quote->created_at->toISOString(),
            'can_delete' => $quote->canDelete(),
            'is_invoice' => $quote->isInvoice(),
        ];
    }
}
