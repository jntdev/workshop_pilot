import { useState, useEffect, useMemo, useRef } from 'react';
import type { QuoteAppointment } from '@/types';
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

interface Props {
    days: string[];
    appointments: QuoteAppointment[];
    onAppointmentsChange: (appointments: QuoteAppointment[]) => void;
    onError: (message: string) => void;
    onQuoteScheduledChange?: (quoteId: number, isScheduled: boolean) => void;
    onAppointmentClick?: (appointment: QuoteAppointment) => void;
}

export default function WeekAgendaGrid({ days, appointments, onAppointmentsChange, onError, onQuoteScheduledChange, onAppointmentClick }: Props) {
    const [resize, setResize] = useState<ResizeState | null>(null);
    const gridRef = useRef<HTMLDivElement>(null);
    const wasDraggedRef = useRef(false);
    const wasResizedRef = useRef(false);
    const resizeOriginalRef = useRef<QuoteAppointment | null>(null);
    const appointmentsRef = useRef(appointments);
    appointmentsRef.current = appointments;

    const appointmentsByDay = useMemo(() => {
        const map = new Map<string, QuoteAppointment[]>();
        for (const appt of appointments) {
            const key = dateIsoOf(new Date(appt.starts_at));
            if (!map.has(key)) map.set(key, []);
            map.get(key)!.push(appt);
        }
        return map;
    }, [appointments]);

    const handleDragStartAppointment = (e: React.DragEvent, appointmentId: number, durationMinutes: number) => {
        wasDraggedRef.current = true;
        const payload: DragPayload = { kind: 'appointment', appointmentId, durationMinutes };
        e.dataTransfer.setData('application/json', JSON.stringify(payload));
        e.dataTransfer.effectAllowed = 'move';
    };

    const handleAppointmentClick = (appt: QuoteAppointment) => {
        if (wasDraggedRef.current || wasResizedRef.current) {
            wasDraggedRef.current = false;
            wasResizedRef.current = false;
            return;
        }
        onAppointmentClick?.(appt);
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
            onAppointmentsChange([...appointments, created]);
            onQuoteScheduledChange?.(quoteId, true);
        } else {
            const data = await res.json().catch(() => null);
            onError(data?.message || 'Impossible de planifier ce créneau.');
        }
    };

    const moveAppointment = async (appointmentId: number, startsAt: Date, durationMinutes: number) => {
        const previous = appointments;
        const endsAt = new Date(startsAt.getTime() + durationMinutes * 60000);

        // Mise à jour optimiste : on déplace le créneau immédiatement à l'écran,
        // sans attendre la réponse du serveur ni recharger toute la liste.
        onAppointmentsChange(appointments.map(a => (
            a.id === appointmentId ? { ...a, starts_at: startsAt.toISOString(), ends_at: endsAt.toISOString() } : a
        )));

        const res = await fetch(`/api/quote-appointments/${appointmentId}`, {
            method: 'PUT',
            headers: apiHeaders(),
            credentials: 'same-origin',
            body: JSON.stringify({ starts_at: startsAt.toISOString(), ends_at: endsAt.toISOString() }),
        });
        if (!res.ok) {
            const data = await res.json().catch(() => null);
            onError(data?.message || 'Impossible de déplacer ce créneau.');
            onAppointmentsChange(previous);
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
        } else {
            moveAppointment(payload.appointmentId, startsAt, payload.durationMinutes);
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

            const current = appointmentsRef.current;
            const appt = current.find(a => a.id === resize.appointmentId);
            if (!appt) return;

            const newTime = new Date(`${resize.dayIso}T${String(DAY_START_HOUR).padStart(2, '0')}:00:00`);
            newTime.setMinutes(newTime.getMinutes() + clampedSlot * SLOT_MINUTES);

            const next = current.map(a => {
                if (a.id !== resize.appointmentId) return a;
                if (resize.edge === 'start' && newTime.getTime() <= new Date(a.ends_at).getTime() - SLOT_MINUTES * 60000) {
                    return { ...a, starts_at: newTime.toISOString() };
                }
                if (resize.edge === 'end' && newTime.getTime() >= new Date(a.starts_at).getTime() + SLOT_MINUTES * 60000) {
                    return { ...a, ends_at: newTime.toISOString() };
                }
                return a;
            });
            appointmentsRef.current = next;
            onAppointmentsChange(next);
        };

        const handleMouseUp = async () => {
            const appt = appointmentsRef.current.find(a => a.id === resize.appointmentId);
            const original = resizeOriginalRef.current;
            setResize(null);
            resizeOriginalRef.current = null;
            if (!appt) return;

            const res = await fetch(`/api/quote-appointments/${appt.id}`, {
                method: 'PUT',
                headers: apiHeaders(),
                credentials: 'same-origin',
                body: JSON.stringify({ starts_at: appt.starts_at, ends_at: appt.ends_at }),
            });
            if (!res.ok) {
                const data = await res.json().catch(() => null);
                onError(data?.message || 'Impossible de redimensionner ce créneau.');
                if (original) {
                    onAppointmentsChange(appointmentsRef.current.map(a => (a.id === original.id ? original : a)));
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

    const handleDelete = async (appointmentId: number) => {
        if (!confirm('Supprimer ce créneau ?')) return;

        const appt = appointments.find(a => a.id === appointmentId);

        const res = await fetch(`/api/quote-appointments/${appointmentId}`, {
            method: 'DELETE',
            headers: apiHeaders(),
            credentials: 'same-origin',
        });

        if (res.ok) {
            onAppointmentsChange(appointments.filter(a => a.id !== appointmentId));
            if (appt) {
                onQuoteScheduledChange?.(appt.quote_id, false);
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
                                />
                            ))}

                            {(appointmentsByDay.get(dayIso) ?? []).map(appt => {
                                const start = new Date(appt.starts_at);
                                const end = new Date(appt.ends_at);
                                const top = slotIndexOf(start) * SLOT_HEIGHT_PX;
                                const durationMinutes = Math.round((end.getTime() - start.getTime()) / 60000);
                                const height = Math.max(SLOT_HEIGHT_PX, (durationMinutes / SLOT_MINUTES) * SLOT_HEIGHT_PX);

                                return (
                                    <div
                                        key={appt.id}
                                        className="agenda__appointment"
                                        style={{ top, height }}
                                        draggable={!resize}
                                        onDragStart={e => handleDragStartAppointment(e, appt.id, durationMinutes)}
                                        onClick={() => handleAppointmentClick(appt)}
                                    >
                                        <div
                                            className="agenda__appointment-handle agenda__appointment-handle--top"
                                            onMouseDown={e => { e.stopPropagation(); e.preventDefault(); resizeOriginalRef.current = appt; setResize({ appointmentId: appt.id, edge: 'start', dayIso }); }}
                                        />
                                        <div className="agenda__appointment-content">
                                            <div className="agenda__appointment-line">
                                                <span className="agenda__appointment-time">
                                                    {formatTime(appt.starts_at)}–{formatTime(appt.ends_at)}
                                                </span>
                                                <span className="agenda__appointment-title">{appt.client_name}</span>
                                                <span className="agenda__appointment-bike">{appt.bike_description ?? appt.quote_reference}</span>
                                            </div>
                                            {appt.status_label && (
                                                <span className="agenda__appointment-status">{appt.status_label}</span>
                                            )}
                                        </div>
                                        <button
                                            type="button"
                                            className="agenda__appointment-remove"
                                            onClick={e => { e.stopPropagation(); handleDelete(appt.id); }}
                                            title="Supprimer ce créneau"
                                        >
                                            ×
                                        </button>
                                        <div
                                            className="agenda__appointment-handle agenda__appointment-handle--bottom"
                                            onMouseDown={e => { e.stopPropagation(); e.preventDefault(); resizeOriginalRef.current = appt; setResize({ appointmentId: appt.id, edge: 'end', dayIso }); }}
                                        />
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                );
            })}
        </div>
    );
}
