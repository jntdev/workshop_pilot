import { useState, useEffect, useCallback, useMemo, useRef } from 'react';
import { Head, Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import WeekAgendaGrid from '@/Components/Atelier/Agenda/WeekAgendaGrid';
import AppointmentDetailPanel from '@/Components/Atelier/Agenda/AppointmentDetailPanel';
import { addDays, startOfWeek, todayIso, daysFrom, formatDateRangeLabel, AGENDA_HOURS_COL_PX, AGENDA_MIN_DAY_COL_PX } from '@/Components/Atelier/Agenda/agendaShared';
import type { QuoteAppointment } from '@/types';

export default function AgendaIndex() {
    const [weekStart, setWeekStart] = useState(startOfWeek(todayIso()));
    const [appointments, setAppointments] = useState<QuoteAppointment[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [dayCount, setDayCount] = useState(7);
    const [selectedAppointment, setSelectedAppointment] = useState<QuoteAppointment | null>(null);
    const containerRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        const el = containerRef.current;
        if (!el) return;

        const computeDayCount = (width: number) => {
            const available = width - AGENDA_HOURS_COL_PX;
            const count = Math.floor(available / AGENDA_MIN_DAY_COL_PX);
            return Math.max(7, count);
        };

        const observer = new ResizeObserver(entries => {
            for (const entry of entries) {
                setDayCount(computeDayCount(entry.contentRect.width));
            }
        });
        observer.observe(el);
        setDayCount(computeDayCount(el.getBoundingClientRect().width));

        return () => observer.disconnect();
    }, []);

    const days = useMemo(() => daysFrom(weekStart, dayCount), [weekStart, dayCount]);

    useEffect(() => {
        setSelectedAppointment(prev => {
            if (!prev) return prev;
            const updated = appointments.find(a => a.id === prev.id);
            return updated ?? null;
        });
    }, [appointments]);

    const loadAppointments = useCallback(async (mondayIso: string, count: number) => {
        const start = `${mondayIso}T00:00:00`;
        const end = `${addDays(mondayIso, count - 1)}T23:59:59`;
        const res = await fetch(`/api/quote-appointments?start=${start}&end=${end}`, { headers: { Accept: 'application/json' } });
        return res.ok ? res.json() : [];
    }, []);

    const load = useCallback(async (mondayIso: string, count: number) => {
        setIsLoading(true);
        setError(null);
        try {
            setAppointments(await loadAppointments(mondayIso, count));
        } catch {
            setError('Impossible de charger l\'agenda.');
        } finally {
            setIsLoading(false);
        }
    }, [loadAppointments]);

    useEffect(() => {
        load(weekStart, dayCount);
    }, [weekStart, dayCount, load]);

    const isCurrentWeek = weekStart === startOfWeek(todayIso());

    return (
        <MainLayout>
            <Head title="Agenda atelier" />

            <div className="page-header">
                <div className="breadcrumb">
                    <Link href="/atelier">Atelier</Link>
                    <span>&gt;</span>
                    <span>Agenda</span>
                </div>
                <h1>Agenda atelier</h1>
            </div>

            <div className="agenda">
                {error && <div className="agenda__error">{error}</div>}

                <div className="agenda__main" ref={containerRef}>
                    <div className="agenda__nav">
                        <button type="button" className="agenda__nav-btn agenda__nav-btn--today" onClick={() => setWeekStart(startOfWeek(todayIso()))} disabled={isCurrentWeek}>
                            Cette semaine
                        </button>
                        <button type="button" className="agenda__nav-btn" onClick={() => setWeekStart(prev => addDays(prev, -7))} aria-label="Semaine précédente">‹</button>
                        <span className="agenda__date">{formatDateRangeLabel(days)}</span>
                        <button type="button" className="agenda__nav-btn" onClick={() => setWeekStart(prev => addDays(prev, 7))} aria-label="Semaine suivante">›</button>
                    </div>

                    {isLoading ? (
                        <div className="agenda__loading">Chargement...</div>
                    ) : (
                        <WeekAgendaGrid
                            days={days}
                            appointments={appointments}
                            onAppointmentsChange={setAppointments}
                            onError={setError}
                            onAppointmentClick={setSelectedAppointment}
                        />
                    )}
                </div>
            </div>

            <AppointmentDetailPanel appointment={selectedAppointment} onClose={() => setSelectedAppointment(null)} />
        </MainLayout>
    );
}
