import { useState, useEffect, useCallback } from 'react';
import WeekAgendaGrid from './WeekAgendaGrid';
import { addDays, todayIso, formatDayFullLabel } from './agendaShared';
import type { QuoteAppointment } from '@/types';

interface Props {
    isOpen: boolean;
    onClose: () => void;
    onQuoteScheduledChange?: (quoteId: number, isScheduled: boolean) => void;
}

export default function PlanningSidePanel({ isOpen, onClose, onQuoteScheduledChange }: Props) {
    const [date, setDate] = useState(todayIso());
    const [appointments, setAppointments] = useState<QuoteAppointment[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const load = useCallback(async (targetDate: string) => {
        setIsLoading(true);
        setError(null);
        try {
            const start = `${targetDate}T00:00:00`;
            const end = `${targetDate}T23:59:59`;
            const res = await fetch(`/api/quote-appointments?start=${start}&end=${end}`, { headers: { Accept: 'application/json' } });
            setAppointments(res.ok ? await res.json() : []);
        } catch {
            setError('Impossible de charger l\'agenda.');
        } finally {
            setIsLoading(false);
        }
    }, []);

    useEffect(() => {
        if (isOpen) {
            load(date);
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [isOpen, date]);

    const isToday = date === todayIso();

    return (
        <div className={`agenda-planning-panel ${isOpen ? 'agenda-planning-panel--open' : ''}`}>
            <div className="agenda-planning-panel__header">
                <h2 className="agenda-planning-panel__title">Planification</h2>
                <button type="button" className="agenda-planning-panel__close" onClick={onClose} aria-label="Fermer">
                    ×
                </button>
            </div>

            <div className="agenda-planning-panel__nav">
                <button type="button" className="agenda__nav-btn agenda__nav-btn--today" onClick={() => setDate(todayIso())} disabled={isToday}>
                    Aujourd'hui
                </button>
                <button type="button" className="agenda__nav-btn" onClick={() => setDate(prev => addDays(prev, -1))} aria-label="Jour précédent">‹</button>
                <span className="agenda__date">{formatDayFullLabel(date)}</span>
                <button type="button" className="agenda__nav-btn" onClick={() => setDate(prev => addDays(prev, 1))} aria-label="Jour suivant">›</button>
            </div>

            {error && <div className="agenda__error">{error}</div>}

            <div className="agenda-planning-panel__grid">
                {isLoading ? (
                    <div className="agenda__loading">Chargement...</div>
                ) : (
                    <WeekAgendaGrid
                        days={[date]}
                        appointments={appointments}
                        onAppointmentsChange={setAppointments}
                        onError={setError}
                        onQuoteScheduledChange={onQuoteScheduledChange}
                    />
                )}
            </div>
        </div>
    );
}
