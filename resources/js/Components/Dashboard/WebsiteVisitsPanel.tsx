import { useState, useEffect } from 'react';
import VisitsChart, { type VisitsGranularity, type VisitsPoint } from '@/Components/Dashboard/VisitsChart';
import { formatPercent, formatShare } from '@/Components/Dashboard/KpiCard';

type Period = '7d' | '30d' | '3m' | '6m' | '12m';

interface Kpi {
    value: number | null;
    trend: number | null;
}

interface BrandRow {
    marque: string;
    visitors: number;
    views: number;
}

interface FicheRow {
    slug: string;
    marque: string;
    visitors: number;
    views: number;
}

interface VisitsStatistics {
    configured: true;
    has_data: true;
    tracked_since: string;
    range: { start: string; end: string; granularity: VisitsGranularity };
    kpis: { visitors: Kpi; views: Kpi; views_per_visitor: Kpi; fiches: Kpi };
    series: VisitsPoint[];
    brands: BrandRow[];
    fiches: FicheRow[];
}

type VisitsResponse = { configured: false } | { configured: true; has_data: false } | VisitsStatistics;

const PERIODS: { key: Period; label: string }[] = [
    { key: '7d', label: '7 jours' },
    { key: '30d', label: '30 jours' },
    { key: '3m', label: '3 mois' },
    { key: '6m', label: '6 mois' },
    { key: '12m', label: '12 mois' },
];

const KPI_TILES: { key: keyof VisitsStatistics['kpis']; label: string; description: string; isRatio?: boolean }[] = [
    { key: 'visitors', label: 'Visiteurs', description: 'Une personne compte une fois par fiche et par jour' },
    { key: 'views', label: 'Pages vues', description: 'Chaque affichage d\'une fiche, rouvertures comprises' },
    { key: 'views_per_visitor', label: 'Vues par visiteur', description: 'Affichages moyens d\'une fiche par visiteur', isRatio: true },
    { key: 'fiches', label: 'Fiches consultées', description: 'Fiches vues au moins une fois sur la période' },
];

const LOAD_TIMEOUT_MS = 10000;
const TOP_FICHES_COUNT = 10;
const DEFAULT_ERROR = 'Impossible de charger les statistiques de visites.';

function formatInteger(value: number): string {
    return new Intl.NumberFormat('fr-FR').format(value);
}

function formatRatio(value: number): string {
    return new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
}

function formatDate(iso: string, withYear: boolean): string {
    return new Date(`${iso}T12:00:00`).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'short',
        ...(withYear ? { year: 'numeric' } : {}),
    });
}

function formatBrand(marque: string): string {
    return marque.toUpperCase();
}

/** "ncm-munich-n8c" → "Munich N8C" : on retire le préfixe de marque, les codes modèle (avec chiffres) passent en majuscules. */
function formatFicheName(fiche: FicheRow): string {
    const name = fiche.slug.startsWith(`${fiche.marque}-`) ? fiche.slug.slice(fiche.marque.length + 1) : fiche.slug;
    return name
        .split('-')
        .map(word => (/\d/.test(word) ? word.toUpperCase() : word.charAt(0).toUpperCase() + word.slice(1)))
        .join(' ');
}

function trendClassName(trend: number | null): string {
    if (trend === null) return 'website-visits__kpi-trend';
    return `website-visits__kpi-trend ${trend >= 0 ? 'website-visits__kpi-trend--up' : 'website-visits__kpi-trend--down'}`;
}

