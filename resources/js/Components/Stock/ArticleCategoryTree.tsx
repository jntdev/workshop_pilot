import { useState, useCallback } from 'react';
import type { ArticleCategory } from '@/types';

interface Props {
    categories: ArticleCategory[];
    selectedSubcategoryId: number | null;
    onSelectSubcategory: (id: number | null) => void;
    onChanged: () => void;
    csrfToken: string;
    allowCreate: boolean;
}

type EditingItem =
    | { kind: 'new-category'; name: string }
    | { kind: 'edit-category'; id: number; name: string }
    | { kind: 'new-subcategory'; categoryId: number; name: string }
    | { kind: 'edit-subcategory'; id: number; categoryId: number; name: string };

export default function ArticleCategoryTree({ categories, selectedSubcategoryId, onSelectSubcategory, onChanged, csrfToken, allowCreate }: Props) {
    const [expandedIds, setExpandedIds] = useState<Set<number>>(() => new Set(categories.map(c => c.id)));
    const [editing, setEditing] = useState<EditingItem | null>(null);
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const toggleExpand = useCallback((id: number) => {
        setExpandedIds(prev => {
            const next = new Set(prev);
            if (next.has(id)) { next.delete(id); } else { next.add(id); }
            return next;
        });
    }, []);

    const save = useCallback(async () => {
        if (!editing) { return; }
        setIsLoading(true);
        setError(null);

        try {
            let url = '';
            let method = 'POST';
            let body: Record<string, unknown> = {};

            if (editing.kind === 'new-category') {
                url = '/api/article-categories';
                body = { name: editing.name };
            } else if (editing.kind === 'edit-category') {
                url = `/api/article-categories/${editing.id}`;
                method = 'PUT';
                body = { name: editing.name };
            } else if (editing.kind === 'new-subcategory') {
                url = '/api/article-subcategories';
                body = { article_category_id: editing.categoryId, name: editing.name };
            } else {
                url = `/api/article-subcategories/${editing.id}`;
                method = 'PUT';
                body = { name: editing.name };
            }

            const res = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(body),
            });

            if (!res.ok) {
                const data = await res.json();
                setError(data.message || 'Erreur');
                return;
            }

            setEditing(null);
            onChanged();
        } finally {
            setIsLoading(false);
        }
    }, [editing, csrfToken, onChanged]);

    const deleteCategory = useCallback(async (id: number) => {
        if (!confirm('Supprimer cette catégorie ?')) { return; }
        const res = await fetch(`/api/article-categories/${id}`, {
            method: 'DELETE',
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
        });
        if (res.ok) {
            onChanged();
        } else {
            const data = await res.json();
            alert(data.message || 'Impossible de supprimer');
        }
    }, [csrfToken, onChanged]);

    const deleteSubcategory = useCallback(async (id: number) => {
        if (!confirm('Supprimer cette sous-catégorie ?')) { return; }
        const res = await fetch(`/api/article-subcategories/${id}`, {
            method: 'DELETE',
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
        });
        if (res.ok) {
            if (selectedSubcategoryId === id) { onSelectSubcategory(null); }
            onChanged();
        } else {
            const data = await res.json();
            alert(data.message || 'Impossible de supprimer');
        }
    }, [csrfToken, onChanged, selectedSubcategoryId, onSelectSubcategory]);

    const isEditingItem = (e: EditingItem | null, id: number, kind: string) =>
        e !== null && 'id' in e && e.id === id && e.kind === kind;

    return (
        <div className="article-tree">
            <div className="article-tree__header">
                <span className="article-tree__title">Catégories</span>
            </div>

            {error && <div className="article-tree__error">{error}</div>}

            <div className="article-tree__list">
                {categories.map(cat => (
                    <div key={cat.id} className="article-tree__category">
                        <div className="article-tree__category-row">
                            {isEditingItem(editing, cat.id, 'edit-category') && editing?.kind === 'edit-category' ? (
                                <div className="article-tree__inline-form">
                                    <input
                                        className="article-tree__input"
                                        value={editing.name}
                                        onChange={e => setEditing({ ...editing, name: e.target.value })}
                                        autoFocus
                                        onKeyDown={e => { if (e.key === 'Enter') { save(); } if (e.key === 'Escape') { setEditing(null); } }}
                                    />
                                    <button type="button" className="article-tree__btn article-tree__btn--primary" onClick={save} disabled={isLoading || !editing.name}>✓</button>
                                    <button type="button" className="article-tree__btn" onClick={() => setEditing(null)}>✕</button>
                                </div>
                            ) : (
                                <>
                                    <button
                                        type="button"
                                        className={`article-tree__expand ${expandedIds.has(cat.id) ? 'article-tree__expand--open' : ''}`}
                                        onClick={() => toggleExpand(cat.id)}
                                        title={expandedIds.has(cat.id) ? 'Réduire' : 'Développer'}
                                    >
                                        <svg viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3 2L7 5L3 8" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/>
                                        </svg>
                                    </button>
                                    <span className="article-tree__category-name">{cat.name}</span>
                                    <span className="article-tree__count">{cat.subcategories.length}</span>
                                    <div className="article-tree__actions">
                                        <button type="button" className="article-tree__action" title="Renommer" onClick={() => setEditing({ kind: 'edit-category', id: cat.id, name: cat.name })}>
                                            <svg viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.5 2.5L11.5 4.5M1 13H3L11 5L9 3L1 11V13Z" stroke="currentColor" strokeWidth="1.4" strokeLinecap="round" strokeLinejoin="round"/></svg>
                                        </button>
                                        <button type="button" className="article-tree__action article-tree__action--danger" title="Supprimer" onClick={() => deleteCategory(cat.id)}>
                                            <svg viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 4H12M5 4V2H9V4M5.5 6.5V10.5M8.5 6.5V10.5M3 4L3.8 12H10.2L11 4H3Z" stroke="currentColor" strokeWidth="1.4" strokeLinecap="round" strokeLinejoin="round"/></svg>
                                        </button>
                                    </div>
                                </>
                            )}
                        </div>

                        {expandedIds.has(cat.id) && (
                            <div className="article-tree__subcategories">
                                {cat.subcategories.map(sub => (
                                    <div key={sub.id} className={`article-tree__subcategory-row ${selectedSubcategoryId === sub.id ? 'article-tree__subcategory-row--selected' : ''}`}>
                                        {isEditingItem(editing, sub.id, 'edit-subcategory') && editing?.kind === 'edit-subcategory' ? (
                                            <div className="article-tree__inline-form">
                                                <input
                                                    className="article-tree__input"
                                                    value={editing.name}
                                                    onChange={e => setEditing({ ...editing, name: e.target.value })}
                                                    autoFocus
                                                    onKeyDown={e => { if (e.key === 'Enter') { save(); } if (e.key === 'Escape') { setEditing(null); } }}
                                                />
                                                <button type="button" className="article-tree__btn article-tree__btn--primary" onClick={save} disabled={isLoading || !editing.name}>✓</button>
                                                <button type="button" className="article-tree__btn" onClick={() => setEditing(null)}>✕</button>
                                            </div>
                                        ) : (
                                            <>
                                                <button
                                                    type="button"
                                                    className="article-tree__subcategory-name"
                                                    onClick={() => onSelectSubcategory(selectedSubcategoryId === sub.id ? null : sub.id)}
                                                >
                                                    {sub.name}
                                                </button>
                                                <div className="article-tree__actions">
                                                    <button type="button" className="article-tree__action" title="Renommer" onClick={() => setEditing({ kind: 'edit-subcategory', id: sub.id, categoryId: cat.id, name: sub.name })}>
                                                        <svg viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.5 2.5L11.5 4.5M1 13H3L11 5L9 3L1 11V13Z" stroke="currentColor" strokeWidth="1.4" strokeLinecap="round" strokeLinejoin="round"/></svg>
                                                    </button>
                                                    <button type="button" className="article-tree__action article-tree__action--danger" title="Supprimer" onClick={() => deleteSubcategory(sub.id)}>
                                                        <svg viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 4H12M5 4V2H9V4M5.5 6.5V10.5M8.5 6.5V10.5M3 4L3.8 12H10.2L11 4H3Z" stroke="currentColor" strokeWidth="1.4" strokeLinecap="round" strokeLinejoin="round"/></svg>
                                                    </button>
                                                </div>
                                            </>
                                        )}
                                    </div>
                                ))}

                                {allowCreate && (
                                    editing?.kind === 'new-subcategory' && editing.categoryId === cat.id ? (
                                        <div className="article-tree__inline-form article-tree__inline-form--sub">
                                            <input
                                                className="article-tree__input"
                                                value={editing.name}
                                                onChange={e => setEditing({ ...editing, name: e.target.value })}
                                                placeholder="Nom de la sous-catégorie"
                                                autoFocus
                                                onKeyDown={e => { if (e.key === 'Enter') { save(); } if (e.key === 'Escape') { setEditing(null); } }}
                                            />
                                            <button type="button" className="article-tree__btn article-tree__btn--primary" onClick={save} disabled={isLoading || !editing.name}>✓</button>
                                            <button type="button" className="article-tree__btn" onClick={() => setEditing(null)}>✕</button>
                                        </div>
                                    ) : (
                                        <button
                                            type="button"
                                            className="article-tree__add-sub"
                                            onClick={() => setEditing({ kind: 'new-subcategory', categoryId: cat.id, name: '' })}
                                        >
                                            + Sous-catégorie
                                        </button>
                                    )
                                )}
                            </div>
                        )}
                    </div>
                ))}

                {allowCreate && (
                    editing?.kind === 'new-category' ? (
                        <div className="article-tree__inline-form article-tree__inline-form--category">
                            <input
                                className="article-tree__input"
                                value={editing.name}
                                onChange={e => setEditing({ ...editing, name: e.target.value })}
                                placeholder="Nom de la catégorie"
                                autoFocus
                                onKeyDown={e => { if (e.key === 'Enter') { save(); } if (e.key === 'Escape') { setEditing(null); } }}
                            />
                            <button type="button" className="article-tree__btn article-tree__btn--primary" onClick={save} disabled={isLoading || !editing.name}>✓</button>
                            <button type="button" className="article-tree__btn" onClick={() => setEditing(null)}>✕</button>
                        </div>
                    ) : (
                        <button
                            type="button"
                            className="article-tree__add-category"
                            onClick={() => setEditing({ kind: 'new-category', name: '' })}
                        >
                            + Catégorie
                        </button>
                    )
                )}
            </div>
        </div>
    );
}
