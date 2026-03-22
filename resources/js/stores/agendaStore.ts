import type {
    AgendaData,
    AgendaSnapshot,
    AgendaSource,
    AgendaLoadResult,
    BikeDefinition,
    BikeCategoryRef,
    BikeSizeRef,
    LoadedReservation,
} from '@/types';

const STORAGE_KEY = 'agenda_snapshot_v1';
const MAX_STORAGE_SIZE = 4 * 1024 * 1024; // 4 MB (safety margin before 5MB limit)

/**
 * AgendaStore - Singleton for managing agenda data with caching.
 * Priority: Instance -> LocalStorage -> Network
 */
class AgendaStore {
    private currentData: AgendaData | null = null;
    private currentVersion: number = 0;
    private isLoading: boolean = false;
    private listeners: Set<() => void> = new Set();

    /**
     * Load agenda data following the priority: Instance -> LocalStorage -> Network.
     */
    async load(
        serverVersion: number,
        initialProps: {
            bikes: BikeDefinition[];
            bikeCategories: BikeCategoryRef[];
            bikeSizes: BikeSizeRef[];
            reservations: LoadedReservation[];
        }
    ): Promise<AgendaLoadResult> {
        // 1. Check instance cache
        if (this.currentData && this.currentVersion === serverVersion) {
            console.info('[AgendaStore] Loading from instance cache');
            return {
                data: this.currentData,
                version: this.currentVersion,
                source: 'instance',
            };
        }

        // 2. Check localStorage
        const cachedSnapshot = this.readFromStorage();
        if (cachedSnapshot && cachedSnapshot.version === serverVersion) {
            console.info('[AgendaStore] Loading from localStorage');
            this.currentData = cachedSnapshot.data;
            this.currentVersion = cachedSnapshot.version;
            return {
                data: cachedSnapshot.data,
                version: cachedSnapshot.version,
                source: 'localStorage',
            };
        }

        // 3. Use initial props (they're fresh from the server)
        // This is equivalent to a network fetch since Inertia already loaded them
        console.info('[AgendaStore] Loading from server props (network)');
        const data: AgendaData = {
            bikes: initialProps.bikes,
            bikeCategories: initialProps.bikeCategories,
            bikeSizes: initialProps.bikeSizes,
            reservations: initialProps.reservations,
        };

        this.setState(data, serverVersion);

        return {
            data,
            version: serverVersion,
            source: 'network',
        };
    }

