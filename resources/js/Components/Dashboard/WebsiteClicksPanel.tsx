import { useState, useEffect, type ReactNode } from 'react';
import { formatShare } from '@/Components/Dashboard/KpiCard';
import TunnelChart from '@/Components/Dashboard/TunnelChart';

type Period = '7d' | '30d' | '3m' | '6m' | '12m';

interface HomeButton {
    key: string;
    visitors: number;
    clicks: number;
    rate: number | null;
}

interface FilterRow {
    univers: string;
    key: string;
    visitors: number;
    clicks: number;
}

interface TunnelStep {
    step: string;
    visits: number;
}

interface Tunnel {
    key: string;
    steps: TunnelStep[];
}

interface BlackFridayFiche {
    slug: string;
    marque: string;
    visitors: number;
    views: number;
}

interface MagasinStats {
    fiches: number;
    visitors: number;
    views: number;
    visitors_per_fiche: number | null;
    requests: number;
    requests_per_100_visitors: number | null;
}

interface ContactType {
    key: string;
    visitors: number;
    clicks: number;
}

interface ContactPlace {
    page: string;
    zone: string;
    key: string;
    visitors: number;
    clicks: number;
}

interface ColorRow {
    slug: string;
    marque: string;
    couleur: string;
    card_clicks: number;
    fiche_clicks: number;
    visitors: number;
}

interface SourceRow {
    source: string;
    visitors: number;
    views: number;
}

interface TopFicheSources {
    slug: string;
    marque: string;
    visitors: number;
    sources: { source: string; visitors: number }[];
}

interface ClicksStatistics {
    configured: true;
    has_data: true;
    tracked_since: string;
    range: { start: string; end: string };
    home: { visitors: number; views: number; buttons: HomeButton[] };
    filters: FilterRow[];
    tunnels: Tunnel[];
    entries: TunnelStep[];
    sources: { home: SourceRow[]; fiches: SourceRow[]; top_fiches: TopFicheSources[] };
    blackfriday: { fiches: BlackFridayFiche[] };
    magasin: { with: MagasinStats; without: MagasinStats };
    contacts: { types: ContactType[]; places: ContactPlace[] };
    colors: ColorRow[];
}

type ClicksResponse = { configured: false } | { configured: true; has_data: false } | ClicksStatistics;

const PERIODS: { key: Period; label: string }[] = [
    { key: '7d', label: '7 jours' },
    { key: '30d', label: '30 jours' },
    { key: '3m', label: '3 mois' },
    { key: '6m', label: '6 mois' },
    { key: '12m', label: '12 mois' },
];

const BUTTON_LABELS: Record<string, string> = {
    acheter: 'Acheter un vélo',
    reparer: 'Faire réparer',
    louer: 'Louer un vélo',
    blackfriday: 'Black Friday',
};

const UNIVERS_LABELS: Record<string, string> = { loisir: 'Loisir', sport: 'Sportifs', accessoires: 'Accessoires' };

const CONTACT_LABELS: Record<string, string> = { telephone: 'Téléphone', email: 'E-mail', itineraire: 'Itinéraire' };

const TUNNEL_LABELS: Record<string, { title: string; hint: string }> = {
    blackfriday: { title: 'Black Friday', hint: 'De l\'accueil à la demande de contact sur un vélo en promo' },
    achat: { title: 'Achat d\'un vélo', hint: 'De l\'accueil à la demande de contact sur une fiche' },
    reparation: { title: 'Réparation', hint: 'De l\'accueil à un appel ou un e-mail' },
    magasin: { title: 'Vélos visibles au magasin', hint: 'De la fiche d\'un vélo exposé à la demande de contact' },
};

