import { useState, useEffect, useMemo, useRef } from 'react';
import type { AgendaItem, QuoteAppointment } from '@/types';
import {
    DAY_START_HOUR,
    DAY_END_HOUR,
    SLOT_MINUTES,
    SLOTS_PER_HOUR,
    TOTAL_SLOTS,
    SLOT_HEIGHT_PX,
    apiHeaders,
    todayIso,
    formatDayLabel,
    formatTime,
    slotIndexOf,
    dateIsoOf,
    type DragPayload,
    type ResizeState,
} from './agendaShared';
import NewEventPopover from './NewEventPopover';

interface Props {
    days: string[];
    items: AgendaItem[];
    onItemsChange: (items: AgendaItem[]) => void;
    onError: (message: string) => void;
    onQuoteScheduledChange?: (quoteId: number, isScheduled: boolean) => void;
    onItemClick?: (item: AgendaItem, cardRect: DOMRect) => void;
}

export default function WeekAgendaGrid({ days, items, onItemsChange, onError, onQuoteScheduledChange, onItemClick }: Props) {
    const [resize, setResize] = useState<ResizeState | null>(null);
    const [newEventSlot, setNewEventSlot] = useState<{ dayIso: string; slotIndex: number } | null>(null);
    const gridRef = useRef<HTMLDivElement>(null);
    const wasDraggedRef = useRef(false);
    const wasResizedRef = useRef(false);
    const resizeOriginalRef = useRef<AgendaItem | null>(null);
    const itemsRef = useRef(items);
    itemsRef.current = items;

    const itemsByDay = useMemo(() => {
        const map = new Map<string, AgendaItem[]>();
        for (const item of items) {
            const key = dateIsoOf(new Date(item.starts_at));
            if (!map.has(key)) map.set(key, []);
            map.get(key)!.push(item);
        }
        return map;
    }, [items]);

    const handleDragStartItem = (e: React.DragEvent, item: AgendaItem, durationMinutes: number) => {
        wasDraggedRef.current = true;
        const payload: DragPayload = item.kind === 'quote'
            ? { kind: 'appointment', appointmentId: item.id, durationMinutes }
            : { kind: 'event', eventId: item.id, durationMinutes };
        e.dataTransfer.setData('application/json', JSON.stringify(payload));
        e.dataTransfer.effectAllowed = 'move';
    };

    const handleItemClick = (item: AgendaItem, cardEl: HTMLElement) => {
        if (wasDraggedRef.current || wasResizedRef.current) {
            wasDraggedRef.current = false;
            wasResizedRef.current = false;
            return;
        }
        onItemClick?.(item, cardEl.getBoundingClientRect());
    };

    const createAppointment = async (quoteId: number, startsAt: Date, durationMinutes: number) => {
        const endsAt = new Date(startsAt.getTime() + durationMinutes * 60000);
        const res = await fetch(`/api/quotes/${quoteId}/appointments`, {
            method: 'POST',
            headers: apiHeaders(),
            credentials: 'same-origin',
            body: JSON.stringify({ starts_at: startsAt.toISOString(), ends_at: endsAt.toISOString() }),
        });
        if (res.ok) {
            const created: QuoteAppointment = await res.json();
            onItemsChange([...items, created]);
            onQuoteScheduledChange?.(quoteId, true);
        } else {
            const data = await res.json().catch(() => null);
            onError(data?.message || 'Impossible de planifier ce créneau.');
        }
    };

    const moveItem = async (item: AgendaItem, startsAt: Date, durationMinutes: number) => {
        const previous = items;
        const endsAt = new Date(startsAt.getTime() + durationMinutes * 60000);

        // Mise à jour optimiste : on déplace le créneau immédiatement à l'écran,
        // sans attendre la réponse du serveur ni recharger toute la liste.
        onItemsChange(items.map(i => (
            i.kind === item.kind && i.id === item.id
                ? { ...i, starts_at: startsAt.toISOString(), ends_at: endsAt.toISOString() }
                : i
        )));

        const url = item.kind === 'quote' ? `/api/quote-appointments/${item.id}` : `/api/agenda-events/${item.id}`;
        const res = await fetch(url, {
            method: 'PUT',
            headers: apiHeaders(),
            credentials: 'same-origin',
            body: JSON.stringify({ starts_at: startsAt.toISOString(), ends_at: endsAt.toISOString() }),
        });
        if (!res.ok) {
            const data = await res.json().catch(() => null);
            onError(data?.message || 'Impossible de déplacer ce créneau.');
            onItemsChange(previous);
        }
    };

    const handleDrop = (e: React.DragEvent, dayIso: string, slotIndex: number) => {
        e.preventDefault();
        const raw = e.dataTransfer.getData('application/json');
        if (!raw) return;

        const payload: DragPayload = JSON.parse(raw);
        const startsAt = new Date(`${dayIso}T${String(DAY_START_HOUR).padStart(2, '0')}:00:00`);
        startsAt.setMinutes(startsAt.getMinutes() + slotIndex * SLOT_MINUTES);

        if (payload.kind === 'unscheduled') {
            createAppointment(payload.quoteId, startsAt, payload.durationMinutes);
        } else if (payload.kind === 'appointment') {
            const item = itemsRef.current.find(i => i.kind === 'quote' && i.id === payload.appointmentId);
            if (item) {
                moveItem(item, startsAt, payload.durationMinutes);
            }
        } else {
            const item = itemsRef.current.find(i => i.kind === 'event' && i.id === payload.eventId);
            if (item) {
                moveItem(item, startsAt, payload.durationMinutes);
            }
        }
    };

    const handleSlotClick = (dayIso: string, slotIndex: number) => {
        if (wasDraggedRef.current || wasResizedRef.current) {
            wasDraggedRef.current = false;
            wasResizedRef.current = false;
            return;
        }
        setNewEventSlot({ dayIso, slotIndex });
    };

    const createEvent = async (title: string, detail: string) => {
        if (!newEventSlot) return;

        const startsAt = new Date(`${newEventSlot.dayIso}T${String(DAY_START_HOUR).padStart(2, '0')}:00:00`);
        startsAt.setMinutes(startsAt.getMinutes() + newEventSlot.slotIndex * SLOT_MINUTES);
        const endsAt = new Date(startsAt.getTime() + SLOT_MINUTES * 60000);

        const res = await fetch('/api/agenda-events', {
            method: 'POST',
            headers: apiHeaders(),
            credentials: 'same-origin',
            body: JSON.stringify({ title, detail: detail || null, starts_at: startsAt.toISOString(), ends_at: endsAt.toISOString() }),
        });
        if (res.ok) {
            const created = await res.json();
            onItemsChange([...itemsRef.current, created]);
            setNewEventSlot(null);
        } else {
            const data = await res.json().catch(() => null);
            onError(data?.message || 'Impossible de créer cet événement.');
        }
    };

    // --- Redimensionnement des bords d'un créneau ---

    useEffect(() => {
        if (!resize) return;

        const handleMouseMove = (e: MouseEvent) => {
            if (!gridRef.current) return;
            const dayCol = gridRef.current.querySelector(`[data-day="${resize.dayIso}"]`) as HTMLElement | null;
            if (!dayCol) return;

            wasResizedRef.current = true;

            const rect = dayCol.getBoundingClientRect();
            const offsetY = e.clientY - rect.top;
            const rawSlot = Math.round(offsetY / SLOT_HEIGHT_PX);
            const clampedSlot = Math.max(0, Math.min(TOTAL_SLOTS, rawSlot));

            const current = itemsRef.current;
            const item = current.find(i => i.kind === resize.kind && i.id === resize.itemId);
            if (!item) return;

            const newTime = new Date(`${resize.dayIso}T${String(DAY_START_HOUR).padStart(2, '0')}:00:00`);
            newTime.setMinutes(newTime.getMinutes() + clampedSlot * SLOT_MINUTES);

            const next = current.map(i => {
                if (!(i.kind === resize.kind && i.id === resize.itemId)) return i;
                if (resize.edge === 'start' && newTime.getTime() <= new Date(i.ends_at).getTime() - SLOT_MINUTES * 60000) {
                    return { ...i, starts_at: newTime.toISOString() };
                }
                if (resize.edge === 'end' && newTime.getTime() >= new Date(i.starts_at).getTime() + SLOT_MINUTES * 60000) {
                    return { ...i, ends_at: newTime.toISOString() };
                }
                return i;
            });
            itemsRef.current = next;
            onItemsChange(next);
        };

        const handleMouseUp = async () => {
            const item = itemsRef.current.find(i => i.kind === resize.kind && i.id === resize.itemId);
            const original = resizeOriginalRef.current;
            setResize(null);
            resizeOriginalRef.current = null;
            if (!item) return;

            const url = item.kind === 'quote' ? `/api/quote-appointments/${item.id}` : `/api/agenda-events/${item.id}`;
            const res = await fetch(url, {
                method: 'PUT',
                headers: apiHeaders(),
                credentials: 'same-origin',
                body: JSON.stringify({ starts_at: item.starts_at, ends_at: item.ends_at }),
            });
            if (!res.ok) {
                const data = await res.json().catch(() => null);
                onError(data?.message || 'Impossible de redimensionner ce créneau.');
                if (original) {
                    onItemsChange(itemsRef.current.map(i => (i.kind === original.kind && i.id === original.id ? original : i)));
                }
            }
        };

        window.addEventListener('mousemove', handleMouseMove);
        window.addEventListener('mouseup', handleMouseUp);
        return () => {
            window.removeEventListener('mousemove', handleMouseMove);
            window.removeEventListener('mouseup', handleMouseUp);
        };
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [resize]);

    const handleDelete = async (item: AgendaItem) => {
        if (!confirm('Supprimer ce créneau ?')) return;

        const url = item.kind === 'quote' ? `/api/quote-appointments/${item.id}` : `/api/agenda-events/${item.id}`;
        const res = await fetch(url, {
            method: 'DELETE',
            headers: apiHeaders(),
            credentials: 'same-origin',
        });

        if (res.ok) {
            onItemsChange(items.filter(i => !(i.kind === item.kind && i.id === item.id)));
            if (item.kind === 'quote') {
                onQuoteScheduledChange?.(item.quote_id, false);
            }
        }
    };

    return (
        <div className="agenda__week" ref={gridRef} style={{ gridTemplateColumns: `48px repeat(${days.length}, 1fr)` }}>
            <div className="agenda__week-hours">
                <div className="agenda__week-corner" />
                {Array.from({ length: DAY_END_HOUR - DAY_START_HOUR }, (_, i) => (
                    <div key={i} className="agenda__hour-label" style={{ height: SLOT_HEIGHT_PX * SLOTS_PER_HOUR }}>
                        {DAY_START_HOUR + i}h
                    </div>
                ))}
            </div>

            {days.map(dayIso => {
                const { weekday, day } = formatDayLabel(dayIso);
                const isToday = dayIso === todayIso();
                return (
                    <div key={dayIso} className={`agenda__week-col ${isToday ? 'agenda__week-col--today' : ''}`}>
                        <div className="agenda__week-day-header">
                            <span className="agenda__week-day-name">{weekday}</span>
                            <span className="agenda__week-day-num">{day}</span>
                        </div>
                        <div
                            className="agenda__week-day-body"
                            data-day={dayIso}
                            style={{ height: SLOT_HEIGHT_PX * TOTAL_SLOTS }}
                        >
                            {Array.from({ length: TOTAL_SLOTS }, (_, slotIndex) => (
                                <div
                                    key={slotIndex}
                                    className={`agenda__slot ${slotIndex % SLOTS_PER_HOUR === 0 ? 'agenda__slot--hour' : ''}`}
                                    style={{ height: SLOT_HEIGHT_PX }}
                                    onDragOver={e => e.preventDefault()}
                                    onDrop={e => handleDrop(e, dayIso, slotIndex)}
                                    onClick={() => handleSlotClick(dayIso, slotIndex)}
                                />
                            ))}

                            {(itemsByDay.get(dayIso) ?? []).map(item => {
                                const start = new Date(item.starts_at);
                                const end = new Date(item.ends_at);
                                const top = slotIndexOf(start) * SLOT_HEIGHT_PX;
                                const durationMinutes = Math.round((end.getTime() - start.getTime()) / 60000);
                                const height = Math.max(SLOT_HEIGHT_PX, (durationMinutes / SLOT_MINUTES) * SLOT_HEIGHT_PX);
                                const itemKey = `${item.kind}-${item.id}`;

                                return (
                                    <div
                                        key={itemKey}
                                        className={`agenda__appointment ${item.kind === 'event' ? 'agenda__appointment--event' : ''}`}
                                        style={{ top, height }}
                                        draggable={!resize}
                                        onDragStart={e => handleDragStartItem(e, item, durationMinutes)}
                                        onClick={e => { e.stopPropagation(); handleItemClick(item, e.currentTarget); }}
                                    >
                                        <div
                                            className="agenda__appointment-handle agenda__appointment-handle--top"
                                            onMouseDown={e => { e.stopPropagation(); e.preventDefault(); resizeOriginalRef.current = item; setResize({ itemId: item.id, kind: item.kind, edge: 'start', dayIso }); }}
                                        />
                                        <div className="agenda__appointment-content">
                                            <div className="agenda__appointment-line">
                                                <span className="agenda__appointment-time">
                                                    {formatTime(item.starts_at)}–{formatTime(item.ends_at)}
                                                </span>
                                                {item.kind === 'quote' ? (
                                                    <>
                                                        <span className="agenda__appointment-title">{item.client_name}</span>
                                                        <span className="agenda__appointment-bike">{item.bike_description ?? item.quote_reference}</span>
                                                    </>
                                                ) : (
                                                    <span className="agenda__appointment-title">{item.title}</span>
                                                )}
                                            </div>
                                            {item.kind === 'quote' && item.status_label && (
                                                <span className="agenda__appointment-status">{item.status_label}</span>
                                            )}
                                        </div>
                                        <button
                                            type="button"
                                            className="agenda__appointment-remove"
                                            onClick={e => { e.stopPropagation(); handleDelete(item); }}
                                            title="Supprimer ce créneau"
                                        >
                                            ×
                                        </button>
                                        <div
                                            className="agenda__appointment-handle agenda__appointment-handle--bottom"
                                            onMouseDown={e => { e.stopPropagation(); e.preventDefault(); resizeOriginalRef.current = item; setResize({ itemId: item.id, kind: item.kind, edge: 'end', dayIso }); }}
                                        />
                                    </div>
                                );
                            })}

                            {newEventSlot && newEventSlot.dayIso === dayIso && (
                                <NewEventPopover
                                    top={newEventSlot.slotIndex * SLOT_HEIGHT_PX}
                                    onCancel={() => setNewEventSlot(null)}
                                    onSubmit={createEvent}
                                />
                            )}
                        </div>
                    </div>
                );
            })}
        </div>
    );
}
