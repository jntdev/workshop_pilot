import { Fragment, useState, useMemo, useCallback, useLayoutEffect, useEffect } from 'react';
import { Link, router } from '@inertiajs/react';
import { Quote, QuoteStatusSlug, QuoteCommentRecipient, QuoteTask } from '@/types';
import { EyeIcon, ArchiveIcon, TrashIcon, ChevronIcon } from './QuotesTableIcons';
import PlanningSidePanel from './Agenda/PlanningSidePanel';
import type { DragQuotePayload } from './Agenda/agendaShared';

type TabType = 'quotes' | 'invoices' | 'clients' | 'archives';

interface QuotesTabsProps {
    quotes: Quote[];
    archivedQuotes: Quote[];
    invoices: Quote[];
    onLoadInvoices: () => void;
    invoicesLoaded: boolean;
    activeTab: TabType;
    onTabChange: (tab: TabType) => void;
    planningMode?: boolean;
    onClosePlanningMode?: () => void;
}

const STATUS_LABELS: Record<QuoteStatusSlug, string> = {
    reception: 'Bon de réception',
    to_complete: 'À compléter',
    to_quote: 'À chiffrer',
    pending_validation: 'Attente validation',
    validated: 'Validé',
    quote_to_order: 'À commander',
    quote_ordered: 'En commande',
    quote_received: 'Commande reçue',
    in_progress: 'En cours',
    done: 'Terminé',
    invoiced: 'Facturé',
};

const QUOTE_STATUSES: QuoteStatusSlug[] = [
    'reception',
    'to_complete',
    'to_quote',
    'pending_validation',
    'validated',
    'quote_to_order',
    'quote_ordered',
    'quote_received',
    'in_progress',
    'done',
];

const STATUS_FILTER_LABELS: Record<string, string> = {
    all: 'Tous',
    to_complete: 'À compléter',
    to_quote: 'À chiffrer',
    pending_validation: 'Attente validation',
    validated: 'Validé',
    quote_to_order: 'À commander',
    quote_ordered: 'En commande',
    quote_received: 'Commande reçue',
    in_progress: 'En cours',
    done: 'Terminé',
};

const RECIPIENT_FILTER_LABELS: Record<'all' | QuoteCommentRecipient, string> = {
    all: 'Tous',
    nikal: 'Pour Nikal',
    jal: 'Pour Jal',
};

const getCsrfToken = (): string => {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
};

const STATUS_FILTER_STORAGE_KEY = 'atelier.quotes.statusFilter';

function getStoredStatusFilter(): string {
    const stored = localStorage.getItem(STATUS_FILTER_STORAGE_KEY);
    return stored && (stored === 'all' || stored in STATUS_FILTER_LABELS) ? stored : 'all';
}

const RECIPIENT_FILTER_STORAGE_KEY = 'atelier.quotes.recipientFilter';

function getStoredRecipientFilter(): 'all' | QuoteCommentRecipient {
    const stored = localStorage.getItem(RECIPIENT_FILTER_STORAGE_KEY);
    return stored && stored in RECIPIENT_FILTER_LABELS ? (stored as 'all' | QuoteCommentRecipient) : 'all';
}

const SCROLL_Y_STORAGE_KEY = 'atelier.quotes.scrollY';
const HIGHLIGHTED_QUOTE_STORAGE_KEY = 'atelier.quotes.highlightedId';

function formatCurrency(value: string | number): string {
    const num = typeof value === 'string' ? parseFloat(value) : value;
    return new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(num) + ' €';
}

function formatDate(dateString: string): string {
    return new Date(dateString).toLocaleDateString('fr-FR');
}

interface QuotesTableProps {
    items: Quote[];
    type: 'quotes' | 'invoices' | 'archives';
    onStatusChange?: (quoteId: number, status: QuoteStatusSlug) => void;
    onArchiveToggle?: (quoteId: number) => void;
    highlightedId?: number | null;
    onRowVisit?: (quoteId: number) => void;
    planningMode?: boolean;
}

