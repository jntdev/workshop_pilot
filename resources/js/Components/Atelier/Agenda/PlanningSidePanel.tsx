import { useState, useEffect, useCallback } from 'react';
import WeekAgendaGrid from './WeekAgendaGrid';
import PlanningItemPopover from './PlanningItemPopover';
import { addDays, todayIso, formatDayFullLabel, apiHeaders } from './agendaShared';
import type { AgendaItem } from '@/types';

interface Props {
    isOpen: boolean;
    onClose: () => void;
    onQuoteScheduledChange?: (quoteId: number, isScheduled: boolean) => void;
}

interface RescheduleSummary {
    moved_count: number;
    days_touched: number;
    max_cascade_depth: number;
}

export default function PlanningSidePanel({ isOpen, onClose, onQuoteScheduledChange }: Props) {
    const [date, setDate] = useState(todayIso());
    const [items, setItems] = useState<AgendaItem[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [selectedItem, setSelectedItem] = useState<AgendaItem | null>(null);
    const [selectedItemRect, setSelectedItemRect] = useState<DOMRect | null>(null);
    const [isRescheduling, setIsRescheduling] = useState(false);
    const [rescheduleMessage, setRescheduleMessage] = useState<string | null>(null);

    const handleItemClick = useCallback((item: AgendaItem, rect: DOMRect) => {
        setSelectedItem(item);
        setSelectedItemRect(rect);
    }, []);

    const load = useCallback(async (targetDate: string) => {
        setIsLoading(true);
        setError(null);
        try {
            const start = `${targetDate}T00:00:00`;
            const end = `${targetDate}T23:59:59`;
            const res = await fetch(`/api/agenda/items?start=${start}&end=${end}`, { headers: { Accept: 'application/json' } });
            setItems(res.ok ? await res.json() : []);
        } catch {
            setError('Impossible de charger l\'agenda.');
        } finally {
            setIsLoading(false);
        }
    }, []);

    const handleReschedule = useCallback(async () => {
        if (!window.confirm(`Reporter tous les éléments non terminés du ${formatDayFullLabel(date)} au prochain jour ouvré ?`)) {
            return;
        }

        setIsRescheduling(true);
        setError(null);
        setRescheduleMessage(null);
        try {
            const res = await fetch('/api/agenda/reschedule', {
                method: 'POST',
                headers: apiHeaders(),
                credentials: 'same-origin',
                body: JSON.stringify({ day: date }),
            });
            if (res.ok) {
                const summary: RescheduleSummary = await res.json();
                await load(date);
                setRescheduleMessage(
                    summary.moved_count === 0
                        ? 'Rien à reporter sur cette journée.'
                        : `${summary.moved_count} élément(s) reporté(s) sur ${summary.days_touched} jour(s).`
                );
            } else {
                const data = await res.json().catch(() => null);
                setError(data?.message || 'Impossible de reporter cette journée.');
            }
        } catch {
            setError('Impossible de reporter cette journée.');
        } finally {
            setIsRescheduling(false);
        }
    }, [date, load]);

    useEffect(() => {
        if (isOpen) {
            load(date);
            setRescheduleMessage(null);
        } else {
            setSelectedItem(null);
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [isOpen, date]);

    useEffect(() => {
        setSelectedItem(prev => {
            if (!prev) return prev;
            const updated = items.find(i => i.kind === prev.kind && i.id === prev.id);
            return updated ?? null;
        });
    }, [items]);

    const isToday = date === todayIso();

    return (
        <>
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
                    <button
                        type="button"
                        className="agenda-planning-panel__reschedule-btn"
                        onClick={handleReschedule}
                        disabled={isRescheduling || isLoading}
                    >
                        {isRescheduling ? 'Report en cours...' : 'Reporter au lendemain'}
                    </button>
                </div>

                {error && <div className="agenda__error">{error}</div>}
                {rescheduleMessage && <div className="agenda-planning-panel__reschedule-summary">{rescheduleMessage}</div>}

                <div className="agenda-planning-panel__grid">
                    {isLoading ? (
                        <div className="agenda__loading">Chargement...</div>
                    ) : (
                        <WeekAgendaGrid
                            days={[date]}
                            items={items}
                            onItemsChange={setItems}
                            onError={setError}
                            onQuoteScheduledChange={onQuoteScheduledChange}
                            onItemClick={handleItemClick}
                        />
                    )}
                </div>
            </div>

            {isOpen && (
                <PlanningItemPopover
                    item={selectedItem}
                    anchorRect={selectedItemRect}
                    onClose={() => setSelectedItem(null)}
                />
            )}
        </>
    );
}
