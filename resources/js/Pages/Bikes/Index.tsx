import { Head } from '@inertiajs/react';
import { useState, useCallback, useMemo } from 'react';
import MainLayout from '@/Layouts/MainLayout';

interface Bike {
    id: number;
    category: 'VAE' | 'VTC';
    size: 'S' | 'M' | 'L' | 'XL';
    frame_type: 'b' | 'h';
    name: string;
    status: 'OK' | 'HS';
    notes: string | null;
    sort_order: number;
}

interface PageProps {
    bikes: Bike[];
}

const CATEGORIES = ['VAE', 'VTC'] as const;
const SIZES = ['S', 'M', 'L', 'XL'] as const;
const FRAME_TYPES = [
    { value: 'b', label: 'Cadre bas' },
    { value: 'h', label: 'Cadre haut' },
] as const;

export default function BikesIndex({ bikes: initialBikes }: PageProps) {
    const [bikes, setBikes] = useState<Bike[]>(initialBikes || []);
    const [editingBike, setEditingBike] = useState<Bike | null>(null);
    const [isCreating, setIsCreating] = useState(false);
    const [formData, setFormData] = useState({
        category: 'VAE' as 'VAE' | 'VTC',
        size: 'M' as 'S' | 'M' | 'L' | 'XL',
        frame_type: 'b' as 'b' | 'h',
        name: '',
        status: 'OK' as 'OK' | 'HS',
        notes: '',
    });
    const [isLoading, setIsLoading] = useState(false);

    const resetForm = useCallback(() => {
        setFormData({
            category: 'VAE',
            size: 'M',
            frame_type: 'b',
            name: '',
            status: 'OK',
            notes: '',
        });
        setEditingBike(null);
        setIsCreating(false);
    }, []);

    const handleCreate = useCallback(() => {
        setIsCreating(true);
        setEditingBike(null);
        setFormData({
            category: 'VAE',
            size: 'M',
            frame_type: 'b',
            name: '',
            status: 'OK',
            notes: '',
        });
    }, []);

    const handleEdit = useCallback((bike: Bike) => {
        setEditingBike(bike);
        setIsCreating(false);
        setFormData({
            category: bike.category,
            size: bike.size,
            frame_type: bike.frame_type,
            name: bike.name,
            status: bike.status,
            notes: bike.notes || '',
        });
    }, []);

    const handleSubmit = useCallback(async (e: React.FormEvent) => {
        e.preventDefault();
        setIsLoading(true);

        try {
            const url = editingBike ? `/api/bikes/${editingBike.id}` : '/api/bikes';
            const method = editingBike ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify(formData),
            });

            if (response.ok) {
                const updatedBike = await response.json();

                if (editingBike) {
                    setBikes(prev => prev.map(b => b.id === updatedBike.id ? updatedBike : b));
                } else {
                    setBikes(prev => [...prev, updatedBike]);
                }

                resetForm();
            }
        } catch (error) {
            console.error('Erreur:', error);
        } finally {
            setIsLoading(false);
        }
    }, [editingBike, formData, resetForm]);

    const handleDelete = useCallback(async (bikeId: number) => {
        if (!confirm('Supprimer ce velo ?')) {
            return;
        }

        setIsLoading(true);

        try {
            const response = await fetch(`/api/bikes/${bikeId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
            });

            if (response.ok) {
                setBikes(prev => prev.filter(b => b.id !== bikeId));
                if (editingBike?.id === bikeId) {
                    resetForm();
                }
            }
        } catch (error) {
            console.error('Erreur:', error);
        } finally {
            setIsLoading(false);
        }
    }, [editingBike, resetForm]);

    const handleToggleStatus = useCallback(async (bike: Bike) => {
        const newStatus = bike.status === 'OK' ? 'HS' : 'OK';

        try {
            const response = await fetch(`/api/bikes/${bike.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({ status: newStatus }),
            });

            if (response.ok) {
                const updatedBike = await response.json();
                setBikes(prev => prev.map(b => b.id === updatedBike.id ? updatedBike : b));
            }
        } catch (error) {
            console.error('Erreur:', error);
        }
    }, []);

    // Grouper les velos par categorie puis par taille
    const bikesByCategory = useMemo(() => {
        const grouped: Record<string, Record<string, Bike[]>> = {};

        for (const category of CATEGORIES) {
            grouped[category] = {};
            for (const size of SIZES) {
                grouped[category][size] = [];
            }
        }

        for (const bike of bikes) {
            if (grouped[bike.category] && grouped[bike.category][bike.size]) {
                grouped[bike.category][bike.size].push(bike);
            }
        }

        return grouped;
    }, [bikes]);

    // Stats
    const totalBikes = bikes.length;
    const okBikes = bikes.filter(b => b.status === 'OK').length;
    const hsBikes = bikes.filter(b => b.status === 'HS').length;

    const getFrameLabel = (frameType: string) => frameType === 'b' ? 'bas' : 'haut';

    return (
        <MainLayout>
            <Head title="Gestion des velos" />

            <div className="bikes-page">
                <div className="bikes-page__header">
                    <h1 className="bikes-page__title">Gestion des velos</h1>
                    <div className="bikes-page__stats">
                        <span className="bikes-page__stat">{totalBikes} velos</span>
                        <span className="bikes-page__stat bikes-page__stat--ok">{okBikes} OK</span>
                        <span className="bikes-page__stat bikes-page__stat--hs">{hsBikes} HS</span>
                    </div>
                    <button
                        type="button"
                        className="bikes-page__btn bikes-page__btn--primary"
                        onClick={handleCreate}
                    >
                        Ajouter un velo
                    </button>
                </div>

                <div className="bikes-page__content">
                    <div className="bikes-page__list">
                        {CATEGORIES.map(category => {
                            const categoryBikes = bikes.filter(b => b.category === category);
                            if (categoryBikes.length === 0) return null;

                            return (
                                <div key={category} className="bikes-category">
                                    <h2 className="bikes-category__title">
                                        {category}
                                        <span className="bikes-category__count">{categoryBikes.length}</span>
                                    </h2>

                                    {SIZES.map(size => {
                                        const sizeBikes = bikesByCategory[category][size];
                                        if (sizeBikes.length === 0) return null;

                                        return (
                                            <div key={`${category}-${size}`} className="bikes-group">
                                                <h3 className="bikes-group__title">
                                                    Taille {size}
                                                    <span className="bikes-group__count">{sizeBikes.length}</span>
                                                </h3>
                                                <div className="bikes-group__items">
                                                    {sizeBikes.map(bike => (
                                                        <div
                                                            key={bike.id}
                                                            className={`bike-card ${bike.status === 'HS' ? 'bike-card--hs' : ''} ${editingBike?.id === bike.id ? 'bike-card--editing' : ''}`}
                                                        >
                                                            <div className="bike-card__main">
                                                                <span className="bike-card__label">{bike.name}</span>
                                                                <span className="bike-card__type">
                                                                    {getFrameLabel(bike.frame_type)}
                                                                </span>
                                                                <button
                                                                    type="button"
                                                                    className={`bike-card__status ${bike.status === 'OK' ? 'bike-card__status--ok' : 'bike-card__status--hs'}`}
                                                                    onClick={() => handleToggleStatus(bike)}
                                                                    title="Cliquer pour changer le statut"
                                                                >
                                                                    {bike.status}
                                                                </button>
                                                            </div>
                                                            {bike.notes && (
                                                                <p className="bike-card__notes">{bike.notes}</p>
                                                            )}
                                                            <div className="bike-card__actions">
                                                                <button
                                                                    type="button"
                                                                    className="bike-card__action"
                                                                    onClick={() => handleEdit(bike)}
                                                                >
                                                                    Modifier
                                                                </button>
                                                                <button
                                                                    type="button"
                                                                    className="bike-card__action bike-card__action--danger"
                                                                    onClick={() => handleDelete(bike.id)}
                                                                >
                                                                    Supprimer
                                                                </button>
                                                            </div>
                                                        </div>
                                                    ))}
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            );
                        })}

                        {bikes.length === 0 && (
                            <div className="bikes-page__empty">
                                <p>Aucun velo enregistre.</p>
                                <button
                                    type="button"
                                    className="bikes-page__btn bikes-page__btn--primary"
                                    onClick={handleCreate}
                                >
                                    Ajouter le premier velo
                                </button>
                            </div>
                        )}
                    </div>

                    {(isCreating || editingBike) && (
                        <div className="bikes-page__form-panel">
                            <form onSubmit={handleSubmit} className="bike-form">
                                <h3 className="bike-form__title">
                                    {editingBike ? `Modifier ${editingBike.name}` : 'Nouveau velo'}
                                </h3>

                                <div className="bike-form__field">
                                    <label htmlFor="name">Nom du velo *</label>
                                    <input
                                        type="text"
                                        id="name"
                                        value={formData.name}
                                        onChange={e => setFormData(prev => ({ ...prev, name: e.target.value }))}
                                        placeholder="Ex: VAE-M-1, VTC Bleu..."
                                        required
                                    />
                                </div>

                                <div className="bike-form__row">
                                    <div className="bike-form__field">
                                        <label htmlFor="category">Categorie</label>
                                        <select
                                            id="category"
                                            value={formData.category}
                                            onChange={e => setFormData(prev => ({ ...prev, category: e.target.value as 'VAE' | 'VTC' }))}
                                            required
                                        >
                                            {CATEGORIES.map(cat => (
                                                <option key={cat} value={cat}>{cat}</option>
                                            ))}
                                        </select>
                                    </div>

                                    <div className="bike-form__field">
                                        <label htmlFor="size">Taille</label>
                                        <select
                                            id="size"
                                            value={formData.size}
                                            onChange={e => setFormData(prev => ({ ...prev, size: e.target.value as 'S' | 'M' | 'L' | 'XL' }))}
                                            required
                                        >
                                            {SIZES.map(s => (
                                                <option key={s} value={s}>{s}</option>
                                            ))}
                                        </select>
                                    </div>
                                </div>

                                <div className="bike-form__row">
                                    <div className="bike-form__field">
                                        <label htmlFor="frame_type">Type de cadre</label>
                                        <select
                                            id="frame_type"
                                            value={formData.frame_type}
                                            onChange={e => setFormData(prev => ({ ...prev, frame_type: e.target.value as 'b' | 'h' }))}
                                            required
                                        >
                                            {FRAME_TYPES.map(ft => (
                                                <option key={ft.value} value={ft.value}>{ft.label}</option>
                                            ))}
                                        </select>
                                    </div>

                                    <div className="bike-form__field">
                                        <label htmlFor="status">Statut</label>
                                        <select
                                            id="status"
                                            value={formData.status}
                                            onChange={e => setFormData(prev => ({ ...prev, status: e.target.value as 'OK' | 'HS' }))}
                                        >
                                            <option value="OK">OK - Operationnel</option>
                                            <option value="HS">HS - Hors service</option>
                                        </select>
                                    </div>
                                </div>

                                <div className="bike-form__field">
                                    <label htmlFor="notes">Notes</label>
                                    <textarea
                                        id="notes"
                                        value={formData.notes}
                                        onChange={e => setFormData(prev => ({ ...prev, notes: e.target.value }))}
                                        placeholder="Remarques, problemes connus..."
                                        rows={3}
                                    />
                                </div>

                                <div className="bike-form__actions">
                                    <button
                                        type="button"
                                        className="bikes-page__btn bikes-page__btn--secondary"
                                        onClick={resetForm}
                                    >
                                        Annuler
                                    </button>
                                    <button
                                        type="submit"
                                        className="bikes-page__btn bikes-page__btn--primary"
                                        disabled={isLoading}
                                    >
                                        {isLoading ? 'Enregistrement...' : (editingBike ? 'Mettre a jour' : 'Creer')}
                                    </button>
                                </div>
                            </form>
                        </div>
                    )}
                </div>
            </div>
        </MainLayout>
    );
}