export default function WebsiteVisitsPanel() {
    const [period, setPeriod] = useState<Period>('30d');
    const [marque, setMarque] = useState<string | null>(null);
    const [response, setResponse] = useState<VisitsResponse | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [reloadKey, setReloadKey] = useState(0);
    const [showAllFiches, setShowAllFiches] = useState(false);

    useEffect(() => {
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), LOAD_TIMEOUT_MS);
        let isSuperseded = false;

        const params = new URLSearchParams({ period });
        if (marque) {
            params.set('marque', marque);
        }

        const load = async () => {
            setIsLoading(true);
            setError(null);

            try {
                const res = await fetch(`/api/dashboard/website-visits?${params}`, {
                    headers: { Accept: 'application/json' },
                    signal: controller.signal,
                });
                if (!res.ok) {
                    const body = res.status === 503 ? await res.json().catch(() => null) : null;
                    throw new Error(body?.message ?? DEFAULT_ERROR);
                }
                const json: VisitsResponse = await res.json();
                if (!isSuperseded) {
                    setResponse(json);
                }
            } catch (err) {
                if (isSuperseded) return;
                const timedOut = err instanceof DOMException && err.name === 'AbortError';
                setError(timedOut ? 'Le chargement prend trop de temps.' : err instanceof Error ? err.message : DEFAULT_ERROR);
            } finally {
                clearTimeout(timeout);
                if (!isSuperseded) {
                    setIsLoading(false);
                }
            }
        };

        load();

        return () => {
            isSuperseded = true;
            clearTimeout(timeout);
            controller.abort();
        };
    }, [period, marque, reloadKey]);

    const stats = response && response.configured && response.has_data ? response : null;
    const isUnavailable = response !== null && stats === null;

    return (
        <div className="website-visits">
            <div className="website-visits__toolbar">
                <div className="website-visits__heading">
                    <span className="website-visits__title">Visites des fiches vélos</span>
                    {stats && (
                        <span className="website-visits__range">
                            {formatDate(stats.range.start, false)} – {formatDate(stats.range.end, true)}
                        </span>
                    )}
                </div>
                {!isUnavailable && (
                    <div className="website-visits__periods" role="group" aria-label="Période">
                        {PERIODS.map(p => (
                            <button
                                key={p.key}
                                type="button"
                                className={`website-visits__period ${period === p.key ? 'website-visits__period--active' : ''}`}
                                aria-pressed={period === p.key}
                                onClick={() => setPeriod(p.key)}
                            >
                                {p.label}
                            </button>
                        ))}
                    </div>
                )}
            </div>

            {marque && !isUnavailable && (
                <div className="website-visits__filter">
                    <span>Marque : <strong>{formatBrand(marque)}</strong></span>
                    <button type="button" className="website-visits__filter-clear" onClick={() => setMarque(null)} aria-label="Retirer le filtre marque">
                        ×
                    </button>
                </div>
            )}

            {error ? (
                <div className="website-visits__error">
                    <span>{error}</span>
                    <button type="button" className="website-visits__retry-btn" onClick={() => setReloadKey(key => key + 1)}>
                        Réessayer
                    </button>
                </div>
            ) : response === null ? (
                <div className="website-visits__loading">
                    <span className="website-visits__spinner" aria-hidden="true" />
                    Chargement des visites...
                </div>
            ) : !response.configured ? (
                <div className="website-visits__notice">
                    Le suivi des visites n'est pas configuré sur cet environnement.
                    <span className="website-visits__notice-hint">Renseignez les variables DB_TRACKING_* du fichier .env pour l'activer.</span>
                </div>
            ) : stats === null ? (
                <div className="website-visits__notice">Aucune visite enregistrée pour le moment.</div>
            ) : (
                <div className={`website-visits__body ${isLoading ? 'website-visits__body--refreshing' : ''}`}>
                    <div className="website-visits__kpis">
                        {KPI_TILES.map(tile => {
                            const kpi = stats.kpis[tile.key];
                            return (
                                <div key={tile.key} className="website-visits__kpi">
                                    <span className="website-visits__kpi-label">{tile.label}</span>
                                    <span className="website-visits__kpi-value">
                                        {kpi.value === null ? '—' : tile.isRatio ? formatRatio(kpi.value) : formatInteger(kpi.value)}
                                    </span>
                                    <span
                                        className={trendClassName(kpi.trend)}
                                        title={kpi.trend === null ? 'Pas de période précédente comparable (suivi trop récent ou aucune donnée)' : undefined}
                                    >
                                        {kpi.trend === null ? '—' : `${formatPercent(kpi.trend)} vs période précédente`}
                                    </span>
                                    <span className="website-visits__kpi-description">{tile.description}</span>
                                </div>
                            );
                        })}
                    </div>

                    <details className="website-visits__method">
                        <summary>Comment sont comptés les visiteurs et les pages vues ?</summary>
                        <p>
                            Chaque fois qu'une fiche vélo s'affiche sur le site, elle gagne <strong>une page vue</strong>.
                            Elle gagne aussi <strong>un visiteur</strong> la première fois qu'une personne l'ouvre dans la journée :
                            le site reconnaît la personne sans cookie, par son adresse IP et son navigateur, et l'oublie le lendemain.
                        </p>
                        <p>
                            Exemple : quelqu'un ouvre 3 fois la fiche Rakish et 1 fois la fiche Simun le même jour
                            → <strong>2 visiteurs</strong> (un par fiche) et <strong>4 pages vues</strong>. S'il revient le lendemain, il est compté à nouveau.
                        </p>
                        <p>
                            Les visiteurs s'additionnent donc fiche par fiche et jour par jour : ce n'est pas le nombre de personnes
                            différentes venues sur le site. Les robots et les connexions depuis le magasin ne sont pas comptés.
                        </p>
                    </details>

                    <div className="website-visits__card">
                        <div className="website-visits__card-header">
                            <span className="website-visits__card-title">
                                Évolution {stats.range.granularity === 'week' ? 'par semaine' : 'par jour'}
                            </span>
                            <div className="website-visits__legend">
                                <span className="website-visits__legend-item website-visits__legend-item--visitors">Visiteurs</span>
                                <span className="website-visits__legend-item website-visits__legend-item--views">Pages vues</span>
                            </div>
                        </div>
                        <VisitsChart points={stats.series} granularity={stats.range.granularity} />
                    </div>

                    <div className="website-visits__rankings">
                        <BrandRanking brands={stats.brands} selected={marque} onSelect={setMarque} />
                        <FicheRanking
                            fiches={stats.fiches}
                            showAll={showAllFiches}
                            onToggleShowAll={() => setShowAllFiches(value => !value)}
                        />
                    </div>

                    <p className="website-visits__footnote">Suivi depuis le {formatDate(stats.tracked_since, true)}.</p>
                </div>
            )}
        </div>
    );
}