function QuotesTable({ items, type, onStatusChange, onArchiveToggle, highlightedId, onRowVisit, planningMode }: QuotesTableProps) {
    const [clientNotifiedMap, setClientNotifiedMap] = useState<Record<number, { notified: boolean; date: string }>>({});
    const [workCompletedNotifiedMap, setWorkCompletedNotifiedMap] = useState<Record<number, { notified: boolean; date: string }>>({});

    const handleDelete = (quoteId: number, e: React.FormEvent) => {
        e.preventDefault();
        if (confirm('Voulez-vous vraiment supprimer ce devis ?')) {
            router.delete(`/atelier/devis/${quoteId}`);
        }
    };

    const persistClientNotified = useCallback(async (quoteId: number, notified: boolean, date: string): Promise<boolean> => {
        try {
            const response = await fetch(`/api/quotes/${quoteId}/client-notified`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-XSRF-TOKEN': getCsrfToken(),
                },
                body: JSON.stringify({ client_notified: notified, client_notified_at: notified ? date : null }),
            });
            return response.ok;
        } catch (error) {
            console.error('Client notified update error:', error);
            return false;
        }
    }, []);

    // Optimiste : on met à jour l'affichage immédiatement, puis on restaure l'état
    // précédent si la sauvegarde échoue — sinon la case reste cochée à l'écran sans
    // que rien n'ait été enregistré en base.
    const handleClientNotifiedToggle = useCallback(async (quoteId: number, previous: { notified: boolean; date: string }, checked: boolean) => {
        setClientNotifiedMap(prev => ({ ...prev, [quoteId]: { notified: checked, date: previous.date } }));
        const ok = await persistClientNotified(quoteId, checked, previous.date);
        if (!ok) {
            setClientNotifiedMap(prev => ({ ...prev, [quoteId]: previous }));
            alert('Erreur lors de l\'enregistrement de l\'information "devis envoyé".');
        }
    }, [persistClientNotified]);

    const handleClientNotifiedDateChange = useCallback(async (quoteId: number, previous: { notified: boolean; date: string }, date: string) => {
        setClientNotifiedMap(prev => ({ ...prev, [quoteId]: { notified: previous.notified, date } }));
        if (!previous.notified) return;

        const ok = await persistClientNotified(quoteId, true, date);
        if (!ok) {
            setClientNotifiedMap(prev => ({ ...prev, [quoteId]: previous }));
            alert('Erreur lors de l\'enregistrement de l\'information "devis envoyé".');
        }
    }, [persistClientNotified]);

    const persistWorkCompletedNotified = useCallback(async (quoteId: number, notified: boolean, date: string): Promise<boolean> => {
        try {
            const response = await fetch(`/api/quotes/${quoteId}/work-completed-notified`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-XSRF-TOKEN': getCsrfToken(),
                },
                body: JSON.stringify({ work_completed_notified: notified, work_completed_notified_at: notified ? date : null }),
            });
            return response.ok;
        } catch (error) {
            console.error('Work completed notified update error:', error);
            return false;
        }
    }, []);

    const handleWorkCompletedNotifiedToggle = useCallback(async (quoteId: number, previous: { notified: boolean; date: string }, checked: boolean) => {
        setWorkCompletedNotifiedMap(prev => ({ ...prev, [quoteId]: { notified: checked, date: previous.date } }));
        const ok = await persistWorkCompletedNotified(quoteId, checked, previous.date);
        if (!ok) {
            setWorkCompletedNotifiedMap(prev => ({ ...prev, [quoteId]: previous }));
            alert('Erreur lors de l\'enregistrement de l\'information "client prévenu".');
        }
    }, [persistWorkCompletedNotified]);

    const handleWorkCompletedNotifiedDateChange = useCallback(async (quoteId: number, previous: { notified: boolean; date: string }, date: string) => {
        setWorkCompletedNotifiedMap(prev => ({ ...prev, [quoteId]: { notified: previous.notified, date } }));
        if (!previous.notified) return;

        const ok = await persistWorkCompletedNotified(quoteId, true, date);
        if (!ok) {
            setWorkCompletedNotifiedMap(prev => ({ ...prev, [quoteId]: previous }));
            alert('Erreur lors de l\'enregistrement de l\'information "client prévenu".');
        }
    }, [persistWorkCompletedNotified]);

    if (items.length === 0) {
        return (
            <div className="quotes-list__empty">
                <p>
                    {type === 'quotes'
                        ? 'Aucun devis pour le moment.'
                        : type === 'invoices'
                        ? 'Aucune facture pour cette période.'
                        : 'Aucun document archivé.'}
                </p>
            </div>
        );
    }

    if (planningMode && type === 'quotes') {
        return <PlanningQuotesTable items={items} />;
    }

    return (
        <table className="quotes-list__table">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Client</th>
                    <th>Vélo</th>
                    <th>Total TTC</th>
                    <th>{type === 'invoices' ? 'Date de facturation' : 'Date'}</th>
                    {type === 'invoices' && <th>Payé le</th>}
                    {type === 'quotes' && <th>Devis envoyé</th>}
                    {type === 'quotes' && <th>Client prévenu</th>}
                    {type === 'quotes' && <th>Statut</th>}
                    {type === 'archives' && <th>Type</th>}
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                {items.map((item) => (
                    <tr
                        key={item.id}
                        className={[
                            item.id === highlightedId ? 'quotes-list__row--highlighted' : '',
                            type === 'quotes' && item.is_scheduled ? 'quotes-list__row--scheduled' : '',
                        ].filter(Boolean).join(' ') || undefined}
                    >
                        <td>{item.reference}</td>
                        <td>{item.client.prenom} {item.client.nom}</td>
                        <td>{item.bike_description || '-'}</td>
                        <td>{formatCurrency(item.total_ttc)}</td>
                        <td>
                            {formatDate(
                                type === 'invoices' && item.invoiced_at
                                    ? item.invoiced_at
                                    : item.created_at
                            )}
                        </td>
                        {type === 'invoices' && (
                            <td>
                                {item.paid_at ? formatDate(item.paid_at) : '—'}
                            </td>
                        )}
                        {type === 'quotes' && (() => {
                            const state = clientNotifiedMap[item.id] ?? {
                                notified: item.client_notified,
                                date: item.client_notified_at ?? new Date().toISOString().split('T')[0],
                            };
                            return (
                                <td className="quotes-list__client-notified">
                                    <label className="quotes-list__client-notified-label">
                                        <input
                                            type="checkbox"
                                            checked={state.notified}
                                            onChange={(e) => handleClientNotifiedToggle(item.id, state, e.target.checked)}
                                        />
                                    </label>
                                    {state.notified && (
                                        <input
                                            type="date"
                                            value={state.date}
                                            onChange={(e) => handleClientNotifiedDateChange(item.id, state, e.target.value)}
                                            className="quotes-list__client-notified-date-input"
                                        />
                                    )}
                                </td>
                            );
                        })()}
                        {type === 'quotes' && (() => {
                            const state = workCompletedNotifiedMap[item.id] ?? {
                                notified: item.work_completed_notified,
                                date: item.work_completed_notified_at ?? new Date().toISOString().split('T')[0],
                            };
                            const isDone = item.status === 'done';
                            return (
                                <td className="quotes-list__client-notified">
                                    <label className="quotes-list__client-notified-label">
                                        <input
                                            type="checkbox"
                                            checked={state.notified}
                                            disabled={!isDone}
                                            onChange={(e) => handleWorkCompletedNotifiedToggle(item.id, state, e.target.checked)}
                                        />
                                    </label>
                                    {isDone && state.notified && (
                                        <input
                                            type="date"
                                            value={state.date}
                                            onChange={(e) => handleWorkCompletedNotifiedDateChange(item.id, state, e.target.value)}
                                            className="quotes-list__client-notified-date-input"
                                        />
                                    )}
                                </td>
                            );
                        })()}
                        {type === 'quotes' && (
                            <td>
                                <select
                                    value={item.status ?? 'reception'}
                                    onChange={(e) => onStatusChange?.(item.id, e.target.value as QuoteStatusSlug)}
                                    className={`quotes-list__status-select quotes-list__status-select--${item.status ?? 'reception'}`}
                                >
                                    {QUOTE_STATUSES.map(s => (
                                        <option key={s} value={s}>{STATUS_LABELS[s]}</option>
                                    ))}
                                </select>
                            </td>
                        )}
                        {type === 'archives' && (
                            <td>
                                {item.is_invoice ? (
                                    <span className="quotes-list__status quotes-list__status--invoice">Facture</span>
                                ) : (
                                    <span className="quotes-list__status quotes-list__status--quote">Devis</span>
                                )}
                            </td>
                        )}
                        <td className="quotes-list__actions">
                            <Link
                                href={
                                    item.is_invoice
                                        ? `/atelier/devis/${item.id}`
                                        : `/atelier/devis/${item.id}/modifier`
                                }
                                className="quotes-list__icon-link"
                                onClick={() => onRowVisit?.(item.id)}
                                title="Consulter"
                                aria-label="Consulter"
                            >
                                <EyeIcon />
                            </Link>
                            {type === 'quotes' && (
                                <button
                                    type="button"
                                    className="quotes-list__icon-link quotes-list__icon-link--muted"
                                    onClick={() => onArchiveToggle?.(item.id)}
                                    title="Archiver"
                                    aria-label="Archiver"
                                >
                                    <ArchiveIcon />
                                </button>
                            )}
                            {type === 'archives' && (
                                <button
                                    type="button"
                                    className="quotes-list__icon-link quotes-list__icon-link--muted"
                                    onClick={() => onArchiveToggle?.(item.id)}
                                    title="Désarchiver"
                                    aria-label="Désarchiver"
                                >
                                    <ArchiveIcon />
                                </button>
                            )}
                            {type === 'quotes' && item.can_delete && (
                                <form
                                    onSubmit={(e) => handleDelete(item.id, e)}
                                    style={{ display: 'inline' }}
                                >
                                    <button
                                        type="submit"
                                        className="quotes-list__icon-link quotes-list__icon-link--danger"
                                        title="Supprimer"
                                        aria-label="Supprimer"
                                    >
                                        <TrashIcon />
                                    </button>
                                </form>
                            )}
                        </td>
                    </tr>
                ))}
            </tbody>
        </table>
    );
}

