<?php

namespace App\Services\Tracking;

use App\Enums\VisitsPeriod;
use App\Models\VisiteFiche;
use App\Models\VisiteSource;
use Carbon\CarbonImmutable;

/**
 * Détail d'une fiche vélo du site public : sa courbe de visites jour par jour (ou semaine par semaine) et l'origine
 * de ses visiteurs, pour comprendre un pic de fréquentation.
 */
class WebsiteFicheReport
{
    public function isConfigured(): bool
    {
        return filled(config('database.connections.tracking.database'));
    }

    /**
     * @return array{has_data: false}|array{
     *     has_data: true,
     *     slug: string,
     *     marque: string,
     *     range: array{start: string, end: string, granularity: 'day'|'week'},
     *     totals: array{visitors: int, views: int},
     *     series: list<array{date: string, visitors: int, views: int}>,
     *     sources: list<array{source: string, visitors: int, views: int}>,
     *     promo_blackfriday: bool,
     *     visible_magasin: bool
     * }
     */
    public function build(string $slug, VisitsPeriod $period, CarbonImmutable $today): array
    {
        $firstDay = VisiteFiche::query()->where('slug', $slug)->min('jour');

        if ($firstDay === null) {
            return ['has_data' => false];
        }

        $range = VisitsRange::forPeriod($period, $today, CarbonImmutable::parse($firstDay)->startOfDay());
        $buckets = array_fill_keys($range->bucketStarts(), ['visitors' => 0, 'views' => 0]);

        $days = VisiteFiche::query()
            ->betweenDays($range->start, $range->end)
            ->where('slug', $slug)
            ->orderBy('jour')
            ->get();

        foreach ($days as $day) {
            $bucket = $range->bucketOf((string) $day->jour);
            $buckets[$bucket]['visitors'] += (int) $day->visiteurs;
            $buckets[$bucket]['views'] += (int) $day->vues;
        }

        $sources = VisiteSource::query()
            ->betweenDays($range->start, $range->end)
            ->where('cible', $slug)
            ->groupBy('source')
            ->selectRaw('source, SUM(visiteurs) AS visitors, SUM(vues) AS views')
            ->orderByDesc('visitors')
            ->orderBy('source')
            ->toBase()
            ->get()
            ->map(fn (object $row) => ['source' => $row->source, 'visitors' => (int) $row->visitors, 'views' => (int) $row->views])
            ->all();

        $latest = VisiteFiche::query()->where('slug', $slug)->orderByDesc('jour')->first();

        return [
            'has_data' => true,
            'slug' => $slug,
            'marque' => (string) $latest?->marque,
            'range' => ['start' => $range->start->toDateString(), 'end' => $range->end->toDateString(), 'granularity' => $range->granularity()],
            'totals' => ['visitors' => (int) $days->sum('visiteurs'), 'views' => (int) $days->sum('vues')],
            'series' => array_map(fn (string $date, array $totals) => ['date' => $date, ...$totals], array_keys($buckets), $buckets),
            'sources' => $sources,
            'promo_blackfriday' => (bool) $latest?->promo_blackfriday,
            'visible_magasin' => (bool) $latest?->visible_magasin,
        ];
    }
}
