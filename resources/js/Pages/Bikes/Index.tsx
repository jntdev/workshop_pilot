import { Head } from '@inertiajs/react';
import { useState, useCallback, useMemo, useEffect } from 'react';
import MainLayout from '@/Layouts/MainLayout';
import type { BikeCategoryRef, BikeSizeRef } from '@/types';

interface OrderLine {
    id: number;
    bike_id: number;
    bike_name: string;
    bike_type: string;
    description: string;
    reference: string | null;
    cost: number | null;
    supply_status: 'to_order' | 'ordered' | 'received';
    ordered_at: string | null;
    received_at: string | null;
}

interface Bike {
    id: number;
    bike_category_id: number;
    bike_size_id: number | null;
    category: BikeCategoryRef;
    size: BikeSizeRef | null;
    frame_type: 'b' | 'h' | null;
    model: '500' | '625' | 'autre' | null;
    battery_type: 'rack' | 'gourde' | 'rail' | null;
    name: string;
    status: 'OK' | 'HS';
    notes: string | null;
    sort_order: number;
    pending_maintenance_count: number;
}

interface PageProps {
    bikes: Bike[];
    categories: BikeCategoryRef[];
    sizes: BikeSizeRef[];
}

const FRAME_TYPES = [
    { value: 'b', label: 'Cadre bas' },
    { value: 'h', label: 'Cadre haut' },
] as const;

const MODELS = [
    { value: '500', label: '500' },
    { value: '625', label: '625' },
    { value: 'autre', label: 'Autre' },
] as const;

const BATTERY_TYPES = [
    { value: 'rack', label: 'Rack' },
    { value: 'gourde', label: 'Gourde' },
    { value: 'rail', label: 'Rail' },
] as const;

