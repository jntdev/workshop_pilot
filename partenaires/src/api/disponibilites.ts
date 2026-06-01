import client from './client';

export interface Disponibilite {
    categorie: string;
    taille: string;
    stock_operationnel: number;
    reserve: number;
    disponible: number;
}

export interface DisponibilitesResponse {
    date_debut: string;
    date_fin: string;
    disponibilites: Disponibilite[];
}

export const getDisponibilites = (dateDebut: string, dateFin: string): Promise<DisponibilitesResponse> =>
    client.get('/api/partenaires/disponibilites', {
        params: { date_debut: dateDebut, date_fin: dateFin },
    }).then((r) => r.data);
