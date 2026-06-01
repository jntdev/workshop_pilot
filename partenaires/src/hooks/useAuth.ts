import { useState } from 'react';
import * as authApi from '../api/auth';
import type { PartenaireData } from '../api/auth';

const TOKEN_KEY = 'partenaire_token';
const DATA_KEY = 'partenaire_data';

const loadStored = (): PartenaireData | null => {
    try {
        const raw = localStorage.getItem(DATA_KEY);
        return raw ? JSON.parse(raw) : null;
    } catch {
        return null;
    }
};

export function useAuth() {
    const [token, setToken] = useState<string | null>(() => localStorage.getItem(TOKEN_KEY));
    const [partenaire, setPartenaire] = useState<PartenaireData | null>(loadStored);

    const persist = (t: string, p: PartenaireData) => {
        localStorage.setItem(TOKEN_KEY, t);
        localStorage.setItem(DATA_KEY, JSON.stringify(p));
        setToken(t);
        setPartenaire(p);
    };

    const clear = () => {
        localStorage.removeItem(TOKEN_KEY);
        localStorage.removeItem(DATA_KEY);
        setToken(null);
        setPartenaire(null);
    };

    const register = async (data: {
        nom: string;
        email: string;
        password: string;
        password_confirmation: string;
    }) => {
        const res = await authApi.inscription(data);
        persist(res.token, res.partenaire);
    };

    const login = async (email: string, password: string) => {
        const res = await authApi.login({ email, password });
        persist(res.token, res.partenaire);
    };

    const logout = async () => {
        try {
            await authApi.logout();
        } finally {
            clear();
        }
    };

    return {
        isAuthenticated: !!token,
        partenaire,
        register,
        login,
        logout,
    };
}