    /**
     * Force a full refresh from the network.
     */
    async forceRefresh(): Promise<AgendaLoadResult> {
        this.isLoading = true;
        this.notifyListeners();

        try {
            const response = await fetch('/api/location/full', {
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error(`Failed to fetch: ${response.status}`);
            }

            const json = await response.json();
            const data: AgendaData = {
                bikes: json.bikes,
                bikeCategories: json.bikeCategories,
                bikeSizes: json.bikeSizes,
                reservations: json.reservations,
            };

            this.setState(data, json.version);

            return {
                data,
                version: json.version,
                source: 'network',
            };
        } finally {
            this.isLoading = false;
            this.notifyListeners();
        }
    }

    /**
     * Get the current server version (for polling comparison).
     */
    async fetchServerVersion(): Promise<number> {
        const response = await fetch('/api/location/version', {
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            throw new Error(`Failed to fetch version: ${response.status}`);
        }

        const json = await response.json();
        return json.version;
    }

    /**
     * Create a snapshot of the current state (for rollback).
     */
    snapshot(): AgendaSnapshot | null {
        if (!this.currentData) return null;

        return {
            version: this.currentVersion,
            data: JSON.parse(JSON.stringify(this.currentData)), // Deep clone
            savedAt: new Date().toISOString(),
        };
    }

    /**
     * Update the store with new data and version.
     */
    setState(data: AgendaData, version: number): void {
        this.currentData = data;
        this.currentVersion = version;
        this.writeToStorage(data, version);
        this.notifyListeners();
    }

    /**
     * Rollback to a previous snapshot.
     */
    rollback(snapshot: AgendaSnapshot): void {
        this.currentData = snapshot.data;
        this.currentVersion = snapshot.version;
        this.writeToStorage(snapshot.data, snapshot.version);
        this.notifyListeners();
    }

    /**
     * Update reservations in the store (optimistic update).
     */
    updateReservations(reservations: LoadedReservation[]): void {
        if (!this.currentData) return;

        this.currentData = {
            ...this.currentData,
            reservations,
        };
        this.writeToStorage(this.currentData, this.currentVersion);
        this.notifyListeners();
    }

    /**
     * Add a single reservation and update version.
     */
    addReservation(reservation: LoadedReservation, newVersion?: number): void {
        if (!this.currentData) return;

        const existing = this.currentData.reservations.find((r) => r.id === reservation.id);
        if (existing) {
            // Update existing
            this.currentData.reservations = this.currentData.reservations.map((r) =>
                r.id === reservation.id ? reservation : r
            );
        } else {
            // Add new
            this.currentData.reservations = [...this.currentData.reservations, reservation];
        }

        // Update version if provided
        if (newVersion !== undefined) {
            this.currentVersion = newVersion;
        }

        // Always persist to localStorage
        this.writeToStorage(this.currentData, this.currentVersion);
        this.notifyListeners();
    }

    /**
     * Remove a reservation and update version.
     */
    removeReservation(reservationId: number, newVersion?: number): void {
        if (!this.currentData) return;

        this.currentData.reservations = this.currentData.reservations.filter((r) => r.id !== reservationId);

        // Update version if provided
        if (newVersion !== undefined) {
            this.currentVersion = newVersion;
        }

        // Always persist to localStorage
        this.writeToStorage(this.currentData, this.currentVersion);
        this.notifyListeners();
    }

    /**
     * Get current state.
     */
    getState(): { data: AgendaData | null; version: number; isLoading: boolean } {
        return {
            data: this.currentData,
            version: this.currentVersion,
            isLoading: this.isLoading,
        };
    }

    /**
     * Subscribe to state changes.
     */
    subscribe(listener: () => void): () => void {
        this.listeners.add(listener);
        return () => this.listeners.delete(listener);
    }

    /**
     * Clear all data (for testing or logout).
     */
    clear(): void {
        this.currentData = null;
        this.currentVersion = 0;
        try {
            localStorage.removeItem(STORAGE_KEY);
        } catch {
            // Ignore storage errors
        }
        this.notifyListeners();
    }

    private notifyListeners(): void {
        this.listeners.forEach((listener) => listener());
    }

    private readFromStorage(): AgendaSnapshot | null {
        try {
            const stored = localStorage.getItem(STORAGE_KEY);
            if (!stored) return null;

            const snapshot = JSON.parse(stored) as AgendaSnapshot;

            // Basic validation
            if (
                typeof snapshot.version !== 'number' ||
                !snapshot.data ||
                !Array.isArray(snapshot.data.reservations)
            ) {
                console.warn('[AgendaStore] Invalid snapshot structure, ignoring');
                return null;
            }

            return snapshot;
        } catch (error) {
            // Safari private mode, quota exceeded, or other storage errors
            console.warn('[AgendaStore] Failed to read from localStorage:', error);
            return null;
        }
    }

    private writeToStorage(data: AgendaData, version: number): void {
        try {
            const snapshot: AgendaSnapshot = {
                version,
                data,
                savedAt: new Date().toISOString(),
            };

            const json = JSON.stringify(snapshot);

            // Check size before writing
            if (json.length > MAX_STORAGE_SIZE) {
                console.warn(
                    `[AgendaStore] Snapshot too large (${(json.length / 1024 / 1024).toFixed(2)}MB), skipping storage`
                );
                // TODO: Implement windowing or compression if needed
                return;
            }

            localStorage.setItem(STORAGE_KEY, json);
            console.info(`[AgendaStore] Saved to localStorage (${(json.length / 1024).toFixed(1)}KB)`);
        } catch (error) {
            // Safari private mode, quota exceeded, or other storage errors
            console.warn('[AgendaStore] Failed to write to localStorage:', error);
        }
    }
}

// Export singleton instance
export const agendaStore = new AgendaStore();

// Export class for testing
export { AgendaStore };
