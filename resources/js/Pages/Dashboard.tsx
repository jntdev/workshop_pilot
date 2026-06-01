import { useState, useCallback } from 'react';
import { router } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import { Head } from '@inertiajs/react';
import { KpiCell, RangeCell, type MetierKpi, type RangeKpi, formatCurrency } from '@/Components/Dashboard/KpiCard';

const MONTHS = [
    'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
    'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre',
];

interface Period {
    year: number;
    month: number;
    label: string;
}

interface Range {
    start_year: number;
    start_month: number;
    end_year: number;
    end_month: number;
}

interface DashboardProps {
    kpis: { vente: MetierKpi; atelier: MetierKpi; location: MetierKpi };
    period: Period;
    season_kpis: { vente: RangeKpi; atelier: RangeKpi; location: RangeKpi };
    season: Range;
    yearly_kpis: { vente: RangeKpi; atelier: RangeKpi; location: RangeKpi };
    yearly: Range;
    exercice_kpis: { vente: RangeKpi; atelier: RangeKpi; location: RangeKpi };
    exercice: Range;
}

const METIERS = [
    { key: 'vente', label: 'Vente', href: '/atelier' },
    { key: 'atelier', label: 'Atelier', href: '/atelier' },
    { key: 'location', label: 'Location', href: '/location', marginUnavailable: true },
] as const;

const METRICS = [
    { key: 'revenue', label: 'CA' },
    { key: 'margin', label: 'Marge' },
] as const;

function RangeTable({ rangeKpis }: { rangeKpis: Record<string, RangeKpi> }) {
    const totalRevenue = METIERS.reduce((sum, m) => sum + (rangeKpis[m.key]?.revenue ?? 0), 0);
    const totalMargin = METIERS.reduce((sum, m) => sum + (rangeKpis[m.key]?.margin ?? 0), 0);
    const totals = { revenue: totalRevenue, margin: totalMargin > 0 ? totalMargin : null };
    const hasAnyData = METIERS.some((m) => rangeKpis[m.key]?.has_data);

    return (
        <div className="dashboard__table">
            <div className="dashboard__table-header">
                <div className="dashboard__th" />
                {METIERS.map((m) => (
                    <a key={m.key} href={m.href} className="dashboard__th dashboard__th--metier">{m.label}</a>
                ))}
                <div className="dashboard__th dashboard__th--total">Total</div>
            </div>
            {METRICS.map((metric) => {
                const total = metric.key === 'revenue' ? totals.revenue : totals.margin;
                return (
                    <div key={metric.key} className="dashboard__row">
                        <div className="dashboard__row-label">{metric.label}</div>
                        {METIERS.map((metier) => (
                            <a key={metier.key} href={metier.href} className="dashboard__row-cell">
                                <RangeCell
                                    kpi={rangeKpis[metier.key]}
                                    metric={metric.key}
                                    marginUnavailable={metier.marginUnavailable}
                                    total={total ?? undefined}
                                />
                            </a>
                        ))}
                        <div className="dashboard__row-cell dashboard__row-cell--total">
                            {hasAnyData && total !== null ? (
                                <div className="kpi-cell">
                                    <span className="kpi-cell__value">{formatCurrency(total!)}</span>
                                </div>
                            ) : (
                                <div className="kpi-cell kpi-cell--empty">—</div>
                            )}
                        </div>
                    </div>
                );
            })}
        </div>
    );
}

