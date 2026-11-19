import { FormEvent, KeyboardEvent, useCallback, useEffect, useRef, useState } from 'react';
import { Link } from '@inertiajs/react';
import { ApiError, apiGet, apiPost } from '@/utils/api';
import AssistantActionCard from './AssistantActionCard';
import AssistantText from './AssistantText';
import { readChatSnapshot, saveChatSnapshot } from './assistantStore';
import type { AssistantAction, AssistantMessage } from './types';

const STORAGE_KEY = 'assistant.conversation_id';
const POLL_MESSAGES_MS = 2000;
const POLL_STATUS_MS = 30000;
const SLOW_AFTER_MS = 90000;

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
    const bottomRef = useRef<HTMLDivElement>(null);

    const hasUnfinished = messages.some(isUnfinished);

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
                <section className="assistant-chat__panel" aria-label="Assistant de l'atelier">
                    <header className="assistant-chat__header">
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
