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