const STEP_LABELS: Record<string, string> = {
    accueil: 'Page d\'accueil',
    bouton_acheter: 'Bouton « Acheter un vélo »',
    bouton_reparer: 'Bouton « Faire réparer »',
    bouton_louer: 'Bouton « Louer un vélo »',
    bouton_blackfriday: 'Bouton Black Friday',
    catalogue_loisir: 'Catalogue loisir',
    catalogue_sport: 'Catalogue sportifs',
    catalogue_accessoires: 'Catalogue accessoires',
    catalogue_blackfriday: 'Catalogue filtré Black Friday',
    fiche: 'Fiche vélo',
    fiche_blackfriday: 'Fiche d\'un vélo en promo',
    fiche_magasin: 'Fiche d\'un vélo visible au magasin',
    couleur: 'Couleur choisie',
    contact_telephone: 'Appel',
    contact_email: 'E-mail',
    contact_itineraire: 'Itinéraire',
    demande: 'Demande de contact',
    demande_blackfriday: 'Demande sur un vélo en promo',
};

const SOURCE_LABELS: Record<string, string> = {
    direct: 'Accès direct',
    interne: 'Navigation sur le site',
    moteur: 'Moteur de recherche',
    reseau_social: 'Réseau social',
    autre_site: 'Autre site',
    qr: 'QR code (affichettes)',
    mail: 'E-mail',
    fiche_google: 'Fiche Google',
    pub: 'Publicité',
};

const PAGE_LABELS: Record<string, string> = { accueil: 'Accueil', fiche: 'Fiche vélo' };

const ZONE_LABELS: Record<string, string> = { pied_de_page: 'Pied de page', menu: 'Menu', page: 'Corps de page', header: 'En-tête' };

const LOAD_TIMEOUT_MS = 10000;
const TOP_COUNT = 10;
const DEFAULT_ERROR = 'Impossible de charger les indicateurs de clics.';

function formatInteger(value: number): string {
    return new Intl.NumberFormat('fr-FR').format(value);
}

function formatDecimal(value: number | null): string {
    return value === null ? '—' : new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 1 }).format(value);
}

function formatDate(iso: string, withYear: boolean): string {
    return new Date(`${iso}T12:00:00`).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'short',
        ...(withYear ? { year: 'numeric' } : {}),
    });
}

function humanize(value: string): string {
    const text = value.replace(/[-_]/g, ' ');
    return text.charAt(0).toUpperCase() + text.slice(1);
}

function formatFilter(row: FilterRow): string {
    const filter = row.key === 'toutes' ? 'Toutes les marques' : row.key === 'blackfriday' ? 'Promo Black Friday' : row.key.toUpperCase();
    return `${UNIVERS_LABELS[row.univers] ?? humanize(row.univers)} · ${filter}`;
}

function formatPlace(place: ContactPlace): string {
    const page = PAGE_LABELS[place.page] ?? humanize(place.page);
    const zone = place.zone === '' ? '' : ` · ${ZONE_LABELS[place.zone] ?? humanize(place.zone)}`;
    return `${page}${zone}`;
}

/** "ncm-munich-n8c" → "Munich N8C" : on retire le préfixe de marque, les codes modèle (avec chiffres) passent en majuscules. */
function formatSlug(slug: string, marque: string): string {
    const name = slug.startsWith(`${marque}-`) ? slug.slice(marque.length + 1) : slug;
    return name
        .split('-')
        .map(word => (/\d/.test(word) ? word.toUpperCase() : word.charAt(0).toUpperCase() + word.slice(1)))
        .join(' ');
}

