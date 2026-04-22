import { useState, useEffect, useCallback } from 'react';
import { Head, Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';

interface OrderLine {
    quote_line_id: number;
    quote_id: number;
    client_id: number;
    client_nom_complet: string;
    bike_description: string | null;
    line_title: string;
    line_reference: string | null;
    quantity: string;
    needs_order: boolean;
    ordered_at: string | null;
    received_at: string | null;
    supply_status: 'to_order' | 'ordered' | 'received';
}

const getCsrfToken = (): string => {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
};

const apiHeaders = (): HeadersInit => ({
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'X-XSRF-TOKEN': getCsrfToken(),
});

const STATUS_LABELS: Record<OrderLine['supply_status'], string> = {
    to_order: 'À commander',
    ordered: 'Commandée',
    received: 'Reçue',
};

const STATUS_CLASS: Record<OrderLine['supply_status'], string> = {
    to_order: 'order-lines__badge--to-order',
    ordered: 'order-lines__badge--ordered',
    received: 'order-lines__badge--received',
};

export default function OrderLinesIndex() {
    const [lines, setLines] = useState<OrderLine[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [includeReceived, setIncludeReceived] = useState(false);
    const [pendingIds, setPendingIds] = useState<Set<number>>(new Set());
    const [error, setError] = useState<string | null>(null);

    const loadLines = useCallback(async () => {
        setIsLoading(true);
        setError(null);
        try {
            const url = '/api/quotes/order-lines' + (includeReceived ? '?include_received=1' : '');
            const response = await fetch(url, { headers: apiHeaders(), credentials: 'same-origin' });
            if (response.ok) {
                setLines(await response.json());
            } else {
                setError('Impossible de charger les pièces à commander.');
            }
        } catch {
            setError('Impossible de charger les pièces à commander.');
        } finally {
            setIsLoading(false);
        }
    }, [includeReceived]);

    useEffect(() => {
        loadLines();
    }, [loadLines]);

    const updateStatus = async (lineId: number, payload: Record<string, boolean>) => {
        if (pendingIds.has(lineId)) return;

        setPendingIds(prev => new Set(prev).add(lineId));
        setError(null);

        try {
            const response = await fetch(`/api/quote-lines/${lineId}/order-status`, {
                method: 'PATCH',
                headers: apiHeaders(),
                credentials: 'same-origin',
                body: JSON.stringify(payload),
            });

            if (response.ok) {
                const updated = await response.json();
                setLines(prev =>
                    prev.map(l =>
                        l.quote_line_id === lineId
                            ? { ...l, ...updated, quote_line_id: lineId }
                            : l
                    )
                );
            } else {
                const data = await response.json();
                setError(data.message || 'Une erreur est survenue.');
            }
        } catch {
            setError('Une erreur est survenue.');
        } finally {
            setPendingIds(prev => {
                const next = new Set(prev);
                next.delete(lineId);
                return next;
            });
        }
    };

    const openLines = lines.filter(l => l.supply_status !== 'received');
    const receivedLines = lines.filter(l => l.supply_status === 'received');

    return (
        <MainLayout>
            <Head title="Pièces à commander" />

            <div className="page-header">
                <div className="breadcrumb">
                    <Link href="/atelier">Atelier</Link>
                    <span>&gt;</span>
                    <span>Pièces à commander</span>
                </div>
                <h1>Pièces à commander</h1>
            </div>

            <div className="order-lines">
                {error && (
                    <div className="order-lines__error">{error}</div>
                )}

                <div className="order-lines__toolbar">
                    <label className="order-lines__filter">
                        <input
                            type="checkbox"
                            checked={includeReceived}
                            onChange={e => setIncludeReceived(e.target.checked)}
                        />
                        Afficher les pièces reçues
                    </label>
                </div>

                {isLoading ? (
                    <div className="order-lines__loading">Chargement...</div>
                ) : openLines.length === 0 && receivedLines.length === 0 ? (
                    <div className="order-lines__empty">Aucune pièce à commander.</div>
                ) : (
                    <>
                        <div className="order-lines__table">
                            <div className="order-lines__header">
                                <div className="order-lines__cell">Client</div>
                                <div className="order-lines__cell">Vélo</div>
                                <div className="order-lines__cell">Pièce / Intitulé</div>
                                <div className="order-lines__cell order-lines__cell--ref">Référence</div>
                                <div className="order-lines__cell order-lines__cell--qty">Qté</div>
                                <div className="order-lines__cell">Statut</div>
                                <div className="order-lines__cell">Actions</div>
                            </div>

                            {openLines.map(line => (
                                <div key={line.quote_line_id} className="order-lines__row">
                                    <div className="order-lines__cell">{line.client_nom_complet}</div>
                                    <div className="order-lines__cell order-lines__cell--muted">{line.bike_description ?? '—'}</div>
                                    <div className="order-lines__cell">{line.line_title}</div>
                                    <div className="order-lines__cell order-lines__cell--ref">
                                        <strong>{line.line_reference ?? '—'}</strong>
                                    </div>
                                    <div className="order-lines__cell order-lines__cell--qty">{line.quantity}</div>
                                    <div className="order-lines__cell">
                                        <span className={`order-lines__badge ${STATUS_CLASS[line.supply_status]}`}>
                                            {STATUS_LABELS[line.supply_status]}
                                        </span>
                                    </div>
                                    <div className="order-lines__cell order-lines__cell--actions">
                                        {line.supply_status === 'to_order' && (
                                            <button
                                                type="button"
                                                className="order-lines__btn order-lines__btn--ordered"
                                                disabled={pendingIds.has(line.quote_line_id)}
                                                onClick={() => updateStatus(line.quote_line_id, { mark_as_ordered: true })}
                                            >
                                                Commandée
                                            </button>
                                        )}
                                        {line.supply_status === 'ordered' && (
                                            <>
                                                <button
                                                    type="button"
                                                    className="order-lines__btn order-lines__btn--received"
                                                    disabled={pendingIds.has(line.quote_line_id)}
                                                    onClick={() => updateStatus(line.quote_line_id, { mark_as_received: true })}
                                                >
                                                    Reçue
                                                </button>
                                                <button
                                                    type="button"
                                                    className="order-lines__btn order-lines__btn--unmark"
                                                    disabled={pendingIds.has(line.quote_line_id)}
                                                    onClick={() => updateStatus(line.quote_line_id, { unmark: true })}
                                                >
                                                    Annuler
                                                </button>
                                            </>
                                        )}
                                        <Link
                                            href={`/atelier/devis/${line.quote_id}`}
                                            className="order-lines__btn order-lines__btn--consult"
                                        >
                                            Consulter
                                        </Link>
                                    </div>
                                </div>
                            ))}

                            {includeReceived && receivedLines.map(line => (
                                <div key={line.quote_line_id} className="order-lines__row order-lines__row--received">
                                    <div className="order-lines__cell">{line.client_nom_complet}</div>
                                    <div className="order-lines__cell order-lines__cell--muted">{line.bike_description ?? '—'}</div>
                                    <div className="order-lines__cell">{line.line_title}</div>
                                    <div className="order-lines__cell order-lines__cell--ref">
                                        <strong>{line.line_reference ?? '—'}</strong>
                                    </div>
                                    <div className="order-lines__cell order-lines__cell--qty">{line.quantity}</div>
                                    <div className="order-lines__cell">
                                        <span className={`order-lines__badge ${STATUS_CLASS[line.supply_status]}`}>
                                            {STATUS_LABELS[line.supply_status]}
                                        </span>
                                    </div>
                                    <div className="order-lines__cell order-lines__cell--actions">
                                        <Link
                                            href={`/atelier/devis/${line.quote_id}`}
                                            className="order-lines__btn order-lines__btn--consult"
                                        >
                                            Consulter
                                        </Link>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </>
                )}
            </div>
        </MainLayout>
    );
}