interface PlanningQuotesTableProps {
    items: Quote[];
}

function PlanningQuotesTable({ items }: PlanningQuotesTableProps) {
    const [expandedId, setExpandedId] = useState<number | null>(null);
    const [tasksByQuote, setTasksByQuote] = useState<Record<number, QuoteTask[]>>({});
    const [loadingId, setLoadingId] = useState<number | null>(null);

    const handleDragStart = (e: React.DragEvent, quoteId: number, estimatedMinutes: number | null) => {
        const payload: DragQuotePayload = { kind: 'unscheduled', quoteId, durationMinutes: estimatedMinutes ?? 30 };
        e.dataTransfer.setData('application/json', JSON.stringify(payload));
        e.dataTransfer.effectAllowed = 'copy';
    };

    const toggleExpanded = useCallback(async (quoteId: number) => {
        if (expandedId === quoteId) {
            setExpandedId(null);
            return;
        }

        setExpandedId(quoteId);

        if (tasksByQuote[quoteId]) {
            return;
        }

        setLoadingId(quoteId);
        try {
            const response = await fetch(`/api/quotes/${quoteId}/tasks`, { headers: { Accept: 'application/json' } });
            const data: QuoteTask[] = response.ok ? await response.json() : [];
            setTasksByQuote(prev => ({ ...prev, [quoteId]: data }));
        } catch {
            setTasksByQuote(prev => ({ ...prev, [quoteId]: [] }));
        } finally {
            setLoadingId(null);
        }
    }, [expandedId, tasksByQuote]);

    return (
        <table className="quotes-list__table quotes-list__table--planning">
            <thead>
                <tr>
                    <th />
                    <th>Client</th>
                    <th>Vélo</th>
                    <th>Date</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                {items.map((item) => {
                    const isExpanded = expandedId === item.id;
                    return (
                        <Fragment key={item.id}>
                            <tr
                                className={`quotes-list__row--draggable ${item.is_scheduled ? 'quotes-list__row--scheduled' : ''}`}
                                draggable
                                onDragStart={e => handleDragStart(e, item.id, item.total_estimated_time_minutes ?? null)}
                                title="Glisser sur l'agenda pour planifier"
                            >
                                <td className="quotes-list__expand-cell">
                                    <button
                                        type="button"
                                        className={`quotes-list__expand-btn ${isExpanded ? 'quotes-list__expand-btn--open' : ''}`}
                                        onClick={() => toggleExpanded(item.id)}
                                        aria-label={isExpanded ? 'Masquer les travaux' : 'Voir les travaux'}
                                        aria-expanded={isExpanded}
                                    >
                                        <ChevronIcon />
                                    </button>
                                </td>
                                <td>{item.client.prenom} {item.client.nom}</td>
                                <td>{item.bike_description || '-'}</td>
                                <td>{formatDate(item.created_at)}</td>
                                <td>
                                    <span className={`quotes-list__status-badge quotes-list__status-badge--${item.status ?? 'reception'}`}>
                                        {STATUS_LABELS[item.status ?? 'reception']}
                                    </span>
                                </td>
                            </tr>
                            {isExpanded && (
                                <tr className="quotes-list__tasks-row">
                                    <td colSpan={5}>
                                        {loadingId === item.id ? (
                                            <p className="quotes-list__tasks-loading">Chargement des travaux...</p>
                                        ) : (tasksByQuote[item.id]?.length ?? 0) === 0 ? (
                                            <p className="quotes-list__tasks-empty">Aucun travail renseigné.</p>
                                        ) : (
                                            <ul className="quotes-list__tasks-list">
                                                {tasksByQuote[item.id].map((task) => (
                                                    <li key={task.id}>
                                                        {task.title}
                                                        {task.quantity > 1 && ` (x${task.quantity})`}
                                                    </li>
                                                ))}
                                            </ul>
                                        )}
                                    </td>
                                </tr>
                            )}
                        </Fragment>
                    );
                })}
            </tbody>
        </table>
    );
}

