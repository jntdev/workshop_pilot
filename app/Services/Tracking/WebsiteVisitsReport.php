<?php

namespace App\Services\Tracking;

use App\Enums\VisitsPeriod;
use App\Models\VisiteFiche;
use Carbon\CarbonImmutable;

/**
 * Statistiques de visites des fiches du site public : chiffres clés, courbe et classements.
 * Le site public compte une personne une seule fois par fiche et par jour (colonne `visiteurs`) et ne relie pas
 * ses visites d'un jour à l'autre : additionnés, les visiteurs ne sont donc pas des personnes distinctes.
 */
class WebsiteVisitsReport
{
    public function isConfigured(): bool
    {
        return filled(config('database.connections.tracking.database'));
    }

    /**
     * @return array{has_data: false}|array{
     *     has_data: true,
     *     tracked_since: string,
     *     range: array{start: string, end: string, granularity: 'day'|'week'},
     *     kpis: array{visitors: array{value: int, trend: float|null}, views: array{value: int, trend: float|null}, views_per_visitor: array{value: float|null, trend: float|null}, fiches: array{value: int, trend: float|null}},
     *     series: list<array{date: string, visitors: int, views: int}>,
     *     brands: list<array{marque: string, visitors: int, views: int}>,
     *     fiches: list<array{slug: string, marque: string, visitors: int, views: int}>
     * }
     */
    public function build(VisitsPeriod $period, ?string $marque, CarbonImmutable $today): array
    {
        $firstTrackedDay = VisiteFiche::query()->min('jour');

        if ($firstTrackedDay === null) {
            return ['has_data' => false];
        }

        $firstTrackedDay = CarbonImmutable::parse($firstTrackedDay)->startOfDay();
        $range = VisitsRange::forPeriod($period, $today, $firstTrackedDay);
        $previousRange = $range->previous();

        $previousTotals = $previousRange->start->greaterThanOrEqualTo($firstTrackedDay)
            ? $this->totals($previousRange, $marque)
            : null;

        return [
            'has_data' => true,
            'tracked_since' => $firstTrackedDay->toDateString(),
            'range' => [
                'start' => $range->start->toDateString(),
                'end' => $range->end->toDateString(),
                'granularity' => $range->granularity(),
            ],
            'kpis' => $this->kpis($this->totals($range, $marque), $previousTotals),
            'series' => $this->series($range, $marque),
            'brands' => $this->brandRanking($range),
            'fiches' => $this->ficheRanking($range, $marque),
        ];
    }

    /**
     * @return array{visitors: int, views: int, fiches: int}
     */
    private function totals(VisitsRange $range, ?string $marque): array
    {
        $row = VisiteFiche::query()
            ->betweenDays($range->start, $range->end)
            ->ofMarque($marque)
            ->selectRaw('COALESCE(SUM(visiteurs), 0) AS visitors, COALESCE(SUM(vues), 0) AS views, COUNT(DISTINCT slug) AS fiches')
            ->toBase()
            ->first();

        return [
            'visitors' => (int) $row->visitors,
            'views' => (int) $row->views,
            'fiches' => (int) $row->fiches,
        ];
    }

    /**
     * @param  array{visitors: int, views: int, fiches: int}  $current
     * @param  array{visitors: int, views: int, fiches: int}|null  $previous  null si la période précédente n'est pas entièrement suivie
     * @return array{visitors: array{value: int, trend: float|null}, views: array{value: int, trend: float|null}, views_per_visitor: array{value: float|null, trend: float|null}, fiches: array{value: int, trend: float|null}}
     */
    private function kpis(array $current, ?array $previous): array
    {
        $viewsPerVisitor = $this->viewsPerVisitor($current);

        return [
            'visitors' => ['value' => $current['visitors'], 'trend' => $this->trend($current['visitors'], $previous['visitors'] ?? null)],
            'views' => ['value' => $current['views'], 'trend' => $this->trend($current['views'], $previous['views'] ?? null)],
            'views_per_visitor' => ['value' => $viewsPerVisitor, 'trend' => $this->trend($viewsPerVisitor, $previous === null ? null : $this->viewsPerVisitor($previous))],
            'fiches' => ['value' => $current['fiches'], 'trend' => $this->trend($current['fiches'], $previous['fiches'] ?? null)],
        ];
    }

    /**
     * @param  array{visitors: int, views: int, fiches: int}  $totals
     */
    private function viewsPerVisitor(array $totals): ?float
    {
        return $totals['visitors'] > 0 ? $totals['views'] / $totals['visitors'] : null;
    }

    /** Évolution en % par rapport à la période précédente. */
    private function trend(int|float|null $current, int|float|null $previous): ?float
    {
        if ($current === null || $previous === null || $previous <= 0) {
            return null;
        }

        return ($current - $previous) / $previous * 100;
    }

    /**
     * Une entrée par jour ou par semaine de la période, y compris quand personne n'est venu.
     *
     * @return list<array{date: string, visitors: int, views: int}>
     */
    private function series(VisitsRange $range, ?string $marque): array
    {
        $buckets = array_fill_keys($range->bucketStarts(), ['visitors' => 0, 'views' => 0]);

        $days = VisiteFiche::query()
            ->betweenDays($range->start, $range->end)
            ->ofMarque($marque)
            ->groupBy('jour')
            ->selectRaw('jour, SUM(visiteurs) AS visitors, SUM(vues) AS views')
            ->toBase()
            ->get();

        foreach ($days as $day) {
            $bucket = $range->bucketOf((string) $day->jour);
            $buckets[$bucket]['visitors'] += (int) $day->visitors;
            $buckets[$bucket]['views'] += (int) $day->views;
        }

        return array_map(
            fn (string $date, array $totals) => ['date' => $date, ...$totals],
            array_keys($buckets),
            $buckets,
        );
    }

    /**
     * Toutes les marques, sans tenir compte du filtre : le classement sert aussi à choisir la marque filtrée.
     *
     * @return list<array{marque: string, visitors: int, views: int}>
     */
    private function brandRanking(VisitsRange $range): array
    {
        return VisiteFiche::query()
            ->betweenDays($range->start, $range->end)
            ->groupBy('marque')
            ->selectRaw('marque, SUM(visiteurs) AS visitors, SUM(vues) AS views')
            ->orderByDesc('visitors')
            ->orderByDesc('views')
            ->orderBy('marque')
            ->toBase()
            ->get()
            ->map(fn (object $row) => [
                'marque' => $row->marque,
                'visitors' => (int) $row->visitors,
                'views' => (int) $row->views,
            ])
            ->all();
    }

    /**
     * @return list<array{slug: string, marque: string, visitors: int, views: int}>
     */
    private function ficheRanking(VisitsRange $range, ?string $marque): array
    {
        return VisiteFiche::query()
            ->betweenDays($range->start, $range->end)
            ->ofMarque($marque)
            ->groupBy('slug', 'marque')
            ->selectRaw('slug, marque, SUM(visiteurs) AS visitors, SUM(vues) AS views')
            ->orderByDesc('visitors')
            ->orderByDesc('views')
            ->orderBy('slug')
            ->toBase()
            ->get()
            ->map(fn (object $row) => [
                'slug' => $row->slug,
                'marque' => $row->marque,
                'visitors' => (int) $row->visitors,
                'views' => (int) $row->views,
            ])
            ->all();
    }
}
