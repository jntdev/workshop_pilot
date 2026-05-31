import { useState, useMemo } from 'react';
import { Link, router } from '@inertiajs/react';
import { Quote, QuoteStatusSlug } from '@/types';

interface QuotesTabsProps {
    quotes: Quote[];
    archivedQuotes: Quote[];
    invoices: Quote[];
    onLoadInvoices: () => void;
    invoicesLoaded: boolean;
}

type TabType = 'quotes' | 'invoices' | 'clients' | 'archives';

const STATUS_LABELS: Record<QuoteStatusSlug, string> = {
    reception: 'Bon de réception',
    to_complete: 'À compléter',
    to_quote: 'À chiffrer',
    pending_validation: 'Attente validation',
    validated: 'Validé',
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
    'in_progress',
    'done',
];

const STATUS_FILTER_LABELS: Record<string, string> = {
    all: 'Tous',
    to_complete: 'À compléter',
    to_quote: 'À chiffrer',
    pending_validation: 'Attente validation',
    validated: 'Validé',
    in_progress: 'En cours',
    done: 'Terminé',
};

const getCsrfToken = (): string => {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
};

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
}

function QuotesTable({ items, type, onStatusChange, onArchiveToggle }: QuotesTableProps) {
    const handleDelete = (quoteId: number, e: React.FormEvent) => {
        e.preventDefault();
        if (confirm('Voulez-vous vraiment supprimer ce devis ?')) {
            router.delete(`/atelier/devis/${quoteId}`);
        }
    };

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

    return (
        <table className="quotes-list__table">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Client</th>
                    <th>Vélo</th>
                    <th>Total TTC</th>
                    <th>{type === 'invoices' ? 'Date de facturation' : 'Date'}</th>
                    {type === 'quotes' && <th>Statut</th>}
                    {type === 'archives' && <th>Type</th>}
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                {items.map((item) => (
                    <tr key={item.id}>
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
                                className="quotes-list__link"
                            >
                                Consulter
                            </Link>
                            {type === 'quotes' && (
                                <button
                                    type="button"
                                    className="quotes-list__link quotes-list__link--muted"
                                    onClick={() => onArchiveToggle?.(item.id)}
                                >
                                    Archiver
                                </button>
                            )}
                            {type === 'archives' && (
                                <button
                                    type="button"
                                    className="quotes-list__link quotes-list__link--muted"
                                    onClick={() => onArchiveToggle?.(item.id)}
                                >
                                    Désarchiver
                                </button>
                            )}
                            {type === 'quotes' && item.can_delete && (
                                <form
                                    onSubmit={(e) => handleDelete(item.id, e)}
                                    style={{ display: 'inline' }}
                                >
                                    <button
                                        type="submit"
                                        className="quotes-list__link quotes-list__link--danger"
                                    >
                                        Supprimer
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
                                                        className="quotes-list__link"
                                                    >
                                                        Consulter
                                                    </Link>
                                                    {quote.can_delete && (
                                                        <form
                                                            onSubmit={(e) => handleDelete(quote.id, e)}
                                                            style={{ display: 'inline' }}
                                                        >
                                                            <button
                                                                type="submit"
                                                                className="quotes-list__link quotes-list__link--danger"
                                                            >
                                                                Supprimer
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
}: QuotesTabsProps) {
    const [activeTab, setActiveTab] = useState<TabType>('quotes');
    const [statusFilter, setStatusFilter] = useState<string>('all');
    const [quotes, setQuotes] = useState<Quote[]>(initialQuotes);
    const [archivedQuotes, setArchivedQuotes] = useState<Quote[]>(initialArchivedQuotes);
    const [clientSearch, setClientSearch] = useState('');
    const [clientResults, setClientResults] = useState<Map<number, Quote[]>>(new Map());
    const [isSearching, setIsSearching] = useState(false);

    const handleTabChange = (tab: TabType) => {
        setActiveTab(tab);
        if (tab === 'invoices' && !invoicesLoaded) {
            onLoadInvoices();
        }
    };

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
                    prev.map(q => q.id === quoteId ? { ...q, status: data.status } : q)
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
        if (statusFilter === 'all') return quotes;
        return quotes.filter(q => q.status === statusFilter);
    }, [quotes, statusFilter]);

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
                            onClick={() => setStatusFilter(slug)}
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
                <QuotesTable
                    items={filteredQuotes}
                    type="quotes"
                    onStatusChange={handleStatusChange}
                    onArchiveToggle={handleArchiveToggle}
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
        </div>
    );
}
