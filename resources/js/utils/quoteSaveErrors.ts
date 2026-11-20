import type { QuoteLine } from '@/types';

export interface SaveErrorItem {
    text: string;
    lineKey?: string;
}

export interface SaveErrorFeedback {
    title: string;
    items: SaveErrorItem[];
}

/** Ligne envoyée au serveur, avec son numéro tel qu'affiché dans le tableau (1 = première ligne). */
export interface SentLine {
    line: QuoteLine;
    rowNumber: number;
}

/** Erreurs par ligne (clé de ligne) puis par champ, pour signaler les cases en cause dans le tableau. */
export type LineFieldErrors = Record<string, Record<string, string>>;

const FIELD_LABELS: Record<string, string> = {
    client_id: 'client',
    client_prenom: 'prénom du client',
    client_nom: 'nom du client',
    client_email: 'e-mail du client',
    client_telephone: 'téléphone du client',
    client_adresse: 'adresse du client',
    client_origine_contact: 'origine du contact',
    client_commentaires: 'commentaires client',
    client_avantage_type: "type d'avantage client",
    client_avantage_valeur: "valeur de l'avantage client",
    client_avantage_expiration: "date d'expiration de l'avantage client",
    bike_description: 'vélo',
    reception_comment: 'commentaire de réception',
    remarks: 'remarques',
    email_note: "note de l'e-mail",
    valid_until: 'date de validité',
    discount_type: 'type de remise',
    discount_value: 'remise',
    actual_time_minutes: 'temps réel',
    lines: 'prestations',
    totals: 'totaux du devis',
};

const LINE_FIELD_LABELS: Record<string, string> = {
    title: 'intitulé',
    reference: 'référence',
    quantity: 'quantité',
    purchase_price_ht: "prix d'achat HT (PA HT)",
    sale_price_ht: 'prix de vente HT',
    sale_price_ttc: 'prix de vente TTC (PV TTC)',
    margin_amount_ht: 'marge',
    margin_rate: 'taux de marge',
    tva_rate: 'TVA',
    line_purchase_ht: 'total PA',
    line_margin_ht: 'marge de la ligne',
    line_total_ht: 'total HT',
    line_total_ttc: 'total TTC',
    estimated_time_minutes: 'temps estimé',
    article_id: 'article du catalogue',
    needs_order: 'case « à commander »',
    ordered_at: 'date de commande',
    received_at: 'date de réception',
};

/** Champs de ligne dont la colonne est masquée en mode Comptoir. */
const COMPTOIR_HIDDEN_LINE_FIELDS = [
    'purchase_price_ht',
    'line_purchase_ht',
    'margin_amount_ht',
    'margin_rate',
    'line_margin_ht',
    'estimated_time_minutes',
];

const LINE_ERROR_KEY = /^lines\.(\d+)\.(.+)$/;
const TITLE_MAX_LENGTH = 40;

/** Identifiant stable d'une ligne du tableau, partagé entre le formulaire et le tableau des lignes. */
export function quoteLineKey(line: QuoteLine): string {
    return line.id != null ? `id-${line.id}` : `key-${line.client_key ?? ''}`;
}

export function isComptoirModeActive(): boolean {
    return document.querySelector('[data-privacy-mode="comptoir"]') !== null;
}

export function saveErrorTitle(count: number): string {
    return `Devis non enregistré : ${count} point${count > 1 ? 's' : ''} à corriger`;
}

/** Remplace le nom technique du champ (« lines.5.purchase_price_ht ») par son libellé. */
function withLabel(message: string, rawKey: string, label: string): string {
    for (const variant of [rawKey, rawKey.replace(/_/g, ' ')]) {
        if (message.includes(variant)) {
            return message.split(variant).join(label);
        }
    }

    return message;
}

function lowerFirst(text: string): string {
    return text.charAt(0).toLowerCase() + text.slice(1);
}

function shortTitle(title: string): string {
    const trimmed = title.trim();

    return trimmed.length > TITLE_MAX_LENGTH ? `${trimmed.slice(0, TITLE_MAX_LENGTH - 1).trimEnd()}…` : trimmed;
}

/**
 * Traduit les erreurs de validation du serveur en messages lisibles : numéro de ligne tel
 * qu'affiché, intitulé de la ligne et libellé du champ, au lieu de « lines.5.purchase_price_ht ».
 */
export function describeSaveErrors(
    errors: Record<string, string | string[]>,
    sentLines: SentLine[],
    isComptoirMode: boolean,
): { items: SaveErrorItem[]; fieldErrors: Record<string, string>; lineErrors: LineFieldErrors } {
    const generalItems: SaveErrorItem[] = [];
    const lineItems: (SaveErrorItem & { rowNumber: number })[] = [];
    const fieldErrors: Record<string, string> = {};
    const lineErrors: LineFieldErrors = {};

    Object.entries(errors).forEach(([key, value]) => {
        const message = Array.isArray(value) ? value[0] : String(value);
        const lineMatch = key.match(LINE_ERROR_KEY);
        const sent = lineMatch ? sentLines[Number(lineMatch[1])] : undefined;

        if (!lineMatch || !sent) {
            const rootField = key.split('.')[0];
            const readable = withLabel(message, key, FIELD_LABELS[key] ?? FIELD_LABELS[rootField] ?? key);
            fieldErrors[key] = readable;
            generalItems.push({ text: readable });
            return;
        }

        const field = lineMatch[2];
        const readable = withLabel(message, key, LINE_FIELD_LABELS[field] ?? field);
        const lineKey = quoteLineKey(sent.line);
        const hiddenInComptoir = isComptoirMode && COMPTOIR_HIDDEN_LINE_FIELDS.includes(field);

        lineErrors[lineKey] = { ...lineErrors[lineKey], [field]: readable };
        lineItems.push({
            rowNumber: sent.rowNumber,
            lineKey,
            text: `Ligne ${sent.rowNumber} « ${shortTitle(sent.line.title)} » : ${lowerFirst(readable)}`
                + (hiddenInComptoir ? ' Colonne masquée en mode Comptoir : passez en mode Atelier (Ctrl+Maj+P) pour la corriger.' : ''),
        });
    });

    lineItems.sort((a, b) => a.rowNumber - b.rowNumber);

    return {
        items: [...generalItems, ...lineItems.map(({ text, lineKey }) => ({ text, lineKey }))],
        fieldErrors,
        lineErrors,
    };
}
