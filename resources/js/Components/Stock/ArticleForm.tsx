import { useState, useCallback, useEffect } from 'react';
import type { Article, ArticleCategory, Brand, Supplier } from '@/types';
import ArticleAutocomplete from '@/Components/Stock/ArticleAutocomplete';

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

    const [suppliers, setSuppliers] = useState<Supplier[]>([]);
    const [newSupplierName, setNewSupplierName] = useState('');
    const [showNewSupplier, setShowNewSupplier] = useState(false);
    const [supplierError, setSupplierError] = useState<string | null>(null);

    const [lotSearch, setLotSearch] = useState(article?.lot ? `${article.lot.reference} — ${article.lot.designation}` : '');
    const [selectedLot, setSelectedLot] = useState<Article | null>(article?.lot ?? null);

    useEffect(() => {
        fetch('/api/brands', { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(data => setBrands(data.brands ?? []));
        fetch('/api/suppliers', { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(data => setSuppliers(data.suppliers ?? []));
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

    const handleCreateSupplier = useCallback(async () => {
        if (!newSupplierName.trim()) { return; }
        setSupplierError(null);
        const res = await fetch('/api/suppliers', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ name: newSupplierName.trim() }),
        });
        const data = await res.json();
        if (res.ok) {
            setSuppliers(prev => [...prev, data.supplier].sort((a, b) => a.name.localeCompare(b.name)));
            setForm(p => ({ ...p, supplier_id: String(data.supplier.id) }));
            setNewSupplierName('');
            setShowNewSupplier(false);
        } else {
            setSupplierError(data.errors?.name?.[0] ?? data.message ?? 'Erreur');
        }
    }, [newSupplierName, csrfToken]);

    const [form, setForm] = useState({
        article_subcategory_id: article?.article_subcategory_id ? String(article.article_subcategory_id) : '',
        brand_id: article?.brand_id ? String(article.brand_id) : '',
        reference: article?.reference ?? '',
        designation: article?.designation ?? '',
        purchase_price_ht: article ? centsToEuros(article.purchase_price_ht) : '',
        sale_price_ttc: article ? centsToEuros(article.sale_price_ttc) : '',
        tva_rate: article ? String(article.tva_rate) : '20',
        unit: article?.unit ?? 'pièce',
        supplier_id: article?.supplier_id ? String(article.supplier_id) : '',
        lot_article_id: article?.lot_article_id ? String(article.lot_article_id) : '',
        lot_quantity: article?.lot_quantity ? String(article.lot_quantity) : '',
        notes: article?.notes ?? '',
    });
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [isLoading, setIsLoading] = useState(false);

    const allSubcategories = categories.flatMap(c =>
        c.subcategories.map(s => ({ ...s, categoryName: c.name }))
    );

    const isReadOnly = article !== null && !article.is_editable;

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
            sale_price_ttc: eurosToCents(form.sale_price_ttc),
            tva_rate: parseFloat(form.tva_rate),
            unit: form.unit,
            supplier_id: form.supplier_id ? Number(form.supplier_id) : null,
            lot_article_id: form.lot_article_id ? Number(form.lot_article_id) : null,
            lot_quantity: form.lot_quantity ? Number(form.lot_quantity) : null,
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

                {isReadOnly && (
                    <div className="article-form__info-banner">
                        Cet article provient du catalogue fournisseur et ne peut pas être modifié.
                    </div>
                )}

                <form className="article-form__body" onSubmit={handleSubmit}>
                    <fieldset className="article-form__fieldset" disabled={isReadOnly}>
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
                            <label className="article-form__label">Prix vente TTC (€) *</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                className={`article-form__input ${errors.sale_price_ttc ? 'article-form__input--error' : ''}`}
                                value={form.sale_price_ttc}
                                onChange={e => set('sale_price_ttc', e.target.value)}
                                placeholder="7.08"
                            />
                            {errors.sale_price_ttc && <span className="article-form__field-error">{errors.sale_price_ttc}</span>}
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
                        <div className="article-form__brand-row">
                            <select
                                className="article-form__select"
                                value={form.supplier_id}
                                onChange={e => {
                                    if (e.target.value === '__new__') {
                                        setShowNewSupplier(true);
                                    } else {
                                        set('supplier_id', e.target.value);
                                        setShowNewSupplier(false);
                                    }
                                }}
                            >
                                <option value="">— Sans fournisseur —</option>
                                {suppliers.map(s => (
                                    <option key={s.id} value={s.id}>{s.name}</option>
                                ))}
                                <option value="__new__">+ Ajouter un fournisseur...</option>
                            </select>
                        </div>
                        {showNewSupplier && (
                            <div className="article-form__new-brand">
                                <input
                                    className="article-form__input"
                                    placeholder="Nom du nouveau fournisseur"
                                    value={newSupplierName}
                                    onChange={e => setNewSupplierName(e.target.value)}
                                    onKeyDown={e => { if (e.key === 'Enter') { e.preventDefault(); handleCreateSupplier(); } }}
                                    autoFocus
                                />
                                <button type="button" className="article-form__btn article-form__btn--primary" onClick={handleCreateSupplier}>
                                    Créer
                                </button>
                                <button type="button" className="article-form__btn" onClick={() => { setShowNewSupplier(false); setNewSupplierName(''); setSupplierError(null); }}>
                                    Annuler
                                </button>
                                {supplierError && <span className="article-form__field-error">{supplierError}</span>}
                            </div>
                        )}
                    </div>

                    <div className="article-form__row">
                        <div className="article-form__field article-form__field--grow">
                            <label className="article-form__label">Lot d'origine</label>
                            <ArticleAutocomplete
                                value={lotSearch}
                                articleId={form.lot_article_id ? Number(form.lot_article_id) : null}
                                onChange={setLotSearch}
                                onSelect={a => {
                                    set('lot_article_id', String(a.id));
                                    setLotSearch(`${a.reference} — ${a.designation}`);
                                    setSelectedLot(a);
                                }}
                                onDetach={() => { set('lot_article_id', ''); setLotSearch(''); setSelectedLot(null); }}
                                placeholder="Rechercher le lot en stock (rouleau, boîte...)"
                                hasStock
                            />
                            <span className="article-form__hint">
                                À renseigner si cet article est vendu à l'unité depuis un lot déjà en stock (ex: chaîne au mètre depuis un touret).
                                {selectedLot?.lot_quantity ? ` Prix unitaire suggéré : ${centsToEuros(Math.round(selectedLot.sale_price_ttc / selectedLot.lot_quantity))} €.` : ''}
                            </span>
                        </div>
                        <div className="article-form__field">
                            <label className="article-form__label">Unités par lot</label>
                            <input
                                type="number"
                                min="1"
                                step="1"
                                className="article-form__input"
                                value={form.lot_quantity}
                                onChange={e => set('lot_quantity', e.target.value)}
                                placeholder="ex: 25"
                            />
                            <span className="article-form__hint">
                                Si ce lot se décompte facilement (ex: boîte de 25 plaquettes). Laisser vide sinon (ex: touret de chaîne, bidon de liquide).
                            </span>
                        </div>
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

                    </fieldset>

                    <div className="article-form__footer">
                        <button type="button" className="article-form__btn" onClick={onClose}>
                            {isReadOnly ? 'Fermer' : 'Annuler'}
                        </button>
                        {!isReadOnly && (
                            <button type="submit" className="article-form__btn article-form__btn--primary" disabled={isLoading}>
                                {isLoading ? 'Enregistrement...' : (article ? 'Enregistrer' : 'Créer')}
                            </button>
                        )}
                    </div>
                </form>
            </div>
        </div>
    );
}
