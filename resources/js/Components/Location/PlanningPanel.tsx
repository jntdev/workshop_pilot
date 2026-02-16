import { useMemo } from 'react';
import type { ReservationStatut, ReservationColorIndex } from '@/types';

export interface PlanningReservation {
    id: number;
    client_id: number | null;
    client_name: string;
    client: {
        id: number;
        prenom: string;
        nom: string;
        email: string | null;
        telephone: string | null;
        adresse: string | null;
    } | null;
    date_reservation: string;
    date_retour: string;
    livraison_necessaire: boolean;
    adresse_livraison: string | null;
    contact_livraison: string | null;
    creneau_livraison: string | null;
    recuperation_necessaire: boolean;
    adresse_recuperation: string | null;
    contact_recuperation: string | null;
    creneau_recuperation: string | null;
    acompte_demande: boolean;
    acompte_paye_le: string | null;
    statut: ReservationStatut;
    commentaires: string | null;
    color: ReservationColorIndex;
    items: Array<{
        bike_type_id: string;
        quantite: number;
        bike_type: {
            id: string;
            label: string;
            category: string;
            size: string;
            frame_type: string;
        } | null;
    }>;
}

interface PlanningPanelProps {
    date: string;
    departures: PlanningReservation[];
    returns: PlanningReservation[];
    onDateChange: (date: string) => void;
    onClose: () => void;
    onReservationClick: (reservationId: number) => void;
}

const formatDateFr = (dateStr: string): string => {
    const date = new Date(dateStr);
    return date.toLocaleDateString('fr-FR', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
    });
};

const addDays = (dateStr: string, days: number): string => {
    const date = new Date(dateStr);
    date.setDate(date.getDate() + days);
    return date.toISOString().split('T')[0];
};

const isToday = (dateStr: string): boolean => {
    const today = new Date().toISOString().split('T')[0];
    return dateStr === today;
};

interface ReservationCardProps {
    reservation: PlanningReservation;
    type: 'departure' | 'return';
    onReservationClick: (reservationId: number) => void;
}

function ReservationCard({ reservation, type, onReservationClick }: ReservationCardProps) {
    const isDeparture = type === 'departure';
    const isDelivery = isDeparture ? reservation.livraison_necessaire : reservation.recuperation_necessaire;
    const address = isDeparture ? reservation.adresse_livraison : reservation.adresse_recuperation;
    const slot = isDeparture ? reservation.creneau_livraison : reservation.creneau_recuperation;

    const needsAttention = reservation.acompte_demande && !reservation.acompte_paye_le;
    const isLate = type === 'return' && reservation.statut !== 'paye' && isToday(reservation.date_retour);

    const bikesSummary = useMemo(() => {
        const grouped = new Map<string, number>();
        reservation.items.forEach((item) => {
            const label = item.bike_type?.label || item.bike_type_id;
            grouped.set(label, (grouped.get(label) || 0) + item.quantite);
        });
        return Array.from(grouped.entries()).map(([label, qty]) => ({
            label,
            qty,
        }));
    }, [reservation.items]);

    return (
        <div
            className="planning-card"
            data-color={reservation.color}
            onClick={() => onReservationClick(reservation.id)}
            role="button"
            tabIndex={0}
            onKeyDown={(e) => e.key === 'Enter' && onReservationClick(reservation.id)}
        >
            <div className="planning-card__header">
                <div className="planning-card__badges">
                    {isDelivery ? (
                        <span className="planning-card__badge planning-card__badge--delivery">
                            Livraison
                        </span>
                    ) : (
                        <span className="planning-card__badge planning-card__badge--pickup">
                            Sur place
                        </span>
                    )}
                    {needsAttention && (
                        <span className="planning-card__badge planning-card__badge--warning">
                            Acompte ?
                        </span>
                    )}
                    {isLate && (
                        <span className="planning-card__badge planning-card__badge--danger">
                            Suivi
                        </span>
                    )}
                </div>
                <span className="planning-card__status" data-status={reservation.statut}>
                    {reservation.statut === 'reserve' && 'Réservé'}
                    {reservation.statut === 'en_attente_acompte' && 'Attente acompte'}
                    {reservation.statut === 'en_cours' && 'En cours'}
                    {reservation.statut === 'paye' && 'Payé'}
                </span>
            </div>

            <div className="planning-card__client">
                <span className="planning-card__client-name">{reservation.client_name}</span>
                {reservation.client?.telephone && (
                    <a
                        href={`tel:${reservation.client.telephone}`}
                        className="planning-card__client-phone"
                        onClick={(e) => e.stopPropagation()}
                    >
                        {reservation.client.telephone}
                    </a>
                )}
            </div>

            <div className="planning-card__bikes">
                {bikesSummary.map(({ label, qty }) => (
                    <span key={label} className="planning-card__bike">
                        {qty > 1 && <span className="planning-card__bike-qty">{qty}x</span>}
                        {label}
                    </span>
                ))}
            </div>

            {isDelivery && address && (
                <div className="planning-card__logistics">
                    <div className="planning-card__address">{address}</div>
                    {slot && <div className="planning-card__slot">Créneau : {slot}</div>}
                </div>
            )}

            {reservation.commentaires && (
                <div className="planning-card__comment">
                    {reservation.commentaires}
                </div>
            )}
        </div>
    );
}