export default function WebsiteClicksPanel() {
    const [period, setPeriod] = useState<Period>('30d');
    const [response, setResponse] = useState<ClicksResponse | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [reloadKey, setReloadKey] = useState(0);

    useEffect(() => {
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), LOAD_TIMEOUT_MS);
        let isSuperseded = false;

        const load = async () => {
            setIsLoading(true);
            setError(null);

            try {
                const res = await fetch(`/api/dashboard/website-clicks?${new URLSearchParams({ period })}`, {
                    headers: { Accept: 'application/json' },
                    signal: controller.signal,
                });
                if (!res.ok) {
                    const body = res.status === 503 ? await res.json().catch(() => null) : null;
                    throw new Error(body?.message ?? DEFAULT_ERROR);
                }
                const json: ClicksResponse = await res.json();
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
    }, [period, reloadKey]);

    const stats = response && response.configured && response.has_data ? response : null;
    const isUnavailable = response !== null && stats === null;

    return (
        <div className="website-visits website-clicks">
            <div className="website-visits__toolbar">
                <div className="website-visits__heading">
                    <span className="website-visits__title">Clics sur le site</span>
                    {stats && (
                        <span className="website-visits__range">
                            {formatDate(stats.range.start, false)} – {formatDate(stats.range.end, true)}
                        </span>
                    )}
                </div>
                {!isUnavailable && (
                    <div className="website-visits__periods" role="group" aria-label="Période des clics">
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
                    Chargement des clics...
                </div>
            ) : !response.configured ? (
                <div className="website-visits__notice">
                    Le suivi des clics n'est pas configuré sur cet environnement.
                    <span className="website-visits__notice-hint">Renseignez les variables DB_TRACKING_* du fichier .env pour l'activer.</span>
                </div>
            ) : stats === null ? (
                <div className="website-visits__notice">Aucun clic enregistré pour le moment.</div>
            ) : (
                <div className={`website-visits__body ${isLoading ? 'website-visits__body--refreshing' : ''}`}>
                    <HomeSection home={stats.home} />

                    <details className="website-visits__method">
                        <summary>Comment sont comptés les clics et les visiteurs ?</summary>
                        <p>
                            Chaque clic sur un bouton ou un lien suivi s'ajoute au nombre de <strong>clics</strong>. Une personne compte comme
                            <strong> un visiteur</strong> la première fois qu'elle clique sur un élément dans la journée (reconnue sans cookie, oubliée le lendemain).
                        </p>
                        <p>
                            Le taux de clic d'un bouton de l'accueil est ses visiteurs divisés par les visiteurs de la page d'accueil.
                            Les chiffres s'additionnent élément par élément et jour par jour : ce ne sont pas des personnes distinctes.
                            Les robots et les connexions depuis le magasin ne sont pas comptés.
                        </p>
                    </details>

                    <TunnelsCard tunnels={stats.tunnels} entries={stats.entries} />

                    <SourcesCard sources={stats.sources} />

                    <div className="website-visits__rankings">
                        <FiltersCard filters={stats.filters} />
                        <BlackFridayCard fiches={stats.blackfriday.fiches} />
                    </div>

                    <div className="website-visits__rankings">
                        <MagasinCard magasin={stats.magasin} />
                        <ContactsCard contacts={stats.contacts} />
                    </div>

                    <ColorsCard colors={stats.colors} />

                    <p className="website-visits__footnote">Suivi depuis le {formatDate(stats.tracked_since, true)}.</p>
                </div>
            )}
        </div>
    );
}

function Card({ title, hint, children }: { title: string; hint?: string; children: ReactNode }) {
    return (
        <div className="website-visits__card">
            <div className="website-visits__card-header">
                <span className="website-visits__card-title">{title}</span>
                {hint && <span className="website-visits__card-hint">{hint}</span>}
            </div>
            {children}
        </div>
    );
}

function HomeSection({ home }: { home: ClicksStatistics['home'] }) {
    return (
        <div className="website-visits__kpis">
            <div className="website-visits__kpi">
                <span className="website-visits__kpi-label">Page d'accueil</span>
                <span className="website-visits__kpi-value">{formatInteger(home.visitors)}</span>
                <span className="website-visits__kpi-trend">{formatInteger(home.views)} pages vues</span>
                <span className="website-visits__kpi-description">Visiteurs : la base des taux de clic ci-contre</span>
            </div>
            {home.buttons.map(button => (
                <div key={button.key} className="website-visits__kpi">
                    <span className="website-visits__kpi-label">{BUTTON_LABELS[button.key] ?? button.key}</span>
                    <span className="website-visits__kpi-value">{formatInteger(button.visitors)}</span>
                    <span className="website-visits__kpi-trend">
                        {button.rate === null ? '—' : `${formatShare(button.rate)} des visiteurs`}
                    </span>
                    <span className="website-visits__kpi-description">{formatInteger(button.clicks)} clics</span>
                </div>
            ))}
        </div>
    );
}

