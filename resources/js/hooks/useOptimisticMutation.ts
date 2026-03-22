import { useCallback, useState } from 'react';
import { agendaStore } from '@/stores/agendaStore';
import type { LoadedReservation, AgendaSnapshot } from '@/types';

/**
 * Validation errors from a 422 response.
 */
export interface ValidationErrors {
    [field: string]: string[];
}

/**
 * Custom error class for validation errors (422).
 */
export class ValidationError extends Error {
    constructor(
        message: string,
        public readonly errors: ValidationErrors
    ) {
        super(message);
        this.name = 'ValidationError';
    }
}

/**
 * Result type for mutations that can fail with validation errors.
 */
export type MutationResult<T> =
    | { success: true; data: T }
    | { success: false; validationErrors?: ValidationErrors; message: string };

interface MutationOptions {
    /** Called before the API request with optimistic data */
    onOptimistic?: () => void;
    /** Called on successful response */
    onSuccess?: (data: unknown) => void;
    /** Called on error after rollback */
    onError?: (error: Error) => void;
}

interface UseMutationResult {
    /** Whether a mutation is in progress */
    isLoading: boolean;
    /** Execute a mutation with optimistic update */
    mutate: <T>(
        apiFn: () => Promise<T>,
        options?: MutationOptions
    ) => Promise<T | null>;
    /** Create a reservation with optimistic update, returns validation errors if any */
    createReservation: (
        payload: Record<string, unknown>,
        optimisticReservation?: Partial<LoadedReservation>
    ) => Promise<MutationResult<LoadedReservation>>;
    /** Update a reservation with optimistic update, returns validation errors if any */
    updateReservation: (
        id: number,
        payload: Record<string, unknown>,
        optimisticChanges?: Partial<LoadedReservation>
    ) => Promise<MutationResult<LoadedReservation>>;
    /** Delete a reservation with optimistic update */
    deleteReservation: (id: number) => Promise<boolean>;
}

/**
 * Hook for executing API mutations with optimistic updates and rollback.
 */
export function useOptimisticMutation(): UseMutationResult {
    const [isLoading, setIsLoading] = useState(false);

    const mutate = useCallback(async <T>(
        apiFn: () => Promise<T>,
        options?: MutationOptions
    ): Promise<T | null> => {
        // Take snapshot before mutation
        const snapshot = agendaStore.snapshot();

        // Apply optimistic update if provided
        options?.onOptimistic?.();

        setIsLoading(true);

        try {
            const result = await apiFn();

            // On success, update the store with official data
            options?.onSuccess?.(result);

            return result;
        } catch (error) {
            console.error('[useOptimisticMutation] Mutation failed:', error);

            // Rollback on error
            if (snapshot) {
                console.info('[useOptimisticMutation] Rolling back to previous state');
                agendaStore.rollback(snapshot);
            }

            // Check for version conflict (409)
            if (error instanceof Error && error.message.includes('409')) {
                console.info('[useOptimisticMutation] Version conflict, forcing refresh');
                try {
                    await agendaStore.forceRefresh();
                } catch {
                    // Ignore refresh errors
                }
            }

            options?.onError?.(error instanceof Error ? error : new Error('Unknown error'));

            return null;
        } finally {
            setIsLoading(false);
        }
    }, []);

    const createReservation = useCallback(async (
        payload: Record<string, unknown>,
        _optimisticReservation?: Partial<LoadedReservation>
    ): Promise<MutationResult<LoadedReservation>> => {
        try {
            const response = await fetch('/api/reservations', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-XSRF-TOKEN': getCsrfToken(),
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload),
            });

            const json = await response.json().catch(() => ({}));

            // Handle validation errors (422)
            if (response.status === 422 && json.errors) {
                return {
                    success: false,
                    validationErrors: json.errors as ValidationErrors,
                    message: json.message || 'Erreur de validation',
                };
            }

            if (!response.ok) {
                return {
                    success: false,
                    message: json.message || `HTTP ${response.status}`,
                };
            }

            const reservation = json.data as LoadedReservation;
            const version = json.version as number;
            agendaStore.addReservation(reservation, version);

            return { success: true, data: reservation };
        } catch (error) {
            console.error('[useOptimisticMutation] createReservation failed:', error);
            return {
                success: false,
                message: error instanceof Error ? error.message : 'Erreur inconnue',
            };
        }
    }, []);

    const updateReservation = useCallback(async (
        id: number,
        payload: Record<string, unknown>,
        optimisticChanges?: Partial<LoadedReservation>
    ): Promise<MutationResult<LoadedReservation>> => {
        // Take snapshot for rollback
        const snapshot = agendaStore.snapshot();

        // Apply optimistic update if provided
        if (optimisticChanges) {
            const state = agendaStore.getState();
            if (state.data) {
                const updatedReservations = state.data.reservations.map((r) =>
                    r.id === id ? { ...r, ...optimisticChanges } : r
                );
                agendaStore.updateReservations(updatedReservations);
            }
        }

        try {
            const response = await fetch(`/api/reservations/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-XSRF-TOKEN': getCsrfToken(),
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload),
            });

            const json = await response.json().catch(() => ({}));

            // Handle validation errors (422)
            if (response.status === 422 && json.errors) {
                // Rollback on validation error
                if (snapshot) {
                    agendaStore.rollback(snapshot);
                }
                return {
                    success: false,
                    validationErrors: json.errors as ValidationErrors,
                    message: json.message || 'Erreur de validation',
                };
            }

            if (!response.ok) {
                // Rollback on other errors
                if (snapshot) {
                    agendaStore.rollback(snapshot);
                }
                return {
                    success: false,
                    message: json.message || `HTTP ${response.status}`,
                };
            }

            const reservation = json.data as LoadedReservation;
            const version = json.version as number;
            agendaStore.addReservation(reservation, version);

            return { success: true, data: reservation };
        } catch (error) {
            console.error('[useOptimisticMutation] updateReservation failed:', error);
            // Rollback on exception
            if (snapshot) {
                agendaStore.rollback(snapshot);
            }
            return {
                success: false,
                message: error instanceof Error ? error.message : 'Erreur inconnue',
            };
        }
    }, []);

    const deleteReservation = useCallback(async (id: number): Promise<boolean> => {
        const result = await mutate(
            async () => {
                const response = await fetch(`/api/reservations/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-XSRF-TOKEN': getCsrfToken(),
                    },
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || `HTTP ${response.status}`);
                }

                const json = await response.json();
                return json.version as number;
            },
            {
                onOptimistic: () => {
                    agendaStore.removeReservation(id);
                },
                onSuccess: (version) => {
                    // Update version after successful delete
                    agendaStore.removeReservation(id, version as number);
                },
            }
        );

        return result !== null;
    }, [mutate]);

    return {
        isLoading,
        mutate,
        createReservation,
        updateReservation,
        deleteReservation,
    };
}

/**
 * Get CSRF token from cookies.
 */
function getCsrfToken(): string {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
}
