<?php

namespace App\Services\Tracking;

use App\Enums\VisitsPeriod;
use App\Models\ClicSite;
use App\Models\ParcoursSite;
use App\Models\VisiteFiche;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Indicateurs de clics du site public : boutons de l'accueil, filtres du catalogue, entonnoir Black Friday,
 * effet du label « Visible au magasin », contacts et itinéraires, couleurs choisies.
 * Le site compte une personne une seule fois par élément et par jour (colonne `visiteurs`) : additionnés,
 * les visiteurs ne sont donc pas des personnes distinctes.
 */
class WebsiteClicksReport
{
    private const HOME_BUTTONS = ['acheter', 'reparer', 'louer', 'blackfriday'];

    private const CONTACT_TYPES = ['telephone', 'email', 'itineraire'];

    public function isConfigured(): bool
    {
        return filled(config('database.connections.tracking.database'));
    }

    /**
     * @return array{has_data: false}|array{
     *     has_data: true,
     *     tracked_since: string,
     *     range: array{start: string, end: string},
     *     home: array{visitors: int, views: int, buttons: list<array{key: string, visitors: int, clicks: int, rate: float|null}>},
     *     filters: list<array{univers: string, key: string, visitors: int, clicks: int}>,
     *     tunnels: list<array{key: string, steps: list<array{step: string, visits: int}>}>,
     *     entries: list<array{step: string, visits: int}>,
     *     blackfriday: array{fiches: list<array{slug: string, marque: string, visitors: int, views: int}>},
     *     magasin: array{with: array<string, int|float|null>, without: array<string, int|float|null>},
     *     contacts: array{types: list<array{key: string, visitors: int, clicks: int}>, places: list<array{page: string, zone: string, key: string, visitors: int, clicks: int}>},
     *     colors: list<array{slug: string, marque: string, couleur: string, card_clicks: int, fiche_clicks: int, visitors: int}>
     * }
     */
    public function build(VisitsPeriod $period, CarbonImmutable $today): array
    {
        $firstTrackedDay = ClicSite::query()->min('jour');

        if ($firstTrackedDay === null) {
            return ['has_data' => false];
        }

        $firstTrackedDay = CarbonImmutable::parse($firstTrackedDay)->startOfDay();
        $range = VisitsRange::forPeriod($period, $today, $firstTrackedDay);

        $rows = ClicSite::query()
            ->betweenDays($range->start, $range->end)
            ->groupBy('categorie', 'cle', 'contexte')
            ->selectRaw('categorie, cle, contexte, SUM(visiteurs) AS visitors, SUM(clics) AS clicks')
            ->toBase()
            ->get();

        return [
            'has_data' => true,
            'tracked_since' => $firstTrackedDay->toDateString(),
            'range' => ['start' => $range->start->toDateString(), 'end' => $range->end->toDateString()],
            'home' => $this->home($rows),
            'filters' => $this->filters($rows),
            'tunnels' => $this->tunnels($range),
            'entries' => $this->entries($range),
            'blackfriday' => $this->blackfriday($range),
            'magasin' => $this->magasin($range),
            'contacts' => $this->contacts($rows),
            'colors' => $this->colors($rows),
        ];
    }

    /**
     * @param  Collection<int, object>  $rows
     * @return array{visitors: int, views: int, buttons: list<array{key: string, visitors: int, clicks: int, rate: float|null}>}
     */
    private function home(Collection $rows): array
    {
        $page = $this->total($rows, 'page', 'accueil');

        $buttons = array_map(function (string $key) use ($rows, $page): array {
            $button = $this->total($rows, 'accueil', $key);

            return [
                'key' => $key,
                'visitors' => $button['visitors'],
                'clicks' => $button['clicks'],
                'rate' => $page['visitors'] > 0 ? $button['visitors'] / $page['visitors'] * 100 : null,
            ];
        }, self::HOME_BUTTONS);

        return ['visitors' => $page['visitors'], 'views' => $page['clicks'], 'buttons' => $buttons];
    }

