import { useState, useEffect, useCallback } from 'react';
import { agendaStore } from '@/stores/agendaStore';
import type {
    AgendaData,
    BikeDefinition,
    BikeCategoryRef,
    BikeSizeRef,
    LoadedReservation,
    AgendaSource,
} from '@/types';

interface UseAgendaStoreResult {
    data: AgendaData | null;
    version: number;
    isLoading: boolean;
    source: AgendaSource | null;
    forceRefresh: () => Promise<void>;
}

/**
 * React hook to access the AgendaStore.
 * Automatically subscribes to changes and triggers re-renders.
 */
export function useAgendaStore(
    serverVersion: number,
    initialProps: {
        bikes: BikeDefinition[];
        bikeCategories: BikeCategoryRef[];
        bikeSizes: BikeSizeRef[];
        reservations: LoadedReservation[];
    }
): UseAgendaStoreResult {
    const [state, setState] = useState(() => agendaStore.getState());
    const [source, setSource] = useState<AgendaSource | null>(null);
    const [initialized, setInitialized] = useState(false);

    // Initialize the store on mount
    useEffect(() => {
        let mounted = true;

        const init = async () => {
            try {
                const result = await agendaStore.load(serverVersion, initialProps);
                if (mounted) {
                    setSource(result.source);
                    setInitialized(true);
                }
            } catch (error) {
                console.error('[useAgendaStore] Failed to initialize:', error);
                // Fall back to initial props
                if (mounted) {
                    agendaStore.setState(
                        {
                            bikes: initialProps.bikes,
                            bikeCategories: initialProps.bikeCategories,
                            bikeSizes: initialProps.bikeSizes,
                            reservations: initialProps.reservations,
                        },
                        serverVersion
                    );
                    setSource('network');
                    setInitialized(true);
                }
            }
        };

        init();

        return () => {
            mounted = false;
        };
    }, [serverVersion]); // Only re-run if server version changes

    // Subscribe to store changes
    useEffect(() => {
        const unsubscribe = agendaStore.subscribe(() => {
            setState(agendaStore.getState());
        });

        return unsubscribe;
    }, []);

    const forceRefresh = useCallback(async () => {
        try {
            const result = await agendaStore.forceRefresh();
            setSource(result.source);
        } catch (error) {
            console.error('[useAgendaStore] Force refresh failed:', error);
            throw error;
        }
    }, []);

    return {
        data: state.data,
        version: state.version,
        isLoading: state.isLoading,
        source,
        forceRefresh,
    };
}
