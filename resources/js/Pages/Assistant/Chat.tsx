import { FormEvent, KeyboardEvent, useCallback, useEffect, useRef, useState } from 'react';
import { Head } from '@inertiajs/react';
import AssistantActionCard from '@/Components/Assistant/AssistantActionCard';
import AssistantText from '@/Components/Assistant/AssistantText';
import type { AssistantAction, AssistantMessage } from '@/Components/Assistant/types';
import { usePrivacyMode } from '@/hooks/usePrivacyMode';
import { openInWorkTab } from '@/lib/assistantBus';
import { ApiError, apiGet, apiPost } from '@/utils/api';

const CONVERSATION_KEY = 'assistant.conversation_id';
const DRAFT_KEY = 'assistant.draft';
const POLL_MESSAGES_MS = 2000;
const POLL_STATUS_MS = 30000;
const SLOW_AFTER_MS = 90000;
const NOTICE_MS = 4000;

function newConversationId(): string {
    return crypto.randomUUID();
}

function readConversationId(): string {
    try {
        const stored = window.localStorage.getItem(CONVERSATION_KEY);
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
        window.localStorage.setItem(CONVERSATION_KEY, id);
    } catch {
        // stockage indisponible
    }
}

function readDraft(): string {
    try {
        return window.sessionStorage.getItem(DRAFT_KEY) ?? '';
    } catch {
        return '';
    }
}

function isUnfinished(message: AssistantMessage): boolean {
    return message.status === 'pending' || message.status === 'processing';
}

/**
 * Conversation avec l'assistant de l'atelier, dans son propre onglet.
 * L'onglet de travail reste libre : les liens s'y ouvrent, et ce que fait l'assistant y apparaît sans rafraîchir.
 */