function stepLabel(step: string): string {
    return step
        .split('|')
        .map(part => STEP_LABELS[part] ?? humanize(part))
        .join(' ou ');
}

function TunnelsCard({ tunnels, entries }: { tunnels: Tunnel[]; entries: TunnelStep[] }) {
    const [view, setView] = useState<'chart' | 'table'>('chart');
    const maxEntry = Math.max(1, ...entries.map(entry => entry.visits));

    return (
        <Card title="Tunnels" hint="Visites qui ont suivi les étapes dans l'ordre">
            {tunnels.length > 0 && (
                <div className="website-visits__periods website-clicks__view" role="group" aria-label="Affichage des tunnels">
                    {([['chart', 'Graphique'], ['table', 'Tableau']] as const).map(([key, label]) => (
                        <button
                            key={key}
                            type="button"
                            className={`website-visits__period ${view === key ? 'website-visits__period--active' : ''}`}
                            aria-pressed={view === key}
                            onClick={() => setView(key)}
                        >
                            {label}
                        </button>
                    ))}
                </div>
            )}
            {tunnels.length === 0 ? (
                <div className="website-visits__empty">
                    Les tunnels se calculent chaque nuit : les premiers chiffres arrivent demain matin.
                </div>
            ) : (
                <div className="website-clicks__tunnels">
                    {tunnels.map(tunnel => {
                        const first = Math.max(1, tunnel.steps[0]?.visits ?? 0);
                        const label = TUNNEL_LABELS[tunnel.key];
                        return (
                            <div key={tunnel.key} className="website-clicks__tunnel">
                                <span className="website-clicks__tunnel-title">{label?.title ?? humanize(tunnel.key)}</span>
                                {label && <span className="website-clicks__tunnel-hint">{label.hint}</span>}
                                {view === 'chart' ? (
                                    <TunnelChart
                                        title={label?.title ?? humanize(tunnel.key)}
                                        steps={tunnel.steps.map(step => ({ label: stepLabel(step.step), visits: step.visits }))}
                                    />
                                ) : (
                                    <ol className="website-visits__ranking">
                                        {tunnel.steps.map((step, index) => {
                                            const previous = index > 0 ? tunnel.steps[index - 1].visits : null;
                                            return (
                                                <li key={`${index}-${step.step}`} className="website-visits__rank-row">
                                                    <span className="website-visits__rank-position">{index + 1}</span>
                                                    <span className="website-visits__rank-label">{stepLabel(step.step)}</span>
                                                    <span className="website-visits__rank-bar">
                                                        <span style={{ width: `${(step.visits / first) * 100}%` }} />
                                                    </span>
                                                    <span className="website-visits__rank-value">{formatInteger(step.visits)}</span>
                                                    <span className="website-visits__rank-secondary">
                                                        {index === 0
                                                            ? '—'
                                                            : `${formatShare((step.visits / first) * 100)}${previous !== null && previous > 0 ? ` · ${formatShare((step.visits / previous) * 100)}` : ''}`}
                                                    </span>
                                                </li>
                                            );
                                        })}
                                    </ol>
                                )}
                            </div>
                        );
                    })}
                </div>
            )}
            {entries.length > 0 && (
                <>
                    <p className="website-clicks__subtitle">Par où les visites entrent sur le site</p>
                    <ol className="website-visits__ranking">
                        {entries.map((entry, index) => (
                            <li key={entry.step} className="website-visits__rank-row">
                                <span className="website-visits__rank-position">{index + 1}</span>
                                <span className="website-visits__rank-label">{stepLabel(entry.step)}</span>
                                <span className="website-visits__rank-bar">
                                    <span style={{ width: `${(entry.visits / maxEntry) * 100}%` }} />
                                </span>
                                <span className="website-visits__rank-value">{formatInteger(entry.visits)}</span>
                            </li>
                        ))}
                    </ol>
                </>
            )}
        </Card>
    );
}