export default function Dashboard({ kpis, period, season_kpis, season, yearly_kpis, yearly, exercice_kpis, exercice }: DashboardProps) {
    const [isRebuilding, setIsRebuilding] = useState(false);

    const currentYear = new Date().getFullYear();
    const currentMonth = new Date().getMonth() + 1;
    const years = Array.from({ length: currentYear - 2019 }, (_, i) => currentYear - i);

    const navigate = useCallback((params: Record<string, number>) => {
        router.get('/dashboard', {
            year: period.year,
            month: period.month,
            season_start_year: season.start_year,
            season_start_month: season.start_month,
            season_end_year: season.end_year,
            season_end_month: season.end_month,
            yearly_start_year: yearly.start_year,
            yearly_start_month: yearly.start_month,
            yearly_end_year: yearly.end_year,
            yearly_end_month: yearly.end_month,
            ...params,
        }, { preserveState: false });
    }, [period, season, yearly]);

    const isCurrentMonth = period.year === currentYear && period.month === currentMonth;

    const prevMonth = () => period.month === 1
        ? navigate({ year: period.year - 1, month: 12 })
        : navigate({ month: period.month - 1 });

    const nextMonth = () => {
        if (isCurrentMonth) { return; }
        period.month === 12
            ? navigate({ year: period.year + 1, month: 1 })
            : navigate({ month: period.month + 1 });
    };

    const handleRebuildKpis = useCallback(async () => {
        setIsRebuilding(true);
        try {
            const response = await fetch('/api/dashboard/kpis/rebuild', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
            });
            if (response.ok) { router.reload(); }
        } catch (error) {
            console.error('Failed to rebuild KPIs:', error);
        } finally {
            setIsRebuilding(false);
        }
    }, []);

    // Tableau mensuel avec total
    const monthlyTotalRevenue = METIERS.reduce((sum, m) => sum + (kpis[m.key]?.revenue ?? 0), 0);
    const monthlyTotalMargin = METIERS.reduce((sum, m) => sum + (kpis[m.key]?.margin ?? 0), 0);

    return (
        <MainLayout>
            <Head title="Tableau de bord" />

            <div className="dashboard">
                <div className="dashboard__header">
                    <div className="dashboard__header-left">
                        <h1 className="dashboard__title">Tableau de bord</h1>
                    </div>
                    <button type="button" onClick={handleRebuildKpis} disabled={isRebuilding} className="dashboard__btn-rebuild">
                        {isRebuilding ? 'Recalcul...' : 'Recalculer les KPIs'}
                    </button>
                </div>

                <div className="dashboard__grid">
                    {/* Colonne gauche */}
                    <div className="dashboard__col">
                        <div className="dashboard__section-header">
                            <span className="dashboard__section-title">Résultat mensuel</span>
                            <div className="dashboard__period-nav">
                                <button type="button" onClick={prevMonth} className="dashboard__period-btn" aria-label="Mois précédent">‹</button>
                                <select className="dashboard__period-select" value={period.month} onChange={(e) => navigate({ month: parseInt(e.target.value) })}>
                                    {MONTHS.map((label, i) => <option key={i + 1} value={i + 1}>{label}</option>)}
                                </select>
                                <select className="dashboard__period-select" value={period.year} onChange={(e) => navigate({ year: parseInt(e.target.value) })}>
                                    {years.map((y) => <option key={y} value={y}>{y}</option>)}
                                </select>
                                <button type="button" onClick={nextMonth} disabled={isCurrentMonth} className="dashboard__period-btn" aria-label="Mois suivant">›</button>
                            </div>
                        </div>
                        <div className="dashboard__table">
                            <div className="dashboard__table-header">
                                <div className="dashboard__th" />
                                {METIERS.map((m) => (
                                    <a key={m.key} href={m.href} className="dashboard__th dashboard__th--metier">{m.label}</a>
                                ))}
                                <div className="dashboard__th dashboard__th--total">Total</div>
                            </div>
                            {METRICS.map((metric) => {
                                const total = metric.key === 'revenue' ? monthlyTotalRevenue : (monthlyTotalMargin > 0 ? monthlyTotalMargin : null);
                                return (
                                    <div key={metric.key} className="dashboard__row">
                                        <div className="dashboard__row-label">{metric.label}</div>
                                        {METIERS.map((metier) => (
                                            <a key={metier.key} href={metier.href} className="dashboard__row-cell">
                                                <KpiCell
                                                    kpi={kpis[metier.key]}
                                                    metric={metric.key}
                                                    marginUnavailable={metier.marginUnavailable}
                                                    total={total ?? undefined}
                                                />
                                            </a>
                                        ))}
                                        <div className="dashboard__row-cell dashboard__row-cell--total">
                                            {total !== null ? (
                                                <div className="kpi-cell">
                                                    <span className="kpi-cell__value">{formatCurrency(total!)}</span>
                                                </div>
                                            ) : (
                                                <div className="kpi-cell kpi-cell--empty">—</div>
                                            )}
                                        </div>
                                    </div>
                                );
                            })}
                        </div>

                        <div className="dashboard__section-header">
                            <span className="dashboard__section-title">Résultat d'exercice</span>
                            <span className="dashboard__period-label">Mai {exercice.start_year} → Avril {exercice.end_year}</span>
                        </div>
                        <RangeTable rangeKpis={exercice_kpis} />
                    </div>

                    {/* Colonne droite */}
                    <div className="dashboard__col">
                        <div className="dashboard__section-header">
                            <span className="dashboard__section-title">Résultat de saison</span>
                            <div className="dashboard__period-nav">
                                <span className="dashboard__period-label">Du</span>
                                <select className="dashboard__period-select" value={season.start_month} onChange={(e) => navigate({ season_start_month: parseInt(e.target.value) })}>
                                    {MONTHS.map((label, i) => <option key={i + 1} value={i + 1}>{label}</option>)}
                                </select>
                                <select className="dashboard__period-select" value={season.start_year} onChange={(e) => navigate({ season_start_year: parseInt(e.target.value) })}>
                                    {years.map((y) => <option key={y} value={y}>{y}</option>)}
                                </select>
                                <span className="dashboard__period-label">au</span>
                                <select className="dashboard__period-select" value={season.end_month} onChange={(e) => navigate({ season_end_month: parseInt(e.target.value) })}>
                                    {MONTHS.map((label, i) => <option key={i + 1} value={i + 1}>{label}</option>)}
                                </select>
                                <select className="dashboard__period-select" value={season.end_year} onChange={(e) => navigate({ season_end_year: parseInt(e.target.value) })}>
                                    {years.map((y) => <option key={y} value={y}>{y}</option>)}
                                </select>
                            </div>
                        </div>
                        <RangeTable rangeKpis={season_kpis} />

                        <div className="dashboard__section-header">
                            <span className="dashboard__section-title">Résultat de l'année</span>
                            <div className="dashboard__period-nav">
                                <span className="dashboard__period-label">Du</span>
                                <select className="dashboard__period-select" value={yearly.start_month} onChange={(e) => navigate({ yearly_start_month: parseInt(e.target.value) })}>
                                    {MONTHS.map((label, i) => <option key={i + 1} value={i + 1}>{label}</option>)}
                                </select>
                                <select className="dashboard__period-select" value={yearly.start_year} onChange={(e) => navigate({ yearly_start_year: parseInt(e.target.value) })}>
                                    {years.map((y) => <option key={y} value={y}>{y}</option>)}
                                </select>
                                <span className="dashboard__period-label">au</span>
                                <select className="dashboard__period-select" value={yearly.end_month} onChange={(e) => navigate({ yearly_end_month: parseInt(e.target.value) })}>
                                    {MONTHS.map((label, i) => <option key={i + 1} value={i + 1}>{label}</option>)}
                                </select>
                                <select className="dashboard__period-select" value={yearly.end_year} onChange={(e) => navigate({ yearly_end_year: parseInt(e.target.value) })}>
                                    {years.map((y) => <option key={y} value={y}>{y}</option>)}
                                </select>
                            </div>
                        </div>
                        <RangeTable rangeKpis={yearly_kpis} />
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}
