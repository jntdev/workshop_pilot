import {
    CSSProperties,
    FormEvent,
    KeyboardEvent,
    MouseEvent as ReactMouseEvent,
    PointerEvent as ReactPointerEvent,
    useCallback,
    useEffect,
    useRef,
    useState,
} from 'react';
import { Link } from '@inertiajs/react';
import { ApiError, apiGet, apiPost } from '@/utils/api';
import AssistantActionCard from './AssistantActionCard';
import AssistantText from './AssistantText';
import { ChatOffset, readChatOffset, readChatSnapshot, saveChatOffset, saveChatSnapshot } from './assistantStore';
import type { AssistantAction, AssistantMessage } from './types';

const STORAGE_KEY = 'assistant.conversation_id';
const POLL_MESSAGES_MS = 2000;
const POLL_STATUS_MS = 30000;
const SLOW_AFTER_MS = 90000;
const VIEWPORT_MARGIN_PX = 8;
const DOCKED_MARGIN_PX = 16;
const MOBILE_QUERY = '(max-width: 640px)';

/** Sur petit écran la fenêtre occupe presque tout l'écran : elle ne se déplace pas. */
function isMobileViewport(): boolean {
    return window.matchMedia(MOBILE_QUERY).matches;
}

function newConversationId(): string {
    return crypto.randomUUID();
}

function readConversationId(): string {
    try {
        const stored = window.localStorage.getItem(STORAGE_KEY);
        if (stored) {
            return stored;
        }
    } catch {
        // stockage indisponible : la conversation ne survit pas au rechargement
    }

    return newConversationId();
}

function saveConversationId(id: string): void {
    try {
        window.localStorage.setItem(STORAGE_KEY, id);
    } catch {
        // stockage indisponible
    }
}

function isUnfinished(message: AssistantMessage): boolean {
    return message.status === 'pending' || message.status === 'processing';
}

