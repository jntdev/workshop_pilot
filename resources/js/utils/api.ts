/**
 * Helper API pour les appels fetch avec credentials et CSRF token
 */

/**
 * Récupère le token CSRF depuis les cookies
 */
export const getCsrfToken = (): string => {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
};

/**
 * Headers par défaut pour les requêtes API
 */
export const apiHeaders = (): HeadersInit => ({
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'X-XSRF-TOKEN': getCsrfToken(),
});

/**
 * Options par défaut pour les requêtes fetch
 */
const defaultOptions: RequestInit = {
    credentials: 'same-origin',
};

/**
 * Effectue une requête GET
 */
export const apiGet = async <T>(url: string): Promise<T> => {
    const response = await fetch(url, {
        ...defaultOptions,
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    });

    if (!response.ok) {
        throw new ApiError(response.status, await response.json());
    }

    return response.json();
};

/**
 * Effectue une requête POST
 */
export const apiPost = async <T>(url: string, data?: unknown): Promise<T> => {
    const response = await fetch(url, {
        ...defaultOptions,
        method: 'POST',
        headers: apiHeaders(),
        body: data ? JSON.stringify(data) : undefined,
    });

    if (!response.ok) {
        throw new ApiError(response.status, await response.json());
    }

    return response.json();
};

/**
 * Effectue une requête PUT
 */
export const apiPut = async <T>(url: string, data?: unknown): Promise<T> => {
    const response = await fetch(url, {
        ...defaultOptions,
        method: 'PUT',
        headers: apiHeaders(),
        body: data ? JSON.stringify(data) : undefined,
    });

    if (!response.ok) {
        throw new ApiError(response.status, await response.json());
    }

    return response.json();
};

/**
 * Effectue une requête DELETE
 */
export const apiDelete = async (url: string): Promise<void> => {
    const response = await fetch(url, {
        ...defaultOptions,
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-XSRF-TOKEN': getCsrfToken(),
        },
    });

    if (!response.ok && response.status !== 204) {
        throw new ApiError(response.status, await response.json());
    }
};

/**
 * Classe d'erreur API personnalisée
 */
export class ApiError extends Error {
    public status: number;
    public data: ApiErrorData;

    constructor(status: number, data: ApiErrorData) {
        super(data.message || 'Une erreur est survenue');
        this.status = status;
        this.data = data;
    }

    /**
     * Vérifie si c'est une erreur de validation (422)
     */
    isValidationError(): boolean {
        return this.status === 422;
    }

    /**
     * Récupère les erreurs de validation formatées
     */
    getValidationErrors(): Record<string, string> {
        if (!this.data.errors) return {};

        const formatted: Record<string, string> = {};
        Object.keys(this.data.errors).forEach(key => {
            const errors = this.data.errors![key];
            formatted[key] = Array.isArray(errors) ? errors[0] : errors;
        });
        return formatted;
    }
}

interface ApiErrorData {
    message?: string;
    errors?: Record<string, string | string[]>;
}
