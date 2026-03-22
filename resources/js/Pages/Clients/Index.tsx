import { useState, useEffect, useCallback } from 'react';
import { Head, Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import { Client, PageProps } from '@/types';

interface Props extends PageProps {
    clients: Client[];
}

export default function ClientsIndex({ clients: initialClients }: Props) {
    const [search, setSearch] = useState('');
    const [clients, setClients] = useState<Client[]>(initialClients);
    const [isLoading, setIsLoading] = useState(false);

    const fetchClients = useCallback(async (searchQuery: string) => {
        setIsLoading(true);
        try {
            const url = '/api/clients?search=' + encodeURIComponent(searchQuery);
            const response = await fetch(url, {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            const data = await response.json();
            setClients(data);
        } catch (error) {
            console.error('Failed to fetch clients:', error);
        } finally {
            setIsLoading(false);
        }
    }, []);

    useEffect(() => {
        const timeoutId = setTimeout(() => {
            fetchClients(search);
        }, 300);

        return () => clearTimeout(timeoutId);
    }, [search, fetchClients]);

    return (
        <MainLayout>
            <Head title="Clients" />

            <div className="page-header">
                <h1>Clients</h1>
            </div>

            <div className="clients-list">
                <div className="clients-list__header">
                    <div className="clients-list__search-bar">
                        <input
                            type="text"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Rechercher un client..."
                            className="clients-list__search-input"
                        />
                        <Link href="/clients/nouveau" className="btn btn-primary">
                            + Créer nouveau client
                        </Link>
                    </div>
                </div>

                <div className="clients-list__grid">
                    {isLoading ? (
                        <div className="clients-list__empty">
                            Chargement...
                        </div>
                    ) : clients.length > 0 ? (
                        clients.map((client) => (
                            <Link
                                key={client.id}
                                href={'/clients/' + client.id}
                                className="client-card"
                            >
                                <div className="client-card__name">
                                    {client.prenom} {client.nom}
                                </div>
                                <div className="client-card__phone">{client.telephone}</div>
                                {client.email && (
                                    <div className="client-card__email">{client.email}</div>
                                )}
                            </Link>
                        ))
                    ) : (
                        <div className="clients-list__empty">
                            Aucun client trouvé.
                        </div>
                    )}
                </div>
            </div>
        </MainLayout>
    );
}
