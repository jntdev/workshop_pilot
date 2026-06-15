import { Head } from '@inertiajs/react';
import { useState, useCallback, useEffect } from 'react';
import MainLayout from '@/Layouts/MainLayout';
import type { Article, ArticleCategory, ArticleSubcategory, Brand } from '@/types';
import ArticleCategoryTree from '@/Components/Stock/ArticleCategoryTree';
import ArticleList from '@/Components/Stock/ArticleList';
import ArticleForm from '@/Components/Stock/ArticleForm';
import ArticleStockPanel from '@/Components/Stock/ArticleStockPanel';

export default function StockIndex() {
    const [categories, setCategories] = useState<ArticleCategory[]>([]);
    const [brands, setBrands] = useState<Brand[]>([]);
    const [selectedSubcategoryId, setSelectedSubcategoryId] = useState<number | null>(null);
    const [selectedBrandId, setSelectedBrandId] = useState<number | null>(null);
    const [editingArticle, setEditingArticle] = useState<Article | null | 'new'>(null);
    const [stockArticle, setStockArticle] = useState<Article | null>(null);
    const [listRefreshKey, setListRefreshKey] = useState(0);

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const loadCategories = useCallback(async () => {
        const res = await fetch('/api/article-categories', { headers: { Accept: 'application/json' } });
        const data = await res.json();
        setCategories(data.categories ?? []);
    }, []);

    const loadBrands = useCallback(async () => {
        const res = await fetch('/api/brands', { headers: { Accept: 'application/json' } });
        const data = await res.json();
        setBrands(data.brands ?? []);
    }, []);

    useEffect(() => {
        loadCategories();
        loadBrands();
    }, [loadCategories, loadBrands]);

    const refreshList = useCallback(() => {
        setListRefreshKey(k => k + 1);
    }, []);

    const handleArticleSaved = useCallback(() => {
        setEditingArticle(null);
        loadBrands();
        refreshList();
    }, [refreshList, loadBrands]);

    const handleCategoriesChanged = useCallback(() => {
        loadCategories();
        refreshList();
    }, [loadCategories, refreshList]);

    const selectedSubcategory: ArticleSubcategory | null = categories
        .flatMap(c => c.subcategories)
        .find(s => s.id === selectedSubcategoryId) ?? null;

    const selectedBrand: Brand | null = brands.find(b => b.id === selectedBrandId) ?? null;

    const handleSelectSubcategory = (id: number | null) => {
        setSelectedSubcategoryId(id);
        setSelectedBrandId(null);
    };

    const handleSelectBrand = (id: number | null) => {
        setSelectedBrandId(id);
        setSelectedSubcategoryId(null);
    };

    return (
        <MainLayout title="Catalogue & Stock">
            <Head title="Catalogue & Stock" />

            <div className="stock-page">
                <div className="stock-page__header">
                    <h1 className="stock-page__title">Catalogue articles</h1>
                    <button
                        type="button"
                        className="stock-page__new-btn"
                        onClick={() => setEditingArticle('new')}
                    >
                        + Nouvel article
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
                        />

                        {brands.length > 0 && (
                            <div className="stock-page__brand-filter">
                                <div className="stock-page__brand-filter-title">Marques</div>
                                <button
                                    type="button"
                                    className={`stock-page__brand-btn ${selectedBrandId === null && selectedSubcategoryId === null ? '' : ''}`}
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
                        )}
                    </aside>

                    <main className="stock-page__main">
                        <ArticleList
                            key={listRefreshKey}
                            subcategoryId={selectedSubcategoryId}
                            subcategoryLabel={selectedBrand ? `Marque : ${selectedBrand.name}` : (selectedSubcategory?.name ?? null)}
                            brandId={selectedBrandId}
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
