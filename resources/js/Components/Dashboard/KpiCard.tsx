export interface KpiRowProps {
    title: string;
    href: string;
    revenue: number;
    margin: number | null;
    averageBasket: number | null;
    trend: number | null;
    hasData: boolean;
    marginUnavailable?: boolean;
}

function formatCurrency(value: number): string {
    return new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value) + ' €';
}

function formatPercent(value: number): string {
    const sign = value >= 0 ? '+' : '';
    return sign + new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 1,
        maximumFractionDigits: 1,
    }).format(value) + '%';
}

export default function KpiRow({
    title,
    href,
    revenue,
    margin,
    averageBasket,
    trend,
    hasData,
    marginUnavailable,
}: KpiRowProps) {
    if (!hasData) {
        return (
            <a href={href} className="kpi-row kpi-row--empty">
                <div className="kpi-row__title">{title}</div>
                <div className="kpi-row__empty">Pas de données ce mois-ci</div>
                <div className="kpi-row__link">→</div>
            </a>
        );
    }

    return (
        <a href={href} className="kpi-row">
            <div className="kpi-row__title">{title}</div>

            <div className="kpi-row__kpi">
                <span className="kpi-row__label">CA</span>
                <span className="kpi-row__value">{formatCurrency(revenue)}</span>
                {trend !== null && (
                    <span className={`kpi-row__trend ${trend >= 0 ? 'kpi-row__trend--up' : 'kpi-row__trend--down'}`}>
                        {formatPercent(trend)}
                    </span>
                )}
            </div>

            <div className="kpi-row__kpi">
                <span className="kpi-row__label">Marge</span>
                {margin !== null && margin !== 0 ? (
                    <span className="kpi-row__value">{formatCurrency(margin)}</span>
                ) : (
                    <span className="kpi-row__value kpi-row__value--placeholder">
                        {marginUnavailable ? 'À venir' : '—'}
                    </span>
                )}
            </div>

            <div className="kpi-row__kpi">
                <span className="kpi-row__label">Panier</span>
                <span className="kpi-row__value">
                    {averageBasket !== null ? formatCurrency(averageBasket) : '—'}
                </span>
            </div>

            <div className="kpi-row__link">→</div>
        </a>
    );
}
