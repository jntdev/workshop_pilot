import type { AssistantMessage } from './types';

/**
 * État du chat gardé hors de React.
 *
 * Chaque page du workshop crée sa propre mise en page : le composant de chat est donc recréé à chaque
 * changement de page. Ce stockage en mémoire (et en sessionStorage pour l'ouverture et le brouillon)
 * permet de retrouver la fenêtre « telle quelle » : ouverte, conversation affichée, brouillon en cours.
 */
interface ChatSnapshot {
    isOpen: boolean;
    draft: string;
    messages: AssistantMessage[];
}

const SESSION_KEY = 'assistant.chat_state';

function loadPersisted(): Pick<ChatSnapshot, 'isOpen' | 'draft'> {
    try {
        const raw = window.sessionStorage.getItem(SESSION_KEY);
        if (raw) {
            const parsed = JSON.parse(raw) as Partial<ChatSnapshot>;

            return { isOpen: parsed.isOpen === true, draft: typeof parsed.draft === 'string' ? parsed.draft : '' };
        }
    } catch {
        // stockage indisponible : la fenêtre repart fermée
    }

    return { isOpen: false, draft: '' };
}

let snapshot: ChatSnapshot = { ...loadPersisted(), messages: [] };

export function readChatSnapshot(): ChatSnapshot {
    return snapshot;
}

export function saveChatSnapshot(patch: Partial<ChatSnapshot>): void {
    snapshot = { ...snapshot, ...patch };

    try {
        window.sessionStorage.setItem(SESSION_KEY, JSON.stringify({ isOpen: snapshot.isOpen, draft: snapshot.draft }));
    } catch {
        // stockage indisponible
    }
}

/**
 * Décalage de la fenêtre par rapport à sa place d'origine (en bas à droite), en pixels.
 * C'est une préférence de l'utilisateur : elle est gardée d'une session à l'autre (localStorage).
 */
export interface ChatOffset {
    x: number;
    y: number;
}

const OFFSET_KEY = 'assistant.chat_offset';

function loadOffset(): ChatOffset {
    try {
        const raw = window.localStorage.getItem(OFFSET_KEY);
        if (raw) {
            const parsed = JSON.parse(raw) as Partial<ChatOffset>;
            if (Number.isFinite(parsed.x) && Number.isFinite(parsed.y)) {
                return { x: Number(parsed.x), y: Number(parsed.y) };
            }
        }
    } catch {
        // stockage indisponible : la fenêtre reste à sa place d'origine
    }

    return { x: 0, y: 0 };
}

let offset: ChatOffset = loadOffset();

export function readChatOffset(): ChatOffset {
    return offset;
}

export function saveChatOffset(next: ChatOffset): void {
    offset = next;

    try {
        window.localStorage.setItem(OFFSET_KEY, JSON.stringify(next));
    } catch {
        // stockage indisponible
    }
}
