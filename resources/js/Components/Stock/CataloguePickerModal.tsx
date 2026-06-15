import { useState, useEffect, useCallback, useRef } from 'react';
import type { Article, ArticleCategory } from '@/types';

interface Props {
    onSelect: (article: Article) => void;
    onClose: () => void;
}

function formatPrice(cents: number): string {
    return (cents / 100).toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + ' €';
}

export default function CataloguePickerModal({ onSelect, onClose }: Props) {
    const [categories, setCategories] = useState<ArticleCategory[]>([]);
    const [selectedSubcategoryId, setSelectedSubcategoryId] = useState<number | null>(null);
    const [articles, setArticles] = useState<Article[]>([]);
    const [search, setSearch] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const [expandedIds, setExpandedIds] = useState<Set<number>>(new Set());
    const debounceRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    useEffect(() => {
        fetch('/api/article-categories', { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(data => {
                setCategories(data.categories ?? []);
                if (data.categories?.length > 0) {
                    setExpandedIds(new Set([data.categories[0].id]));
                }
            });
    }, []);

    const loadArticles = useCallback(async (subcategoryId: number | null, q: string) => {
        setIsLoading(true);
        let url = '';
        if (q.length >= 3) {
            url = `/api/articles/search?q=${encodeURIComponent(q)}`;
        } else {
            const params = new URLSearchParams({ per_page: '50' });
            if (subcategoryId) { params.set('subcategory_id', String(subcategoryId)); }
            url = `/api/articles?${params}`;
        }
        const res = await fetch(url, { headers: { Accept: 'application/json' } });
        const data = await res.json();
        setArticles(Array.isArray(data) ? data : (data.data ?? []));
        setIsLoading(false);
    }, []);

    useEffect(() => {
        if (debounceRef.current) { clearTimeout(debounceRef.current); }
        debounceRef.current = setTimeout(() => loadArticles(selectedSubcategoryId, search), 250);
    }, [selectedSubcategoryId, search, loadArticles]);

    const toggleExpand = (id: number) => {
        setExpandedIds(prev => {
            const next = new Set(prev);
            if (next.has(id)) { next.delete(id); } else { next.add(id); }
            return next;
        });
    };

    return (
        <div className="catalogue-picker__backdrop" onClick={e => { if (e.target === e.currentTarget) { onClose(); } }}>
            <div className="catalogue-picker">
                <div className="catalogue-picker__header">
                    <h2 className="catalogue-picker__title">Sélectionner un article</h2>
                    <input
                        type="search"
                        className="catalogue-picker__search"
                        placeholder="Recherche..."
                        value={search}
                        onChange={e => setSearch(e.target.value)}
                        autoFocus
                    />
                    <button type="button" className="catalogue-picker__close" onClick={onClose}>✕</button>
                </div>

                <div className="catalogue-picker__body">
                    <nav className="catalogue-picker__nav">
                        <button
                            type="button"
                            className={`catalogue-picker__nav-all ${selectedSubcategoryId === null && !search ? 'catalogue-picker__nav-all--active' : ''}`}
                            onClick={() => { setSelectedSubcategoryId(null); setSearch(''); }}
                        >
                            Tous les articles
                        </button>
                        {categories.map(cat => (
                            <div key={cat.id} className="catalogue-picker__nav-cat">
                                <button
                                    type="button"
                                    className="catalogue-picker__nav-cat-name"
                                    onClick={() => toggleExpand(cat.id)}
                                >
                                    {expandedIds.has(cat.id) ? '▼' : '▶'} {cat.name}
                                </button>
                                {expandedIds.has(cat.id) && cat.subcategories.map(sub => (
                                    <button
                                        key={sub.id}
                                        type="button"
                                        className={`catalogue-picker__nav-sub ${selectedSubcategoryId === sub.id ? 'catalogue-picker__nav-sub--active' : ''}`}
                                        onClick={() => { setSelectedSubcategoryId(sub.id); setSearch(''); }}
                                    >
                                        {sub.name}
                                    </button>
                                ))}
                            </div>
                        ))}
                    </nav>

                    <div className="catalogue-picker__results">
                        {isLoading ? (
                            <div className="catalogue-picker__loading">Chargement...</div>
                        ) : articles.length === 0 ? (
                            <div className="catalogue-picker__empty">Aucun article</div>
                        ) : (
                            <table className="catalogue-picker__table">
                                <thead>
                                    <tr>
                                        <th>Référence</th>
                                        <th>Désignation</th>
                                        <th>Px vente HT</th>
                                        <th>Stock</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {articles.map(article => (
                                        <tr key={article.id} className="catalogue-picker__row">
                                            <td>{article.reference}</td>
                                            <td>{article.designation}</td>
                                            <td>{formatPrice(article.sale_price_ht)}</td>
                                            <td className={article.stock_quantity > 0 ? 'catalogue-picker__stock--ok' : 'catalogue-picker__stock--zero'}>
                                                {article.stock_quantity}
                                            </td>
                                            <td>
                                                <button
                                                    type="button"
                                                    className="catalogue-picker__select-btn"
                                                    onClick={() => onSelect(article)}
                                                >
                                                    Sélectionner
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}
