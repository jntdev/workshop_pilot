import { useMemo, useState } from 'react';
import { useOptimisticMutation } from '@/hooks/useOptimisticMutation';
import type { LoadedReservation, ReservationStatut } from '@/types';

interface AcomptesPanelProps {
    reservations: LoadedReservation[];
    onClose: () => void;
    onReservationClick: (reservationId: number) => void;
}

type AcompteFilter = 'tous' | 'en_attente' | 'recu';

const STATUT_LABELS: Record<ReservationStatut, string> = {
    reserve: 'Réservé',
    en_attente_acompte: 'Attente acompte',
    en_cours: 'En cours',
    paye: 'Payé',
    annule: 'Annulé',
};

const STATUT_OPTIONS: ReservationStatut[] = ['reserve', 'en_attente_acompte', 'en_cours', 'paye', 'annule'];

const formatDateFr = (dateStr: string): string => {
    const date = new Date(dateStr);
    return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' });
};

const formatAmount = (amount: string | null): string => {
    if (!amount) return '';
    return parseFloat(amount).toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' });
};

interface AcompteCardProps {
    reservation: LoadedReservation;
    onReservationClick: (id: number) => void;
}

function AcompteCard({ reservation, onReservationClick }: AcompteCardProps) {
    const { updateReservation } = useOptimisticMutation();

    const [localStatut, setLocalStatut] = useState<ReservationStatut>(reservation.statut);
    const [localDate, setLocalDate] = useState<string>(reservation.acompte_paye_le ?? '');
    const [isSaving, setIsSaving] = useState(false);
    const [isLeaving, setIsLeaving] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const hasChanges = localStatut !== reservation.statut || localDate !== (reservation.acompte_paye_le ?? '');

    const handleConfirm = async (e: React.MouseEvent) => {
        e.stopPropagation();
        setError(null);
        setIsSaving(true);
        setIsLeaving(true);

        const payload: Record<string, unknown> = {};
        if (localStatut !== reservation.statut) {
            payload.statut = localStatut;
        }
        if (localDate !== (reservation.acompte_paye_le ?? '')) {
            payload.acompte_paye_le = localDate || null;
        }

        const result = await updateReservation(reservation.id, payload);
        setIsSaving(false);

        if (!result.success) {
            setIsLeaving(false);
            const firstError = result.validationErrors
                ? Object.values(result.validationErrors)[0]?.[0]
                : result.message;
            setError(firstError ?? 'Erreur');
            return;
        }
    };

    const handleDiscard = (e: React.MouseEvent) => {
        e.stopPropagation();
        setLocalStatut(reservation.statut);
        setLocalDate(reservation.acompte_paye_le ?? '');
        setError(null);
    };

    return (
        <div
            className={`acompte-card ${isLeaving ? 'acompte-card--leaving' : ''}`}
            data-color={reservation.color}
            onClick={() => !hasChanges && onReservationClick(reservation.id)}
            role="button"
            tabIndex={0}
            onKeyDown={(e) => e.key === 'Enter' && !hasChanges && onReservationClick(reservation.id)}
        >
            <div className="acompte-card__header">
                <span className="acompte-card__client">{reservation.client_name}</span>
                <span className="acompte-card__amount">{formatAmount(reservation.acompte_montant)}</span>
            </div>

            <div className="acompte-card__meta">
                <span className="acompte-card__dates">
                    {formatDateFr(reservation.date_reservation)} → {formatDateFr(reservation.date_retour)}
                </span>
            </div>

            <div className="acompte-card__footer">
                {reservation.acompte_paye_le ? (
                    <span className="acompte-card__badge acompte-card__badge--recu">
                        Reçu le {formatDateFr(reservation.acompte_paye_le)}
                    </span>
                ) : reservation.statut === 'en_attente_acompte' ? (
                    <span className="acompte-card__badge acompte-card__badge--attente">
                        En attente d'acompte
                    </span>
                ) : (
                    <span className="acompte-card__badge acompte-card__badge--demande">
                        Acompte demandé
                    </span>
                )}
                {reservation.client?.telephone && (
                    <a
                        href={`tel:${reservation.client.telephone}`}
                        className="acompte-card__phone"
                        onClick={(e) => e.stopPropagation()}
                    >
                        {reservation.client.telephone}
                    </a>
                )}
            </div>

            <div className="acompte-card__status-row" onClick={(e) => e.stopPropagation()}>
                <select
                    className={`acompte-card__status-select acompte-card__status-select--${localStatut}`}
                    value={localStatut}
                    onChange={(e) => setLocalStatut(e.target.value as ReservationStatut)}
                    disabled={isSaving}
                    aria-label="Statut de la réservation"
                >
                    {STATUT_OPTIONS.map((s) => (
                        <option key={s} value={s}>{STATUT_LABELS[s]}</option>
                    ))}
                </select>
            </div>

            <div className="acompte-card__date-row" onClick={(e) => e.stopPropagation()}>
                <label className="acompte-card__date-label">Acompte reçu le</label>
                <input
                    type="date"
                    className="acompte-card__date-input"
                    value={localDate}
                    onChange={(e) => setLocalDate(e.target.value)}
                    disabled={isSaving}
                />
                {localDate && (
                    <button
                        type="button"
                        className="acompte-card__date-clear"
                        onClick={(e) => { e.stopPropagation(); setLocalDate(''); }}
                        aria-label="Effacer la date"
                    >
                        ×
                    </button>
                )}
            </div>

            {hasChanges && (
                <div className="acompte-card__confirm-row" onClick={(e) => e.stopPropagation()}>
                    <button
                        type="button"
                        className="acompte-card__discard"
                        onClick={handleDiscard}
                        disabled={isSaving}
                    >
                        Annuler
                    </button>
                    <button
                        type="button"
                        className="acompte-card__confirm"
                        onClick={handleConfirm}
                        disabled={isSaving}
                    >
                        {isSaving ? '…' : 'OK ?'}
                    </button>
                </div>
            )}

            {error && (
                <div className="acompte-card__error" onClick={(e) => e.stopPropagation()}>
                    {error}
                </div>
            )}
        </div>
    );
}

