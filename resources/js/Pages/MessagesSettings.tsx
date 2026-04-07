import { useState } from 'react';
import MainLayout from '@/Layouts/MainLayout';
import { Head, router } from '@inertiajs/react';
import { useMessaging } from '@/Contexts/MessagingContext';
import { MessageCategoryData } from '@/types';

function MessagesSettingsContent() {
    const { categories, createCategory, updateCategory, deleteCategory } = useMessaging();

    const [newLabel, setNewLabel] = useState('');
    const [newColor, setNewColor] = useState('#6b7280');
    const [isCreating, setIsCreating] = useState(false);
    const [editingId, setEditingId] = useState<number | null>(null);
    const [editLabel, setEditLabel] = useState('');
    const [editColor, setEditColor] = useState('');

    const handleCreate = async () => {
        if (!newLabel.trim()) return;
        setIsCreating(true);
        try {
            await createCategory(newLabel.trim(), newColor);
            setNewLabel('');
            setNewColor('#6b7280');
        } catch (error) {
            console.error('Failed to create category:', error);
        } finally {
            setIsCreating(false);
        }
    };

    const handleStartEdit = (cat: MessageCategoryData) => {
        setEditingId(cat.id);
        setEditLabel(cat.label);
        setEditColor(cat.color);
    };

    const handleCancelEdit = () => {
        setEditingId(null);
        setEditLabel('');
        setEditColor('');
    };

    const handleSaveEdit = async () => {
        if (!editingId || !editLabel.trim()) return;
        try {
            await updateCategory(editingId, { label: editLabel.trim(), color: editColor });
            setEditingId(null);
        } catch (error) {
            console.error('Failed to update category:', error);
        }
    };

    const handleDelete = async (cat: MessageCategoryData) => {
        if (cat.is_default) {
            alert('Impossible de supprimer la categorie par defaut');
            return;
        }
        if (!confirm(`Supprimer la categorie "${cat.label}" ? Les messages seront deplaces vers "Autre".`)) {
            return;
        }
        try {
            await deleteCategory(cat.id);
        } catch (error) {
            console.error('Failed to delete category:', error);
        }
    };

    const handleBack = () => {
        router.visit('/messages');
    };

    return (
        <>
            <Head title="Reglages Messages" />

            <div className="messages-settings">
                <div className="messages-settings__header">
                    <button
                        type="button"
                        className="messages-settings__back"
                        onClick={handleBack}
                    >
                        &larr; Retour aux messages
                    </button>
                    <h1 className="messages-settings__title">Reglages des messages</h1>
                </div>

                <div className="messages-settings__section">
                    <h2 className="messages-settings__section-title">Categories</h2>
                    <p className="messages-settings__section-desc">
                        Gerez les categories pour organiser vos messages.
                    </p>

                    <div className="messages-settings__categories">
                        {categories.map(cat => (
                            <div key={cat.id} className="messages-settings__category">
                                {editingId === cat.id ? (
                                    <div className="messages-settings__category-edit">
                                        <input
                                            type="color"
                                            value={editColor}
                                            onChange={e => setEditColor(e.target.value)}
                                            className="messages-settings__color-input"
                                        />
                                        <input
                                            type="text"
                                            value={editLabel}
                                            onChange={e => setEditLabel(e.target.value)}
                                            className="messages-settings__text-input"
                                            placeholder="Nom de la categorie"
                                        />
                                        <button
                                            type="button"
                                            className="messages-settings__btn messages-settings__btn--save"
                                            onClick={handleSaveEdit}
                                        >
                                            Enregistrer
                                        </button>
                                        <button
                                            type="button"
                                            className="messages-settings__btn messages-settings__btn--cancel"
                                            onClick={handleCancelEdit}
                                        >
                                            Annuler
                                        </button>
                                    </div>
                                ) : (
                                    <div className="messages-settings__category-view">
                                        <span
                                            className="messages-settings__category-dot"
                                            style={{ backgroundColor: cat.color }}
                                        />
                                        <span className="messages-settings__category-label">
                                            {cat.label}
                                            {cat.is_default && (
                                                <span className="messages-settings__category-default">(defaut)</span>
                                            )}
                                        </span>
                                        <div className="messages-settings__category-actions">
                                            <button
                                                type="button"
                                                className="messages-settings__btn messages-settings__btn--edit"
                                                onClick={() => handleStartEdit(cat)}
                                            >
                                                Modifier
                                            </button>
                                            {!cat.is_default && (
                                                <button
                                                    type="button"
                                                    className="messages-settings__btn messages-settings__btn--delete"
                                                    onClick={() => handleDelete(cat)}
                                                >
                                                    Supprimer
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                )}
                            </div>
                        ))}
                    </div>

                    <div className="messages-settings__new-category">
                        <h3 className="messages-settings__new-title">Ajouter une categorie</h3>
                        <div className="messages-settings__new-form">
                            <input
                                type="color"
                                value={newColor}
                                onChange={e => setNewColor(e.target.value)}
                                className="messages-settings__color-input"
                            />
                            <input
                                type="text"
                                value={newLabel}
                                onChange={e => setNewLabel(e.target.value)}
                                className="messages-settings__text-input"
                                placeholder="Nom de la nouvelle categorie"
                                onKeyDown={e => e.key === 'Enter' && handleCreate()}
                            />
                            <button
                                type="button"
                                className="messages-settings__btn messages-settings__btn--create"
                                onClick={handleCreate}
                                disabled={isCreating || !newLabel.trim()}
                            >
                                {isCreating ? 'Creation...' : 'Creer'}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}

export default function MessagesSettings() {
    return (
        <MainLayout>
            <MessagesSettingsContent />
        </MainLayout>
    );
}
