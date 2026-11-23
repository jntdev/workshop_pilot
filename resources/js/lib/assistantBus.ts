/**
 * Canal entre les onglets du workshop (BroadcastChannel, même navigateur, même site).
 *
 * L'assistant vit dans son propre onglet (/assistant). Quand il fait quelque chose (statut d'un devis,
 * pièce commandée, rendez-vous déplacé), il envoie ici ce qui a changé : les pages ouvertes dans les autres
 * onglets mettent leur état à jour sans rafraîchir (voir useAssistantEffects). Les liens cités par
 * l'assistant s'ouvrent dans l'onglet de travail, pas dans celui de la conversation.
 */

export interface QuoteStatusEffect {
    type: 'quote_status';
    quote_id: number;
    status: string | null;
    status_label: string | null;
    work_completed_notified: boolean;
}

export interface OrderLineEffect {
    type: 'order_line';
    line_id: number;
    quote_id: number | null;
    supply_status: 'to_order' | 'ordered' | 'received';
    ordered_at: string | null;
    received_at: string | null;
}

export interface AppointmentMoveEffect {
    type: 'appointment_move';
    /** Comme l'API de l'agenda : « quote » (rendez-vous d'un devis) ou « event » (événement libre). */
    kind: 'quote' | 'event';
    id: number;
    quote_id: number | null;
    starts_at: string;
    ends_at: string;
}

export type AssistantEffect = QuoteStatusEffect | OrderLineEffect | AppointmentMoveEffect;

type BusMessage =
    | { type: 'effect'; effect: AssistantEffect }
    | { type: 'navigate-request'; id: string }
    | { type: 'navigate-offer'; id: string; tabId: string; visible: boolean; lastActiveAt: number }
    | { type: 'navigate-go'; id: string; tabId: string; url: string };

const CHANNEL_NAME = 'workshop-assistant';
const OFFER_WAIT_MS = 300;

/** Fenêtre nommée de l'onglet de travail, utilisée seulement si aucun onglet ne répond. */
export const WORK_TAB_NAME = 'workshop-main';
/** Fenêtre nommée de l'onglet de l'assistant : un second clic sur « Assistant » le réutilise. */
export const ASSISTANT_TAB_NAME = 'assistant-atelier';

let channel: BroadcastChannel | null | undefined;

function getChannel(): BroadcastChannel | null {
    if (channel === undefined) {
        channel = typeof BroadcastChannel === 'undefined' ? null : new BroadcastChannel(CHANNEL_NAME);
    }

    return channel;
}

function post(message: BusMessage): void {
    getChannel()?.postMessage(message);
}

function listen(handler: (message: BusMessage) => void): () => void {
    const current = getChannel();
    if (current === null) {
        return () => undefined;
    }

    const listener = (event: MessageEvent<BusMessage>) => handler(event.data);
    current.addEventListener('message', listener);

    return () => current.removeEventListener('message', listener);
}

/** Dans l'onglet de l'assistant : annonce aux autres onglets ce qui vient de changer. */
export function publishAssistantEffect(effect: AssistantEffect): void {
    post({ type: 'effect', effect });
}

/** Dans un onglet de travail : reçoit les changements faits par l'assistant. */
export function subscribeAssistantEffects(handler: (effect: AssistantEffect) => void): () => void {
    return listen((message) => {
        if (message.type === 'effect') {
            handler(message.effect);
        }
    });
}

// ---------------------------------------------------------------- navigation de l'onglet de travail

const tabId = typeof crypto !== 'undefined' && 'randomUUID' in crypto ? crypto.randomUUID() : String(Math.random());
let lastActiveAt = Date.now();

/**
 * Dans un onglet de travail : se déclare joignable. Quand l'assistant demande d'ouvrir une page, chaque onglet
 * se propose ; celui qui a été actif le plus récemment (visible d'abord) reçoit l'ordre et y navigue.
 */
export function serveNavigationRequests(navigate: (url: string) => void): () => void {
    const markActive = () => {
        lastActiveAt = Date.now();
    };
    window.addEventListener('focus', markActive);
    document.addEventListener('visibilitychange', markActive);
    markActive();

    const stopListening = listen((message) => {
        if (message.type === 'navigate-request') {
            post({
                type: 'navigate-offer',
                id: message.id,
                tabId,
                visible: document.visibilityState === 'visible',
                lastActiveAt,
            });
        }
        if (message.type === 'navigate-go' && message.tabId === tabId) {
            navigate(message.url);
        }
    });

    return () => {
        window.removeEventListener('focus', markActive);
        document.removeEventListener('visibilitychange', markActive);
        stopListening();
    };
}

/**
 * Dans l'onglet de l'assistant : ouvre une page du workshop dans l'onglet de travail.
 * Renvoie « tab » si un onglet de travail l'a ouverte, « opened » si aucun ne répondait (nouvel onglet ouvert).
 */
export function openInWorkTab(url: string): Promise<'tab' | 'opened'> {
    return new Promise((resolve) => {
        const id = crypto.randomUUID();
        const offers: Array<{ tabId: string; visible: boolean; lastActiveAt: number }> = [];

        const stop = listen((message) => {
            if (message.type === 'navigate-offer' && message.id === id) {
                offers.push({ tabId: message.tabId, visible: message.visible, lastActiveAt: message.lastActiveAt });
            }
        });

        post({ type: 'navigate-request', id });

        window.setTimeout(() => {
            stop();

            if (offers.length === 0) {
                window.open(url, WORK_TAB_NAME);
                resolve('opened');

                return;
            }

            const best = [...offers].sort(
                (a, b) => Number(b.visible) - Number(a.visible) || b.lastActiveAt - a.lastActiveAt,
            )[0];
            post({ type: 'navigate-go', id, tabId: best.tabId, url });
            resolve('tab');
        }, OFFER_WAIT_MS);
    });
}