function SourceRanking({ title, rows }: { title: string; rows: SourceRow[] }) {
    const total = rows.reduce((sum, row) => sum + row.visitors, 0);
    const max = Math.max(1, ...rows.map(row => row.visitors));

    return (
        <div>
            <p className="website-clicks__subtitle">{title}</p>
            {rows.length === 0 ? (
                <div className="website-visits__empty">Pas encore de données.</div>
            ) : (
                <ol className="website-visits__ranking">
                    {rows.map((row, index) => (
                        <li key={row.source} className="website-visits__rank-row">
                            <span className="website-visits__rank-position">{index + 1}</span>
                            <span className="website-visits__rank-label">{SOURCE_LABELS[row.source] ?? humanize(row.source)}</span>
                            <span className="website-visits__rank-bar">
                                <span style={{ width: `${(row.visitors / max) * 100}%` }} />
                            </span>
                            <span className="website-visits__rank-value">{formatInteger(row.visitors)}</span>
                            <span className="website-visits__rank-secondary">{total > 0 ? formatShare((row.visitors / total) * 100) : '—'}</span>
                        </li>
                    ))}
                </ol>
            )}
        </div>
    );
}

function SourcesCard({ sources }: { sources: ClicksStatistics['sources'] }) {
    const isEmpty = sources.home.length === 0 && sources.fiches.length === 0;
    const maxFiche = Math.max(1, ...sources.top_fiches.map(fiche => fiche.visitors));

    return (
        <Card title="D'où viennent les visites" hint="Visiteurs par catégorie d'origine · jamais l'adresse du site d'origine">
            {isEmpty ? (
                <div className="website-visits__empty">Les origines s'enregistrent depuis la mise en ligne du suivi : pas encore de données.</div>
            ) : (
                <>
                    <div className="website-visits__rankings">
                        <SourceRanking title="Page d'accueil" rows={sources.home} />
                        <SourceRanking title="Fiches vélos" rows={sources.fiches} />
                    </div>
                    {sources.top_fiches.length > 0 && (
                        <>
                            <p className="website-clicks__subtitle">Origine des fiches les plus visitées</p>
                            <ol className="website-visits__ranking">
                                {sources.top_fiches.map((fiche, index) => (
                                    <li key={fiche.slug} className="website-visits__rank-row website-visits__rank-row--fiche" title={fiche.slug}>
                                        <span className="website-visits__rank-position">{index + 1}</span>
                                        <span className="website-visits__rank-label">{formatSlug(fiche.slug, fiche.marque)}</span>
                                        <span className="website-visits__brand-tag">{fiche.marque.toUpperCase()}</span>
                                        <span className="website-visits__rank-bar">
                                            <span style={{ width: `${(fiche.visitors / maxFiche) * 100}%` }} />
                                        </span>
                                        <span className="website-visits__rank-value">{formatInteger(fiche.visitors)}</span>
                                        <span className="website-visits__rank-secondary">
                                            {fiche.sources
                                                .slice(0, 2)
                                                .map(row => `${SOURCE_LABELS[row.source] ?? humanize(row.source)} ${formatShare((row.visitors / Math.max(1, fiche.visitors)) * 100)}`)
                                                .join(' · ')}
                                        </span>
                                    </li>
                                ))}
                            </ol>
                        </>
                    )}
                </>
            )}
        </Card>
    );
}