export default function AssistantChat() {
    // L'état de départ vient du stockage : la fenêtre est recréée à chaque changement de page.
    const [isOpen, setIsOpen] = useState<boolean>(() => readChatSnapshot().isOpen);
    const [conversationId, setConversationId] = useState<string>(readConversationId);
    const [messages, setMessages] = useState<AssistantMessage[]>(() => readChatSnapshot().messages);
    const [draft, setDraft] = useState<string>(() => readChatSnapshot().draft);
    const [isSending, setIsSending] = useState(false);
    const [errorText, setErrorText] = useState<string | null>(null);
    const [isOnline, setIsOnline] = useState<boolean | null>(null);
    const [now, setNow] = useState<number>(() => Date.now());
    const [offset, setOffset] = useState<ChatOffset>(() => readChatOffset());
    const bottomRef = useRef<HTMLDivElement>(null);
    const panelRef = useRef<HTMLElement>(null);
    const offsetRef = useRef<ChatOffset>(offset);
    const dragRef = useRef<{ pointerX: number; pointerY: number; origin: ChatOffset } | null>(null);

    const hasUnfinished = messages.some(isUnfinished);

    const applyOffset = useCallback((next: ChatOffset, persist: boolean) => {
        offsetRef.current = next;
        setOffset(next);
        if (persist) {
            saveChatOffset(next);
        }
    }, []);

    // Garde la fenêtre entièrement visible : la place d'origine se déduit de la position actuelle moins le décalage.
    const clampOffset = useCallback((candidate: ChatOffset): ChatOffset => {
        const panel = panelRef.current;
        if (!panel) {
            return candidate;
        }

        const rect = panel.getBoundingClientRect();
        const originLeft = rect.left - offsetRef.current.x;
        const originTop = rect.top - offsetRef.current.y;
        const minX = VIEWPORT_MARGIN_PX - originLeft;
        const maxX = window.innerWidth - rect.width - VIEWPORT_MARGIN_PX - originLeft;
        const minY = VIEWPORT_MARGIN_PX - originTop;
        const maxY = window.innerHeight - rect.height - VIEWPORT_MARGIN_PX - originTop;

        return {
            x: Math.min(Math.max(candidate.x, minX), Math.max(minX, maxX)),
            y: Math.min(Math.max(candidate.y, minY), Math.max(minY, maxY)),
        };
    }, []);

    // À l'ouverture et quand la fenêtre du navigateur change de taille : la fenêtre ne doit pas sortir de l'écran.
    useEffect(() => {
        if (!isOpen) {
            return;
        }

        const keepVisible = () => {
            if (isMobileViewport()) {
                return;
            }
            const clamped = clampOffset(offsetRef.current);
            if (clamped.x !== offsetRef.current.x || clamped.y !== offsetRef.current.y) {
                applyOffset(clamped, true);
            }
        };

        keepVisible();
        window.addEventListener('resize', keepVisible);

        return () => window.removeEventListener('resize', keepVisible);
    }, [isOpen, applyOffset, clampOffset]);

    const handleDragStart = (event: ReactPointerEvent<HTMLElement>) => {
        if (event.button !== 0 || isMobileViewport() || (event.target as HTMLElement).closest('button, a')) {
            return;
        }

        event.currentTarget.setPointerCapture(event.pointerId);
        dragRef.current = { pointerX: event.clientX, pointerY: event.clientY, origin: offsetRef.current };
    };

    const handleDragMove = (event: ReactPointerEvent<HTMLElement>) => {
        const drag = dragRef.current;
        if (!drag) {
            return;
        }

        applyOffset(
            clampOffset({
                x: drag.origin.x + event.clientX - drag.pointerX,
                y: drag.origin.y + event.clientY - drag.pointerY,
            }),
            false,
        );
    };

    const handleDragEnd = () => {
        if (dragRef.current) {
            dragRef.current = null;
            saveChatOffset(offsetRef.current);
        }
    };

    // Bouton « ⇄ » : à gauche si la fenêtre est à droite, et inversement (retour à la place d'origine).
    const toggleSide = () => {
        const panel = panelRef.current;
        if (!panel || isMobileViewport()) {
            return;
        }

        const rect = panel.getBoundingClientRect();
        const isOnLeft = rect.left + rect.width / 2 < window.innerWidth / 2;
        const originLeft = rect.left - offsetRef.current.x;

        applyOffset(
            clampOffset({ x: isOnLeft ? 0 : DOCKED_MARGIN_PX - originLeft, y: offsetRef.current.y }),
            true,
        );
    };

    const resetPosition = (event: ReactMouseEvent<HTMLElement>) => {
        if ((event.target as HTMLElement).closest('button, a')) {
            return;
        }
        applyOffset({ x: 0, y: 0 }, true);
    };

    useEffect(() => {
        saveChatSnapshot({ isOpen });
    }, [isOpen]);

    useEffect(() => {
        saveChatSnapshot({ draft });
    }, [draft]);

    useEffect(() => {
        saveChatSnapshot({ messages });
    }, [messages]);

    const loadMessages = useCallback(async () => {
        try {
            const data = await apiGet<AssistantMessage[]>(
                `/api/assistant/messages?conversation_id=${encodeURIComponent(conversationId)}`,
            );
            setMessages(data);
        } catch (error) {
            console.error('Assistant : lecture des messages impossible', error);
        }
    }, [conversationId]);

    const loadStatus = useCallback(async () => {
        try {
            const data = await apiGet<{ online: boolean }>('/api/assistant/status');
            setIsOnline(data.online);
        } catch {
            setIsOnline(null);
        }
    }, []);

    useEffect(() => {
        if (!isOpen) {
            return;
        }
        void loadMessages();
        void loadStatus();
        const interval = setInterval(() => void loadStatus(), POLL_STATUS_MS);

        return () => clearInterval(interval);
    }, [isOpen, loadMessages, loadStatus]);

    useEffect(() => {
        if (!isOpen || !hasUnfinished) {
            return;
        }
        const interval = setInterval(() => {
            void loadMessages();
            setNow(Date.now());
        }, POLL_MESSAGES_MS);

        return () => clearInterval(interval);
    }, [isOpen, hasUnfinished, loadMessages]);

    useEffect(() => {
        bottomRef.current?.scrollIntoView({ block: 'end' });
    }, [messages, isOpen]);

    const send = async () => {
        const question = draft.trim();
        if (question === '' || isSending) {
            return;
        }

        setIsSending(true);
        setErrorText(null);
        try {
            saveConversationId(conversationId);
            const created = await apiPost<AssistantMessage>('/api/assistant/messages', {
                question,
                conversation_id: conversationId,
            });
            setMessages((previous) => [...previous, created]);
            setDraft('');
            void loadStatus();
        } catch (error) {
            setErrorText(error instanceof ApiError ? error.message : 'Envoi impossible, réessaie.');
        } finally {
            setIsSending(false);
        }
    };

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        void send();
    };

    const handleKeyDown = (event: KeyboardEvent<HTMLTextAreaElement>) => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            void send();
        }
    };

    const startNewConversation = () => {
        const id = newConversationId();
        saveConversationId(id);
        setConversationId(id);
        setMessages([]);
        setErrorText(null);
    };

    const updateAction = (messageId: number, updated: AssistantAction) => {
        setMessages((previous) =>
            previous.map((message) =>
                message.id === messageId
                    ? {
                          ...message,
                          actions: message.actions.map((action) => (action.id === updated.id ? updated : action)),
                      }
                    : message,
            ),
        );
    };

    const isSlow = (message: AssistantMessage): boolean =>
        isUnfinished(message) && now - new Date(message.created_at).getTime() > SLOW_AFTER_MS;

    return (
        <div className="assistant-chat">
            {isOpen && (
                <section
                    ref={panelRef}
                    className="assistant-chat__panel"
                    aria-label="Assistant de l'atelier"
                    style={{ '--assistant-dx': `${offset.x}px`, '--assistant-dy': `${offset.y}px` } as CSSProperties}
                >
                    <header
                        className="assistant-chat__header"
                        title="Glisse ce bandeau pour déplacer la fenêtre (double-clic : retour à la place d’origine)"
                        onPointerDown={handleDragStart}
                        onPointerMove={handleDragMove}
                        onPointerUp={handleDragEnd}
                        onPointerCancel={handleDragEnd}
                        onDoubleClick={resetPosition}
                    >
                        <div>
                            <h2 className="assistant-chat__title">Assistant de l&apos;atelier</h2>
                            <p className="assistant-chat__subtitle">
                                {isOnline === false ? 'Hors ligne' : 'Il propose, tu confirmes'}
                            </p>
                        </div>
                        <div className="assistant-chat__header-actions">
                            <button type="button" className="assistant-chat__link" onClick={startNewConversation}>
                                Nouvelle conversation
                            </button>
                            <Link href="/assistant/journal" className="assistant-chat__link">
                                Journal
                            </Link>
                            <button
                                type="button"
                                className="assistant-chat__side"
                                onClick={toggleSide}
                                aria-label="Mettre la fenêtre à gauche ou à droite"
                                title="Mettre la fenêtre à gauche ou à droite"
                            >
                                &#8644;
                            </button>
                            <button
                                type="button"
                                className="assistant-chat__close"
                                onClick={() => setIsOpen(false)}
                                aria-label="Fermer"
                            >
                                &times;
                            </button>
                        </div>
                    </header>

                    <div className="assistant-chat__messages">
                        {messages.length === 0 && (
                            <p className="assistant-chat__empty">
                                Pose une question sur un client, un devis, un forfait, une pièce ou l&apos;agenda.
                                Tu peux aussi lui demander de changer un statut ou de déplacer un rendez-vous : il le
                                propose, rien ne bouge avant ton clic sur « Confirmer ».
                            </p>
                        )}

                        {messages.map((message) => (
                            <div key={message.id} className="assistant-chat__exchange">
                                <div className="assistant-chat__bubble assistant-chat__bubble--question">
                                    {message.question}
                                </div>

                                {isUnfinished(message) && (
                                    <div className="assistant-chat__bubble assistant-chat__bubble--waiting">
                                        {isOnline === false
                                            ? 'L’assistant est hors ligne. Demande à Jonathan.'
                                            : isSlow(message)
                                              ? 'Ça prend du temps… Si rien ne vient, demande à Jonathan.'
                                              : 'L’assistant réfléchit…'}
                                    </div>
                                )}

                                {message.status === 'answered' && (
                                    <div className="assistant-chat__bubble assistant-chat__bubble--answer">
                                        <AssistantText text={message.answer ?? ''} />
                                        {message.consultations.length > 0 && (
                                            <p className="assistant-chat__sources">
                                                Consulté : {message.consultations.join(', ')}
                                            </p>
                                        )}
                                    </div>
                                )}

                                {message.status === 'failed' && (
                                    <div className="assistant-chat__bubble assistant-chat__bubble--error">
                                        {message.error ?? 'L’assistant n’a pas pu répondre.'} Demande à Jonathan.
                                    </div>
                                )}

                                {message.actions.map((action) => (
                                    <AssistantActionCard
                                        key={action.id}
                                        action={action}
                                        onChange={(updated) => updateAction(message.id, updated)}
                                    />
                                ))}
                            </div>
                        ))}
                        <div ref={bottomRef} />
                    </div>

                    <form className="assistant-chat__form" onSubmit={handleSubmit}>
                        {errorText && <p className="assistant-chat__form-error">{errorText}</p>}
                        <div className="assistant-chat__form-row">
                            <textarea
                                className="assistant-chat__input"
                                value={draft}
                                onChange={(event) => setDraft(event.target.value)}
                                onKeyDown={handleKeyDown}
                                placeholder="Ta question…"
                                rows={2}
                                maxLength={2000}
                                aria-label="Ta question"
                            />
                            <button
                                type="submit"
                                className="assistant-chat__send"
                                disabled={isSending || draft.trim() === ''}
                            >
                                Envoyer
                            </button>
                        </div>
                        <p className="assistant-chat__disclaimer">
                            Réponse d&apos;une IA, à vérifier en cas de doute.
                        </p>
                    </form>
                </section>
            )}

            <button
                type="button"
                className="assistant-chat__toggle"
                onClick={() => setIsOpen((open) => !open)}
                aria-expanded={isOpen}
            >
                {isOpen ? 'Fermer l’assistant' : 'Assistant'}
            </button>
        </div>
    );
}
