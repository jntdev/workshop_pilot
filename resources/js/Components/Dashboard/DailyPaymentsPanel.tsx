import { useState, useEffect, useCallback, useMemo } from 'react';
import type { PaymentMethod } from '@/types';

type Source = 'atelier' | 'location' | 'caisse';

interface DayData {
    by_source: Record<Source, Record<PaymentMethod, number>>;
    by_method: Record<PaymentMethod, number>;
    total: number;
}

interface PaymentsHistoryResponse {
    days: Record<string, DayData>;
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

const EMPTY_ROW: Record<PaymentMethod, number> = { cb: 0, liquide: 0, cheque: 0, virement: 0, autre: 0 };
const EMPTY_DAY: DayData = {
    by_source: { atelier: EMPTY_ROW, location: EMPTY_ROW, caisse: EMPTY_ROW },
    by_method: EMPTY_ROW,
    total: 0,
};

function formatCurrency(value: number): string {
    return new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value) + ' €';
}

function pad(n: number): string {
    return String(n).padStart(2, '0');
}

function toIso(year: number, month: number, day: number): string {
    return `${year}-${pad(month)}-${pad(day)}`;
}

function todayIso(): string {
    const now = new Date();
    return toIso(now.getFullYear(), now.getMonth() + 1, now.getDate());
}

function addDays(dateIso: string, days: number): string {
    const [year, month, day] = dateIso.split('-').map(Number);
    // Heure locale midi (pas minuit) pour ne jamais franchir un changement de jour DST.
    const d = new Date(year, month - 1, day, 12);
    d.setDate(d.getDate() + days);
    return toIso(d.getFullYear(), d.getMonth() + 1, d.getDate());
}

function formatDateLabel(dateIso: string): string {
    const [year, month, day] = dateIso.split('-').map(Number);
    return new Date(year, month - 1, day, 12).toLocaleDateString('fr-FR', {
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
    const [history, setHistory] = useState<Record<string, DayData> | null>(null);
    const [isLoading, setIsLoading] = useState(true);

    const load = useCallback(async () => {
        setIsLoading(true);
        const res = await fetch('/api/dashboard/payments-history', { headers: { Accept: 'application/json' } });
        const json: PaymentsHistoryResponse = await res.json();
        setHistory(json.days);
        setIsLoading(false);
    }, []);

    useEffect(() => {
        load();
    }, [load]);

    const isToday = date === todayIso();
    const data = useMemo(() => history?.[date] ?? EMPTY_DAY, [history, date]);

    return (
        <div className="daily-payments">
            <div className="daily-payments__header">
                <span className="daily-payments__title">Encaissements du jour</span>
                <div className="daily-payments__nav">
                    <button
                        type="button"
                        className="daily-payments__today-btn"
                        onClick={() => setDate(todayIso())}
                        disabled={isToday}
                    >
                        Aujourd'hui
                    </button>
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
                </div>
            </div>

            {isLoading || !history ? (
                <div className="daily-payments__loading">Chargement de l'historique...</div>
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
