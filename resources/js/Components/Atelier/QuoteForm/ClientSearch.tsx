import { useState, useEffect, useCallback } from 'react';
import { Client } from '@/types';

interface ClientSearchProps {
    onClientSelect: (client: Client) => void;
    disabled?: boolean;
}

export default function ClientSearch({ onClientSelect, disabled }: ClientSearchProps) {
    const [searchTerm, setSearchTerm] = useState('');
    const [clients, setClients] = useState<Client[]>([]);
    const [isSearching, setIsSearching] = useState(false);
    const [showResults, setShowResults] = useState(false);

    const searchClients = useCallback(async (term: string) => {
        if (term.length < 2) {
            setClients([]);
            return;
        }

        setIsSearching(true);
        try {
            const response = await fetch('/api/clients?search=' + encodeURIComponent(term));
            const data = await response.json();
            setClients(data.slice(0, 10));
        } catch (error) {
            console.error('Search error:', error);
        } finally {
            setIsSearching(false);
        }
    }, []);

    useEffect(() => {
        const timeoutId = setTimeout(() => {
            searchClients(searchTerm);
        }, 300);

        return () => clearTimeout(timeoutId);
    }, [searchTerm, searchClients]);

    const handleSelect = (client: Client) => {
        onClientSelect(client);
        setSearchTerm('');
        setClients([]);
        setShowResults(false);
    };

    return (
        <div className="client-search">
            <div className="client-search__input-wrapper">
                <input
                    type="text"
                    value={searchTerm}
                    onChange={(e) => {
                        setSearchTerm(e.target.value);
                        setShowResults(true);
                    }}
                    onFocus={() => setShowResults(true)}
                    onBlur={() => setTimeout(() => setShowResults(false), 200)}
                    placeholder="Rechercher un client existant..."
                    className="client-search__input"
                    disabled={disabled}
                />
            </div>

            {showResults && searchTerm.length >= 2 && (
                <div className="client-search__results">
                    {isSearching ? (
                        <div className="client-search__result-item">
                            Recherche en cours...
                        </div>
                    ) : clients.length > 0 ? (
                        clients.map((client) => (
                            <button
                                key={client.id}
                                type="button"
                                className="client-search__result-item"
                                onClick={() => handleSelect(client)}
                            >
                                <div className="client-search__result-name">
                                    {client.prenom} {client.nom}
                                </div>
                                <div className="client-search__result-details">
                                    {client.telephone && <span>{client.telephone}</span>}
                                    {client.email && <span>{client.email}</span>}
                                </div>
                            </button>
                        ))
                    ) : (
                        <div className="client-search__result-item">
                            Aucun client trouvé
                        </div>
                    )}
                </div>
            )}
        </div>
    );
}