interface BrandRankingProps {
    brands: BrandRow[];
    selected: string | null;
    onSelect: (marque: string | null) => void;
}

function BrandRanking({ brands, selected, onSelect }: BrandRankingProps) {
    const totalVisitors = brands.reduce((sum, brand) => sum + brand.visitors, 0);
    const maxVisitors = Math.max(1, ...brands.map(brand => brand.visitors));

    return (
        <div className="website-visits__card">
            <div className="website-visits__card-header">
                <span className="website-visits__card-title">Par marque</span>
                <span className="website-visits__card-hint">Visiteurs · cliquez pour filtrer</span>
            </div>
            {brands.length === 0 ? (
                <div className="website-visits__empty">Aucune visite sur la période.</div>
            ) : (
                <ol className="website-visits__ranking">
                    {brands.map((brand, index) => {
                        const isSelected = selected === brand.marque;
                        const modifiers = [
                            isSelected ? 'website-visits__rank-row--active' : '',
                            selected !== null && !isSelected ? 'website-visits__rank-row--dimmed' : '',
                        ].join(' ');

                        return (
                            <li key={brand.marque}>
                                <button
                                    type="button"
                                    className={`website-visits__rank-row website-visits__rank-row--brand ${modifiers}`}
                                    aria-pressed={isSelected}
                                    onClick={() => onSelect(isSelected ? null : brand.marque)}
                                    title={`${formatInteger(brand.views)} pages vues`}
                                >
                                    <span className="website-visits__rank-position">{index + 1}</span>
                                    <span className="website-visits__rank-label">{formatBrand(brand.marque)}</span>
                                    <span className="website-visits__rank-bar">
                                        <span style={{ width: `${(brand.visitors / maxVisitors) * 100}%` }} />
                                    </span>
                                    <span className="website-visits__rank-value">{formatInteger(brand.visitors)}</span>
                                    <span className="website-visits__rank-secondary">
                                        {totalVisitors > 0 ? formatShare((brand.visitors / totalVisitors) * 100) : '—'}
                                    </span>
                                </button>
                            </li>
                        );
                    })}
                </ol>
            )}
        </div>
    );
}

interface FicheRankingProps {
    fiches: FicheRow[];
    showAll: boolean;
    onToggleShowAll: () => void;
}

function FicheRanking({ fiches, showAll, onToggleShowAll }: FicheRankingProps) {
    const visibleFiches = showAll ? fiches : fiches.slice(0, TOP_FICHES_COUNT);
    const maxVisitors = Math.max(1, ...fiches.map(fiche => fiche.visitors));

    return (
        <div className="website-visits__card">
            <div className="website-visits__card-header">
                <span className="website-visits__card-title">Fiches les plus visitées</span>
                <span className="website-visits__card-hint">Visiteurs · pages vues</span>
            </div>
            {fiches.length === 0 ? (
                <div className="website-visits__empty">Aucune visite sur la période.</div>
            ) : (
                <>
                    <ol className="website-visits__ranking">
                        {visibleFiches.map((fiche, index) => (
                            <li key={fiche.slug} className="website-visits__rank-row website-visits__rank-row--fiche" title={fiche.slug}>
                                <span className="website-visits__rank-position">{index + 1}</span>
                                <span className="website-visits__rank-label">{formatFicheName(fiche)}</span>
                                <span className="website-visits__brand-tag">{formatBrand(fiche.marque)}</span>
                                <span className="website-visits__rank-bar">
                                    <span style={{ width: `${(fiche.visitors / maxVisitors) * 100}%` }} />
                                </span>
                                <span className="website-visits__rank-value">{formatInteger(fiche.visitors)}</span>
                                <span className="website-visits__rank-secondary">{formatInteger(fiche.views)} vues</span>
                            </li>
                        ))}
                    </ol>
                    {fiches.length > TOP_FICHES_COUNT && (
                        <button type="button" className="website-visits__more" onClick={onToggleShowAll}>
                            {showAll ? `Afficher le top ${TOP_FICHES_COUNT}` : `Afficher les ${fiches.length} fiches`}
                        </button>
                    )}
                </>
            )}
        </div>
    );
}
