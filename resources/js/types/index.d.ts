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

declare module '@inertiajs/react' {
    export function usePage<T extends PageProps>(): {
        props: T;
        url: string;
        component: string;
    };
}