interface ColumnProps {
    title: string;
    reservations: PlanningReservation[];
    type: 'departure' | 'return';
    onReservationClick: (reservationId: number) => void;
}

function Column({ title, reservations, type, onReservationClick }: ColumnProps) {
    const deliveries = reservations.filter((r) =>
        type === 'departure' ? r.livraison_necessaire : r.recuperation_necessaire
    );
    const pickups = reservations.filter((r) =>
        type === 'departure' ? !r.livraison_necessaire : !r.recuperation_necessaire
    );

    return (
        <div className="planning-column">
            <h3 className="planning-column__title">
                {title}
                <span className="planning-column__count">{reservations.length}</span>
            </h3>

            {reservations.length === 0 ? (
                <div className="planning-column__empty">
                    Aucun {type === 'departure' ? 'départ' : 'retour'}
                </div>
            ) : (
                <>
                    {deliveries.length > 0 && (
                        <div className="planning-section">
                            <h4 className="planning-section__title">
                                {type === 'departure' ? 'Livraisons' : 'Récupérations'}
                            </h4>
                            <div className="planning-section__cards">
                                {deliveries.map((r) => (
                                    <ReservationCard
                                        key={r.id}
                                        reservation={r}
                                        type={type}
                                        onReservationClick={onReservationClick}
                                    />
                                ))}
                            </div>
                        </div>
                    )}

                    {pickups.length > 0 && (
                        <div className="planning-section">
                            <h4 className="planning-section__title">Sur place</h4>
                            <div className="planning-section__cards">
                                {pickups.map((r) => (
                                    <ReservationCard
                                        key={r.id}
                                        reservation={r}
                                        type={type}
                                        onReservationClick={onReservationClick}
                                    />
                                ))}
                            </div>
                        </div>
                    )}
                </>
            )}
        </div>
    );
}

export default function PlanningPanel({
    date,
    departures,
    returns,
    onDateChange,
    onClose,
    onReservationClick,
}: PlanningPanelProps) {
    const goToToday = () => {
        const today = new Date().toISOString().split('T')[0];
        onDateChange(today);
    };

    return (
        <div className="planning-panel">
            <div className="planning-panel__header">
                <button
                    type="button"
                    className="planning-panel__close"
                    onClick={onClose}
                    aria-label="Fermer"
                >
                    ×
                </button>
                <h2 className="planning-panel__title">{formatDateFr(date)}</h2>
                <div className="planning-panel__summary">
                    {departures.length} départ{departures.length !== 1 ? 's' : ''} · {returns.length} retour{returns.length !== 1 ? 's' : ''}
                </div>
            </div>

            <div className="planning-panel__nav">
                <button
                    type="button"
                    className="planning-panel__nav-btn"
                    onClick={() => onDateChange(addDays(date, -1))}
                >
                    ← J-1
                </button>
                <button
                    type="button"
                    className="planning-panel__nav-btn planning-panel__nav-btn--today"
                    onClick={goToToday}
                    disabled={isToday(date)}
                >
                    Aujourd'hui
                </button>
                <button
                    type="button"
                    className="planning-panel__nav-btn"
                    onClick={() => onDateChange(addDays(date, 1))}
                >
                    J+1 →
                </button>
                <input
                    type="date"
                    className="planning-panel__date-input"
                    value={date}
                    onChange={(e) => onDateChange(e.target.value)}
                />
            </div>

            <div className="planning-panel__content">
                <Column
                    title="Départs"
                    reservations={departures}
                    type="departure"
                    onReservationClick={onReservationClick}
                />
                <Column
                    title="Retours"
                    reservations={returns}
                    type="return"
                    onReservationClick={onReservationClick}
                />
            </div>
        </div>
    );
}
