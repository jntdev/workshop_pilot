import { useState, useCallback } from 'react';
import { Head, Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import type { BikeCategoryRef, BikeSizeRef, Article } from '@/types';
import ArticleAutocomplete from '@/Components/Stock/ArticleAutocomplete';
import CataloguePickerModal from '@/Components/Stock/CataloguePickerModal';

interface MaintenanceLog {
    id: number;
    date: string;
    description: string;
    reference: string | null;
    article_id: number | null;
    cost: number | null;
    duration_minutes: number | null;
    status: 'todo' | 'done';
    needs_order: boolean;
    ordered_at: string | null;
    received_at: string | null;
    supply_status: 'to_order' | 'ordered' | 'received';
}

interface Bike {
    id: number;
    name: string;
    status: 'OK' | 'HS';
    frame_type: 'b' | 'h' | null;
    model: string | null;
    battery_type: string | null;
    notes: string | null;
    category: BikeCategoryRef;
    size: BikeSizeRef | null;
    maintenance_logs: MaintenanceLog[];
}

interface PageProps {
    bike: Bike;
}

const CSRF = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

function formatCost(cents: number | null): string {
    if (cents === null) return '—';
    return new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(cents / 100) + ' €';
}

function formatDuration(minutes: number | null): string {
    if (minutes === null) return '—';
    if (minutes < 60) return `${minutes} min`;
    const h = Math.floor(minutes / 60);
    const m = minutes % 60;
    return m > 0 ? `${h}h${String(m).padStart(2, '0')}` : `${h}h`;
}

function formatDate(iso: string): string {
    return new Date(iso).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

const emptyForm = {
    date: new Date().toISOString().slice(0, 10),
    description: '',
    reference: '',
    article_id: null as number | null,
    cost: '',
    duration_minutes: '',
    status: 'todo' as 'todo' | 'done',
    needs_order: false,
};

export default function BikeShow({ bike: initialBike }: PageProps) {
    const [logs, setLogs] = useState<MaintenanceLog[]>(initialBike.maintenance_logs);
    const [form, setForm] = useState(emptyForm);
    const [editingId, setEditingId] = useState<number | null>(null);
    const [isLoading, setIsLoading] = useState(false);
    const [showCataloguePicker, setShowCataloguePicker] = useState(false);

    const todoLogs = logs.filter(l => l.status === 'todo');
    const doneLogs = logs.filter(l => l.status === 'done');
    const totalCost = doneLogs.reduce((sum, l) => sum + (l.cost ?? 0), 0);
    const totalDuration = doneLogs.reduce((sum, l) => sum + (l.duration_minutes ?? 0), 0);

    const handleEdit = useCallback((log: MaintenanceLog) => {
        setEditingId(log.id);
        setForm({
            date: log.date.slice(0, 10),
            description: log.description,
            reference: log.reference ?? '',
            article_id: log.article_id ?? null,
            cost: log.cost !== null ? String(log.cost / 100) : '',
            duration_minutes: log.duration_minutes !== null ? String(log.duration_minutes) : '',
            status: log.status,
            needs_order: log.needs_order,
        });
    }, []);

    const handleArticleSelect = useCallback((article: Article) => {
        setForm(p => ({
            ...p,
            article_id: article.id,
            reference: article.reference,
            cost: p.cost || String(article.sale_price_ttc / 100),
        }));
        setShowCataloguePicker(false);
    }, []);

    const handleCancel = useCallback(() => {
        setEditingId(null);
        setForm(emptyForm);
    }, []);

    const handleSubmit = useCallback(async (e: React.FormEvent) => {
        e.preventDefault();
        setIsLoading(true);

        const payload = {
            date: form.date,
            description: form.description,
            reference: form.reference || null,
            article_id: form.article_id ?? null,
            cost: form.cost !== '' ? Math.round(parseFloat(form.cost) * 100) : null,
            duration_minutes: form.duration_minutes !== '' ? parseInt(form.duration_minutes) : null,
            status: form.status,
            needs_order: form.needs_order,
        };

        try {
            const url = editingId
                ? `/api/bikes/${initialBike.id}/maintenance/${editingId}`
                : `/api/bikes/${initialBike.id}/maintenance`;

            const response = await fetch(url, {
                method: editingId ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF() },
                body: JSON.stringify(payload),
            });

            if (response.ok) {
                const saved: MaintenanceLog = await response.json();
                if (editingId) {
                    setLogs(prev => prev.map(l => l.id === saved.id ? saved : l));
                } else {
                    setLogs(prev => [saved, ...prev]);
                }
                handleCancel();
            }
        } finally {
            setIsLoading(false);
        }
    }, [editingId, form, initialBike.id, handleCancel]);

    const handleDelete = useCallback(async (logId: number) => {
        if (!confirm('Supprimer cette entrée ?')) { return; }
        const response = await fetch(`/api/bikes/${initialBike.id}/maintenance/${logId}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF() },
        });
        if (response.ok) {
            setLogs(prev => prev.filter(l => l.id !== logId));
        }
    }, [initialBike.id]);

    const handleToggleStatus = useCallback(async (log: MaintenanceLog) => {
        const newStatus = log.status === 'todo' ? 'done' : 'todo';
        const response = await fetch(`/api/bikes/${initialBike.id}/maintenance/${log.id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF() },
            body: JSON.stringify({ status: newStatus }),
        });
        if (response.ok) {
            const saved: MaintenanceLog = await response.json();
            setLogs(prev => prev.map(l => l.id === saved.id ? saved : l));
        }
    }, [initialBike.id]);

    const frameLabel = initialBike.frame_type === 'b' ? 'Cadre bas' : initialBike.frame_type === 'h' ? 'Cadre haut' : null;

    const supplyBadge = (log: MaintenanceLog) => {
        if (!log.needs_order) { return null; }
        const map = { to_order: { label: 'À commander', cls: 'to-order' }, ordered: { label: 'Commandée', cls: 'ordered' }, received: { label: 'Reçue', cls: 'received' } };
        const s = map[log.supply_status];
        return <span className={`mlog-row__supply mlog-row__supply--${s.cls}`}>{s.label}</span>;
    };

    const renderLogRow = (log: MaintenanceLog) => (
        <div key={log.id} className={`mlog-row ${log.status === 'done' ? 'mlog-row--done' : ''}`}>
            <span className="mlog-row__date">{formatDate(log.date)}</span>
            <div className="mlog-row__desc-wrap">
                <span className="mlog-row__desc">{log.description}</span>
                {log.reference && <span className="mlog-row__ref">{log.reference}</span>}
                {supplyBadge(log)}
            </div>
            <span className="mlog-row__cost">{formatCost(log.cost)}</span>
            <span className="mlog-row__duration">{formatDuration(log.duration_minutes)}</span>
            <div className="mlog-row__actions">
                <button type="button" className="mlog-row__toggle" onClick={() => handleToggleStatus(log)} title={log.status === 'todo' ? 'Marquer fait' : 'Remettre à faire'}>
                    {log.status === 'todo' ? '✓' : '↩'}
                </button>
                <button type="button" className="mlog-row__action" onClick={() => handleEdit(log)}>Modifier</button>
                <button type="button" className="mlog-row__action mlog-row__action--danger" onClick={() => handleDelete(log.id)}>Supprimer</button>
            </div>
        </div>
    );

    return (
        <MainLayout>
            <Head title={`Fiche – ${initialBike.name}`} />

            <div className="bike-show">
                <div className="bike-show__header">
                    <Link href="/bikes" className="bike-show__back">← Vélos</Link>
                    <div className="bike-show__identity">
                        <h1 className="bike-show__name">{initialBike.name}</h1>
                        <span className="bike-show__meta">
                            {initialBike.category.name}
                            {initialBike.size && ` · ${initialBike.size.name}`}
                            {frameLabel && ` · ${frameLabel}`}
                            {initialBike.model && ` · ${initialBike.model}`}
                        </span>
                    </div>
                    <span className={`bike-show__status bike-show__status--${initialBike.status.toLowerCase()}`}>
                        {initialBike.status}
                    </span>
                </div>

                {initialBike.notes && (
                    <p className="bike-show__notes">{initialBike.notes}</p>
                )}

                {/* Formulaire */}
                <div className="bike-show__section">
                    <h2 className="bike-show__section-title">
                        {editingId ? 'Modifier une entrée' : 'Ajouter une entrée'}
                    </h2>
                    <form onSubmit={handleSubmit} className="mlog-form">
                        <input type="date" value={form.date} onChange={e => setForm(p => ({ ...p, date: e.target.value }))} required className="mlog-form__date" />
                        <input type="text" value={form.description} onChange={e => setForm(p => ({ ...p, description: e.target.value }))} placeholder="Description des travaux" required className="mlog-form__desc" />
                        <div className="mlog-form__ref-wrap">
                            <ArticleAutocomplete
                                value={form.reference}
                                articleId={form.article_id}
                                onChange={v => setForm(p => ({ ...p, reference: v, article_id: null }))}
                                onSelect={handleArticleSelect}
                                onDetach={() => setForm(p => ({ ...p, article_id: null }))}
                                placeholder="Référence..."
                            />
                            <button type="button" className="mlog-form__catalogue-btn" onClick={() => setShowCataloguePicker(true)} title="Catalogue">📋</button>
                        </div>
                        <input type="number" value={form.cost} onChange={e => setForm(p => ({ ...p, cost: e.target.value }))} placeholder="Coût (€)" min="0" step="0.01" className="mlog-form__cost" />
                        <input type="number" value={form.duration_minutes} onChange={e => setForm(p => ({ ...p, duration_minutes: e.target.value }))} placeholder="Durée (min)" min="1" className="mlog-form__duration" />
                        <select value={form.status} onChange={e => setForm(p => ({ ...p, status: e.target.value as 'todo' | 'done' }))} className="mlog-form__status">
                            <option value="todo">À faire</option>
                            <option value="done">Fait</option>
                        </select>
                        <label className="mlog-form__order-label">
                            <input type="checkbox" checked={form.needs_order} onChange={e => setForm(p => ({ ...p, needs_order: e.target.checked }))} />
                            À commander
                        </label>
                        <div className="mlog-form__actions">
                            <button type="submit" disabled={isLoading} className="mlog-form__btn mlog-form__btn--primary">
                                {isLoading ? '...' : (editingId ? 'Mettre à jour' : 'Ajouter')}
                            </button>
                            {editingId && (
                                <button type="button" onClick={handleCancel} className="mlog-form__btn mlog-form__btn--secondary">Annuler</button>
                            )}
                        </div>
                    </form>
                </div>

                {/* Travaux à faire */}
                {todoLogs.length > 0 && (
                    <div className="bike-show__section">
                        <h2 className="bike-show__section-title">À faire <span className="bike-show__count">{todoLogs.length}</span></h2>
                        <div className="mlog-table">
                            <div className="mlog-table__header">
                                <span>Date</span><span>Description</span><span>Coût</span><span>Durée</span><span />
                            </div>
                            {todoLogs.map(renderLogRow)}
                        </div>
                    </div>
                )}

                {/* Historique */}
                <div className="bike-show__section">
                    <h2 className="bike-show__section-title">Historique <span className="bike-show__count">{doneLogs.length}</span></h2>
                    {doneLogs.length === 0 ? (
                        <p className="bike-show__empty">Aucun travail effectué pour l'instant.</p>
                    ) : (
                        <div className="mlog-table">
                            <div className="mlog-table__header">
                                <span>Date</span><span>Description</span><span>Coût</span><span>Durée</span><span />
                            </div>
                            {doneLogs.map(renderLogRow)}
                            <div className="mlog-table__footer">
                                <span /><span>Total</span><span>{formatCost(totalCost)}</span><span>{formatDuration(totalDuration)}</span><span />
                            </div>
                        </div>
                    )}
                </div>
            </div>
            {showCataloguePicker && (
                <CataloguePickerModal
                    onSelect={handleArticleSelect}
                    onClose={() => setShowCataloguePicker(false)}
                />
            )}
        </MainLayout>
    );
}
