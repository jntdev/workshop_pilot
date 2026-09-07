import { useState, useEffect, useCallback } from 'react';
import { apiGet, apiPost, ApiError } from '@/utils/api';
import type { SaleStatus } from '@/types';

interface RecentSale {
    id: number;
    reference: string | null;
    status: SaleStatus;
    total_ttc: number;
    completed_at: string | null;
    lines_count: number;
}

interface RecentSalesResponse {
    sales: RecentSale[];
}

interface RecentSalesProps {
    refreshTrigger: number;
}

export default function RecentSales({ refreshTrigger }: RecentSalesProps) {
    const [sales, setSales] = useState<RecentSale[]>([]);
    const [error, setError] = useState<string | null>(null);
    const [cancellingId, setCancellingId] = useState<number | null>(null);

    const loadRecentSales = useCallback(async () => {
        try {
            const response = await apiGet<RecentSalesResponse>('/api/sales/recent');
            setSales(response.sales);
        } catch (err) {
            setError(err instanceof ApiError ? err.message : 'Erreur lors du chargement des ventes récentes.');
        }
    }, []);

    useEffect(() => {
        loadRecentSales();
    }, [loadRecentSales, refreshTrigger]);

    const handleCancel = useCallback(async (sale: RecentSale) => {
        if (!window.confirm(`Confirmer l'annulation de la vente ${sale.reference} ?`)) {
            return;
        }

        setCancellingId(sale.id);

        try {
            await apiPost(`/api/sales/${sale.id}/cancel`);
            await loadRecentSales();
        } catch (err) {
            setError(err instanceof ApiError ? err.message : "Erreur lors de l'annulation.");
        } finally {
            setCancellingId(null);
        }
    }, [loadRecentSales]);

    if (sales.length === 0) {
        return null;
    }

    return (
        <div className="recent-sales">
            <h2 className="recent-sales__title">Ventes récentes</h2>

            {error && <div className="recent-sales__error">{error}</div>}

            <table className="recent-sales__table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Heure</th>
                        <th>Lignes</th>
                        <th>Total TTC</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    {sales.map((sale) => (
                        <tr key={sale.id} className="recent-sales__row">
                            <td>{sale.reference}</td>
                            <td>
                                {sale.completed_at
                                    ? new Date(sale.completed_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
                                    : '—'}
                            </td>
                            <td>{sale.lines_count}</td>
                            <td>{(sale.total_ttc / 100).toFixed(2)} €</td>
                            <td>
                                <span className={`recent-sales__status recent-sales__status--${sale.status}`}>
                                    {sale.status === 'completed' ? 'Finalisée' : 'Annulée'}
                                </span>
                            </td>
                            <td>
                                {sale.status === 'completed' && (
                                    <button
                                        type="button"
                                        onClick={() => handleCancel(sale)}
                                        disabled={cancellingId === sale.id}
                                        className="recent-sales__cancel"
                                    >
                                        {cancellingId === sale.id ? 'Annulation...' : 'Annuler'}
                                    </button>
                                )}
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}