function BlackFridayCard({ fiches }: { fiches: BlackFridayFiche[] }) {
    const [showAll, setShowAll] = useState(false);
    const visibleFiches = showAll ? fiches : fiches.slice(0, TOP_COUNT);
    const maxFiche = Math.max(1, ...fiches.map(fiche => fiche.visitors));

    return (
        <Card title="Fiches Black Friday" hint="Visiteurs · pages vues">
            {fiches.length === 0 ? (
                <div className="website-visits__empty">Aucune fiche Black Friday consultée sur la période.</div>
            ) : (
                <>
                    <ol className="website-visits__ranking">
                        {visibleFiches.map((fiche, index) => (
                            <li key={fiche.slug} className="website-visits__rank-row website-visits__rank-row--fiche" title={fiche.slug}>
                                <span className="website-visits__rank-position">{index + 1}</span>
                                <span className="website-visits__rank-label">{formatSlug(fiche.slug, fiche.marque)}</span>
                                <span className="website-visits__brand-tag">{fiche.marque.toUpperCase()}</span>
                                <span className="website-visits__rank-bar">
                                    <span style={{ width: `${(fiche.visitors / maxFiche) * 100}%` }} />
                                </span>
                                <span className="website-visits__rank-value">{formatInteger(fiche.visitors)}</span>
                                <span className="website-visits__rank-secondary">{formatInteger(fiche.views)} vues</span>
                            </li>
                        ))}
                    </ol>
                    {fiches.length > TOP_COUNT && (
                        <button type="button" className="website-visits__more" onClick={() => setShowAll(value => !value)}>
                            {showAll ? `Afficher le top ${TOP_COUNT}` : `Afficher les ${fiches.length} fiches`}
                        </button>
                    )}
                </>
            )}
        </Card>
    );
}

function FiltersCard({ filters }: { filters: FilterRow[] }) {
    const maxVisitors = Math.max(1, ...filters.map(row => row.visitors));

    return (
        <Card title="Filtres du catalogue" hint="Visiteurs · clics">
            {filters.length === 0 ? (
                <div className="website-visits__empty">Aucun clic sur un filtre sur la période.</div>
            ) : (
                <ol className="website-visits__ranking">
                    {filters.map((row, index) => (
                        <li key={`${row.univers}-${row.key}`} className="website-visits__rank-row">
                            <span className="website-visits__rank-position">{index + 1}</span>
                            <span className="website-visits__rank-label">{formatFilter(row)}</span>
                            <span className="website-visits__rank-bar">
                                <span style={{ width: `${(row.visitors / maxVisitors) * 100}%` }} />
                            </span>
                            <span className="website-visits__rank-value">{formatInteger(row.visitors)}</span>
                            <span className="website-visits__rank-secondary">{formatInteger(row.clicks)} clics</span>
                        </li>
                    ))}
                </ol>
            )}
        </Card>
    );
}

