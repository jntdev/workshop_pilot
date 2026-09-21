import { useState, useEffect, useCallback } from 'react';
import type { PaymentMethod } from '@/types';

type Source = 'atelier' | 'location' | 'caisse';

interface DailyPaymentsResponse {
    date: string;
    by_source: Record<Source, Record<PaymentMethod, number>>;
    by_method: Record<PaymentMethod, number>;
    total: number;
}

const METHODS: { key: PaymentMethod; label: string }[] = [
    { key: 'cb', label: 'CB' },
    { key: 'liquide', label: 'Liquide' },
    { key: 'cheque', label: 'Chèque' },
    { key: 'virement', label: 'Virement' },
    { key: 'autre', label: 'Autre' },
];

const SOURCES: { key: Source; label: string }[] = [
    { key: 'atelier', label: 'Atelier' },
    { key: 'location', label: 'Location' },
    { key: 'caisse', label: 'Caisse' },
];

function formatCurrency(value: number): string {
    return new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value) + ' €';
}

function todayIso(): string {
    return new Date().toISOString().split('T')[0];
}

function addDays(dateIso: string, days: number): string {
    const d = new Date(dateIso + 'T00:00:00');
    d.setDate(d.getDate() + days);
    return d.toISOString().split('T')[0];
}

function formatDateLabel(dateIso: string): string {
    return new Date(dateIso + 'T00:00:00').toLocaleDateString('fr-FR', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
    });
}

function sourceTotal(row: Record<PaymentMethod, number> | undefined): number {
    if (!row) return 0;
    return METHODS.reduce((sum, m) => sum + (row[m.key] ?? 0), 0);
}

export default function DailyPaymentsPanel() {
    const [date, setDate] = useState(todayIso());
    const [data, setData] = useState<DailyPaymentsResponse | null>(null);
    const [isLoading, setIsLoading] = useState(true);

    const load = useCallback(async (targetDate: string) => {
        setIsLoading(true);
        const res = await fetch(`/api/dashboard/daily-payments?date=${targetDate}`, { headers: { Accept: 'application/json' } });
        const json = await res.json();
        setData(json);
        setIsLoading(false);
    }, []);

    useEffect(() => {
        load(date);
    }, [date, load]);

    const isToday = date === todayIso();

    return (
        <div className="daily-payments">
            <div className="daily-payments__header">
                <span className="daily-payments__title">Encaissements du jour</span>
                <div className="daily-payments__nav">
                    <button type="button" className="daily-payments__nav-btn" onClick={() => setDate(prev => addDays(prev, -1))} aria-label="Jour précédent">‹</button>
                    <span className="daily-payments__date">{formatDateLabel(date)}</span>
                    <button
                        type="button"
                        className="daily-payments__nav-btn"
                        onClick={() => setDate(prev => addDays(prev, 1))}
                        disabled={isToday}
                        aria-label="Jour suivant"
                    >
                        ›
                    </button>
                    {!isToday && (
                        <button type="button" className="daily-payments__today-btn" onClick={() => setDate(todayIso())}>
                            Aujourd'hui
                        </button>
                    )}
                </div>
            </div>

            {isLoading || !data ? (
                <div className="daily-payments__loading">Chargement...</div>
            ) : (
                <div className="daily-payments__table-wrap">
                    <table className="daily-payments__table">
                        <thead>
                            <tr>
                                <th></th>
                                {METHODS.map(m => <th key={m.key}>{m.label}</th>)}
                                <th className="daily-payments__col-total">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            {SOURCES.map(s => (
                                <tr key={s.key}>
                                    <th scope="row">{s.label}</th>
                                    {METHODS.map(m => (
                                        <td key={m.key}>{formatCurrency(data.by_source[s.key]?.[m.key] ?? 0)}</td>
                                    ))}
                                    <td className="daily-payments__col-total">{formatCurrency(sourceTotal(data.by_source[s.key]))}</td>
                                </tr>
                            ))}
                        </tbody>
                        <tfoot>
                            <tr>
                                <th scope="row">Total</th>
                                {METHODS.map(m => (
                                    <td key={m.key}>{formatCurrency(data.by_method[m.key] ?? 0)}</td>
                                ))}
                                <td className="daily-payments__col-total">{formatCurrency(data.total)}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            )}
        </div>
    );
}