    /**
     * @param  Collection<int, object>  $rows
     * @return list<array{univers: string, key: string, visitors: int, clicks: int}>
     */
    private function filters(Collection $rows): array
    {
        return $rows->where('categorie', 'filtre')
            ->map(fn (object $row) => [
                'univers' => $row->contexte,
                'key' => $row->cle,
                'visitors' => (int) $row->visitors,
                'clicks' => (int) $row->clicks,
            ])
            ->sortBy([['univers', 'asc'], ['clicks', 'desc'], ['key', 'asc']])
            ->values()
            ->all();
    }

    /**
     * Chaque tunnel du site : combien de visites atteignent chaque étape, dans l'ordre (calculé la nuit par le site).
     *
     * @return list<array{key: string, steps: list<array{step: string, visits: int}>}>
     */
    private function tunnels(VisitsRange $range): array
    {
        return ParcoursSite::query()
            ->betweenDays($range->start, $range->end)
            ->where('parcours', '!=', ParcoursSite::ENTRY_PARCOURS)
            ->groupBy('parcours', 'rang', 'etape')
            ->selectRaw('parcours, rang, etape, SUM(visites) AS visits')
            ->orderBy('parcours')
            ->orderBy('rang')
            ->toBase()
            ->get()
            ->groupBy('parcours')
            ->map(fn (Collection $steps, string $key) => [
                'key' => $key,
                'steps' => $steps->map(fn (object $row) => ['step' => $row->etape, 'visits' => (int) $row->visits])->values()->all(),
            ])
            ->values()
            ->all();
    }

    /**
     * Par quelle étape les visites entrent sur le site (première étape de chaque visite).
     *
     * @return list<array{step: string, visits: int}>
     */
    private function entries(VisitsRange $range): array
    {
        return ParcoursSite::query()
            ->betweenDays($range->start, $range->end)
            ->where('parcours', ParcoursSite::ENTRY_PARCOURS)
            ->groupBy('etape')
            ->selectRaw('etape, SUM(visites) AS visits')
            ->orderByDesc('visits')
            ->orderBy('etape')
            ->toBase()
            ->get()
            ->map(fn (object $row) => ['step' => $row->etape, 'visits' => (int) $row->visits])
            ->all();
    }

