import { useEffect, useRef, useState, useCallback } from 'react';
import { agendaStore } from '@/stores/agendaStore';

interface UseAgendaVersionWatcherOptions {
    /** Polling interval in milliseconds (default: 60000 = 1 minute) */
    pollInterval?: number;
    /** Whether the watcher is enabled */
    enabled?: boolean;
}

interface UseAgendaVersionWatcherResult {
    /** Whether a drift was detected */
    hasDrift: boolean;
    /** Whether the store is currently refreshing */
    isRefreshing: boolean;
    /** Manually trigger a version check */
    checkVersion: () => Promise<void>;
    /** Dismiss the drift banner */
    dismissDrift: () => void;
}

/**
 * Hook to watch for agenda version changes (drift detection).
 * Polls the server on interval and on visibility change.
 */
export function useAgendaVersionWatcher(options: UseAgendaVersionWatcherOptions = {}): UseAgendaVersionWatcherResult {
    const { pollInterval = 60000, enabled = true } = options;

    const [hasDrift, setHasDrift] = useState(false);
    const [isRefreshing, setIsRefreshing] = useState(false);
    const pollTimeoutRef = useRef<NodeJS.Timeout | null>(null);
    const isCheckingRef = useRef(false);

    const checkVersion = useCallback(async () => {
        // Prevent concurrent checks
        if (isCheckingRef.current || !enabled) return;

        isCheckingRef.current = true;

        try {
            const serverVersion = await agendaStore.fetchServerVersion();
            const { version: localVersion } = agendaStore.getState();

            if (serverVersion > localVersion) {
                console.info(`[AgendaVersionWatcher] Drift detected: server=${serverVersion}, local=${localVersion}`);
                setHasDrift(true);
                setIsRefreshing(true);

                await agendaStore.forceRefresh();

                setIsRefreshing(false);
                setHasDrift(false);
            }
        } catch (error) {
            console.warn('[AgendaVersionWatcher] Failed to check version:', error);
            // Don't show drift on network errors
        } finally {
            isCheckingRef.current = false;
        }
    }, [enabled]);

    const dismissDrift = useCallback(() => {
        setHasDrift(false);
    }, []);

    // Set up polling
    useEffect(() => {
        if (!enabled) return;

        const schedulePoll = () => {
            pollTimeoutRef.current = setTimeout(() => {
                checkVersion().finally(schedulePoll);
            }, pollInterval);
        };

        schedulePoll();

        return () => {
            if (pollTimeoutRef.current) {
                clearTimeout(pollTimeoutRef.current);
            }
        };
    }, [enabled, pollInterval, checkVersion]);

    // Check on visibility change
    useEffect(() => {
        if (!enabled) return;

        const handleVisibilityChange = () => {
            if (document.visibilityState === 'visible') {
                checkVersion();
            }
        };

        document.addEventListener('visibilitychange', handleVisibilityChange);

        return () => {
            document.removeEventListener('visibilitychange', handleVisibilityChange);
        };
    }, [enabled, checkVersion]);

    // Check on online event (network comes back)
    useEffect(() => {
        if (!enabled) return;

        const handleOnline = () => {
            console.info('[AgendaVersionWatcher] Network online, checking version');
            checkVersion();
        };

        window.addEventListener('online', handleOnline);

        return () => {
            window.removeEventListener('online', handleOnline);
        };
    }, [enabled, checkVersion]);

    return {
        hasDrift,
        isRefreshing,
        checkVersion,
        dismissDrift,
    };
}
