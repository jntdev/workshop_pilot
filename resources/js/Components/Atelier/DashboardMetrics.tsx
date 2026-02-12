import { KpiStats } from '@/types';

interface DashboardMetricsProps {
    stats: KpiStats;
    comparisonStats: KpiStats;
    selectedYear: number;
    selectedMonth: number;
    availableYears: number[];
    onYearChange: (year: number) => void;
    onMonthChange: (month: number) => void;
    onRebuildStats?: () => void;
    isRebuilding?: boolean;
}

const MONTHS = [
    'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
    'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
];

function formatCurrency(value: number): string {
    return new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value) + ' €';
}

function formatPercent(value: number): string {
    return new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 1,
        maximumFractionDigits: 1,
    }).format(value) + '%';
}

interface MetricCardProps {
    title: string;
    value: string;
    period: string;
    comparison?: {
        percent: number;
        label: string;
    };
    detail: string;
}

function MetricCard({ title, value, period, comparison, detail }: MetricCardProps) {
    return (
        <div className="atelier-dashboard__metric-card">
            <div className="atelier-dashboard__metric-header">
                <h3 className="atelier-dashboard__metric-title">{title}</h3>
                <span className="atelier-dashboard__metric-period">{period}</span>
            </div>
            <div className="atelier-dashboard__metric-value">{value}</div>
            {comparison && (
                <div
                    className={`atelier-dashboard__metric-comparison ${
                        comparison.percent >= 0
                            ? 'atelier-dashboard__metric-comparison--positive'
                            : 'atelier-dashboard__metric-comparison--negative'
                    }`}
                >
                    {comparison.percent >= 0 ? '+' : ''}
                    {formatPercent(comparison.percent)} {comparison.label}
                </div>
            )}
            <div className="atelier-dashboard__metric-detail">{detail}</div>
        </div>
    );
}

export default function DashboardMetrics({
    stats,
    comparisonStats,
    selectedYear,
    selectedMonth,
    availableYears,
    onYearChange,
    onMonthChange,
    onRebuildStats,
    isRebuilding,
}: DashboardMetricsProps) {
    const monthName = MONTHS[selectedMonth - 1];
    const period = `${monthName} ${selectedYear}`;
    const comparisonLabel = `vs ${monthName} ${selectedYear - 1}`;

    // Calculate comparisons
    const revenueComparison = comparisonStats.revenue > 0
        ? { percent: ((stats.revenue - comparisonStats.revenue) / comparisonStats.revenue) * 100, label: comparisonLabel }
        : undefined;

    const marginComparison = comparisonStats.margin > 0
        ? { percent: ((stats.margin - comparisonStats.margin) / comparisonStats.margin) * 100, label: comparisonLabel }
        : undefined;

    const averageBasket = stats.count > 0 ? stats.revenue / stats.count : 0;
    const prevAverageBasket = comparisonStats.count > 0 ? comparisonStats.revenue / comparisonStats.count : 0;
    const basketComparison = prevAverageBasket > 0
        ? { percent: ((averageBasket - prevAverageBasket) / prevAverageBasket) * 100, label: comparisonLabel }
        : undefined;

    return (
        <div className="atelier-dashboard">
            <div className="atelier-dashboard__filters">
                <div className="atelier-dashboard__filter-group">
                    <label htmlFor="year-select" className="atelier-dashboard__label">
                        Année
                    </label>
                    <select
                        id="year-select"
                        value={selectedYear}
                        onChange={(e) => onYearChange(Number(e.target.value))}
                        className="atelier-dashboard__select"
                    >
                        {availableYears.map((year) => (
                            <option key={year} value={year}>
                                {year}
                            </option>
                        ))}
                    </select>
                </div>

                <div className="atelier-dashboard__filter-group">
                    <label htmlFor="month-select" className="atelier-dashboard__label">
                        Mois
                    </label>
                    <select
                        id="month-select"
                        value={selectedMonth}
                        onChange={(e) => onMonthChange(Number(e.target.value))}
                        className="atelier-dashboard__select"
                    >
                        {MONTHS.map((month, index) => (
                            <option key={index + 1} value={index + 1}>
                                {month}
                            </option>
                        ))}
                    </select>
                </div>

                {onRebuildStats && (
                    <div className="atelier-dashboard__filter-group atelier-dashboard__filter-group--action">
                        <button
                            type="button"
                            onClick={onRebuildStats}
                            disabled={isRebuilding}
                            className="atelier-dashboard__btn-rebuild"
                        >
                            {isRebuilding ? 'Recalcul...' : 'Recalculer les stats'}
                        </button>
                    </div>
                )}
            </div>

            <div className="atelier-dashboard__metrics">
                <MetricCard
                    title="Chiffre d'affaires HT"
                    value={formatCurrency(stats.revenue)}
                    period={period}
                    comparison={revenueComparison}
                    detail={`${stats.count} facture${stats.count > 1 ? 's' : ''}`}
                />

                <MetricCard
                    title="Marge brute HT"
                    value={formatCurrency(stats.margin)}
                    period={period}
                    comparison={marginComparison}
                    detail={`Taux de marge : ${formatPercent(stats.margin_rate)}`}
                />

                <MetricCard
                    title="Panier moyen HT"
                    value={formatCurrency(averageBasket)}
                    period={period}
                    comparison={basketComparison}
                    detail={`Basé sur ${stats.count} facture${stats.count > 1 ? 's' : ''}`}
                />
            </div>
        </div>
    );
}
