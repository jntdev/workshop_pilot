export interface User {
    id: number;
    name: string;
    email: string;
}

export interface PageProps {
    auth: {
        user: User | null;
    };
    flash: {
        message: string | null;
        error: string | null;
    };
}

export interface Client {
    id: number;
    prenom: string;
    nom: string;
    email: string | null;
    telephone: string | null;
    adresse: string | null;
    origine_contact: string | null;
    commentaires: string | null;
    avantage_type: 'aucun' | 'pourcentage' | 'montant';
    avantage_valeur: number;
    avantage_expiration: string | null;
}

export interface ClientFormData {
    prenom: string;
    nom: string;
    telephone: string;
    email: string;
    adresse: string;
    origine_contact: string;
    commentaires: string;
    avantage_type: 'aucun' | 'pourcentage' | 'montant';
    avantage_valeur: number;
    avantage_expiration: string;
}

export interface ClientFormPageProps extends PageProps {
    client?: Client;
}

export interface QuoteLine {
    id?: number;
    title: string;
    reference: string | null;
    quantity: string;
    purchase_price_ht: string;
    sale_price_ht: string;
    sale_price_ttc: string;
    margin_amount_ht: string;
    margin_rate: string;
    tva_rate: string;
    line_purchase_ht?: string;
    line_margin_ht?: string;
    line_total_ht?: string;
    line_total_ttc?: string;
    position: number;
    estimated_time_minutes?: number | null;
}

export interface QuoteTotals {
    total_ht: string;
    total_tva: string;
    total_ttc: string;
    margin_total_ht: string;
    total_estimated_time_minutes?: number | null;
}

export interface QuoteDetail {
    id: number;
    reference: string;
    client_id: number;
    client: Client;
    bike_description: string | null;
    reception_comment: string | null;
    remarks: string | null;
    valid_until: string;
    discount_type: 'amount' | 'percent' | null;
    discount_value: string | null;
    total_ht: string;
    total_tva: string;
    total_ttc: string;
    margin_total_ht: string;
    total_estimated_time_minutes: number | null;
    actual_time_minutes: number | null;
    invoiced_at: string | null;
    created_at: string;
    is_invoice: boolean;
    can_edit: boolean;
    can_delete: boolean;
    lines: QuoteLine[];
}

export interface Quote {
    id: number;
    reference: string;
    client_id: number;
    client: Client;
    bike_description: string | null;
    total_ht: string;
    total_tva: string;
    total_ttc: string;
    margin_total_ht: string;
    invoiced_at: string | null;
    created_at: string;
    can_delete: boolean;
    is_invoice: boolean;
}

export interface KpiStats {
    revenue: number;
    margin: number;
    count: number;
    margin_rate: number;
}

export interface AtelierPageProps extends PageProps {
    stats: KpiStats;
    comparisonStats: KpiStats;
    selectedYear: number;
    selectedMonth: number;
    availableYears: number[];
    quotes: Quote[];
    invoices: Quote[];
}

export interface QuoteShowPageProps extends PageProps {
    quote: QuoteDetail;
}

export interface QuoteFormPageProps extends PageProps {
    quote?: QuoteDetail;
}

// Location vélos
export type BikeCategory = 'VAE' | 'VTC';
export type BikeSize = 'S' | 'M' | 'L' | 'XL';
export type BikeFrameType = 'b' | 'h'; // b = cadre bas, h = cadre haut
export type BikeStatus = 'OK' | 'HS';

export interface BikeDefinition {
    id: string;
    category: BikeCategory;
    size: BikeSize;
    frame_type: BikeFrameType;
    label: string;
    status: BikeStatus;
    notes: string | null;
}

export type AvailabilityStatus = 'available' | 'reserved' | 'pre_reserved' | 'maintenance';

export interface DayInfo {
    date: string; // Format ISO YYYY-MM-DD
    dayOfWeek: number; // 0 = dimanche, 6 = samedi
    dayNumber: number;
    monthShort: string;
    isToday: boolean;
    isWeekend: boolean;
}

// Réservation chargée pour affichage dans la grille et édition
export interface LoadedReservation {
    id: number;
    client_id: number | null;
    client_name: string;
    client: Client | null;
    date_contact: string | null;
    date_reservation: string;
    date_retour: string;
    livraison_necessaire: boolean;
    adresse_livraison: string | null;
    contact_livraison: string | null;
    creneau_livraison: string | null;
    recuperation_necessaire: boolean;
    adresse_recuperation: string | null;
    contact_recuperation: string | null;
    creneau_recuperation: string | null;
    prix_total_ttc: string;
    acompte_demande: boolean;
    acompte_montant: string | null;
    acompte_paye_le: string | null;
    paiement_final_le: string | null;
    statut: ReservationStatut;
    raison_annulation: string | null;
    commentaires: string | null;
    color: ReservationColorIndex;
    selection: SelectionBike[];
    items: ReservationItem[];
}