interface ClientSearchTabProps {
    onSearch: (query: string) => void;
    searchQuery: string;
    results: Map<number, Quote[]>;
    isSearching: boolean;
}

function ClientSearchTab({ onSearch, searchQuery, results, isSearching }: ClientSearchTabProps) {
    const handleDelete = (quoteId: number, e: React.FormEvent) => {
        e.preventDefault();
        if (confirm('Voulez-vous vraiment supprimer ce devis ?')) {
            router.delete(`/atelier/devis/${quoteId}`);
        }
    };

    return (
        <div>
            <div style={{ marginBottom: '20px' }}>
                <input
                    type="text"
                    value={searchQuery}
                    onChange={(e) => onSearch(e.target.value)}
                    placeholder="Rechercher un client (prénom, nom, email)..."
                    style={{
                        width: '100%',
                        maxWidth: '400px',
                        padding: '10px 12px',
                        border: '1px solid #ced4da',
                        borderRadius: '4px',
                        fontSize: '14px',
                    }}
                />
                {searchQuery.length > 0 && searchQuery.length < 2 && (
                    <p style={{ marginTop: '8px', fontSize: '13px', color: '#6c757d' }}>
                        Saisissez au moins 2 caractères pour lancer la recherche.
                    </p>
                )}
            </div>

            {isSearching && <p>Recherche en cours...</p>}

            {results.size > 0 ? (
                Array.from(results.entries()).map(([clientId, quotes]) => {
                    const client = quotes[0].client;
                    const totalQuotes = quotes.filter((q) => !q.is_invoice).length;
                    const totalInvoices = quotes.filter((q) => q.is_invoice).length;

                    return (
                        <div
                            key={clientId}
                            style={{
                                marginBottom: '30px',
                                border: '1px solid #e9ecef',
                                borderRadius: '8px',
                                padding: '20px',
                                background: 'white',
                            }}
                        >
                            <h3 style={{ margin: '0 0 15px 0', fontSize: '18px', color: '#212529' }}>
                                {client.prenom} {client.nom}
                                <span style={{ fontSize: '14px', color: '#6c757d', fontWeight: 'normal' }}>
                                    {' '}({totalQuotes} devis, {totalInvoices} facture{totalInvoices > 1 ? 's' : ''})
                                </span>
                            </h3>

                            {(client.email || client.telephone) && (
                                <p style={{ margin: '0 0 15px 0', fontSize: '14px', color: '#6c757d' }}>
                                    {client.email}
                                    {client.email && client.telephone && ' • '}
                                    {client.telephone}
                                </p>
                            )}

                            <table className="quotes-list__table">
                                <thead>
                                    <tr>
                                        <th>Référence</th>
                                        <th>Vélo</th>
                                        <th>Type</th>
                                        <th>Total TTC</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {quotes
                                        .sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
                                        .map((quote) => (
                                            <tr key={quote.id}>
                                                <td>{quote.reference}</td>
                                                <td>{quote.bike_description || '-'}</td>
                                                <td>
                                                    {quote.is_invoice ? (
                                                        <span className="quotes-list__status quotes-list__status--invoice">
                                                            Facture
                                                        </span>
                                                    ) : (
                                                        <span className="quotes-list__status quotes-list__status--quote">
                                                            Devis
                                                        </span>
                                                    )}
                                                </td>
                                                <td>{formatCurrency(quote.total_ttc)}</td>
                                                <td>
                                                    {formatDate(
                                                        quote.is_invoice && quote.invoiced_at
                                                            ? quote.invoiced_at
                                                            : quote.created_at
                                                    )}
                                                </td>
                                                <td className="quotes-list__actions">
                                                    <Link
                                                        href={
                                                            quote.is_invoice
                                                                ? `/atelier/devis/${quote.id}`
                                                                : `/atelier/devis/${quote.id}/modifier`
                                                        }
                                                        className="quotes-list__icon-link"
                                                        title="Consulter"
                                                        aria-label="Consulter"
                                                    >
                                                        <EyeIcon />
                                                    </Link>
                                                    {quote.can_delete && (
                                                        <form
                                                            onSubmit={(e) => handleDelete(quote.id, e)}
                                                            style={{ display: 'inline' }}
                                                        >
                                                            <button
                                                                type="submit"
                                                                className="quotes-list__icon-link quotes-list__icon-link--danger"
                                                                title="Supprimer"
                                                                aria-label="Supprimer"
                                                            >
                                                                <TrashIcon />
                                                            </button>
                                                        </form>
                                                    )}
                                                </td>
                                            </tr>
                                        ))}
                                </tbody>
                            </table>
                        </div>
                    );
                })
            ) : searchQuery.length >= 2 && !isSearching ? (
                <div className="quotes-list__empty">
                    <p>Aucun client trouvé pour "{searchQuery}".</p>
                </div>
            ) : (
                <div className="quotes-list__empty">
                    <p>Utilisez la recherche pour trouver un client.</p>
                </div>
            )}
        </div>
    );
}

