import { Head } from '@inertiajs/react';
import { useState, useCallback, useEffect } from 'react';
import MainLayout from '@/Layouts/MainLayout';
import type { Article, ArticleCategory, ArticleFilterOptions, ArticleSubcategory, Brand } from '@/types';
import ArticleCategoryTree from '@/Components/Stock/ArticleCategoryTree';
import ArticleList from '@/Components/Stock/ArticleList';
import ArticleForm from '@/Components/Stock/ArticleForm';
import ArticleStockPanel from '@/Components/Stock/ArticleStockPanel';

export default function StockIndex() {
    const [categories, setCategories] = useState<ArticleCategory[]>([]);
    const [brands, setBrands] = useState<Brand[]>([]);
    const [selectedSubcategoryId, setSelectedSubcategoryId] = useState<number | null>(null);
    const [selectedBrandId, setSelectedBrandId] = useState<number | null>(null);
    const [hasMovements, setHasMovements] = useState<boolean | null>(true);
    const [filterOptions, setFilterOptions] = useState<ArticleFilterOptions | null>(null);
    const [selectedAttributes, setSelectedAttributes] = useState<Record<string, string>>({});
    const [editingArticle, setEditingArticle] = useState<Article | null | 'new'>(null);
    const [stockArticle, setStockArticle] = useState<Article | null>(null);
    const [listRefreshKey, setListRefreshKey] = useState(0);
    const [isSyncingCgn, setIsSyncingCgn] = useState(false);
    const [cgnSyncResult, setCgnSyncResult] = useState<{ success: boolean; message: string } | null>(null);

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const loadCategories = useCallback(async (hasMovementsFilter: boolean | null) => {
        const source = hasMovementsFilter === true ? 'manual' : 'catalogue';
        const res = await fetch(`/api/article-categories?source=${source}`, { headers: { Accept: 'application/json' } });
        const data = await res.json();
        setCategories(data.categories ?? []);
    }, []);

    const loadBrands = useCallback(async () => {
        const res = await fetch('/api/brands', { headers: { Accept: 'application/json' } });
        const data = await res.json();
        setBrands(data.brands ?? []);
    }, []);

    useEffect(() => {
        loadCategories(hasMovements);
        loadBrands();
    }, [hasMovements, loadCategories, loadBrands]);

    const handleTabChange = (value: boolean | null) => {
        setHasMovements(value);
        setSelectedSubcategoryId(null);
    };

    useEffect(() => {
        setSelectedAttributes({});

        if (selectedSubcategoryId === null) {
            setFilterOptions(null);
            return;
        }

        fetch(`/api/article-subcategories/${selectedSubcategoryId}/filter-options`, { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(data => setFilterOptions(data));
    }, [selectedSubcategoryId]);

    const setAttributeFilter = (key: string, value: string) => {
        setSelectedAttributes(prev => {
            const next = { ...prev };
            if (value) { next[key] = value; } else { delete next[key]; }
            return next;
        });
    };

    const refreshList = useCallback(() => {
        setListRefreshKey(k => k + 1);
    }, []);

    const handleArticleSaved = useCallback(() => {
        setEditingArticle(null);
        loadBrands();
        refreshList();
    }, [refreshList, loadBrands]);

    const handleCategoriesChanged = useCallback(() => {
        loadCategories(hasMovements);
        refreshList();
    }, [loadCategories, hasMovements, refreshList]);

    const selectedSubcategory: ArticleSubcategory | null = categories
        .flatMap(c => c.subcategories)
        .find(s => s.id === selectedSubcategoryId) ?? null;

    const selectedBrand: Brand | null = brands.find(b => b.id === selectedBrandId) ?? null;

    const handleSelectSubcategory = (id: number | null) => {
        setSelectedSubcategoryId(id);
    };

    const handleSelectBrand = (id: number | null) => {
        setSelectedBrandId(id);
    };

    const syncCgnCatalogue = async () => {
        setIsSyncingCgn(true);
        setCgnSyncResult(null);

        try {
            const res = await fetch('/api/catalogue/sync-cgn', {
                method: 'POST',
                headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
            });
            const data = await res.json();

            setCgnSyncResult({ success: data.success, message: data.message });

            if (data.success) {
                loadBrands();
                refreshList();
            }
        } catch {
            setCgnSyncResult({ success: false, message: 'Erreur réseau : impossible de contacter le serveur.' });
        } finally {
            setIsSyncingCgn(false);
        }
    };

    return (
        <MainLayout title="Catalogue & Stock">
            <Head title="Catalogue & Stock" />

            <div className="stock-page">
                <div className="stock-page__header">
                    <h1 className="stock-page__title">Catalogue articles</h1>
                    <div className="stock-page__header-actions">
                        <button
                            type="button"
                            className="stock-page__sync-btn"
                            onClick={syncCgnCatalogue}
                            disabled={isSyncingCgn}
                        >
                            {isSyncingCgn ? '⏳ Import en cours...' : '📥 Importer le catalogue CGN'}
                        </button>
                        <a href="/inventaire/scan" className="stock-page__scan-btn">📷 Scanner</a>
                        <button
                            type="button"
                            className="stock-page__new-btn"
                            onClick={() => setEditingArticle('new')}
                        >
                            + Nouvel article
                        </button>
                    </div>
                </div>

                {cgnSyncResult && (
                    <div className={`stock-page__sync-result stock-page__sync-result--${cgnSyncResult.success ? 'success' : 'error'}`}>
                        {cgnSyncResult.success ? '✓ ' : '✗ '}{cgnSyncResult.message}
                    </div>
                )}

                <div className="stock-page__tabs">
                    <button
                        type="button"
                        className={`stock-page__tab ${hasMovements === true ? 'stock-page__tab--active' : ''}`}
                        onClick={() => handleTabChange(true)}
                    >
                        Stock atelier
                    </button>
                    <button
                        type="button"
                        className={`stock-page__tab ${hasMovements === null ? 'stock-page__tab--active' : ''}`}
                        onClick={() => handleTabChange(null)}
                    >
                        Catalogue complet
                    </button>
                </div>

                <div className="stock-page__body">
                    <aside className="stock-page__sidebar">
                        <ArticleCategoryTree
                            categories={categories}
                            selectedSubcategoryId={selectedSubcategoryId}
                            onSelectSubcategory={handleSelectSubcategory}
                            onChanged={handleCategoriesChanged}
                            csrfToken={csrfToken}
                            allowCreate={hasMovements === true}
                        />

                        {brands.length > 0 && (
                            <div className="stock-page__brand-filter">
                                <div className="stock-page__brand-filter-header">
                                    <span className="stock-page__brand-filter-title">Marques</span>
                                </div>
                                <div className="stock-page__brand-list">
                                    <button
                                        type="button"
                                        className={`stock-page__brand-btn ${selectedBrandId === null ? 'stock-page__brand-btn--active' : ''}`}
                                        onClick={() => handleSelectBrand(null)}
                                    >
                                        Toutes les marques
                                    </button>
                                    {brands.map(brand => (
                                        <button
                                            key={brand.id}
                                            type="button"
                                            className={`stock-page__brand-btn ${selectedBrandId === brand.id ? 'stock-page__brand-btn--active' : ''}`}
                                            onClick={() => handleSelectBrand(brand.id)}
                                        >
                                            {brand.name}
                                        </button>
                                    ))}
                                </div>
                            </div>
                        )}
                    </aside>

                    <main className="stock-page__main">
                        <ArticleList
                            key={listRefreshKey}
                            subcategoryId={selectedSubcategoryId}
                            subcategoryLabel={[selectedSubcategory?.name, selectedBrand ? `Marque : ${selectedBrand.name}` : null].filter(Boolean).join(' · ') || null}
                            brandId={selectedBrandId}
                            attributes={selectedAttributes}
                            onAttributesChange={setAttributeFilter}
                            filterOptions={selectedSubcategoryId !== null ? filterOptions : null}
                            hasMovements={hasMovements}
                            onEdit={setEditingArticle}
                            onOpenStock={setStockArticle}
                            csrfToken={csrfToken}
                        />
                    </main>
                </div>
            </div>

            {editingArticle !== null && (
                <ArticleForm
                    article={editingArticle === 'new' ? null : editingArticle}
                    categories={categories}
                    csrfToken={csrfToken}
                    onSaved={handleArticleSaved}
                    onClose={() => setEditingArticle(null)}
                />
            )}

            {stockArticle !== null && (
                <ArticleStockPanel
                    article={stockArticle}
                    csrfToken={csrfToken}
                    onClose={() => setStockArticle(null)}
                    onChanged={() => { refreshList(); }}
                />
            )}
        </MainLayout>
    );
}