export default function AcomptesPanel({ reservations, onClose, onReservationClick }: AcomptesPanelProps) {
    const [filter, setFilter] = useState<AcompteFilter>('en_attente');

    const withAcompte = useMemo(
        () => reservations.filter((r) => r.acompte_demande && r.acompte_montant && r.statut !== 'annule'),
        [reservations]
    );

    const filtered = useMemo(() => {
        if (filter === 'en_attente') {
            return withAcompte.filter((r) => !r.acompte_paye_le);
        }
        if (filter === 'recu') {
            return withAcompte.filter((r) => !!r.acompte_paye_le);
        }
        return withAcompte;
    }, [withAcompte, filter]);

    const totalAttente = useMemo(
        () => withAcompte.filter((r) => !r.acompte_paye_le).reduce((sum, r) => sum + parseFloat(r.acompte_montant ?? '0'), 0),
        [withAcompte]
    );

    const totalRecu = useMemo(
        () => withAcompte.filter((r) => !!r.acompte_paye_le).reduce((sum, r) => sum + parseFloat(r.acompte_montant ?? '0'), 0),
        [withAcompte]
    );

    const sorted = useMemo(
        () => [...filtered].sort((a, b) => a.date_reservation.localeCompare(b.date_reservation)),
        [filtered]
    );

    return (
        <div className="acomptes-panel">
            <div className="acomptes-panel__header">
                <button
                    type="button"
                    className="acomptes-panel__close"
                    onClick={onClose}
                    aria-label="Fermer"
                >
                    ×
                </button>
                <h2 className="acomptes-panel__title">Acomptes</h2>
                <span className="acomptes-panel__count">{withAcompte.length}</span>
            </div>

            <div className="acomptes-panel__totals">
                <div className="acomptes-panel__total acomptes-panel__total--attente">
                    <span className="acomptes-panel__total-label">En attente</span>
                    <span className="acomptes-panel__total-amount">
                        {totalAttente.toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' })}
                    </span>
                </div>
                <div className="acomptes-panel__total acomptes-panel__total--recu">
                    <span className="acomptes-panel__total-label">Reçus</span>
                    <span className="acomptes-panel__total-amount">
                        {totalRecu.toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' })}
                    </span>
                </div>
            </div>

            <div className="acomptes-panel__filters">
                {(['en_attente', 'recu', 'tous'] as AcompteFilter[]).map((f) => (
                    <button
                        key={f}
                        type="button"
                        className={`acomptes-panel__filter-btn ${filter === f ? 'acomptes-panel__filter-btn--active' : ''}`}
                        onClick={() => setFilter(f)}
                    >
                        {f === 'en_attente' && 'En attente'}
                        {f === 'recu' && 'Reçus'}
                        {f === 'tous' && 'Tous'}
                    </button>
                ))}
            </div>

            <div className="acomptes-panel__list">
                {sorted.length === 0 ? (
                    <div className="acomptes-panel__empty">
                        Aucun acompte {filter === 'en_attente' ? 'en attente' : filter === 'recu' ? 'reçu' : ''}
                    </div>
                ) : (
                    sorted.map((reservation) => (
                        <AcompteCard
                            key={reservation.id}
                            reservation={reservation}
                            onReservationClick={onReservationClick}
                        />
                    ))
                )}
            </div>
        </div>
    );
}