function MagasinCard({ magasin }: { magasin: ClicksStatistics['magasin'] }) {
    const rows: { label: string; render: (stats: MagasinStats) => string }[] = [
        { label: 'Fiches consultées', render: stats => formatInteger(stats.fiches) },
        { label: 'Visiteurs', render: stats => formatInteger(stats.visitors) },
        { label: 'Visiteurs par fiche', render: stats => formatDecimal(stats.visitors_per_fiche) },
        { label: 'Demandes de contact', render: stats => formatInteger(stats.requests) },
        { label: 'Demandes pour 100 visiteurs', render: stats => formatDecimal(stats.requests_per_100_visitors) },
    ];

    return (
        <Card title="Label « Visible au magasin »" hint="Vélos avec le label comparés aux autres">
            <table className="website-clicks__compare">
                <thead>
                    <tr>
                        <th scope="col" />
                        <th scope="col">Avec le label</th>
                        <th scope="col">Sans</th>
                    </tr>
                </thead>
                <tbody>
                    {rows.map(row => (
                        <tr key={row.label}>
                            <th scope="row">{row.label}</th>
                            <td>{row.render(magasin.with)}</td>
                            <td>{row.render(magasin.without)}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </Card>
    );
}

function ContactsCard({ contacts }: { contacts: ClicksStatistics['contacts'] }) {
    const [showAll, setShowAll] = useState(false);
    const places = showAll ? contacts.places : contacts.places.slice(0, TOP_COUNT);
    const maxClicks = Math.max(1, ...contacts.places.map(place => place.clicks));

    return (
        <Card title="Contacts et itinéraires" hint="Clics · visiteurs">
            <div className="website-clicks__types">
                {contacts.types.map(type => (
                    <div key={type.key} className="website-clicks__type">
                        <span className="website-clicks__type-label">{CONTACT_LABELS[type.key] ?? type.key}</span>
                        <span className="website-clicks__type-value">{formatInteger(type.clicks)}</span>
                        <span className="website-clicks__type-hint">{formatInteger(type.visitors)} visiteurs</span>
                    </div>
                ))}
            </div>
            {contacts.places.length === 0 ? (
                <div className="website-visits__empty">Aucun clic de contact sur la période.</div>
            ) : (
                <>
                    <ol className="website-visits__ranking">
                        {places.map((place, index) => (
                            <li key={`${place.page}-${place.zone}-${place.key}`} className="website-visits__rank-row website-visits__rank-row--fiche">
                                <span className="website-visits__rank-position">{index + 1}</span>
                                <span className="website-visits__rank-label">{formatPlace(place)}</span>
                                <span className="website-visits__brand-tag">{CONTACT_LABELS[place.key] ?? place.key}</span>
                                <span className="website-visits__rank-bar">
                                    <span style={{ width: `${(place.clicks / maxClicks) * 100}%` }} />
                                </span>
                                <span className="website-visits__rank-value">{formatInteger(place.clicks)}</span>
                                <span className="website-visits__rank-secondary">{formatInteger(place.visitors)} vis.</span>
                            </li>
                        ))}
                    </ol>
                    {contacts.places.length > TOP_COUNT && (
                        <button type="button" className="website-visits__more" onClick={() => setShowAll(value => !value)}>
                            {showAll ? `Afficher le top ${TOP_COUNT}` : `Afficher les ${contacts.places.length} emplacements`}
                        </button>
                    )}
                </>
            )}
        </Card>
    );
}

function ColorsCard({ colors }: { colors: ColorRow[] }) {
    const [showAll, setShowAll] = useState(false);
    const rows = showAll ? colors : colors.slice(0, TOP_COUNT);
    const maxClicks = Math.max(1, ...colors.map(color => color.card_clicks + color.fiche_clicks));

    return (
        <Card title="Couleurs choisies" hint="Clics total · sur la carte · sur la fiche">
            {colors.length === 0 ? (
                <div className="website-visits__empty">Aucune couleur choisie sur la période.</div>
            ) : (
                <>
                    <ol className="website-visits__ranking">
                        {rows.map((color, index) => {
                            const total = color.card_clicks + color.fiche_clicks;
                            return (
                                <li key={`${color.slug}-${color.couleur}`} className="website-visits__rank-row website-visits__rank-row--fiche" title={color.slug}>
                                    <span className="website-visits__rank-position">{index + 1}</span>
                                    <span className="website-visits__rank-label">
                                        {formatSlug(color.slug, color.marque)} · {color.couleur}
                                    </span>
                                    <span className="website-visits__brand-tag">{color.marque.toUpperCase()}</span>
                                    <span className="website-visits__rank-bar">
                                        <span style={{ width: `${(total / maxClicks) * 100}%` }} />
                                    </span>
                                    <span className="website-visits__rank-value">{formatInteger(total)}</span>
                                    <span className="website-visits__rank-secondary">
                                        {formatInteger(color.card_clicks)} carte · {formatInteger(color.fiche_clicks)} fiche
                                    </span>
                                </li>
                            );
                        })}
                    </ol>
                    {colors.length > TOP_COUNT && (
                        <button type="button" className="website-visits__more" onClick={() => setShowAll(value => !value)}>
                            {showAll ? `Afficher le top ${TOP_COUNT}` : `Afficher les ${colors.length} couleurs`}
                        </button>
                    )}
                </>
            )}
        </Card>
    );
}