export default function BikesIndex({ bikes: initialBikes, categories, sizes }: PageProps) {
    const [bikes, setBikes] = useState<Bike[]>(initialBikes || []);
    const [editingBike, setEditingBike] = useState<Bike | null>(null);
    const [isCreating, setIsCreating] = useState(false);

    const defaultCategoryId = categories[0]?.id ?? 0;
    const defaultSizeId = sizes[1]?.id ?? sizes[0]?.id ?? 0; // M par defaut, sinon premier

    const [formData, setFormData] = useState({
        bike_category_id: defaultCategoryId,
        bike_size_id: defaultSizeId as number | null,
        frame_type: 'b' as 'b' | 'h' | null,
        model: '500' as '500' | '625' | 'autre' | null,
        battery_type: 'rack' as 'rack' | 'gourde' | 'rail' | null,
        name: '',
        status: 'OK' as 'OK' | 'HS',
        notes: '',
    });
    const [isLoading, setIsLoading] = useState(false);
    const [orderLines, setOrderLines] = useState<OrderLine[]>([]);
    const [pendingOrderIds, setPendingOrderIds] = useState<Set<number>>(new Set());

    const CSRF = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    useEffect(() => {
        fetch('/api/bikes/maintenance/order-lines', { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(setOrderLines)
            .catch(() => {});
    }, []);

    const updateOrderStatus = useCallback(async (id: number, action: 'mark_as_ordered' | 'mark_as_received' | 'unmark') => {
        if (pendingOrderIds.has(id)) { return; }
        setPendingOrderIds(prev => new Set(prev).add(id));
        try {
            const response = await fetch(`/api/bikes/maintenance/${id}/order-status`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF() },
                body: JSON.stringify({ [action]: true }),
            });
            if (response.ok) {
                const updated = await response.json();
                setOrderLines(prev => prev.map(l => l.id === id ? { ...l, ...updated } : l).filter(l => l.supply_status !== 'received'));
            }
        } finally {
            setPendingOrderIds(prev => { const s = new Set(prev); s.delete(id); return s; });
        }
    }, [pendingOrderIds]);

    const selectedCategory = useMemo(
        () => categories.find(c => c.id === formData.bike_category_id),
        [categories, formData.bike_category_id]
    );

    const resetForm = useCallback(() => {
        const defaultCategory = categories[0];
        setFormData({
            bike_category_id: defaultCategoryId,
            bike_size_id: defaultCategory?.has_size !== false ? defaultSizeId : null,
            frame_type: defaultCategory?.has_frame_type !== false ? 'b' : null,
            model: '500',
            battery_type: defaultCategory?.has_battery ? 'rack' : null,
            name: '',
            status: 'OK',
            notes: '',
        });
        setEditingBike(null);
        setIsCreating(false);
    }, [defaultCategoryId, defaultSizeId, categories]);

    const handleCreate = useCallback(() => {
        const defaultCategory = categories[0];
        setIsCreating(true);
        setEditingBike(null);
        setFormData({
            bike_category_id: defaultCategoryId,
            bike_size_id: defaultCategory?.has_size !== false ? defaultSizeId : null,
            frame_type: defaultCategory?.has_frame_type !== false ? 'b' : null,
            model: '500',
            battery_type: defaultCategory?.has_battery ? 'rack' : null,
            name: '',
            status: 'OK',
            notes: '',
        });
    }, [defaultCategoryId, defaultSizeId, categories]);

    const handleEdit = useCallback((bike: Bike) => {
        setEditingBike(bike);
        setIsCreating(false);
        setFormData({
            bike_category_id: bike.bike_category_id,
            bike_size_id: bike.bike_size_id,
            frame_type: bike.frame_type,
            model: bike.model,
            battery_type: bike.battery_type,
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

            const payload = {
                ...formData,
                bike_size_id: selectedCategory?.has_size !== false ? formData.bike_size_id : null,
                frame_type: selectedCategory?.has_frame_type !== false ? formData.frame_type : null,
                battery_type: selectedCategory?.has_battery ? formData.battery_type : null,
            };

            const response = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify(payload),
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
    }, [editingBike, formData, resetForm, selectedCategory]);

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
        const grouped: Record<number, Record<number | 0, Bike[]>> = {};

        for (const category of categories) {
            grouped[category.id] = {};
            if (category.has_size !== false) {
                for (const size of sizes) {
                    grouped[category.id][size.id] = [];
                }
            } else {
                grouped[category.id][0] = [];
            }
        }

        for (const bike of bikes) {
            const catGroup = grouped[bike.bike_category_id];
            if (!catGroup) continue;
            const sizeKey = bike.bike_size_id ?? 0;
            if (!catGroup[sizeKey]) {
                catGroup[sizeKey] = [];
            }
            catGroup[sizeKey].push(bike);
        }

        return grouped;
    }, [bikes, categories, sizes]);

    // Stats
    const totalBikes = bikes.length;
    const okBikes = bikes.filter(b => b.status === 'OK').length;
    const hsBikes = bikes.filter(b => b.status === 'HS').length;

    const getFrameLabel = (frameType: string | null) => {
        if (!frameType) return '';
        return frameType === 'b' ? 'bas' : 'haut';
    };

    const renderBikeCard = (bike: Bike) => (
        <div
            key={bike.id}
            className={`bike-card ${bike.status === 'HS' ? 'bike-card--hs' : ''} ${editingBike?.id === bike.id ? 'bike-card--editing' : ''}`}
        >
            <div className="bike-card__main">
                <span className="bike-card__label">{bike.name}</span>
                {bike.frame_type && (
                    <span className="bike-card__type">{getFrameLabel(bike.frame_type)}</span>
                )}
                {bike.pending_maintenance_count > 0 && (
                    <span className="bike-card__maintenance-badge" title={`${bike.pending_maintenance_count} travail(aux) à faire`}>
                        {bike.pending_maintenance_count}
                    </span>
                )}
            </div>
            {bike.notes && <p className="bike-card__notes">{bike.notes}</p>}
            <button
                type="button"
                className={`bike-card__status ${bike.status === 'OK' ? 'bike-card__status--ok' : 'bike-card__status--hs'}`}
                onClick={() => handleToggleStatus(bike)}
                title="Cliquer pour changer le statut"
            >
                {bike.status}
            </button>
            <div className="bike-card__actions">
                <a href={`/bikes/${bike.id}`} className="bike-card__action">Fiche</a>
                <button type="button" className="bike-card__action" onClick={() => handleEdit(bike)}>Modifier</button>
                <button type="button" className="bike-card__action bike-card__action--danger" onClick={() => handleDelete(bike.id)}>Supprimer</button>
            </div>
        </div>
    );

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

                <div className="bikes-page__content bikes-page__content--with-panel">
                    <div className="bikes-page__list">
                        {categories.map(category => {
                            const categoryBikes = bikes.filter(b => b.bike_category_id === category.id);
                            if (categoryBikes.length === 0) return null;

                            return (
                                <div key={category.id} className="bikes-category">
                                    <h2 className="bikes-category__title" style={{ borderLeftColor: category.color }}>
                                        {category.name}
                                        <span className="bikes-category__count">{categoryBikes.length}</span>
                                    </h2>

                                    {category.has_size !== false ? (
                                        sizes.map(size => {
                                            const sizeBikes = bikesByCategory[category.id]?.[size.id] || [];
                                            if (sizeBikes.length === 0) return null;

                                            return (
                                                <div key={`${category.id}-${size.id}`} className="bikes-group">
                                                    <h3 className="bikes-group__title" style={{ borderLeftColor: size.color }}>
                                                        Taille {size.name}
                                                        <span className="bikes-group__count">{sizeBikes.length}</span>
                                                    </h3>
                                                    <div className="bikes-group__items">
                                                        {sizeBikes.map(renderBikeCard)}
                                                    </div>
                                                </div>
                                            );
                                        })
                                    ) : (
                                        <div className="bikes-group">
                                            <div className="bikes-group__items">
                                                {(bikesByCategory[category.id]?.[0] || []).map(renderBikeCard)}
                                            </div>
                                        </div>
                                    )}
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

                    {/* Panneau pièces à commander */}
                    <div className="bikes-page__order-panel">
                        <h2 className="bikes-page__order-title">Pièces à commander <span className="bikes-page__stat">{orderLines.length}</span></h2>
                        {orderLines.length === 0 ? (
                            <p className="bikes-page__order-empty">Aucune pièce à commander.</p>
                        ) : (
                            <div className="order-lines">
                                {orderLines.map(line => (
                                    <div key={line.id} className="order-line">
                                        <div className="order-line__bike">
                                            <a href={`/bikes/${line.bike_id}`} className="order-line__bike-name">{line.bike_name}</a>
                                            <span className="order-line__bike-type">{line.bike_type}</span>
                                        </div>
                                        <div className="order-line__desc">
                                            <span>{line.description}</span>
                                            {line.reference && <span className="order-line__ref">{line.reference}</span>}
                                        </div>
                                        <span className={`order-line__badge order-line__badge--${line.supply_status === 'to_order' ? 'to-order' : 'ordered'}`}>
                                            {line.supply_status === 'to_order' ? 'À commander' : 'Commandée'}
                                        </span>
                                        <div className="order-line__actions">
                                            {line.supply_status === 'to_order' && (
                                                <button type="button" disabled={pendingOrderIds.has(line.id)} onClick={() => updateOrderStatus(line.id, 'mark_as_ordered')} className="order-line__btn">Commandée</button>
                                            )}
                                            {line.supply_status === 'ordered' && (
                                                <>
                                                    <button type="button" disabled={pendingOrderIds.has(line.id)} onClick={() => updateOrderStatus(line.id, 'mark_as_received')} className="order-line__btn order-line__btn--success">Reçue</button>
                                                    <button type="button" disabled={pendingOrderIds.has(line.id)} onClick={() => updateOrderStatus(line.id, 'unmark')} className="order-line__btn order-line__btn--ghost">Annuler</button>
                                                </>
                                            )}
                                        </div>
                                    </div>
                                ))}
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
                                            value={formData.bike_category_id}
                                            onChange={e => {
                                                const newCategoryId = Number(e.target.value);
                                                const newCategory = categories.find(c => c.id === newCategoryId);
                                                setFormData(prev => ({
                                                    ...prev,
                                                    bike_category_id: newCategoryId,
                                                    bike_size_id: newCategory?.has_size !== false ? (prev.bike_size_id || defaultSizeId) : null,
                                                    frame_type: newCategory?.has_frame_type !== false ? (prev.frame_type || 'b') : null,
                                                    battery_type: newCategory?.has_battery ? (prev.battery_type || 'rack') : null,
                                                }));
                                            }}
                                            required
                                        >
                                            {categories.map(cat => (
                                                <option key={cat.id} value={cat.id}>{cat.name}</option>
                                            ))}
                                        </select>
                                    </div>

                                    {selectedCategory?.has_size !== false && (
                                        <div className="bike-form__field">
                                            <label htmlFor="size">Taille</label>
                                            <select
                                                id="size"
                                                value={formData.bike_size_id ?? ''}
                                                onChange={e => setFormData(prev => ({ ...prev, bike_size_id: Number(e.target.value) }))}
                                                required
                                            >
                                                {sizes.map(s => (
                                                    <option key={s.id} value={s.id}>{s.name}</option>
                                                ))}
                                            </select>
                                        </div>
                                    )}
                                </div>

                                <div className="bike-form__row">
                                    {selectedCategory?.has_frame_type !== false && (
                                        <div className="bike-form__field">
                                            <label htmlFor="frame_type">Type de cadre</label>
                                            <select
                                                id="frame_type"
                                                value={formData.frame_type ?? ''}
                                                onChange={e => setFormData(prev => ({ ...prev, frame_type: e.target.value as 'b' | 'h' }))}
                                                required
                                            >
                                                {FRAME_TYPES.map(ft => (
                                                    <option key={ft.value} value={ft.value}>{ft.label}</option>
                                                ))}
                                            </select>
                                        </div>
                                    )}

                                    <div className="bike-form__field">
                                        <label htmlFor="model">Modele</label>
                                        <select
                                            id="model"
                                            value={formData.model || '500'}
                                            onChange={e => setFormData(prev => ({ ...prev, model: e.target.value as '500' | '625' | 'autre' }))}
                                        >
                                            {MODELS.map(m => (
                                                <option key={m.value} value={m.value}>{m.label}</option>
                                            ))}
                                        </select>
                                    </div>
                                </div>

                                {selectedCategory?.has_battery && (
                                    <div className="bike-form__row">
                                        <div className="bike-form__field">
                                            <label htmlFor="battery_type">Type de batterie</label>
                                            <select
                                                id="battery_type"
                                                value={formData.battery_type || 'rack'}
                                                onChange={e => setFormData(prev => ({ ...prev, battery_type: e.target.value as 'rack' | 'gourde' | 'rail' }))}
                                            >
                                                {BATTERY_TYPES.map(bt => (
                                                    <option key={bt.value} value={bt.value}>{bt.label}</option>
                                                ))}
                                            </select>
                                        </div>
                                    </div>
                                )}

                                <div className="bike-form__row">
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
