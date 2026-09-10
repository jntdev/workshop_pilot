import { useState, useEffect, useCallback } from 'react';
import type { Article, ArticleFilterOptions } from '@/types';
import { ATTRIBUTE_LABELS, orderedAttributeEntries, formatOptionLabel } from '@/utils/articleFilters';
import { quantityStep } from '@/utils/articleQuantity';
import ArticleCardImage from '@/Components/Stock/ArticleCardImage';

interface Props {
    subcategoryId: number | null;
    subcategoryLabel: string | null;
    brandId: number | null;
    attributes?: Record<string, string>;
    onAttributesChange?: (key: string, value: string) => void;
    filterOptions?: ArticleFilterOptions | null;
    hasMovements: boolean | null;
    onEdit: (article: Article) => void;
    onOpenStock: (article: Article) => void;
    csrfToken: string;
}

function formatPrice(centimes: number): string {
    return (centimes / 100).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
}

interface InlineStockCellProps {
    article: Article;
    csrfToken: string;
    stockClass: (qty: number) => string;
    onChanged: () => void;
}

function InlineStockCell({ article, csrfToken, stockClass, onChanged }: InlineStockCellProps) {
    const [isEditing, setIsEditing] = useState(false);
    const [value, setValue] = useState(String(article.stock_quantity));
    const [isSaving, setIsSaving] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const startEditing = () => {
        setValue(String(article.stock_quantity));
        setError(null);
        setIsEditing(true);
    };

    const cancel = () => {
        setIsEditing(false);
        setError(null);
    };

    const save = async () => {
        const target = parseFloat(value);

        if (Number.isNaN(target)) {
            setError('Valeur invalide');
            return;
        }

        const isWholeUnit = quantityStep(article.unit) === '1';

        if (isWholeUnit && !Number.isInteger(target)) {
            setError('Quantité entière requise pour cette unité');
            return;
        }

        const delta = Math.round((target - article.stock_quantity) * 100) / 100;

        if (delta === 0) {
            setIsEditing(false);
            return;
        }

        if (delta < 0) {
            setError('Les sorties de stock ne sont pas encore gérées ici');
            return;
        }

        setIsSaving(true);
        setError(null);

        const res = await fetch(`/api/articles/${article.id}/stock-movements`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ type: 'manual_in', quantity: delta }),
        });

        setIsSaving(false);

        if (res.ok) {
            setIsEditing(false);
            onChanged();
        } else {
            const data = await res.json();
            setError(data.message || 'Erreur');
        }
    };

    if (!isEditing) {
        return (
            <button
                type="button"
                className={`article-list__stock article-list__stock-edit ${stockClass(article.stock_quantity)}`}
                onClick={startEditing}
                title="Modifier le stock"
            >
                {article.stock_quantity}
            </button>
        );
    }

    return (
        <div className="article-list__stock-inline">
            <input
                type="number"
                step={quantityStep(article.unit)}
                className="article-list__stock-input"
                value={value}
                autoFocus
                disabled={isSaving}
                onChange={e => setValue(e.target.value)}
                onKeyDown={e => {
                    if (e.key === 'Enter') { save(); }
                    if (e.key === 'Escape') { cancel(); }
                }}
            />
            <button type="button" className="article-list__stock-confirm" onClick={save} disabled={isSaving}>✓</button>
            <button type="button" className="article-list__stock-cancel" onClick={cancel} disabled={isSaving}>✕</button>
            {error && <span className="article-list__stock-error">{error}</span>}
        </div>
    );
}

