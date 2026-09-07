import { useState, useRef, useEffect, useMemo, useCallback } from 'react';
import { apiGet, apiPost, apiPut, apiDelete, ApiError } from '@/utils/api';
import RecentSales from '@/Components/Vente/RecentSales';
import type { Sale, SaleLine, PaymentMethod } from '@/types';

interface LookupArticle {
    id: number;
    reference: string;
    designation: string;
    barcode: string | null;
    purchase_price_ht: number;
    sale_price_ttc: number;
    tva_rate: number;
    stock_quantity: number;
}

interface LookupResponse {
    result: 'found' | 'ambiguous' | 'choices' | 'not_found';
    article?: LookupArticle;
    articles?: LookupArticle[];
}

const PAYMENT_METHOD_OPTIONS: { value: PaymentMethod; label: string }[] = [
    { value: 'cb', label: 'Carte bancaire' },
    { value: 'liquide', label: 'Liquide' },
    { value: 'cheque', label: 'Chèque' },
    { value: 'virement', label: 'Virement' },
    { value: 'autre', label: 'Autre' },
];

export default function CaisseForm() {
    const [saleId, setSaleId] = useState<number | null>(null);
    const [lines, setLines] = useState<SaleLine[]>([]);
    const [barcodeInput, setBarcodeInput] = useState('');
    const [choices, setChoices] = useState<LookupArticle[]>([]);
    const [error, setError] = useState<string | null>(null);
    const [isLoading, setIsLoading] = useState(false);
    const [paymentMethod, setPaymentMethod] = useState<PaymentMethod | ''>('');
    const [isFinalizing, setIsFinalizing] = useState(false);
    const [finalizeError, setFinalizeError] = useState<string | null>(null);
    const [lastSale, setLastSale] = useState<Sale | null>(null);
    const [recentSalesRefreshTrigger, setRecentSalesRefreshTrigger] = useState(0);

    const barcodeInputRef = useRef<HTMLInputElement>(null);

    useEffect(() => {
        barcodeInputRef.current?.focus();
    }, []);

    // Le champ est `disabled` pendant isLoading ; refocus seulement une fois réactivé,
    // sinon .focus() sur un input disabled est un no-op silencieux
    useEffect(() => {
        if (!isLoading) {
            barcodeInputRef.current?.focus();
        }
    }, [isLoading]);

    const totalTtc = useMemo(
        () => lines.reduce((sum, line) => sum + line.line_total_ttc, 0),
        [lines]
    );

    const refocusBarcodeInput = useCallback(() => {
        barcodeInputRef.current?.focus();
    }, []);

    const ensureSale = useCallback(async (): Promise<number> => {
        if (saleId !== null) {
            return saleId;
        }

        const sale = await apiPost<Sale>('/api/sales');
        setSaleId(sale.id);

        return sale.id;
    }, [saleId]);

    const addArticleToCart = useCallback(async (article: LookupArticle) => {
        const currentSaleId = await ensureSale();

        const sale = await apiPost<Sale>(`/api/sales/${currentSaleId}/lines`, {
            article_id: article.id,
        });
        setLines(sale.lines);
    }, [ensureSale]);

    const handleLookup = useCallback(async (term: string) => {
        setError(null);
        setChoices([]);

        try {
            const response = await apiGet<LookupResponse>(`/api/sales/lookup-article?q=${encodeURIComponent(term)}`);

            if (response.result === 'found' && response.article) {
                await addArticleToCart(response.article);

                return;
            }

            if (response.result === 'ambiguous' || response.result === 'choices') {
                setChoices(response.articles ?? []);

                return;
            }

            setError(`Article introuvable pour le code : ${term}`);
        } catch (err) {
            setError(err instanceof ApiError ? err.message : 'Erreur lors de la recherche.');
        }
    }, [addArticleToCart]);

    const handleBarcodeKeyDown = useCallback((e: React.KeyboardEvent<HTMLInputElement>) => {
        if (e.key !== 'Enter') {
            return;
        }

        const term = barcodeInput.trim();
        setBarcodeInput('');

        if (term === '') {
            return;
        }

        setIsLoading(true);
        handleLookup(term).finally(() => setIsLoading(false));
    }, [barcodeInput, handleLookup]);

    const handleChoiceSelected = useCallback(async (article: LookupArticle) => {
        setChoices([]);
        setIsLoading(true);

        try {
            await addArticleToCart(article);
        } catch (err) {
            setError(err instanceof ApiError ? err.message : "Erreur lors de l'ajout de l'article.");
        } finally {
            setIsLoading(false);
        }
    }, [addArticleToCart]);

    const handleQuantityChange = useCallback(async (line: SaleLine, quantity: number) => {
        if (saleId === null || quantity <= 0) {
            return;
        }

        const sale = await apiPut<Sale>(`/api/sales/${saleId}/lines/${line.id}`, {
            quantity,
            unit_price_ttc: line.unit_price_ttc,
        });
        setLines(sale.lines);
    }, [saleId]);

    const handlePriceChange = useCallback(async (line: SaleLine, unitPriceTtc: number) => {
        if (saleId === null || unitPriceTtc < 0) {
            return;
        }

        const sale = await apiPut<Sale>(`/api/sales/${saleId}/lines/${line.id}`, {
            quantity: line.quantity,
            unit_price_ttc: unitPriceTtc,
        });
        setLines(sale.lines);
    }, [saleId]);

    const handleRemoveLine = useCallback(async (line: SaleLine) => {
        if (saleId === null) {
            return;
        }

        await apiDelete(`/api/sales/${saleId}/lines/${line.id}`);
        const sale = await apiGet<Sale>(`/api/sales/${saleId}`);
        setLines(sale.lines);
    }, [saleId]);

    const resetCart = useCallback(() => {
        setSaleId(null);
        setLines([]);
        setPaymentMethod('');
        setChoices([]);
        setError(null);
        refocusBarcodeInput();
    }, [refocusBarcodeInput]);

    const handleFinalize = useCallback(async () => {
        if (saleId === null || paymentMethod === '') {
            return;
        }

        setIsFinalizing(true);
        setFinalizeError(null);

        try {
            const sale = await apiPost<Sale>(`/api/sales/${saleId}/complete`, {
                payment_method: paymentMethod,
            });
            setLastSale(sale);
            setRecentSalesRefreshTrigger((n) => n + 1);
            resetCart();
        } catch (err) {
            setFinalizeError(err instanceof ApiError ? err.message : 'Erreur lors de la finalisation.');
        } finally {
            setIsFinalizing(false);
        }
    }, [saleId, paymentMethod, resetCart]);

    return (
        <div className="caisse-form">
            <div className="caisse-form__scan">
                <input
                    ref={barcodeInputRef}
                    type="text"
                    autoFocus
                    value={barcodeInput}
                    onChange={(e) => setBarcodeInput(e.target.value)}
                    onKeyDown={handleBarcodeKeyDown}
                    placeholder="Scanner ou taper un code-barres / référence"
                    className="caisse-form__scan-input"
                    disabled={isLoading}
                />
                {isLoading && <span className="caisse-form__scan-loading">Recherche...</span>}
            </div>

            {error && <div className="caisse-form__error">{error}</div>}

            {choices.length > 0 && (
                <div className="caisse-form__choices">
                    <p className="caisse-form__choices-label">Plusieurs articles correspondent, sélectionnez-en un :</p>
                    <ul className="caisse-form__choices-list">
                        {choices.map((article) => (
                            <li key={article.id} className="caisse-form__choice">
                                <button type="button" onClick={() => handleChoiceSelected(article)}>
                                    {article.reference} — {article.designation} ({(article.sale_price_ttc / 100).toFixed(2)} €)
                                </button>
                            </li>
                        ))}
                    </ul>
                </div>
            )}

            {lastSale && (
                <div className="caisse-form__success">
                    Vente {lastSale.reference} finalisée avec succès.
                </div>
            )}

            <table className="caisse-form__cart">
                <thead>
                    <tr>
                        <th>Désignation</th>
                        <th>Référence</th>
                        <th>Prix unitaire TTC</th>
                        <th>Quantité</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    {lines.map((line) => (
                        <tr key={line.id} className="caisse-form__cart-line">
                            <td>{line.designation}</td>
                            <td>{line.reference}</td>
                            <td>
                                <input
                                    type="number"
                                    step="0.01"
                                    value={(line.unit_price_ttc / 100).toFixed(2)}
                                    onChange={(e) => handlePriceChange(line, Math.round(parseFloat(e.target.value || '0') * 100))}
                                    className="caisse-form__cart-price-input"
                                />
                            </td>
                            <td>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0.01"
                                    value={line.quantity}
                                    onChange={(e) => handleQuantityChange(line, parseFloat(e.target.value || '0'))}
                                    className="caisse-form__cart-quantity-input"
                                />
                            </td>
                            <td>{(line.line_total_ttc / 100).toFixed(2)} €</td>
                            <td>
                                <button type="button" onClick={() => handleRemoveLine(line)} className="caisse-form__cart-remove">
                                    Supprimer
                                </button>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>

            <div className="caisse-form__total">Total TTC : {(totalTtc / 100).toFixed(2)} €</div>

            <div className="caisse-form__payment">
                <select
                    value={paymentMethod}
                    onChange={(e) => setPaymentMethod(e.target.value as PaymentMethod)}
                    className="caisse-form__payment-select"
                >
                    <option value="">Mode de paiement...</option>
                    {PAYMENT_METHOD_OPTIONS.map((option) => (
                        <option key={option.value} value={option.value}>
                            {option.label}
                        </option>
                    ))}
                </select>

                <button
                    type="button"
                    onClick={handleFinalize}
                    disabled={lines.length === 0 || paymentMethod === '' || isFinalizing}
                    className="caisse-form__finalize"
                >
                    {isFinalizing ? 'Finalisation...' : 'Finaliser la vente'}
                </button>
            </div>

            {finalizeError && <div className="caisse-form__error">{finalizeError}</div>}

            <RecentSales refreshTrigger={recentSalesRefreshTrigger} />
        </div>
    );
}
