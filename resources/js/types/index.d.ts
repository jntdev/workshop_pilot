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
}

export interface QuoteTotals {
    total_ht: string;
    total_tva: string;
    total_ttc: string;
    margin_total_ht: string;
}

export interface QuoteDetail {
    id: number;
    reference: string;
    client_id: number;
    client: Client;
    bike_description: string | null;
    reception_comment: string | null;
    valid_until: string;
    discount_type: 'amount' | 'percent' | null;
    discount_value: string | null;
    total_ht: string;
    total_tva: string;
    total_ttc: string;
    margin_total_ht: string;
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

declare module '@inertiajs/react' {
    export function usePage<T extends PageProps>(): {
        props: T;
        url: string;
        component: string;
    };
}