    /**
     * Fiches des vélos de l'opération Black Friday, par visiteurs.
     *
     * @return array{fiches: list<array{slug: string, marque: string, visitors: int, views: int}>}
     */
    private function blackfriday(VisitsRange $range): array
    {
        $fiches = VisiteFiche::query()
            ->betweenDays($range->start, $range->end)
            ->where('promo_blackfriday', true)
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
            ]);

        return ['fiches' => $fiches->all()];
    }

    /**
     * Fiches avec le label « Visible au magasin » comparées aux autres, sur les mêmes jours.
     *
     * @return array{with: array<string, int|float|null>, without: array<string, int|float|null>}
     */
    private function magasin(VisitsRange $range): array
    {
        $fiches = VisiteFiche::query()
            ->betweenDays($range->start, $range->end)
            ->groupBy('slug')
            ->selectRaw('slug, MAX(visible_magasin) AS magasin, SUM(visiteurs) AS visitors, SUM(vues) AS views')
            ->toBase()
            ->get();

        return [
            'with' => $this->magasinStats($range, $fiches->where('magasin', 1)),
            'without' => $this->magasinStats($range, $fiches->where('magasin', 0)),
        ];
    }

    /**
     * @param  Collection<int, object>  $fiches
     * @return array<string, int|float|null>
     */
    private function magasinStats(VisitsRange $range, Collection $fiches): array
    {
        $visitors = (int) $fiches->sum('visitors');
        $requests = $this->requestsCount($range, $fiches->pluck('slug')->all());

        return [
            'fiches' => $fiches->count(),
            'visitors' => $visitors,
            'views' => (int) $fiches->sum('views'),
            'visitors_per_fiche' => $fiches->count() > 0 ? $visitors / $fiches->count() : null,
            'requests' => $requests,
            'requests_per_100_visitors' => $visitors > 0 ? $requests / $visitors * 100 : null,
        ];
    }

    /**
     * @param  list<string>  $slugs
     */
    private function requestsCount(VisitsRange $range, array $slugs): int
    {
        if ($slugs === []) {
            return 0;
        }

        return DB::connection('tracking')->table('demandes_recontact')
            ->whereIn('slug', $slugs)
            ->whereBetween('created_at', [$range->start->startOfDay(), $range->end->endOfDay()])
            ->count();
    }

    /**
     * @param  Collection<int, object>  $rows
     * @return array{types: list<array{key: string, visitors: int, clicks: int}>, places: list<array{page: string, zone: string, key: string, visitors: int, clicks: int}>}
     */
    private function contacts(Collection $rows): array
    {
        $contacts = $rows->where('categorie', 'contact');

        $types = array_map(fn (string $key) => [
            'key' => $key,
            'visitors' => (int) $contacts->where('cle', $key)->sum('visitors'),
            'clicks' => (int) $contacts->where('cle', $key)->sum('clicks'),
        ], self::CONTACT_TYPES);

        $places = $contacts
            ->map(fn (object $row) => [
                'page' => Str::before($row->contexte, '/'),
                'zone' => Str::contains($row->contexte, '/') ? Str::after($row->contexte, '/') : '',
                'key' => $row->cle,
                'visitors' => (int) $row->visitors,
                'clicks' => (int) $row->clicks,
            ])
            ->sortBy([['clicks', 'desc'], ['page', 'asc'], ['zone', 'asc']])
            ->values()
            ->all();

        return ['types' => $types, 'places' => $places];
    }

    /**
     * Une ligne par vélo et par couleur, avec les clics sur la carte du catalogue et sur la fiche.
     *
     * @param  Collection<int, object>  $rows
     * @return list<array{slug: string, marque: string, couleur: string, card_clicks: int, fiche_clicks: int, visitors: int}>
     */
    private function colors(Collection $rows): array
    {
        $colors = $rows->whereIn('categorie', ['couleur_carte', 'couleur_fiche']);
        $marques = VisiteFiche::query()->whereIn('slug', $colors->pluck('contexte')->unique()->all())->pluck('marque', 'slug');

        return $colors
            ->groupBy(fn (object $row) => $row->contexte.'|'.$row->cle)
            ->map(function (Collection $group) use ($marques): array {
                $slug = $group->first()->contexte;

                return [
                    'slug' => $slug,
                    'marque' => $marques[$slug] ?? Str::before($slug, '-'),
                    'couleur' => $group->first()->cle,
                    'card_clicks' => (int) $group->where('categorie', 'couleur_carte')->sum('clicks'),
                    'fiche_clicks' => (int) $group->where('categorie', 'couleur_fiche')->sum('clicks'),
                    'visitors' => (int) $group->sum('visitors'),
                ];
            })
            ->sortBy([
                fn (array $a, array $b) => ($b['card_clicks'] + $b['fiche_clicks']) <=> ($a['card_clicks'] + $a['fiche_clicks']),
                fn (array $a, array $b) => [$a['slug'], $a['couleur']] <=> [$b['slug'], $b['couleur']],
            ])
            ->values()
            ->all();
    }

    /**
     * Visiteurs et clics d'un élément, tous contextes confondus (ex. le catalogue filtré, loisir et sport).
     *
     * @param  Collection<int, object>  $rows
     * @return array{visitors: int, clicks: int}
     */
    private function total(Collection $rows, string $categorie, string $key): array
    {
        $matching = $rows->where('categorie', $categorie)->where('cle', $key);

        return ['visitors' => (int) $matching->sum('visitors'), 'clicks' => (int) $matching->sum('clicks')];
    }
}
