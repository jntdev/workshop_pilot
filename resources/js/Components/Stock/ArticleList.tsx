import { useState, useEffect, useCallback } from 'react';
import type { Article, Brand } from '@/types';

interface Props {
    subcategoryId: number | null;
    subcategoryLabel: string | null;
    brandId: number | null;
    onEdit: (article: Article) => void;
    onOpenStock: (article: Article) => void;
    csrfToken: string;
}

function formatPrice(centimes: number): string {
    return (centimes / 100).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
}

export default function ArticleList({ subcategoryId, subcategoryLabel, brandId, onEdit, onOpenStock, csrfToken }: Props) {
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

        const res = await fetch(`/api/articles?${params}`, { headers: { Accept: 'application/json' } });
        const data = await res.json();
        setArticles(data.data ?? []);
        setCurrentPage(data.current_page ?? 1);
        setLastPage(data.last_page ?? 1);
        setIsLoading(false);
    }, [subcategoryId, brandId, search]);

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

            {isLoading ? (
                <div className="article-list__loading">Chargement...</div>
            ) : articles.length === 0 ? (
                <div className="article-list__empty">Aucun article{subcategoryLabel ? ` dans ${subcategoryLabel}` : ''}</div>
            ) : (
                <table className="article-list__table">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Désignation</th>
                            <th>Marque</th>
                            <th>Fournisseur</th>
                            <th>Px achat</th>
                            <th>Px vente</th>
                            <th>TVA</th>
                            <th>Stock</th>
                            <th>Unité</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        {articles.map(article => (
                            <tr key={article.id}>
                                <td className="article-list__ref">{article.reference}</td>
                                <td className="article-list__designation">{article.designation}</td>
                                <td className="article-list__brand">{article.brand?.name ?? '—'}</td>
                                <td className="article-list__supplier">{article.supplier?.name ?? '—'}</td>
                                <td className="article-list__price">{formatPrice(article.purchase_price_ht)}</td>
                                <td className="article-list__price">{formatPrice(article.sale_price_ht)}</td>
                                <td className="article-list__tva">{article.tva_rate} %</td>
                                <td className={`article-list__stock ${stockClass(article.stock_quantity)}`}>
                                    {article.stock_quantity}
                                </td>
                                <td className="article-list__unit">{article.unit}</td>
                                <td className="article-list__actions">
                                    <button type="button" className="article-list__action" onClick={() => onOpenStock(article)} title="Mouvements de stock">
                                        📦
                                    </button>
                                    <button type="button" className="article-list__action" onClick={() => onEdit(article)}>
                                        Modifier
                                    </button>
                                    <button type="button" className="article-list__action article-list__action--danger" onClick={() => handleDelete(article.id)}>
                                        Supprimer
                                    </button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
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
