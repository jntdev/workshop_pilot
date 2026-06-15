import { useState, useCallback, useEffect } from 'react';
import type { Article, ArticleCategory, Brand } from '@/types';

interface Props {
    article: Article | null;
    categories: ArticleCategory[];
    csrfToken: string;
    onSaved: () => void;
    onClose: () => void;
}

function centsToEuros(cents: number): string {
    return (cents / 100).toFixed(2);
}

function eurosToCents(euros: string): number {
    return Math.round(parseFloat(euros.replace(',', '.')) * 100) || 0;
}

export default function ArticleForm({ article, categories, csrfToken, onSaved, onClose }: Props) {
    const [brands, setBrands] = useState<Brand[]>([]);
    const [newBrandName, setNewBrandName] = useState('');
    const [showNewBrand, setShowNewBrand] = useState(false);
    const [brandError, setBrandError] = useState<string | null>(null);

    useEffect(() => {
        fetch('/api/brands', { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(data => setBrands(data.brands ?? []));
    }, []);

    const handleCreateBrand = useCallback(async () => {
        if (!newBrandName.trim()) { return; }
        setBrandError(null);
        const res = await fetch('/api/brands', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ name: newBrandName.trim() }),
        });
        const data = await res.json();
        if (res.ok) {
            setBrands(prev => [...prev, data.brand].sort((a, b) => a.name.localeCompare(b.name)));
            setForm(p => ({ ...p, brand_id: String(data.brand.id) }));
            setNewBrandName('');
            setShowNewBrand(false);
        } else {
            setBrandError(data.errors?.name?.[0] ?? data.message ?? 'Erreur');
        }
    }, [newBrandName, csrfToken]);

    const [form, setForm] = useState({
        article_subcategory_id: article?.article_subcategory_id ? String(article.article_subcategory_id) : '',
        brand_id: article?.brand_id ? String(article.brand_id) : '',
        reference: article?.reference ?? '',
        designation: article?.designation ?? '',
        purchase_price_ht: article ? centsToEuros(article.purchase_price_ht) : '',
        sale_price_ht: article ? centsToEuros(article.sale_price_ht) : '',
        tva_rate: article ? String(article.tva_rate) : '20',
        unit: article?.unit ?? 'pièce',
        supplier: article?.supplier ?? '',
        notes: article?.notes ?? '',
    });
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [isLoading, setIsLoading] = useState(false);

    const allSubcategories = categories.flatMap(c =>
        c.subcategories.map(s => ({ ...s, categoryName: c.name }))
    );

    const set = (key: string, value: string) => {
        setForm(prev => ({ ...prev, [key]: value }));
        setErrors(prev => { const n = { ...prev }; delete n[key]; return n; });
    };

    const handleSubmit = useCallback(async (e: React.FormEvent) => {
        e.preventDefault();
        setIsLoading(true);
        setErrors({});

        const payload = {
            article_subcategory_id: form.article_subcategory_id ? Number(form.article_subcategory_id) : null,
            brand_id: form.brand_id ? Number(form.brand_id) : null,
            reference: form.reference,
            designation: form.designation,
            purchase_price_ht: eurosToCents(form.purchase_price_ht),
            sale_price_ht: eurosToCents(form.sale_price_ht),
            tva_rate: parseFloat(form.tva_rate),
            unit: form.unit,
            supplier: form.supplier || null,
            notes: form.notes || null,
        };

        const url = article ? `/api/articles/${article.id}` : '/api/articles';
        const method = article ? 'PUT' : 'POST';

        try {
            const res = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(payload),
            });

            if (res.ok) {
                onSaved();
            } else {
                const data = await res.json();
                if (data.errors) {
                    const flat: Record<string, string> = {};
                    Object.entries(data.errors).forEach(([k, v]) => { flat[k] = (v as string[])[0]; });
                    setErrors(flat);
                } else {
                    setErrors({ _global: data.message || 'Erreur' });
                }
            }
        } finally {
            setIsLoading(false);
        }
    }, [form, article, csrfToken, onSaved]);

    return (
        <div className="article-form__backdrop" onClick={e => { if (e.target === e.currentTarget) { onClose(); } }}>
            <div className="article-form">
                <div className="article-form__header">
                    <h2 className="article-form__title">{article ? 'Modifier un article' : 'Nouvel article'}</h2>
                    <button type="button" className="article-form__close" onClick={onClose}>✕</button>
                </div>

                {errors._global && <div className="article-form__error">{errors._global}</div>}

                <form className="article-form__body" onSubmit={handleSubmit}>
                    <div className="article-form__field">
                        <label className="article-form__label">Sous-catégorie</label>
                        <select
                            className="article-form__select"
                            value={form.article_subcategory_id}
                            onChange={e => set('article_subcategory_id', e.target.value)}
                        >
                            <option value="">— Sans catégorie —</option>
                            {allSubcategories.map(s => (
                                <option key={s.id} value={s.id}>{s.categoryName} › {s.name}</option>
                            ))}
                        </select>
                    </div>

                    <div className="article-form__field">
                        <label className="article-form__label">Marque</label>
                        <div className="article-form__brand-row">
                            <select
                                className="article-form__select"
                                value={form.brand_id}
                                onChange={e => {
                                    if (e.target.value === '__new__') {
                                        setShowNewBrand(true);
                                    } else {
                                        set('brand_id', e.target.value);
                                        setShowNewBrand(false);
                                    }
                                }}
                            >
                                <option value="">— Sans marque —</option>
                                {brands.map(b => (
                                    <option key={b.id} value={b.id}>{b.name}</option>
                                ))}
                                <option value="__new__">+ Ajouter une marque...</option>
                            </select>
                        </div>
                        {showNewBrand && (
                            <div className="article-form__new-brand">
                                <input
                                    className="article-form__input"
                                    placeholder="Nom de la nouvelle marque"
                                    value={newBrandName}
                                    onChange={e => setNewBrandName(e.target.value)}
                                    onKeyDown={e => { if (e.key === 'Enter') { e.preventDefault(); handleCreateBrand(); } }}
                                    autoFocus
                                />
                                <button type="button" className="article-form__btn article-form__btn--primary" onClick={handleCreateBrand}>
                                    Créer
                                </button>
                                <button type="button" className="article-form__btn" onClick={() => { setShowNewBrand(false); setNewBrandName(''); setBrandError(null); }}>
                                    Annuler
                                </button>
                                {brandError && <span className="article-form__field-error">{brandError}</span>}
                            </div>
                        )}
                    </div>

                    <div className="article-form__row">
                        <div className="article-form__field">
                            <label className="article-form__label">Référence *</label>
                            <input
                                className={`article-form__input ${errors.reference ? 'article-form__input--error' : ''}`}
                                value={form.reference}
                                onChange={e => set('reference', e.target.value)}
                                placeholder="CH-700-PR"
                            />
                            {errors.reference && <span className="article-form__field-error">{errors.reference}</span>}
                        </div>
                        <div className="article-form__field article-form__field--grow">
                            <label className="article-form__label">Désignation *</label>
                            <input
                                className={`article-form__input ${errors.designation ? 'article-form__input--error' : ''}`}
                                value={form.designation}
                                onChange={e => set('designation', e.target.value)}
                                placeholder="Chambre à air 700x23-25 Presta"
                            />
                            {errors.designation && <span className="article-form__field-error">{errors.designation}</span>}
                        </div>
                    </div>

                    <div className="article-form__row">
                        <div className="article-form__field">
                            <label className="article-form__label">Prix achat HT (€) *</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                className={`article-form__input ${errors.purchase_price_ht ? 'article-form__input--error' : ''}`}
                                value={form.purchase_price_ht}
                                onChange={e => set('purchase_price_ht', e.target.value)}
                                placeholder="2.80"
                            />
                            {errors.purchase_price_ht && <span className="article-form__field-error">{errors.purchase_price_ht}</span>}
                        </div>
                        <div className="article-form__field">
                            <label className="article-form__label">Prix vente HT (€) *</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                className={`article-form__input ${errors.sale_price_ht ? 'article-form__input--error' : ''}`}
                                value={form.sale_price_ht}
                                onChange={e => set('sale_price_ht', e.target.value)}
                                placeholder="5.90"
                            />
                            {errors.sale_price_ht && <span className="article-form__field-error">{errors.sale_price_ht}</span>}
                        </div>
                        <div className="article-form__field">
                            <label className="article-form__label">TVA (%)</label>
                            <select className="article-form__select" value={form.tva_rate} onChange={e => set('tva_rate', e.target.value)}>
                                <option value="0">0 %</option>
                                <option value="5.5">5.5 %</option>
                                <option value="10">10 %</option>
                                <option value="20">20 %</option>
                            </select>
                        </div>
                        <div className="article-form__field">
                            <label className="article-form__label">Unité</label>
                            <select className="article-form__select" value={form.unit} onChange={e => set('unit', e.target.value)}>
                                {['pièce', 'paire', 'kit', 'litre', 'mètre', 'lot'].map(u => (
                                    <option key={u} value={u}>{u}</option>
                                ))}
                            </select>
                        </div>
                    </div>

                    <div className="article-form__field">
                        <label className="article-form__label">Fournisseur</label>
                        <input
                            className="article-form__input"
                            value={form.supplier}
                            onChange={e => set('supplier', e.target.value)}
                            placeholder="Nom du fournisseur (optionnel)"
                        />
                    </div>

                    <div className="article-form__field">
                        <label className="article-form__label">Notes</label>
                        <textarea
                            className="article-form__textarea"
                            value={form.notes}
                            onChange={e => set('notes', e.target.value)}
                            rows={2}
                            placeholder="Informations complémentaires..."
                        />
                    </div>

                    <div className="article-form__footer">
                        <button type="button" className="article-form__btn" onClick={onClose}>Annuler</button>
                        <button type="submit" className="article-form__btn article-form__btn--primary" disabled={isLoading}>
                            {isLoading ? 'Enregistrement...' : (article ? 'Enregistrer' : 'Créer')}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}
