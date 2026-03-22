import { useState, useMemo, useRef, useEffect, useCallback } from 'react';
import type { LoadedReservation } from '@/types';

interface ReservationSearchProps {
    reservations: LoadedReservation[];
    onSelect: (reservation: LoadedReservation) => void;
}

const formatDate = (dateStr: string): string => {
    const date = new Date(dateStr);
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
    });
};

const getStatutLabel = (statut: string): string => {
    const labels: Record<string, string> = {
        reserve: 'Réservé',
        en_attente_acompte: 'Attente acompte',
        en_cours: 'En cours',
        paye: 'Payé',
        annule: 'Annulé',
    };
    return labels[statut] || statut;
};

export default function ReservationSearch({ reservations, onSelect }: ReservationSearchProps) {
    const [query, setQuery] = useState('');
    const [isOpen, setIsOpen] = useState(false);
    const [highlightedIndex, setHighlightedIndex] = useState(0);
    const inputRef = useRef<HTMLInputElement>(null);
    const dropdownRef = useRef<HTMLDivElement>(null);

    // Filtrer les réservations selon la recherche
    const results = useMemo(() => {
        if (!query.trim()) return [];

        const searchTerm = query.toLowerCase().trim();
        const maxResults = 8;

        return reservations
            .filter((r) => {
                // Recherche par nom du client
                const clientName = r.client_name?.toLowerCase() || '';
                if (clientName.includes(searchTerm)) return true;

                // Recherche par téléphone
                const phone = r.client?.telephone?.toLowerCase() || '';
                if (phone.includes(searchTerm)) return true;

                // Recherche par email
                const email = r.client?.email?.toLowerCase() || '';
                if (email.includes(searchTerm)) return true;

                return false;
            })
            .slice(0, maxResults);
    }, [reservations, query]);

    // Reset highlighted index when results change
    useEffect(() => {
        setHighlightedIndex(0);
    }, [results]);

    // Close dropdown on click outside
    useEffect(() => {
        const handleClickOutside = (e: MouseEvent) => {
            if (
                dropdownRef.current &&
                !dropdownRef.current.contains(e.target as Node) &&
                inputRef.current &&
                !inputRef.current.contains(e.target as Node)
            ) {
                setIsOpen(false);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    const handleSelect = useCallback((reservation: LoadedReservation) => {
        onSelect(reservation);
        setQuery('');
        setIsOpen(false);
        inputRef.current?.blur();
    }, [onSelect]);

    const handleKeyDown = useCallback((e: React.KeyboardEvent) => {
        if (!isOpen || results.length === 0) return;

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                setHighlightedIndex((prev) => (prev + 1) % results.length);
                break;
            case 'ArrowUp':
                e.preventDefault();
                setHighlightedIndex((prev) => (prev - 1 + results.length) % results.length);
                break;
            case 'Enter':
                e.preventDefault();
                if (results[highlightedIndex]) {
                    handleSelect(results[highlightedIndex]);
                }
                break;
            case 'Escape':
                e.preventDefault();
                setIsOpen(false);
                inputRef.current?.blur();
                break;
        }
    }, [isOpen, results, highlightedIndex, handleSelect]);

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        setQuery(e.target.value);
        setIsOpen(true);
    };

    const handleFocus = () => {
        if (query.trim()) {
            setIsOpen(true);
        }
    };

    return (
        <div className="reservation-search">
            <div className="reservation-search__input-wrapper">
                <svg
                    className="reservation-search__icon"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                        fillRule="evenodd"
                        d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                        clipRule="evenodd"
                    />
                </svg>
                <input
                    ref={inputRef}
                    type="text"
                    className="reservation-search__input"
                    placeholder="Rechercher une réservation..."
                    value={query}
                    onChange={handleInputChange}
                    onFocus={handleFocus}
                    onKeyDown={handleKeyDown}
                />
                {query && (
                    <button
                        type="button"
                        className="reservation-search__clear"
                        onClick={() => {
                            setQuery('');
                            setIsOpen(false);
                            inputRef.current?.focus();
                        }}
                    >
                        &times;
                    </button>
                )}
            </div>

            {isOpen && results.length > 0 && (
                <div ref={dropdownRef} className="reservation-search__dropdown">
                    {results.map((reservation, index) => (
                        <button
                            key={reservation.id}
                            type="button"
                            className={`reservation-search__result ${index === highlightedIndex ? 'reservation-search__result--highlighted' : ''}`}
                            onClick={() => handleSelect(reservation)}
                            onMouseEnter={() => setHighlightedIndex(index)}
                        >
                            <span className="reservation-search__result-name">
                                {reservation.client_name || 'Client inconnu'}
                            </span>
                            <span className="reservation-search__result-dates">
                                {formatDate(reservation.date_reservation)} → {formatDate(reservation.date_retour)}
                            </span>
                            <span className={`reservation-search__result-statut reservation-search__result-statut--${reservation.statut}`}>
                                {getStatutLabel(reservation.statut)}
                            </span>
                        </button>
                    ))}
                </div>
            )}

            {isOpen && query.trim() && results.length === 0 && (
                <div ref={dropdownRef} className="reservation-search__dropdown reservation-search__dropdown--empty">
                    Aucune réservation trouvée
                </div>
            )}
        </div>
    );
}
