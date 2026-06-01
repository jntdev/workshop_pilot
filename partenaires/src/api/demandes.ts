import client from './client';

export type Creneau = 'journee' | 'matin' | 'apres-midi';

export type StatutDemande = 'en_attente' | 'confirmee' | 'annulee';

export interface Client {
    taille_cm: number;
}

export interface Demande {
    id: number;
    date_debut: string;
    date_fin: string;
    creneau: Creneau;
    clients: Client[];
    nb_clients: number;
    commentaire: string | null;
    statut: StatutDemande;
    created_at: string;
}

export interface StoreDemande {
    date_debut: string;
    date_fin: string;
    creneau: Creneau;
    clients: Client[];
    commentaire?: string;
}

export const getDemandes = (): Promise<Demande[]> =>
    client.get('/api/partenaires/demandes').then((r) => r.data);

export const storeDemande = (data: StoreDemande): Promise<Demande> =>
    client.post('/api/partenaires/demandes', data).then((r) => r.data);