export default function Chat() {
    const { isComptoir } = usePrivacyMode();
    const [conversationId, setConversationId] = useState<string>(readConversationId);
    const [messages, setMessages] = useState<AssistantMessage[]>([]);
    const [draft, setDraft] = useState<string>(readDraft);
    const [isSending, setIsSending] = useState(false);
    const [errorText, setErrorText] = useState<string | null>(null);
    const [isOnline, setIsOnline] = useState<boolean | null>(null);
    const [notice, setNotice] = useState<string | null>(null);
    const [now, setNow] = useState<number>(() => Date.now());
    const bottomRef = useRef<HTMLDivElement>(null);
    const noticeTimer = useRef<number | undefined>(undefined);

    const hasUnfinished = messages.some(isUnfinished);

    useEffect(() => {
        try {
            window.sessionStorage.setItem(DRAFT_KEY, draft);
        } catch {
            // stockage indisponible
        }
    }, [draft]);

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
        if (isComptoir) {
            return;
        }
        void loadMessages();
        void loadStatus();
        const interval = setInterval(() => void loadStatus(), POLL_STATUS_MS);

        return () => clearInterval(interval);
    }, [isComptoir, loadMessages, loadStatus]);

    useEffect(() => {
        if (isComptoir || !hasUnfinished) {
            return;
        }
        const interval = setInterval(() => {
            void loadMessages();
            setNow(Date.now());
        }, POLL_MESSAGES_MS);

        return () => clearInterval(interval);
    }, [isComptoir, hasUnfinished, loadMessages]);

    useEffect(() => {
        bottomRef.current?.scrollIntoView({ block: 'end' });
    }, [messages]);

    const showNotice = useCallback((text: string) => {
        setNotice(text);
        window.clearTimeout(noticeTimer.current);
        noticeTimer.current = window.setTimeout(() => setNotice(null), NOTICE_MS);
    }, []);

    const openInWork = useCallback(
        async (href: string) => {
            const result = await openInWorkTab(href);
            showNotice(
                result === 'tab'
                    ? 'Page ouverte dans ton onglet de travail.'
                    : 'Aucun onglet de travail ouvert : la page s’ouvre dans un nouvel onglet.',
            );
        },
        [showNotice],
    );

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

    if (isComptoir) {
        return (
            <div className="assistant-page">
                <Head title="Assistant de l'atelier" />
                <div className="assistant-page__locked">
                    <h1>Assistant masqué en mode Comptoir</h1>
                    <p>
                        Des clients peuvent voir l&apos;écran : l&apos;assistant (infos clients, prix) est caché.
                        Repasse en mode Atelier dans l&apos;onglet de travail (interrupteur en haut à droite, ou
                        Ctrl+Maj+P) : cette page se débloquera toute seule.
                    </p>
                </div>
            </div>
        );
    }

    return (
        <div className="assistant-page">
            <Head title="Assistant de l'atelier" />

            <header className="assistant-page__header">
                <div className="assistant-page__header-inner">
                    <div>
                        <h1 className="assistant-page__title">Assistant de l&apos;atelier</h1>
                        <p className="assistant-page__subtitle">
                            {isOnline === false
                                ? 'Hors ligne'
                                : 'Il propose, tu confirmes. Ce qu’il fait apparaît dans ton onglet de travail.'}
                        </p>
                    </div>
                    <div className="assistant-page__header-actions">
                        <button type="button" className="assistant-page__link" onClick={startNewConversation}>
                            Nouvelle conversation
                        </button>
                        <a
                            href="/assistant/journal"
                            className="assistant-page__link"
                            onClick={(event) => {
                                event.preventDefault();
                                void openInWork('/assistant/journal');
                            }}
                        >
                            Journal
                        </a>
                    </div>
                </div>
            </header>

            {notice && (
                <p className="assistant-page__notice" role="status">
                    {notice}
                </p>
            )}

            <main className="assistant-page__messages">
                <div className="assistant-page__thread">
                    {messages.length === 0 && (
                        <p className="assistant-page__empty">
                            Pose une question sur un client, un devis, un forfait, une pièce ou l&apos;agenda. Tu peux
                            aussi lui demander de changer un statut, de marquer une pièce commandée ou de déplacer un
                            rendez-vous : il le propose, rien ne bouge avant ton clic sur « Confirmer ». Les pages
                            ouvertes dans ton onglet de travail se mettent à jour toutes seules.
                        </p>
                    )}

                    {messages.map((message) => (
                        <div key={message.id} className="assistant-page__exchange">
                            <div className="assistant-bubble assistant-bubble--question">{message.question}</div>

                            {isUnfinished(message) && (
                                <div className="assistant-bubble assistant-bubble--waiting">
                                    {isOnline === false
                                        ? 'L’assistant est hors ligne. Demande à Jonathan.'
                                        : isSlow(message)
                                          ? 'Ça prend du temps… Si rien ne vient, demande à Jonathan.'
                                          : 'L’assistant réfléchit…'}
                                </div>
                            )}

                            {message.status === 'answered' && (
                                <div className="assistant-bubble assistant-bubble--answer">
                                    <AssistantText text={message.answer ?? ''} onNavigate={(href) => void openInWork(href)} />
                                    {message.consultations.length > 0 && (
                                        <p className="assistant-bubble__sources">
                                            Consulté : {message.consultations.join(', ')}
                                        </p>
                                    )}
                                </div>
                            )}

                            {message.status === 'failed' && (
                                <div className="assistant-bubble assistant-bubble--error">
                                    {message.error ?? 'L’assistant n’a pas pu répondre.'} Demande à Jonathan.
                                </div>
                            )}

                            {message.actions.map((action) => (
                                <AssistantActionCard
                                    key={action.id}
                                    action={action}
                                    onChange={(updated) => updateAction(message.id, updated)}
                                    onNavigate={(href) => void openInWork(href)}
                                />
                            ))}
                        </div>
                    ))}
                    <div ref={bottomRef} />
                </div>
            </main>

            <form className="assistant-page__form" onSubmit={handleSubmit}>
                <div className="assistant-page__form-inner">
                    {errorText && <p className="assistant-page__form-error">{errorText}</p>}
                    <div className="assistant-page__form-row">
                        <textarea
                            className="assistant-page__input"
                            value={draft}
                            onChange={(event) => setDraft(event.target.value)}
                            onKeyDown={handleKeyDown}
                            placeholder="Ta question…"
                            rows={2}
                            maxLength={2000}
                            aria-label="Ta question"
                            autoFocus
                        />
                        <button
                            type="submit"
                            className="assistant-page__send"
                            disabled={isSending || draft.trim() === ''}
                        >
                            Envoyer
                        </button>
                    </div>
                    <p className="assistant-page__disclaimer">Réponse d&apos;une IA, à vérifier en cas de doute.</p>
                </div>
            </form>
        </div>
    );
}
