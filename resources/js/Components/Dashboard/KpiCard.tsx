export interface MetierKpi {
    metier: string;
    revenue: number;
    margin: number | null;
    average_basket: number | null;
    invoice_count: number;
    trend: number | null;
    has_data: boolean;
}

export interface RangeKpi {
    metier: string;
    revenue: number;
    margin: number | null;
    has_data: boolean;
}

export function formatCurrency(value: number): string {
    return new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value) + ' €';
}

export function formatPercent(value: number): string {
    const sign = value >= 0 ? '+' : '';
    return sign + new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 1,
        maximumFractionDigits: 1,
    }).format(value) + '%';
}

function formatShare(value: number): string {
    return new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value) + '%';
}

interface KpiCellProps {
    kpi: MetierKpi;
    metric: 'revenue' | 'margin';
    marginUnavailable?: boolean;
    total?: number;
}

export function KpiCell({ kpi, metric, marginUnavailable, total }: KpiCellProps) {
    if (!kpi.has_data) {
        return <div className="kpi-cell kpi-cell--empty">—</div>;
    }

    const rawValue = metric === 'revenue' ? kpi.revenue : kpi.margin;
    const hasValue = rawValue !== null && rawValue !== 0;
    const share = total && total > 0 && rawValue ? (rawValue / total) * 100 : null;

    if (metric === 'revenue') {
        return (
            <div className="kpi-cell">
                <span className="kpi-cell__value">{formatCurrency(kpi.revenue)}</span>
                {share !== null && <span className="kpi-cell__share">{formatShare(share)}</span>}
                {kpi.trend !== null && (
                    <span className={`kpi-cell__trend ${kpi.trend >= 0 ? 'kpi-cell__trend--up' : 'kpi-cell__trend--down'}`}>
                        {formatPercent(kpi.trend)}
                    </span>
                )}
            </div>
        );
    }

    return (
        <div className="kpi-cell">
            {hasValue ? (
                <>
                    <span className="kpi-cell__value">{formatCurrency(rawValue!)}</span>
                    {share !== null && <span className="kpi-cell__share">{formatShare(share)}</span>}
                </>
            ) : (
                <span className="kpi-cell__value kpi-cell__value--placeholder">
                    {marginUnavailable ? 'À venir' : '—'}
                </span>
            )}
        </div>
    );
}

interface RangeCellProps {
    kpi: RangeKpi;
    metric: 'revenue' | 'margin';
    marginUnavailable?: boolean;
    total?: number;
}

export function RangeCell({ kpi, metric, marginUnavailable, total }: RangeCellProps) {
    if (!kpi.has_data) {
        return <div className="kpi-cell kpi-cell--empty">—</div>;
    }

    const rawValue = metric === 'revenue' ? kpi.revenue : kpi.margin;
    const hasValue = rawValue !== null && rawValue !== 0;
    const share = total && total > 0 && rawValue ? (rawValue / total) * 100 : null;

    return (
        <div className="kpi-cell">
            {hasValue ? (
                <>
                    <span className="kpi-cell__value">{formatCurrency(rawValue!)}</span>
                    {share !== null && <span className="kpi-cell__share">{formatShare(share)}</span>}
                </>
            ) : (
                <span className="kpi-cell__value kpi-cell__value--placeholder">
                    {marginUnavailable ? 'À venir' : '—'}
                </span>
            )}
        </div>
    );
}
