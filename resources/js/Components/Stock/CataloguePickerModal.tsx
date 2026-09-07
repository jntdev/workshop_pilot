import { useState, useEffect, useCallback, useRef } from 'react';
import type { Article, ArticleFilterOptions, ArticleSubcategory } from '@/types';
import { ATTRIBUTE_LABELS, orderedAttributeEntries, formatOptionLabel } from '@/utils/articleFilters';
import ArticleCardImage from '@/Components/Stock/ArticleCardImage';

interface Props {
    onSelect: (article: Article) => void;
    onClose: () => void;
}

function formatPrice(cents: number): string {
    return (cents / 100).toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + ' €';
}

export default function CataloguePickerModal({ onSelect, onClose }: Props) {
    const [subcategories, setSubcategories] = useState<ArticleSubcategory[]>([]);
    const [selectedSubcategoryId, setSelectedSubcategoryId] = useState<number | null>(null);
    const [filterOptions, setFilterOptions] = useState<ArticleFilterOptions | null>(null);
    const [selectedAttributes, setSelectedAttributes] = useState<Record<string, string>>({});
    const [selectedBrandId, setSelectedBrandId] = useState<number | null>(null);
    const [articles, setArticles] = useState<Article[]>([]);
    const [search, setSearch] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const debounceRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    useEffect(() => {
        fetch('/api/article-categories', { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(data => setSubcategories(data.categories?.[0]?.subcategories ?? []));
    }, []);

    useEffect(() => {
        setSelectedAttributes({});
        setSelectedBrandId(null);
        setFilterOptions(null);

        if (selectedSubcategoryId === null) {
            return;
        }

        fetch(`/api/article-subcategories/${selectedSubcategoryId}/filter-options`, { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(data => setFilterOptions(data));
    }, [selectedSubcategoryId]);

    const loadArticles = useCallback(async (subcategoryId: number | null, brandId: number | null, attributes: Record<string, string>, q: string) => {
        setIsLoading(true);
        let url = '';
        if (q.length >= 3) {
            url = `/api/articles/search?q=${encodeURIComponent(q)}`;
        } else {
            const params = new URLSearchParams({ per_page: '50' });
            if (subcategoryId) { params.set('subcategory_id', String(subcategoryId)); }
            if (brandId) { params.set('brand_id', String(brandId)); }
            Object.entries(attributes).forEach(([key, value]) => {
                if (value) { params.set(`attribute[${key}]`, value); }
            });
            url = `/api/articles?${params}`;
        }
        const res = await fetch(url, { headers: { Accept: 'application/json' } });
        const data = await res.json();
        setArticles(Array.isArray(data) ? data : (data.data ?? []));
        setIsLoading(false);
    }, []);

    useEffect(() => {
        if (debounceRef.current) { clearTimeout(debounceRef.current); }
        debounceRef.current = setTimeout(() => loadArticles(selectedSubcategoryId, selectedBrandId, selectedAttributes, search), 250);
    }, [selectedSubcategoryId, selectedBrandId, selectedAttributes, search, loadArticles]);

    const setAttributeFilter = (key: string, value: string) => {
        setSelectedAttributes(prev => {
            const next = { ...prev };
            if (value) { next[key] = value; } else { delete next[key]; }
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

                        {subcategories.map(sub => (
                            <button
                                key={sub.id}
                                type="button"
                                className={`catalogue-picker__nav-sub ${selectedSubcategoryId === sub.id ? 'catalogue-picker__nav-sub--active' : ''}`}
                                onClick={() => { setSelectedSubcategoryId(sub.id === selectedSubcategoryId ? null : sub.id); setSearch(''); }}
                            >
                                {sub.name}
                            </button>
                        ))}
                    </nav>

                    <div className="catalogue-picker__results">
                        {selectedSubcategoryId !== null && (
                            <div className="catalogue-picker__filters">
                                {filterOptions && orderedAttributeEntries(filterOptions.attributes).map(([key, values]) => (
                                    <select
                                        key={key}
                                        className="catalogue-picker__filter-select"
                                        value={selectedAttributes[key] ?? ''}
                                        onChange={e => setAttributeFilter(key, e.target.value)}
                                    >
                                        <option value="">{ATTRIBUTE_LABELS[key] ?? key}</option>
                                        {values.map(value => (
                                            <option key={value} value={value}>{formatOptionLabel(key, value)}</option>
                                        ))}
                                    </select>
                                ))}

                                {filterOptions && filterOptions.brands.length > 0 && (
                                    <select
                                        className="catalogue-picker__filter-select"
                                        value={selectedBrandId ?? ''}
                                        onChange={e => setSelectedBrandId(e.target.value ? Number(e.target.value) : null)}
                                    >
                                        <option value="">Marque</option>
                                        {filterOptions.brands.map(brand => (
                                            <option key={brand.id} value={brand.id}>{brand.name}</option>
                                        ))}
                                    </select>
                                )}
                            </div>
                        )}

                        {isLoading ? (
                            <div className="catalogue-picker__loading">Chargement...</div>
                        ) : articles.length === 0 ? (
                            <div className="catalogue-picker__empty">Aucun article</div>
                        ) : (
                            <div className="catalogue-picker__grid">
                                {articles.map(article => (
                                    <div key={article.id} className="catalogue-picker__card">
                                        <div className="catalogue-picker__card-image-wrap">
                                            <ArticleCardImage article={article} />
                                        </div>
                                        <div className="catalogue-picker__card-body">
                                            <span className="catalogue-picker__card-reference">{article.reference}</span>
                                            <span className="catalogue-picker__card-designation">{article.designation}</span>
                                            <div className="catalogue-picker__card-prices">
                                                <span className="catalogue-picker__card-price">{formatPrice(article.sale_price_ttc)}</span>
                                                <span className="catalogue-picker__card-price-ht">PA HT: {formatPrice(article.purchase_price_ht)}</span>
                                            </div>
                                            <div className="catalogue-picker__card-footer">
                                                <span className={article.stock_quantity > 0 ? 'catalogue-picker__stock--ok' : 'catalogue-picker__stock--zero'}>
                                                    Stock: {article.stock_quantity}
                                                </span>
                                                {article.is_discontinued && (
                                                    <span className="catalogue-picker__card-badge--discontinued">Non disponible fournisseur</span>
                                                )}
                                            </div>
                                            <button
                                                type="button"
                                                className="catalogue-picker__select-btn"
                                                onClick={() => onSelect(article)}
                                            >
                                                Sélectionner
                                            </button>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}
