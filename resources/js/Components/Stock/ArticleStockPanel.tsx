import { useState, useEffect, useCallback } from 'react';
import type { Article, StockMovement } from '@/types';

interface Props {
    article: Article;
    csrfToken: string;
    onClose: () => void;
    onChanged: () => void;
}

function formatPrice(cents: number | null): string {
    if (cents === null) { return '—'; }
    return (cents / 100).toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + ' €';
}

function formatDate(iso: string): string {
    return new Date(iso).toLocaleDateString('fr-FR');
}

export default function ArticleStockPanel({ article, csrfToken, onClose, onChanged }: Props) {
    const [movements, setMovements] = useState<StockMovement[]>([]);
    const [stockQuantity, setStockQuantity] = useState(article.stock_quantity);
    const [isLoading, setIsLoading] = useState(false);
    const [form, setForm] = useState({
        type: 'manual_in' as 'manual_in' | 'manual_out',
        quantity: '',
        unit_price_ht: (article.purchase_price_ht / 100).toFixed(2),
        note: '',
    });
    const [formError, setFormError] = useState<string | null>(null);

    const load = useCallback(async () => {
        const res = await fetch(`/api/articles/${article.id}/stock-movements`, { headers: { Accept: 'application/json' } });
        const data = await res.json();
        setMovements(data.movements ?? []);
        setStockQuantity(data.stock_quantity ?? 0);
    }, [article.id]);

    useEffect(() => { load(); }, [load]);

    const handleAdd = useCallback(async (e: React.FormEvent) => {
        e.preventDefault();
        setIsLoading(true);
        setFormError(null);

        const res = await fetch(`/api/articles/${article.id}/stock-movements`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({
                type: form.type,
                quantity: parseInt(form.quantity) || 0,
                unit_price_ht: form.unit_price_ht ? Math.round(parseFloat(form.unit_price_ht) * 100) : null,
                note: form.note || null,
            }),
        });

        if (res.ok) {
            setForm({ type: 'manual_in', quantity: '', unit_price_ht: (article.purchase_price_ht / 100).toFixed(2), note: '' });
            await load();
            onChanged();
        } else {
            const data = await res.json();
            setFormError(data.message || 'Erreur');
        }
        setIsLoading(false);
    }, [form, article.id, csrfToken, load, onChanged]);

    const handleDelete = useCallback(async (id: number) => {
        if (!confirm('Supprimer ce mouvement ?')) { return; }
        const res = await fetch(`/api/stock-movements/${id}`, {
            method: 'DELETE',
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
        });
        if (res.ok) {
            await load();
            onChanged();
        } else {
            const data = await res.json();
            alert(data.message);
        }
    }, [csrfToken, load, onChanged]);

    const stockClass = stockQuantity > 0 ? 'stock-panel__qty--positive' : stockQuantity < 0 ? 'stock-panel__qty--negative' : 'stock-panel__qty--zero';

    return (
        <div className="stock-panel__backdrop" onClick={e => { if (e.target === e.currentTarget) { onClose(); } }}>
            <div className="stock-panel">
                <div className="stock-panel__header">
                    <div>
                        <h2 className="stock-panel__title">{article.designation}</h2>
                        <span className="stock-panel__ref">{article.reference}</span>
                    </div>
                    <button type="button" className="stock-panel__close" onClick={onClose}>✕</button>
                </div>

                <div className="stock-panel__current">
                    <span className="stock-panel__qty-label">Stock actuel</span>
                    <span className={`stock-panel__qty ${stockClass}`}>{stockQuantity} {article.unit}</span>
                </div>

                <form className="stock-panel__form" onSubmit={handleAdd}>
                    <div className="stock-panel__form-row">
                        <select
                            className="stock-panel__select"
                            value={form.type}
                            onChange={e => setForm(p => ({ ...p, type: e.target.value as 'manual_in' | 'manual_out' }))}
                        >
                            <option value="manual_in">Entrée</option>
                            <option value="manual_out">Sortie</option>
                        </select>
                        <input
                            type="number"
                            min="1"
                            className="stock-panel__input stock-panel__input--qty"
                            placeholder="Qté"
                            value={form.quantity}
                            onChange={e => setForm(p => ({ ...p, quantity: e.target.value }))}
                            required
                        />
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            className="stock-panel__input"
                            placeholder="Prix unit. HT (€)"
                            value={form.unit_price_ht}
                            onChange={e => setForm(p => ({ ...p, unit_price_ht: e.target.value }))}
                        />
                        <input
                            type="text"
                            className="stock-panel__input stock-panel__input--note"
                            placeholder="Note"
                            value={form.note}
                            onChange={e => setForm(p => ({ ...p, note: e.target.value }))}
                        />
                        <button type="submit" className="stock-panel__btn stock-panel__btn--primary" disabled={isLoading || !form.quantity}>
                            Ajouter
                        </button>
                    </div>
                    {formError && <div className="stock-panel__form-error">{formError}</div>}
                </form>

                <div className="stock-panel__history">
                    <h3 className="stock-panel__history-title">Historique</h3>
                    {movements.length === 0 ? (
                        <div className="stock-panel__empty">Aucun mouvement</div>
                    ) : (
                        <table className="stock-panel__table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Qté</th>
                                    <th>Prix unit.</th>
                                    <th>Note</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                {movements.map(m => (
                                    <tr key={m.id} className={m.quantity > 0 ? 'stock-panel__row--in' : 'stock-panel__row--out'}>
                                        <td>{formatDate(m.created_at)}</td>
                                        <td>{m.type_label}</td>
                                        <td className="stock-panel__cell-qty">{m.quantity > 0 ? '+' : ''}{m.quantity}</td>
                                        <td>{formatPrice(m.unit_price_ht)}</td>
                                        <td>{m.note ?? '—'}</td>
                                        <td>
                                            {m.is_manual && (
                                                <button type="button" className="stock-panel__del" onClick={() => handleDelete(m.id)}>✕</button>
                                            )}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    )}
                </div>
            </div>
        </div>
    );
}
