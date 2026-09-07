import { Head } from '@inertiajs/react';
import { useCallback, useState } from 'react';
import type { Article } from '@/types';
import { quantityStep } from '@/utils/articleQuantity';
import BarcodeScanner from '@/Components/Inventory/BarcodeScanner';
import QuickCreateArticleForm from '@/Components/Inventory/QuickCreateArticleForm';
import ArticleCardImage from '@/Components/Stock/ArticleCardImage';

function formatPrice(cents: number): string {
    return (cents / 100).toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + ' €';
}

export default function InventoryScan() {
    const [scannedArticle, setScannedArticle] = useState<Article | null>(null);
    const [ambiguousArticles, setAmbiguousArticles] = useState<Article[] | null>(null);
    const [notFoundCode, setNotFoundCode] = useState<string | null>(null);
    const [quantity, setQuantity] = useState('');
    const [scannedCount, setScannedCount] = useState(0);
    const [isSaving, setIsSaving] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const isPaused = scannedArticle !== null || ambiguousArticles !== null || notFoundCode !== null;

    const handleDetected = useCallback(async (code: string) => {
        const res = await fetch(`/api/inventory/lookup-barcode?code=${encodeURIComponent(code)}`, {
            headers: { Accept: 'application/json' },
        });
        const data = await res.json();

        if (data.result === 'found') {
            setScannedArticle(data.article);
        } else if (data.result === 'ambiguous') {
            setAmbiguousArticles(data.articles);
        } else {
            setNotFoundCode(code);
        }
    }, []);

    const chooseAmbiguousArticle = (article: Article) => {
        setScannedArticle(article);
        setAmbiguousArticles(null);
    };

    const resetToScan = () => {
        setScannedArticle(null);
        setAmbiguousArticles(null);
        setNotFoundCode(null);
        setQuantity('');
        setError(null);
    };

    const validateQuantity = async () => {
        if (!scannedArticle) { return; }

        setIsSaving(true);
        setError(null);

        const res = await fetch(`/api/articles/${scannedArticle.id}/stock-movements`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ type: 'manual_in', quantity: parseFloat(quantity) || 0 }),
        });

        setIsSaving(false);

        if (res.ok) {
            setScannedCount(c => c + 1);
            resetToScan();
        } else {
            const data = await res.json();
            setError(data.message || 'Erreur lors de l\'enregistrement.');
        }
    };

    return (
        <div className="inventory-scan">
            <Head title="Inventaire — Scan" />

            <div className="inventory-scan__header">
                <a href="/stock" className="inventory-scan__exit">← Stock</a>
                <span className="inventory-scan__count">{scannedCount} produit{scannedCount !== 1 ? 's' : ''} scanné{scannedCount !== 1 ? 's' : ''}</span>
            </div>

            <div className="inventory-scan__camera">
                <BarcodeScanner onDetected={handleDetected} isPaused={isPaused} />
            </div>

            {scannedArticle && (
                <div className="inventory-scan__panel">
                    <div className="inventory-scan__article-image">
                        <ArticleCardImage article={scannedArticle} />
                    </div>
                    <div className="inventory-scan__article-info">
                        <span className="inventory-scan__article-ref">{scannedArticle.reference}</span>
                        <span className="inventory-scan__article-designation">{scannedArticle.designation}</span>
                        <span className="inventory-scan__article-price">{formatPrice(scannedArticle.sale_price_ttc)}</span>
                    </div>
                    <div className="inventory-scan__quantity-row">
                        <input
                            type="number"
                            step={quantityStep(scannedArticle.unit)}
                            className="inventory-scan__quantity-input"
                            placeholder="Quantité comptée"
                            value={quantity}
                            autoFocus
                            disabled={isSaving}
                            onChange={e => setQuantity(e.target.value)}
                            onKeyDown={e => { if (e.key === 'Enter') { validateQuantity(); } }}
                        />
                        <button
                            type="button"
                            className="inventory-scan__validate-btn"
                            onClick={validateQuantity}
                            disabled={isSaving || !quantity}
                        >
                            Valider
                        </button>
                    </div>
                    {error && <div className="inventory-scan__error">{error}</div>}
                    <button type="button" className="inventory-scan__cancel-btn" onClick={resetToScan}>Annuler</button>
                </div>
            )}

            {ambiguousArticles && (
                <div className="inventory-scan__panel">
                    <p className="inventory-scan__ambiguous-title">Plusieurs articles correspondent, sélectionnez le bon :</p>
                    <div className="inventory-scan__ambiguous-list">
                        {ambiguousArticles.map(article => (
                            <button
                                key={article.id}
                                type="button"
                                className="inventory-scan__ambiguous-item"
                                onClick={() => chooseAmbiguousArticle(article)}
                            >
                                <div className="inventory-scan__ambiguous-item-image">
                                    <ArticleCardImage article={article} />
                                </div>
                                <div>
                                    <div>{article.reference}</div>
                                    <div>{article.designation}</div>
                                </div>
                            </button>
                        ))}
                    </div>
                    <button type="button" className="inventory-scan__cancel-btn" onClick={resetToScan}>Annuler</button>
                </div>
            )}

            {notFoundCode && (
                <div className="inventory-scan__panel inventory-scan__panel--full">
                    <QuickCreateArticleForm
                        barcode={notFoundCode}
                        csrfToken={csrfToken}
                        onCreated={() => { setScannedCount(c => c + 1); resetToScan(); }}
                        onCancel={resetToScan}
                    />
                </div>
            )}
        </div>
    );
}
