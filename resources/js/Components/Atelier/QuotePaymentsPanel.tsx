import { useState, useEffect, useCallback, useMemo } from 'react';
import type { QuotePayment, PaymentMethod } from '@/types';

interface Props {
    quoteId: number;
    totalTtc: number;
}

function csrfToken(): string {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function formatCurrency(value: number): string {
    return value.toFixed(2) + ' €';
}

const emptyDraft = () => ({
    amount: '',
    method: 'cb' as PaymentMethod,
    paid_at: new Date().toISOString().slice(0, 16),
    note: '',
});

export default function QuotePaymentsPanel({ quoteId, totalTtc }: Props) {
    const [payments, setPayments] = useState<QuotePayment[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [draft, setDraft] = useState(emptyDraft());
    const [isSaving, setIsSaving] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const load = useCallback(async () => {
        setIsLoading(true);
        const res = await fetch(`/api/quotes/${quoteId}/payments`, { headers: { Accept: 'application/json' } });
        const data = await res.json();
        setPayments(data);
        setIsLoading(false);
    }, [quoteId]);

    useEffect(() => {
        load();
    }, [load]);

    const totalPaid = useMemo(() => payments.reduce((sum, p) => sum + p.amount, 0), [payments]);
    const remaining = totalTtc - totalPaid;

    const badgeType = useMemo(() => {
        if (totalPaid === 0) return null;
        if (totalPaid > totalTtc) return 'error';
        if (totalPaid === totalTtc) return 'success';
        return 'warning';
    }, [totalPaid, totalTtc]);

    const handleAdd = useCallback(async () => {
        const amount = parseFloat(draft.amount);
        if (!amount || amount <= 0) {
            setError('Le montant doit être supérieur à 0.');
            return;
        }

        setIsSaving(true);
        setError(null);
        const res = await fetch(`/api/quotes/${quoteId}/payments`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken() },
            body: JSON.stringify({
                amount,
                method: draft.method,
                paid_at: draft.paid_at,
                note: draft.note || null,
            }),
        });

        if (res.ok) {
            const data = await res.json();
            setPayments(prev => [...prev, data.payment].sort((a, b) => a.paid_at.localeCompare(b.paid_at)));
            setDraft(emptyDraft());
        } else {
            const data = await res.json().catch(() => null);
            setError(data?.message || 'Erreur lors de l\'enregistrement du paiement.');
        }
        setIsSaving(false);
    }, [quoteId, draft]);

    const handleRemove = useCallback(async (paymentId: number) => {
        if (!confirm('Supprimer ce paiement ?')) { return; }
        const res = await fetch(`/api/quote-payments/${paymentId}`, {
            method: 'DELETE',
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken() },
        });
        if (res.ok) {
            setPayments(prev => prev.filter(p => p.id !== paymentId));
        }
    }, []);

    return (
        <div className="quote-payments">
            <div className="quote-payments__header">
                <h3 className="quote-payments__title">Suivi des paiements</h3>
            </div>

            {totalTtc > 0 && (
                <div className="quote-payments__summary">
                    <span className={`quote-payments__badge quote-payments__badge--${badgeType || 'neutral'}`}>
                        Total encaissé : {formatCurrency(totalPaid)} / {formatCurrency(totalTtc)}
                    </span>
                    {remaining > 0 && (
                        <span className="quote-payments__remaining">
                            Reste à encaisser : {formatCurrency(remaining)}
                        </span>
                    )}
                    {remaining < 0 && (
                        <span className="quote-payments__overpaid">
                            Trop perçu : {formatCurrency(Math.abs(remaining))}
                        </span>
                    )}
                </div>
            )}

            <div className="quote-payments__list">
                {isLoading ? (
                    <div className="quote-payments__empty">Chargement...</div>
                ) : payments.length === 0 ? (
                    <div className="quote-payments__empty">Aucun paiement enregistré</div>
                ) : (
                    payments.map(payment => (
                        <div key={payment.id} className="quote-payments__row">
                            <div className="quote-payments__row-main">
                                <span className="quote-payments__row-amount">{formatCurrency(payment.amount)}</span>
                                <span className="quote-payments__row-method">{METHOD_LABELS[payment.method]}</span>
                                <span className="quote-payments__row-date">
                                    {new Date(payment.paid_at).toLocaleDateString('fr-FR')}
                                </span>
                                {payment.note && <span className="quote-payments__row-note">{payment.note}</span>}
                            </div>
                            <button
                                type="button"
                                className="quote-payments__row-remove"
                                onClick={() => handleRemove(payment.id)}
                                title="Supprimer ce paiement"
                            >
                                ×
                            </button>
                        </div>
                    ))
                )}
            </div>

            {error && <div className="quote-payments__error">{error}</div>}

            <div className="quote-payments__form">
                <div className="quote-payments__field">
                    <label>Montant *</label>
                    <div className="quote-payments__input-suffix">
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            value={draft.amount}
                            onChange={e => setDraft(prev => ({ ...prev, amount: e.target.value }))}
                            onWheel={e => e.currentTarget.blur()}
                            placeholder="0.00"
                        />
                        <span>€</span>
                    </div>
                </div>
                <div className="quote-payments__field">
                    <label>Mode *</label>
                    <select
                        value={draft.method}
                        onChange={e => setDraft(prev => ({ ...prev, method: e.target.value as PaymentMethod }))}
                    >
                        <option value="cb">CB</option>
                        <option value="liquide">Espèces</option>
                        <option value="cheque">Chèque</option>
                        <option value="virement">Virement</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>
                <div className="quote-payments__field">
                    <label>Date *</label>
                    <input
                        type="datetime-local"
                        value={draft.paid_at}
                        onChange={e => setDraft(prev => ({ ...prev, paid_at: e.target.value }))}
                    />
                </div>
                <div className="quote-payments__field quote-payments__field--grow">
                    <label>Note</label>
                    <input
                        type="text"
                        value={draft.note}
                        onChange={e => setDraft(prev => ({ ...prev, note: e.target.value }))}
                        placeholder="Commentaire"
                    />
                </div>
                <button type="button" className="quote-payments__add-btn" onClick={handleAdd} disabled={isSaving}>
                    + Ajouter
                </button>
            </div>
        </div>
    );
}

const METHOD_LABELS: Record<PaymentMethod, string> = {
    cb: 'CB',
    liquide: 'Espèces',
    cheque: 'Chèque',
    virement: 'Virement',
    autre: 'Autre',
};
