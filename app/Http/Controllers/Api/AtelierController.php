<?php

namespace App\Http\Controllers\Api;

use App\Enums\Metier;
use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Models\BikeMaintenanceLog;
use App\Models\MonthlyKpi;
use App\Models\Quote;
use App\Models\QuotePayment;
use App\Models\Reservation;
use App\Models\ReservationPayment;
use App\Models\Sale;
use App\Services\Kpis\MonthlyKpiUpdater;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AtelierController extends Controller
{
    public function __construct(
        private MonthlyKpiUpdater $kpiUpdater,
    ) {}

    /**
     * Historique complet des encaissements, groupés par jour / source / mode de paiement.
     * Pensé pour être chargé une seule fois côté client puis gardé en cache local.
     */
    public function paymentsHistory(): JsonResponse
    {
        $emptyRow = collect(PaymentMethod::cases())->mapWithKeys(fn ($m) => [$m->value => 0])->all();
        $byDate = [];

        $ensureDate = function (string $date) use (&$byDate, $emptyRow) {
            if (! isset($byDate[$date])) {
                $byDate[$date] = [
                    'atelier' => $emptyRow,
                    'location' => $emptyRow,
                    'caisse' => $emptyRow,
                ];
            }
        };

        $quotePayments = QuotePayment::select(DB::raw('DATE(paid_at) as day'), 'method', DB::raw('SUM(amount) as total'))
            ->groupBy('day', 'method')
            ->get();

        foreach ($quotePayments as $row) {
            $ensureDate($row->day);
            $method = $row->method instanceof PaymentMethod ? $row->method->value : $row->method;
            $byDate[$row->day]['atelier'][$method] += (float) $row->total;
        }

        $reservationPayments = ReservationPayment::select(DB::raw('DATE(paid_at) as day'), 'method', DB::raw('SUM(amount) as total'))
            ->groupBy('day', 'method')
            ->get();

        foreach ($reservationPayments as $row) {
            $ensureDate($row->day);
            $method = $row->method instanceof PaymentMethod ? $row->method->value : $row->method;
            $byDate[$row->day]['location'][$method] += (float) $row->total;
        }

        $sales = Sale::where('status', 'completed')
            ->select(DB::raw('DATE(completed_at) as day'), 'payment_method', DB::raw('SUM(total_ttc) as total'))
            ->groupBy('day', 'payment_method')
            ->get();

        foreach ($sales as $row) {
            $method = $row->payment_method instanceof PaymentMethod ? $row->payment_method->value : $row->payment_method;
            if ($method === null) {
                continue;
            }
            $ensureDate($row->day);
            $byDate[$row->day]['caisse'][$method] += ((float) $row->total) / 100;
        }

        $days = collect($byDate)->map(function (array $bySource) use ($emptyRow) {
            $byMethod = $emptyRow;
            foreach ($bySource as $sourceTotals) {
                foreach ($sourceTotals as $method => $amount) {
                    $byMethod[$method] += $amount;
                }
            }

            return [
                'by_source' => $bySource,
                'by_method' => $byMethod,
                'total' => array_sum($byMethod),
            ];
        });

        return response()->json(['days' => $days]);
    }

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

        // Mois avec au moins un paiement
        $paymentMonths = ReservationPayment::select(DB::raw('YEAR(paid_at) as year'), DB::raw('MONTH(paid_at) as month'))
            ->groupBy(DB::raw('YEAR(paid_at)'), DB::raw('MONTH(paid_at)'))
            ->get()
            ->map(fn ($row) => $row->year.'-'.$row->month);

        // Mois avec au moins un acompte payé
        $acompteMonths = Reservation::whereNotNull('acompte_paye_le')
            ->where('acompte_montant', '>', 0)
            ->select(DB::raw('YEAR(acompte_paye_le) as year'), DB::raw('MONTH(acompte_paye_le) as month'))
            ->groupBy(DB::raw('YEAR(acompte_paye_le)'), DB::raw('MONTH(acompte_paye_le)'))
            ->get()
            ->map(fn ($row) => $row->year.'-'.$row->month);

        // Mois avec des travaux de maintenance réalisés sur les vélos de location
        $maintenanceMonths = BikeMaintenanceLog::where('status', 'done')
            ->select(DB::raw('YEAR(date) as year'), DB::raw('MONTH(date) as month'))
            ->groupBy(DB::raw('YEAR(date)'), DB::raw('MONTH(date)'))
            ->get()
            ->map(fn ($row) => $row->year.'-'.$row->month);

        $allMonths = $paymentMonths->merge($acompteMonths)->merge($maintenanceMonths)->unique();

        foreach ($allMonths as $key) {
            [$year, $month] = explode('-', $key);
            $this->kpiUpdater->rebuildLocationKpiForMonth((int) $year, (int) $month);
        }

        return $allMonths->count();
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
            'paid_at' => $quote->paid_at?->format('Y-m-d'),
            'client_notified' => $quote->client_notified,
            'client_notified_at' => $quote->client_notified_at?->format('Y-m-d'),
            'created_at' => $quote->created_at->toISOString(),
            'can_delete' => $quote->canDelete(),
            'is_invoice' => $quote->isInvoice(),
        ];
    }
}
