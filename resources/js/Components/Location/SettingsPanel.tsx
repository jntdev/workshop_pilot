import { useState, useCallback } from 'react';
import HexColorPicker from './HexColorPicker';
import type { BikeCategoryRef, BikeSizeRef } from '@/types';

interface SettingsPanelProps {
    categories: BikeCategoryRef[];
    sizes: BikeSizeRef[];
    onClose: () => void;
    onUpdate: () => void;
}

type EditingItem = {
    type: 'category' | 'size';
    id: number | null; // null = création
    name: string;
    color: string;
    has_battery?: boolean;
};

export default function SettingsPanel({ categories, sizes, onClose, onUpdate }: SettingsPanelProps) {
    const [expandedSection, setExpandedSection] = useState<'categories' | 'sizes' | null>('categories');
    const [editingItem, setEditingItem] = useState<EditingItem | null>(null);
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const toggleSection = (section: 'categories' | 'sizes') => {
        setExpandedSection(expandedSection === section ? null : section);
        setEditingItem(null);
        setError(null);
    };

    const startCreateCategory = useCallback(() => {
        setEditingItem({
            type: 'category',
            id: null,
            name: '',
            color: '#6366f1',
            has_battery: false,
        });
        setError(null);
    }, []);

    const startCreateSize = useCallback(() => {
        setEditingItem({
            type: 'size',
            id: null,
            name: '',
            color: '#6366f1',
        });
        setError(null);
    }, []);

    const startEditCategory = useCallback((cat: BikeCategoryRef) => {
        setEditingItem({
            type: 'category',
            id: cat.id,
            name: cat.name,
            color: cat.color,
            has_battery: cat.has_battery,
        });
        setError(null);
    }, []);

    const startEditSize = useCallback((size: BikeSizeRef) => {
        setEditingItem({
            type: 'size',
            id: size.id,
            name: size.name,
            color: size.color,
        });
        setError(null);
    }, []);

    const cancelEdit = useCallback(() => {
        setEditingItem(null);
        setError(null);
    }, []);

    const handleSave = useCallback(async () => {
        if (!editingItem) return;

        setIsLoading(true);
        setError(null);

        try {
            const isCategory = editingItem.type === 'category';
            const baseUrl = isCategory ? '/api/bike-categories' : '/api/bike-sizes';
            const url = editingItem.id ? `${baseUrl}/${editingItem.id}` : baseUrl;
            const method = editingItem.id ? 'PUT' : 'POST';

            const body: Record<string, unknown> = {
                name: editingItem.name,
                color: editingItem.color,
            };

            if (isCategory) {
                body.has_battery = editingItem.has_battery ?? false;
            }

            const response = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(body),
            });

            if (response.ok) {
                setEditingItem(null);
                onUpdate();
            } else {
                const data = await response.json();
                setError(data.message || 'Erreur lors de la sauvegarde');
            }
        } catch (err) {
            setError('Erreur de connexion');
        } finally {
            setIsLoading(false);
        }
    }, [editingItem, csrfToken, onUpdate]);

    const handleDelete = useCallback(async (type: 'category' | 'size', id: number) => {
        if (!confirm('Supprimer cet element ?')) return;

        setIsLoading(true);
        setError(null);

        try {
            const baseUrl = type === 'category' ? '/api/bike-categories' : '/api/bike-sizes';
            const response = await fetch(`${baseUrl}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });

            if (response.ok) {
                onUpdate();
            } else {
                const data = await response.json();
                setError(data.message || 'Impossible de supprimer');
            }
        } catch (err) {
            setError('Erreur de connexion');
        } finally {
            setIsLoading(false);
        }
    }, [csrfToken, onUpdate]);

    return (
        <div className="settings-panel">
            <div className="settings-panel__header">
                <h2 className="settings-panel__title">Parametres agenda</h2>
                <button
                    type="button"
                    className="settings-panel__close"
                    onClick={onClose}
                    aria-label="Fermer"
                >
                    x
                </button>
            </div>

            {error && (
                <div className="settings-panel__error">
                    {error}
                </div>
            )}

            <div className="settings-panel__content">
                {/* Section Categories */}
                <div className="settings-panel__section">
                    <button
                        type="button"
                        className="settings-panel__section-header"
                        onClick={() => toggleSection('categories')}
                    >
                        <span className="settings-panel__section-icon">
                            {expandedSection === 'categories' ? '▼' : '▶'}
                        </span>
                        <span className="settings-panel__section-title">
                            Categories de velos
                        </span>
                        <span className="settings-panel__section-count">
                            {categories.length}
                        </span>
                    </button>

                    {expandedSection === 'categories' && (
                        <div className="settings-panel__section-content">
                            <div className="settings-panel__list">
                                {categories.map((cat) => (
                                    <div key={cat.id} className="settings-panel__item">
                                        {editingItem?.type === 'category' && editingItem.id === cat.id ? (
                                            <div className="settings-panel__edit-form">
                                                <div className="settings-panel__edit-row">
                                                    <HexColorPicker
                                                        value={editingItem.color}
                                                        onChange={(color) => setEditingItem({ ...editingItem, color })}
                                                    />
                                                    <input
                                                        type="text"
                                                        className="settings-panel__input"
                                                        value={editingItem.name}
                                                        onChange={(e) => setEditingItem({ ...editingItem, name: e.target.value })}
                                                        placeholder="Nom"
                                                    />
                                                </div>
                                                <label className="settings-panel__checkbox-label">
                                                    <input
                                                        type="checkbox"
                                                        checked={editingItem.has_battery ?? false}
                                                        onChange={(e) => setEditingItem({ ...editingItem, has_battery: e.target.checked })}
                                                    />
                                                    A une batterie (VAE)
                                                </label>
                                                <div className="settings-panel__edit-actions">
                                                    <button
                                                        type="button"
                                                        className="settings-panel__btn settings-panel__btn--primary"
                                                        onClick={handleSave}
                                                        disabled={isLoading || !editingItem.name}
                                                    >
                                                        Enregistrer
                                                    </button>
                                                    <button
                                                        type="button"
                                                        className="settings-panel__btn"
                                                        onClick={cancelEdit}
                                                    >
                                                        Annuler
                                                    </button>
                                                </div>
                                            </div>
                                        ) : (
                                            <>
                                                <div className="settings-panel__item-info">
                                                    <span
                                                        className="settings-panel__color-badge"
                                                        style={{ backgroundColor: cat.color }}
                                                    />
                                                    <span className="settings-panel__item-name">{cat.name}</span>
                                                    {cat.has_battery && (
                                                        <span className="settings-panel__badge">Batterie</span>
                                                    )}
                                                </div>
                                                <div className="settings-panel__item-actions">
                                                    <button
                                                        type="button"
                                                        className="settings-panel__action"
                                                        onClick={() => startEditCategory(cat)}
                                                    >
                                                        Modifier
                                                    </button>
                                                    <button
                                                        type="button"
                                                        className="settings-panel__action settings-panel__action--danger"
                                                        onClick={() => handleDelete('category', cat.id)}
                                                    >
                                                        Supprimer
                                                    </button>
                                                </div>
                                            </>
                                        )}
                                    </div>
                                ))}
                            </div>

                            {editingItem?.type === 'category' && editingItem.id === null ? (
                                <div className="settings-panel__new-form">
                                    <div className="settings-panel__edit-row">
                                        <HexColorPicker
                                            value={editingItem.color}
                                            onChange={(color) => setEditingItem({ ...editingItem, color })}
                                        />
                                        <input
                                            type="text"
                                            className="settings-panel__input"
                                            value={editingItem.name}
                                            onChange={(e) => setEditingItem({ ...editingItem, name: e.target.value })}
                                            placeholder="Nom de la categorie"
                                            autoFocus
                                        />
                                    </div>
                                    <label className="settings-panel__checkbox-label">
                                        <input
                                            type="checkbox"
                                            checked={editingItem.has_battery ?? false}
                                            onChange={(e) => setEditingItem({ ...editingItem, has_battery: e.target.checked })}
                                        />
                                        A une batterie (VAE)
                                    </label>
                                    <div className="settings-panel__edit-actions">
                                        <button
                                            type="button"
                                            className="settings-panel__btn settings-panel__btn--primary"
                                            onClick={handleSave}
                                            disabled={isLoading || !editingItem.name}
                                        >
                                            Creer
                                        </button>
                                        <button
                                            type="button"
                                            className="settings-panel__btn"
                                            onClick={cancelEdit}
                                        >
                                            Annuler
                                        </button>
                                    </div>
                                </div>
                            ) : (
                                <button
                                    type="button"
                                    className="settings-panel__add-btn"
                                    onClick={startCreateCategory}
                                >
                                    + Ajouter une categorie
                                </button>
                            )}
                        </div>
                    )}
                </div>

                {/* Section Tailles */}
                <div className="settings-panel__section">
                    <button
                        type="button"
                        className="settings-panel__section-header"
                        onClick={() => toggleSection('sizes')}
                    >
                        <span className="settings-panel__section-icon">
                            {expandedSection === 'sizes' ? '▼' : '▶'}
                        </span>
                        <span className="settings-panel__section-title">
                            Tailles de velos
                        </span>
                        <span className="settings-panel__section-count">
                            {sizes.length}
                        </span>
                    </button>

                    {expandedSection === 'sizes' && (
                        <div className="settings-panel__section-content">
                            <div className="settings-panel__list">
                                {sizes.map((size) => (
                                    <div key={size.id} className="settings-panel__item">
                                        {editingItem?.type === 'size' && editingItem.id === size.id ? (
                                            <div className="settings-panel__edit-form">
                                                <div className="settings-panel__edit-row">
                                                    <HexColorPicker
                                                        value={editingItem.color}
                                                        onChange={(color) => setEditingItem({ ...editingItem, color })}
                                                    />
                                                    <input
                                                        type="text"
                                                        className="settings-panel__input"
                                                        value={editingItem.name}
                                                        onChange={(e) => setEditingItem({ ...editingItem, name: e.target.value })}
                                                        placeholder="Nom"
                                                    />
                                                </div>
                                                <div className="settings-panel__edit-actions">
                                                    <button
                                                        type="button"
                                                        className="settings-panel__btn settings-panel__btn--primary"
                                                        onClick={handleSave}
                                                        disabled={isLoading || !editingItem.name}
                                                    >
                                                        Enregistrer
                                                    </button>
                                                    <button
                                                        type="button"
                                                        className="settings-panel__btn"
                                                        onClick={cancelEdit}
                                                    >
                                                        Annuler
                                                    </button>
                                                </div>
                                            </div>
                                        ) : (
                                            <>
                                                <div className="settings-panel__item-info">
                                                    <span
                                                        className="settings-panel__color-badge"
                                                        style={{ backgroundColor: size.color }}
                                                    />
                                                    <span className="settings-panel__item-name">{size.name}</span>
                                                </div>
                                                <div className="settings-panel__item-actions">
                                                    <button
                                                        type="button"
                                                        className="settings-panel__action"
                                                        onClick={() => startEditSize(size)}
                                                    >
                                                        Modifier
                                                    </button>
                                                    <button
                                                        type="button"
                                                        className="settings-panel__action settings-panel__action--danger"
                                                        onClick={() => handleDelete('size', size.id)}
                                                    >
                                                        Supprimer
                                                    </button>
                                                </div>
                                            </>
                                        )}
                                    </div>
                                ))}
                            </div>

                            {editingItem?.type === 'size' && editingItem.id === null ? (
                                <div className="settings-panel__new-form">
                                    <div className="settings-panel__edit-row">
                                        <HexColorPicker
                                            value={editingItem.color}
                                            onChange={(color) => setEditingItem({ ...editingItem, color })}
                                        />
                                        <input
                                            type="text"
                                            className="settings-panel__input"
                                            value={editingItem.name}
                                            onChange={(e) => setEditingItem({ ...editingItem, name: e.target.value })}
                                            placeholder="Nom de la taille"
                                            autoFocus
                                        />
                                    </div>
                                    <div className="settings-panel__edit-actions">
                                        <button
                                            type="button"
                                            className="settings-panel__btn settings-panel__btn--primary"
                                            onClick={handleSave}
                                            disabled={isLoading || !editingItem.name}
                                        >
                                            Creer
                                        </button>
                                        <button
                                            type="button"
                                            className="settings-panel__btn"
                                            onClick={cancelEdit}
                                        >
                                            Annuler
                                        </button>
                                    </div>
                                </div>
                            ) : (
                                <button
                                    type="button"
                                    className="settings-panel__add-btn"
                                    onClick={startCreateSize}
                                >
                                    + Ajouter une taille
                                </button>
                            )}
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
