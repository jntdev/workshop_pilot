import { useState, useEffect, useCallback } from 'react';

type PrivacyMode = 'atelier' | 'comptoir';

const STORAGE_KEY = 'workshop_privacy_mode';

/**
 * Hook pour gérer le mode de confidentialité (Atelier/Admin vs Comptoir)
 * - Mode Atelier : toutes les données visibles (marges, prix achat, KPI)
 * - Mode Comptoir : données sensibles masquées (pour affichage client)
 */
export function usePrivacyMode() {
    const [mode, setMode] = useState<PrivacyMode>(() => {
        if (typeof window === 'undefined') return 'atelier';
        const stored = localStorage.getItem(STORAGE_KEY);
        return (stored === 'comptoir' ? 'comptoir' : 'atelier') as PrivacyMode;
    });

    // Synchroniser avec localStorage
    useEffect(() => {
        localStorage.setItem(STORAGE_KEY, mode);
    }, [mode]);

    // Écouter le raccourci clavier Ctrl+Shift+P
    useEffect(() => {
        const handleKeyDown = (event: KeyboardEvent) => {
            if (event.ctrlKey && event.shiftKey && event.key.toLowerCase() === 'p') {
                event.preventDefault();
                setMode((current) => (current === 'atelier' ? 'comptoir' : 'atelier'));
            }
        };

        document.addEventListener('keydown', handleKeyDown);
        return () => document.removeEventListener('keydown', handleKeyDown);
    }, []);

    // Écouter les changements de localStorage depuis d'autres onglets
    useEffect(() => {
        const handleStorageChange = (event: StorageEvent) => {
            if (event.key === STORAGE_KEY && event.newValue) {
                setMode(event.newValue as PrivacyMode);
            }
        };

        window.addEventListener('storage', handleStorageChange);
        return () => window.removeEventListener('storage', handleStorageChange);
    }, []);

    const toggle = useCallback(() => {
        setMode((current) => (current === 'atelier' ? 'comptoir' : 'atelier'));
    }, []);

    const setAtelier = useCallback(() => setMode('atelier'), []);
    const setComptoir = useCallback(() => setMode('comptoir'), []);

    return {
        mode,
        isAtelier: mode === 'atelier',
        isComptoir: mode === 'comptoir',
        toggle,
        setAtelier,
        setComptoir,
    };
}

export type { PrivacyMode };