export default function ArticleList({ subcategoryId, subcategoryLabel, brandId, attributes, onAttributesChange, filterOptions, hasMovements, onEdit, onOpenStock, csrfToken }: Props) {
    const [articles, setArticles] = useState<Article[]>([]);
    const [search, setSearch] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const [currentPage, setCurrentPage] = useState(1);
    const [lastPage, setLastPage] = useState(1);

    const load = useCallback(async (page = 1) => {
        setIsLoading(true);
        const params = new URLSearchParams({ page: String(page) });
        if (subcategoryId) { params.set('subcategory_id', String(subcategoryId)); }
        if (brandId) { params.set('brand_id', String(brandId)); }
        if (search) { params.set('search', search); }
        // Sur l'onglet Stock atelier, sélectionner une de mes catégories montre tous ses
        // articles (avec ou sans stock) : le filtre has_movements n'a alors plus lieu d'être.
        const suppressMovementsFilter = hasMovements === true && subcategoryId !== null;
        if (hasMovements !== null && !suppressMovementsFilter) { params.set('has_movements', hasMovements ? '1' : '0'); }
        Object.entries(attributes ?? {}).forEach(([key, value]) => {
            if (value) { params.set(`attribute[${key}]`, value); }
        });

        const res = await fetch(`/api/articles?${params}`, { headers: { Accept: 'application/json' } });
        const data = await res.json();
        setArticles(data.data ?? []);
        setCurrentPage(data.current_page ?? 1);
        setLastPage(data.last_page ?? 1);
        setIsLoading(false);
    }, [subcategoryId, brandId, search, hasMovements, attributes]);

    useEffect(() => {
        load(1);
    }, [load]);

    const handleDelete = useCallback(async (id: number) => {
        if (!confirm('Supprimer cet article ?')) { return; }
        const res = await fetch(`/api/articles/${id}`, {
            method: 'DELETE',
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
        });
        if (res.ok) { load(1); }
    }, [csrfToken, load]);

    const stockClass = (qty: number) => {
        if (qty > 0) { return 'article-list__stock--positive'; }
        if (qty < 0) { return 'article-list__stock--negative'; }
        return 'article-list__stock--zero';
    };

    const marginClass = (margin: number) => {
        if (margin > 0) { return 'article-list__margin--positive'; }
        if (margin < 0) { return 'article-list__margin--negative'; }
        return 'article-list__margin--zero';
    };

    return (
        <div className="article-list">
            <div className="article-list__toolbar">
                <span className="article-list__context">
                    {subcategoryLabel ?? 'Tous les articles'}
                </span>
                <input
                    type="search"
                    className="article-list__search"
                    placeholder="Recherche référence ou désignation..."
                    value={search}
                    onChange={e => { setSearch(e.target.value); setCurrentPage(1); }}
                />
            </div>

            {filterOptions && Object.keys(filterOptions.attributes).length > 0 && onAttributesChange && (
                <div className="article-list__filters">
                    {orderedAttributeEntries(filterOptions.attributes).map(([key, values]) => (
                        <select
                            key={key}
                            className="article-list__filter-select"
                            value={attributes?.[key] ?? ''}
                            onChange={e => onAttributesChange(key, e.target.value)}
                        >
                            <option value="">{ATTRIBUTE_LABELS[key] ?? key}</option>
                            {values.map(value => (
                                <option key={value} value={value}>{formatOptionLabel(key, value)}</option>
                            ))}
                        </select>
                    ))}
                </div>
            )}

            {isLoading ? (
                <div className="article-list__loading">Chargement...</div>
            ) : articles.length === 0 ? (
                <div className="article-list__empty">
                    Aucun article{subcategoryLabel ? ` dans ${subcategoryLabel}` : ''}
                    {hasMovements === true ? ' avec des mouvements de stock' : ''}
                </div>
            ) : (
                <div className="article-list__grid">
                    {articles.map(article => {
                        const margin = Math.round(article.sale_price_ttc / (1 + article.tva_rate / 100)) - article.purchase_price_ht;

                        return (
                            <div key={article.id} className="article-list__card article-card">
                                <div className="article-list__card-image-wrap">
                                    <ArticleCardImage article={article} />
                                </div>
                                <div className="article-list__card-body">
                                    <span className="article-list__card-reference">{article.reference}</span>
                                    <span className="article-list__card-designation">{article.designation}</span>
                                    <div className="article-list__card-meta">
                                        <span>{article.brand?.name ?? '—'}</span>
                                        <span>{article.supplier?.name ?? '—'}</span>
                                    </div>
                                    <div className="article-list__card-prices">
                                        <span className="article-list__card-price">{formatPrice(article.sale_price_ttc)}</span>
                                        <span className="article-list__card-price-ht">PA HT: {formatPrice(article.purchase_price_ht)}</span>
                                    </div>
                                    <div className="article-list__card-secondary">
                                        <span className={marginClass(margin)}>Marge: {formatPrice(margin)}</span>
                                        <span>TVA: {article.tva_rate} %</span>
                                        <span>Unité: {article.unit}</span>
                                    </div>
                                    <div className="article-list__card-stock-row">
                                        <span>Stock:</span>
                                        <InlineStockCell
                                            article={article}
                                            csrfToken={csrfToken}
                                            stockClass={stockClass}
                                            onChanged={() => load(currentPage)}
                                        />
                                    </div>
                                    {article.is_discontinued && (
                                        <span className="article-list__card-badge--discontinued">Non disponible fournisseur</span>
                                    )}
                                    <div className="article-list__card-actions">
                                        <button type="button" className="article-list__action" onClick={() => onOpenStock(article)} title="Mouvements de stock">
                                            📦
                                        </button>
                                        {article.is_editable && (
                                            <button type="button" className="article-list__action" onClick={() => onEdit(article)}>
                                                Modifier
                                            </button>
                                        )}
                                        {article.is_editable && (
                                            <button type="button" className="article-list__action article-list__action--danger" onClick={() => handleDelete(article.id)}>
                                                Supprimer
                                            </button>
                                        )}
                                    </div>
                                </div>
                            </div>
                        );
                    })}
                </div>
            )}

            {lastPage > 1 && (
                <div className="article-list__pagination">
                    <button type="button" disabled={currentPage <= 1} onClick={() => load(currentPage - 1)}>←</button>
                    <span>{currentPage} / {lastPage}</span>
                    <button type="button" disabled={currentPage >= lastPage} onClick={() => load(currentPage + 1)}>→</button>
                </div>
            )}
        </div>
    );
}
