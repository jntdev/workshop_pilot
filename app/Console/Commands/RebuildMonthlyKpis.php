<?php

namespace App\Console\Commands;

use App\Enums\Metier;
use App\Models\MonthlyKpi;
use App\Models\Quote;
use App\Models\Reservation;
use App\Models\ReservationPayment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RebuildMonthlyKpis extends Command
{
    protected $signature = 'kpis:rebuild-monthly
                            {--metier= : Métier spécifique (atelier, vente, location)}
                            {--all : Reconstruire tous les métiers}';

    protected $description = 'Reconstruit les KPIs mensuels à partir des factures/paiements existants';

    public function handle(): int
    {
        $metierOption = $this->option('metier');
        $all = $this->option('all');

        if (! $metierOption && ! $all) {
            $this->error('Vous devez spécifier --metier=<valeur> ou --all');

            return Command::FAILURE;
        }

        $metiers = $all
            ? array_column(Metier::cases(), 'value')
            : [$metierOption];

        foreach ($metiers as $metier) {
            if ($metier === Metier::Location->value) {
                $this->rebuildForLocation();
            } else {
                $this->rebuildForMetier($metier);
            }
        }

        $this->info('Reconstruction des KPIs terminée.');

        return Command::SUCCESS;
    }

    protected function rebuildForMetier(string $metier): void
    {
        $this->info("Reconstruction des KPIs pour le métier : {$metier}");

        // Supprimer les KPIs existants pour ce métier
        $deleted = MonthlyKpi::where('metier', $metier)->delete();
        $this->line("  - {$deleted} ligne(s) supprimée(s)");

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

        $this->line("  - {$aggregates->count()} ligne(s) créée(s)");
    }

    protected function rebuildForLocation(): void
    {
        $this->info('Reconstruction des KPIs pour le métier : location');

        // Supprimer les KPIs existants pour location
        $deleted = MonthlyKpi::where('metier', Metier::Location->value)->delete();
        $this->line("  - {$deleted} ligne(s) supprimée(s)");

        // Agréger les paiements par année/mois
        $paymentAggregates = ReservationPayment::select(
            DB::raw('YEAR(paid_at) as year'),
            DB::raw('MONTH(paid_at) as month'),
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('COUNT(DISTINCT reservation_id) as reservation_count')
        )
            ->groupBy(DB::raw('YEAR(paid_at)'), DB::raw('MONTH(paid_at)'))
            ->get()
            ->keyBy(fn ($row) => $row->year . '-' . $row->month);

        // Agréger les acomptes par année/mois
        $acompteAggregates = Reservation::whereNotNull('acompte_paye_le')
            ->where('acompte_montant', '>', 0)
            ->select(
                DB::raw('YEAR(acompte_paye_le) as year'),
                DB::raw('MONTH(acompte_paye_le) as month'),
                DB::raw('SUM(acompte_montant) as total_acompte'),
                DB::raw('COUNT(*) as acompte_count')
            )
            ->groupBy(DB::raw('YEAR(acompte_paye_le)'), DB::raw('MONTH(acompte_paye_le)'))
            ->get()
            ->keyBy(fn ($row) => $row->year . '-' . $row->month);

        // Combiner les deux sources
        $allMonths = $paymentAggregates->keys()->merge($acompteAggregates->keys())->unique();

        $created = 0;
        foreach ($allMonths as $key) {
            $payment = $paymentAggregates->get($key);
            $acompte = $acompteAggregates->get($key);

            $year = $payment?->year ?? $acompte->year;
            $month = $payment?->month ?? $acompte->month;

            $totalRevenue = ($payment->total_amount ?? 0) + ($acompte->total_acompte ?? 0);

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
                'revenue_ht' => $totalRevenue,
                'margin_ht' => 0,
            ]);
            $created++;
        }

        $this->line("  - {$created} ligne(s) créée(s)");
    }
}