export interface LocationPageProps extends PageProps {
    bikes: BikeDefinition[];
    bikeTypes: BikeType[];
    year: number;
    reservations: LoadedReservation[];
}

export interface LineCalculationResult {
    sale_price_ht: string;
    sale_price_ttc: string;
    margin_amount_ht: string;
    margin_rate: string;
    line_purchase_ht?: string;
    line_margin_ht?: string;
    line_total_ht?: string;
    line_total_ttc?: string;
}

// Réservations location
export type ReservationStatut = 'reserve' | 'en_attente_acompte' | 'en_cours' | 'paye' | 'annule';

export interface BikeType {
    id: string; // ex: VAE_sb
    category: BikeCategory;
    size: BikeSize;
    frame_type: BikeFrameType;
    label: string;
    stock: number;
}

export interface ReservationItem {
    id?: number;
    bike_type_id: string;
    bike_type?: BikeType;
    quantite: number;
}

export interface Reservation {
    id: number;
    client_id: number;
    client?: Client;
    date_contact: string;
    date_reservation: string;
    date_retour: string;
    livraison_necessaire: boolean;
    adresse_livraison: string | null;
    contact_livraison: string | null;
    creneau_livraison: string | null;
    recuperation_necessaire: boolean;
    adresse_recuperation: string | null;
    contact_recuperation: string | null;
    creneau_recuperation: string | null;
    prix_total_ttc: string;
    acompte_demande: boolean;
    acompte_montant: string | null;
    acompte_paye_le: string | null;
    paiement_final_le: string | null;
    statut: ReservationStatut;
    raison_annulation: string | null;
    commentaires: string | null;
    items: ReservationItem[];
    created_at: string;
    updated_at: string;
}

export interface ReservationFormData {
    client_id: number | null;
    date_contact: string;
    date_reservation: string;
    date_retour: string;
    livraison_necessaire: boolean;
    adresse_livraison: string;
    contact_livraison: string;
    creneau_livraison: string;
    recuperation_necessaire: boolean;
    adresse_recuperation: string;
    contact_recuperation: string;
    creneau_recuperation: string;
    prix_total_ttc: string;
    acompte_demande: boolean;
    acompte_montant: string;
    acompte_paye_le: string;
    paiement_final_le: string;
    statut: ReservationStatut;
    raison_annulation: string;
    commentaires: string;
    items: ReservationItem[];
    selection: SelectionBike[];
}

// Selection calendrier pour réservation
export interface SelectedCell {
    bikeId: string;
    date: string;
    isHS: boolean;
}

export interface SelectionBike {
    bike_id: string;
    label: string;
    start_date: string;
    end_date: string;
    dates: string[];
    is_hs: boolean;
}

// Index de couleur pour les réservations (0-29)
// 0-9: Saturées, 10-19: Claires, 20-29: Très claires
export type ReservationColorIndex = 0 | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10 | 11 | 12 | 13 | 14 | 15 | 16 | 17 | 18 | 19 | 20 | 21 | 22 | 23 | 24 | 25 | 26 | 27 | 28 | 29;

export interface ReservationDraft {
    id: string;
    cells: Map<string, SelectedCell>;
    isActive: boolean;
    color: ReservationColorIndex;
    editingReservationId: number | null; // ID de la réservation en cours d'édition (null = nouvelle)
}

export interface ReservationDraftActions {
    startSelection: () => void;
    cancelSelection: () => void;
    toggleCell: (cell: SelectedCell) => void;
    removeBike: (bikeId: string) => void;
    clearSelection: () => void;
    setColor: (color: ReservationColorIndex) => void;
    loadReservation: (reservation: LoadedReservation) => void;
}

export interface BikesByPeriod {
    periodKey: string;
    startDate: string;
    endDate: string;
    bikes: SelectionBike[];
}

export interface ReservationDraftSelectors {
    selectedBikes: SelectionBike[];
    bikesByPeriod: BikesByPeriod[];
    globalMinDate: string | null;
    globalMaxDate: string | null;
    hasHSBikes: boolean;
    selectedBikeIds: Set<string>;
    items: ReservationItem[];
}

declare module '@inertiajs/react' {
    export function usePage<T extends PageProps>(): {
        props: T;
        url: string;
        component: string;
    };
}