export default function QuotesTabs({
    quotes: initialQuotes,
    archivedQuotes: initialArchivedQuotes,
    invoices,
    onLoadInvoices,
    invoicesLoaded,
    activeTab,
    onTabChange,
    planningMode,
    onClosePlanningMode,
}: QuotesTabsProps) {
    const [statusFilter, setStatusFilter] = useState<string>(getStoredStatusFilter);
    const [recipientFilter, setRecipientFilter] = useState<'all' | QuoteCommentRecipient>(getStoredRecipientFilter);

    const handleStatusFilterChange = useCallback((slug: string) => {
        setStatusFilter(slug);
        localStorage.setItem(STATUS_FILTER_STORAGE_KEY, slug);
    }, []);

    const handleRecipientFilterChange = useCallback((recipient: 'all' | QuoteCommentRecipient) => {
        setRecipientFilter(recipient);
        localStorage.setItem(RECIPIENT_FILTER_STORAGE_KEY, recipient);
    }, []);
    const [quotes, setQuotes] = useState<Quote[]>(initialQuotes);
    const [archivedQuotes, setArchivedQuotes] = useState<Quote[]>(initialArchivedQuotes);
    const [clientSearch, setClientSearch] = useState('');
    const [clientResults, setClientResults] = useState<Map<number, Quote[]>>(new Map());
    const [isSearching, setIsSearching] = useState(false);
    const [highlightedId, setHighlightedId] = useState<number | null>(null);

    const handleTabChange = (tab: TabType) => {
        onTabChange(tab);
        if (tab === 'invoices' && !invoicesLoaded) {
            onLoadInvoices();
        }
    };

    const handleRowVisit = useCallback((quoteId: number) => {
        sessionStorage.setItem(HIGHLIGHTED_QUOTE_STORAGE_KEY, String(quoteId));
    }, []);

    const handleQuoteScheduledChange = useCallback((quoteId: number, isScheduled: boolean) => {
        setQuotes(prev => prev.map(q => q.id === quoteId ? { ...q, is_scheduled: isScheduled } : q));
        setArchivedQuotes(prev => prev.map(q => q.id === quoteId ? { ...q, is_scheduled: isScheduled } : q));
    }, []);

    useLayoutEffect(() => {
        const restoreHighlight = () => {
            const stored = sessionStorage.getItem(HIGHLIGHTED_QUOTE_STORAGE_KEY);
            if (stored === null) return;

            setHighlightedId(parseInt(stored, 10));
            sessionStorage.removeItem(HIGHLIGHTED_QUOTE_STORAGE_KEY);
        };

        restoreHighlight();
        const removeListener = router.on('navigate', restoreHighlight);

        return removeListener;
    }, []);

    useLayoutEffect(() => {
        const restoreScroll = () => {
            const stored = sessionStorage.getItem(SCROLL_Y_STORAGE_KEY);
            if (stored === null) return;

            // Attend que React ait peint le tableau (hauteur finale de page) avant de scroller.
            requestAnimationFrame(() => window.scrollTo(0, parseInt(stored, 10)));
        };

        // Inertia remet le scroll à 0 après chaque navigation ; on restaure juste après.
        restoreScroll();
        const removeListener = router.on('navigate', restoreScroll);

        return removeListener;
    }, []);

    useLayoutEffect(() => {
        const handleScroll = () => {
            sessionStorage.setItem(SCROLL_Y_STORAGE_KEY, String(window.scrollY));
        };

        window.addEventListener('scroll', handleScroll, { passive: true });
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    useEffect(() => {
        if (highlightedId === null) return;

        const timeout = setTimeout(() => setHighlightedId(null), 3000);
        return () => clearTimeout(timeout);
    }, [highlightedId]);

    const handleStatusChange = async (quoteId: number, status: QuoteStatusSlug) => {
        try {
            const response = await fetch(`/api/quotes/${quoteId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-XSRF-TOKEN': getCsrfToken(),
                },
                credentials: 'same-origin',
                body: JSON.stringify({ status }),
            });

            if (response.ok) {
                const data = await response.json();
                setQuotes(prev =>
                    prev.map(q => q.id === quoteId ? { ...q, status: data.status, work_completed_notified: data.work_completed_notified } : q)
                );
            }
        } catch (error) {
            console.error('Status update error:', error);
        }
    };

    const handleArchiveToggle = async (quoteId: number) => {
        try {
            const response = await fetch(`/api/quotes/${quoteId}/archive`, {
                method: 'PATCH',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-XSRF-TOKEN': getCsrfToken(),
                },
                credentials: 'same-origin',
            });

            if (!response.ok) return;

            const data = await response.json();

            if (data.is_archived) {
                const quote = quotes.find(q => q.id === quoteId);
                if (quote) {
                    setQuotes(prev => prev.filter(q => q.id !== quoteId));
                    setArchivedQuotes(prev => [{ ...quote, is_archived: true }, ...prev]);
                }
            } else {
                const quote = archivedQuotes.find(q => q.id === quoteId);
                if (quote) {
                    setArchivedQuotes(prev => prev.filter(q => q.id !== quoteId));
                    if (!quote.is_invoice) {
                        setQuotes(prev => [{ ...quote, is_archived: false }, ...prev]);
                    }
                }
            }
        } catch (error) {
            console.error('Archive toggle error:', error);
        }
    };

    const filteredQuotes = useMemo(() => {
        return quotes
            .filter(q => statusFilter === 'all' || q.status === statusFilter)
            .filter(q => recipientFilter === 'all' || q.open_comment_recipients.includes(recipientFilter));
    }, [quotes, statusFilter, recipientFilter]);

    const handleClientSearch = async (query: string) => {
        setClientSearch(query);

        if (query.length < 2) {
            setClientResults(new Map());
            return;
        }

        setIsSearching(true);
        try {
            const response = await fetch(`/api/atelier/clients/search?q=${encodeURIComponent(query)}`);
            const data = await response.json();

            const grouped = new Map<number, Quote[]>();
            data.forEach((quote: Quote) => {
                const existing = grouped.get(quote.client_id) || [];
                grouped.set(quote.client_id, [...existing, quote]);
            });
            setClientResults(grouped);
        } catch (error) {
            console.error('Search error:', error);
        } finally {
            setIsSearching(false);
        }
    };

    return (
        <div className="quotes-list-container">
            <div className="quotes-tabs">
                <button
                    type="button"
                    onClick={() => handleTabChange('quotes')}
                    className={`quotes-tab ${activeTab === 'quotes' ? 'quotes-tab--active' : ''}`}
                >
                    Devis
                </button>
                <button
                    type="button"
                    onClick={() => handleTabChange('invoices')}
                    className={`quotes-tab ${activeTab === 'invoices' ? 'quotes-tab--active' : ''}`}
                >
                    Factures
                </button>
                <button
                    type="button"
                    onClick={() => handleTabChange('clients')}
                    className={`quotes-tab ${activeTab === 'clients' ? 'quotes-tab--active' : ''}`}
                >
                    Clients
                </button>
                <button
                    type="button"
                    onClick={() => handleTabChange('archives')}
                    className={`quotes-tab ${activeTab === 'archives' ? 'quotes-tab--active' : ''}`}
                >
                    Archives
                    {archivedQuotes.length > 0 && (
                        <span className="quotes-tab__count">{archivedQuotes.length}</span>
                    )}
                </button>
            </div>

            <div className={`quotes-tab-content ${activeTab === 'quotes' ? 'quotes-tab-content--active' : ''}`}>
                <div className="quotes-status-filters">
                    {Object.entries(STATUS_FILTER_LABELS).map(([slug, label]) => (
                        <button
                            key={slug}
                            type="button"
                            onClick={() => handleStatusFilterChange(slug)}
                            className={`quotes-status-filter ${statusFilter === slug ? 'quotes-status-filter--active' : ''}`}
                        >
                            {label}
                            {slug !== 'all' && (
                                <span className="quotes-status-filter__count">
                                    {quotes.filter(q => q.status === slug).length}
                                </span>
                            )}
                        </button>
                    ))}
                </div>
                <div className="quotes-status-filters">
                    {Object.entries(RECIPIENT_FILTER_LABELS).map(([slug, label]) => (
                        <button
                            key={slug}
                            type="button"
                            onClick={() => handleRecipientFilterChange(slug as 'all' | QuoteCommentRecipient)}
                            className={`quotes-status-filter ${recipientFilter === slug ? 'quotes-status-filter--active' : ''}`}
                        >
                            {label}
                            {slug !== 'all' && (
                                <span className="quotes-status-filter__count">
                                    {quotes.filter(q => q.open_comment_recipients.includes(slug as QuoteCommentRecipient)).length}
                                </span>
                            )}
                        </button>
                    ))}
                </div>
                <QuotesTable
                    items={filteredQuotes}
                    type="quotes"
                    onStatusChange={handleStatusChange}
                    onArchiveToggle={handleArchiveToggle}
                    highlightedId={highlightedId}
                    onRowVisit={handleRowVisit}
                    planningMode={planningMode}
                />
            </div>

            <div className={`quotes-tab-content ${activeTab === 'invoices' ? 'quotes-tab-content--active' : ''}`}>
                <QuotesTable items={invoices} type="invoices" />
            </div>

            <div className={`quotes-tab-content ${activeTab === 'clients' ? 'quotes-tab-content--active' : ''}`}>
                <ClientSearchTab
                    onSearch={handleClientSearch}
                    searchQuery={clientSearch}
                    results={clientResults}
                    isSearching={isSearching}
                />
            </div>

            <div className={`quotes-tab-content ${activeTab === 'archives' ? 'quotes-tab-content--active' : ''}`}>
                <QuotesTable
                    items={archivedQuotes}
                    type="archives"
                    onArchiveToggle={handleArchiveToggle}
                />
            </div>

            <PlanningSidePanel
                isOpen={!!planningMode}
                onClose={() => onClosePlanningMode?.()}
                onQuoteScheduledChange={handleQuoteScheduledChange}
            />
        </div>
    );
}
