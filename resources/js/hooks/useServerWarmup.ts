import { useEffect, useRef } from 'react';

// Intervalle entre les pings (3 minutes)
const PING_INTERVAL = 3 * 60 * 1000;

/**
 * Hook qui fait des pings périodiques au backend pour maintenir le serveur "éveillé"
 * sur les hébergements mutualisés qui mettent en veille les ressources après inactivité.
 *
 * - Premier ping immédiat au montage
 * - Pings suivants toutes les 3 minutes tant que l'utilisateur est sur l'app
 * - S'arrête automatiquement quand le composant se démonte
 */
export function useServerWarmup(): void {
    const intervalRef = useRef<ReturnType<typeof setInterval> | null>(null);

    useEffect(() => {
        const ping = () => {
            fetch('/api/health', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                },
            }).catch(() => {
                // Ignorer les erreurs silencieusement
            });
        };

        // Premier ping immédiat
        ping();

        // Pings périodiques
        intervalRef.current = setInterval(ping, PING_INTERVAL);

        return () => {
            if (intervalRef.current) {
                clearInterval(intervalRef.current);
            }
        };
    }, []);
}
