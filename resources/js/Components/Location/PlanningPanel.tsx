import { useMemo } from 'react';
import type { ReservationStatut, ReservationColorIndex, BikeDefinition } from '@/types';

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
    date_recuperation: string | null;
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
    selection: Array<{ bike_id: number | string; dates: string[]; is_hs: boolean }>;
}

interface PlanningPanelProps {
    date: string;
    departures: PlanningReservation[];
    returns: PlanningReservation[];
    bikes: BikeDefinition[];
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
    bikes: BikeDefinition[];
    onReservationClick: (reservationId: number) => void;
}

function ReservationCard({ reservation, type, bikes, onReservationClick }: ReservationCardProps) {
    const isDeparture = type === 'departure';
    const isDelivery = isDeparture ? reservation.livraison_necessaire : reservation.recuperation_necessaire;
    const address = isDeparture ? reservation.adresse_livraison : reservation.adresse_recuperation;
    const slot = isDeparture ? reservation.creneau_livraison : reservation.creneau_recuperation;

    const needsAttention = reservation.acompte_demande && !reservation.acompte_paye_le;
    const isLate = type === 'return' && reservation.statut !== 'paye' && isToday(reservation.date_retour);

    const bikeNames = useMemo(() => {
        if (!reservation.selection || reservation.selection.length === 0) {
            return null;
        }
        const names = reservation.selection
            .map(s => {
                const numericId = typeof s.bike_id === 'string'
                    ? parseInt(s.bike_id.replace('bike_', ''), 10)
                    : s.bike_id;
                return bikes.find(b => b.id === numericId)?.name;
            })
            .filter((name): name is string => Boolean(name))
            .filter((name, i, arr) => arr.indexOf(name) === i);
        return names.length > 0 ? names : null;
    }, [reservation.selection, bikes]);

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
                {bikeNames ? (
                    bikeNames.map((name) => (
                        <span key={name} className="planning-card__bike">{name}</span>
                    ))
                ) : (
                    <span className="planning-card__bike planning-card__bike--unknown">Vélos non renseignés</span>
                )}
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

interface SubColumnProps {
    title: string;
    subtitle: string;
    reservations: PlanningReservation[];
    type: 'departure' | 'return';
    bikes: BikeDefinition[];
    onReservationClick: (reservationId: number) => void;
}

function SubColumn({ title, subtitle, reservations, type, bikes, onReservationClick }: SubColumnProps) {
    const evening = reservations.filter(r => r.date_recuperation !== null);
    const normal = reservations.filter(r => r.date_recuperation === null);

    return (
        <div className="planning-column">
            <h3 className="planning-column__title">
                {title}
                <span className="planning-column__count">{reservations.length}</span>
            </h3>
            <div className="planning-column__subtitle">{subtitle}</div>
            {reservations.length === 0 ? (
                <div className="planning-column__empty">Aucun</div>
            ) : (
                <>
                    {normal.length > 0 && (
                        <div className="planning-section__cards">
                            {normal.map((r) => (
                                <ReservationCard key={r.id} reservation={r} type={type} bikes={bikes} onReservationClick={onReservationClick} />
                            ))}
                        </div>
                    )}
                    {evening.length > 0 && (
                        <>
                            <div className="planning-column__evening-divider">Veille · après 18h</div>
                            <div className="planning-section__cards">
                                {evening.map((r) => (
                                    <ReservationCard key={r.id} reservation={r} type={type} bikes={bikes} onReservationClick={onReservationClick} />
                                ))}
                            </div>
                        </>
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
    bikes,
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
                <a
                    href={`/location/fiche-departs?date=${date}`}
                    target="_blank"
                    rel="noreferrer"
                    className="planning-panel__print-btn"
                >
                    Fiche départs
                </a>
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
                <SubColumn
                    title="Départs"
                    subtitle="Livraison"
                    reservations={departures.filter(r => r.livraison_necessaire)}
                    type="departure"
                    bikes={bikes}
                    onReservationClick={onReservationClick}
                />
                <SubColumn
                    title="Départs"
                    subtitle="Atelier"
                    reservations={departures.filter(r => !r.livraison_necessaire)}
                    type="departure"
                    bikes={bikes}
                    onReservationClick={onReservationClick}
                />
                <SubColumn
                    title="Retours"
                    subtitle="Récupération"
                    reservations={returns.filter(r => r.recuperation_necessaire)}
                    type="return"
                    bikes={bikes}
                    onReservationClick={onReservationClick}
                />
                <SubColumn
                    title="Retours"
                    subtitle="Atelier"
                    reservations={returns.filter(r => !r.recuperation_necessaire)}
                    type="return"
                    bikes={bikes}
                    onReservationClick={onReservationClick}
                />
            </div>
        </div>
    );
}
