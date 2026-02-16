import { Head } from '@inertiajs/react';
import { useState, useCallback } from 'react';
import MainLayout from '@/Layouts/MainLayout';

interface BikeType {
    id: string;
    category: string;
    size: string;
    frame_type: string;
    label: string;
    stock: number;
}

interface Bike {
    id: number;
    bike_type_id: string;
    label: string;
    status: 'OK' | 'HS';
    notes: string | null;
    sort_order: number;
    bike_type?: BikeType;
}

interface PageProps {
    bikes: Bike[];
    bikeTypes: BikeType[];
}

export default function BikesIndex({ bikes: initialBikes, bikeTypes }: PageProps) {
    const [bikes, setBikes] = useState<Bike[]>(initialBikes);
    const [editingBike, setEditingBike] = useState<Bike | null>(null);
    const [isCreating, setIsCreating] = useState(false);
    const [formData, setFormData] = useState({
        bike_type_id: '',
        label: '',
        status: 'OK' as 'OK' | 'HS',
        notes: '',
    });
    const [isLoading, setIsLoading] = useState(false);

    const resetForm = useCallback(() => {
        setFormData({
            bike_type_id: bikeTypes[0]?.id || '',
            label: '',
            status: 'OK',
            notes: '',
        });
        setEditingBike(null);
        setIsCreating(false);
    }, [bikeTypes]);

    const handleCreate = useCallback(() => {
        setIsCreating(true);
        setEditingBike(null);
        setFormData({
            bike_type_id: bikeTypes[0]?.id || '',
            label: '',
            status: 'OK',
            notes: '',
        });
    }, [bikeTypes]);

    const handleEdit = useCallback((bike: Bike) => {
        setEditingBike(bike);
        setIsCreating(false);
        setFormData({
            bike_type_id: bike.bike_type_id,
            label: bike.label,
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

    // Grouper les velos par type
    const bikesByType = bikes.reduce((acc, bike) => {
        const typeId = bike.bike_type_id;
        if (!acc[typeId]) {
            acc[typeId] = [];
        }
        acc[typeId].push(bike);
        return acc;
    }, {} as Record<string, Bike[]>);

    // Stats
    const totalBikes = bikes.length;
    const okBikes = bikes.filter(b => b.status === 'OK').length;
    const hsBikes = bikes.filter(b => b.status === 'HS').length;

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
                        {bikeTypes.map(type => {
                            const typeBikes = bikesByType[type.id] || [];
                            if (typeBikes.length === 0) return null;

                            return (
                                <div key={type.id} className="bikes-group">
                                    <h2 className="bikes-group__title">
                                        {type.label}
                                        <span className="bikes-group__count">{typeBikes.length}</span>
                                    </h2>
                                    <div className="bikes-group__items">
                                        {typeBikes.map(bike => (
                                            <div
                                                key={bike.id}
                                                className={`bike-card ${bike.status === 'HS' ? 'bike-card--hs' : ''} ${editingBike?.id === bike.id ? 'bike-card--editing' : ''}`}
                                            >
                                                <div className="bike-card__main">
                                                    <span className="bike-card__label">{bike.label}</span>
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

                    {(isCreating || editingBike) && (
                        <div className="bikes-page__form-panel">
                            <form onSubmit={handleSubmit} className="bike-form">
                                <h3 className="bike-form__title">
                                    {editingBike ? `Modifier ${editingBike.label}` : 'Nouveau velo'}
                                </h3>

                                <div className="bike-form__field">
                                    <label htmlFor="bike_type_id">Type de velo</label>
                                    <select
                                        id="bike_type_id"
                                        value={formData.bike_type_id}
                                        onChange={e => setFormData(prev => ({ ...prev, bike_type_id: e.target.value }))}
                                        required
                                    >
                                        {bikeTypes.map(type => (
                                            <option key={type.id} value={type.id}>
                                                {type.label}
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                <div className="bike-form__field">
                                    <label htmlFor="label">Nom du velo</label>
                                    <input
                                        type="text"
                                        id="label"
                                        value={formData.label}
                                        onChange={e => setFormData(prev => ({ ...prev, label: e.target.value }))}
                                        placeholder="Ex: VAE M-7"
                                        required
                                    />
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
