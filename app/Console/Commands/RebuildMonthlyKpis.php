<?php

namespace App\Console\Commands;

use App\Enums\Metier;
use App\Models\MonthlyKpi;
use App\Models\Quote;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RebuildMonthlyKpis extends Command
{
    protected $signature = 'kpis:rebuild-monthly
                            {--metier= : Métier spécifique (atelier, vente, location)}
                            {--all : Reconstruire tous les métiers}';

    protected $description = 'Reconstruit les KPIs mensuels à partir des factures existantes';

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
            $this->rebuildForMetier($metier);
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
}
