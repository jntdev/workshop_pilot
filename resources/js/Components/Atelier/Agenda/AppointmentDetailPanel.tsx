import { useState, useEffect, useCallback } from 'react';
import { formatDayFullLabel, formatTime } from './agendaShared';
import type { QuoteAppointment, QuoteTask } from '@/types';

interface Props {
    appointment: QuoteAppointment | null;
    onClose: () => void;
}

export default function AppointmentDetailPanel({ appointment, onClose }: Props) {
    const [tasks, setTasks] = useState<QuoteTask[]>([]);
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const load = useCallback(async (quoteId: number) => {
        setIsLoading(true);
        setError(null);
        try {
            const res = await fetch(`/api/quotes/${quoteId}/tasks`, { headers: { Accept: 'application/json' } });
            setTasks(res.ok ? await res.json() : []);
        } catch {
            setError('Impossible de charger les travaux.');
        } finally {
            setIsLoading(false);
        }
    }, []);

    useEffect(() => {
        if (appointment) {
            load(appointment.quote_id);
        }
    }, [appointment, load]);

    const isOpen = appointment !== null;

    return (
        <div className={`agenda-planning-panel ${isOpen ? 'agenda-planning-panel--open' : ''}`}>
            {appointment && (
                <>
                    <div className="agenda-planning-panel__header">
                        <h2 className="agenda-planning-panel__title">{appointment.quote_reference}</h2>
                        <button type="button" className="agenda-planning-panel__close" onClick={onClose} aria-label="Fermer">
                            ×
                        </button>
                    </div>

                    <div className="agenda-appointment-detail__meta">
                        <p className="agenda-appointment-detail__client">{appointment.client_name}</p>
                        {appointment.bike_description && (
                            <p className="agenda-appointment-detail__bike">{appointment.bike_description}</p>
                        )}
                        <p className="agenda-appointment-detail__time">
                            {formatDayFullLabel(appointment.starts_at.slice(0, 10))} · {formatTime(appointment.starts_at)}–{formatTime(appointment.ends_at)}
                        </p>
                        {appointment.status_label && (
                            <span className="agenda__appointment-status">{appointment.status_label}</span>
                        )}
                    </div>

                    {error && <div className="agenda__error">{error}</div>}

                    <div className="agenda-appointment-detail__tasks">
                        <h3 className="agenda-appointment-detail__tasks-title">Travaux à faire</h3>
                        {isLoading ? (
                            <p className="quotes-list__tasks-loading">Chargement des travaux...</p>
                        ) : tasks.length === 0 ? (
                            <p className="quotes-list__tasks-empty">Aucun travail renseigné.</p>
                        ) : (
                            <ul className="quotes-list__tasks-list">
                                {tasks.map(task => (
                                    <li key={task.id}>
                                        {task.title}
                                        {task.quantity > 1 && ` (x${task.quantity})`}
                                    </li>
                                ))}
                            </ul>
                        )}
                    </div>

                    <a
                        href={`/atelier/devis/${appointment.quote_id}/pdf?print=1`}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="agenda-appointment-detail__print-btn"
                    >
                        Imprimer le devis complet
                    </a>
                </>
            )}
        </div>
    );
}
