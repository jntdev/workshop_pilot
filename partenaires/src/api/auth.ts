import client from './client';

export interface PartenaireData {
    id: number;
    nom: string;
    email: string;
}

export interface AuthResponse {
    token: string;
    partenaire: PartenaireData;
}

export const inscription = (data: {
    nom: string;
    email: string;
    password: string;
    password_confirmation: string;
}): Promise<AuthResponse> =>
    client.post('/api/partenaires/inscription', data).then((r) => r.data);

export const login = (data: {
    email: string;
    password: string;
}): Promise<AuthResponse> =>
    client.post('/api/partenaires/login', data).then((r) => r.data);

export const logout = (): Promise<void> =>
    client.post('/api/partenaires/logout').then(() => undefined);

export const me = (): Promise<PartenaireData> =>
    client.get('/api/partenaires/me').then((r) => r.data);
