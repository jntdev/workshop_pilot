import React, { createContext, useContext, useState, useEffect, useCallback, ReactNode } from 'react';
import { Message, WorkMode, MessagingState, UnreadByCategory, MessageCategory } from '@/types';

interface MessagingContextValue extends MessagingState {
    setMode: (mode: WorkMode) => void;
    togglePanel: () => void;
    openPanel: () => void;
    closePanel: () => void;
    refreshMessages: () => Promise<void>;
    markMessageAsRead: (messageId: number) => Promise<void>;
    markMessageAsResolved: (messageId: number) => Promise<void>;
    reopenMessage: (messageId: number) => Promise<void>;
    createMessage: (data: CreateMessageData) => Promise<Message>;
    createReply: (messageId: number, data: CreateReplyData) => Promise<void>;
    deleteMessage: (messageId: number) => Promise<void>;
    markReplyAsRead: (replyId: number) => Promise<void>;
}

interface CreateMessageData {
    recipient_mode: WorkMode | null;
    category: MessageCategory;
    contact_name?: string;
    contact_phone?: string;
    contact_email?: string;
    content: string;
}

interface CreateReplyData {
    recipient_mode?: WorkMode | null;
    content: string;
}

const MessagingContext = createContext<MessagingContextValue | null>(null);

// Utilise la même clé que usePrivacyMode pour synchronisation
const STORAGE_KEY = 'workshop_privacy_mode';

function getCsrfToken(): string {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function getStoredMode(): WorkMode {
    if (typeof window === 'undefined') return 'atelier';
    const stored = localStorage.getItem(STORAGE_KEY);
    if (stored === 'comptoir' || stored === 'atelier') {
        return stored;
    }
    return 'atelier';
}

const defaultUnreadByCategory: UnreadByCategory = {
    accueil: 0,
    atelier: 0,
    location: 0,
    autre: 0,
};

export function MessagingProvider({ children }: { children: ReactNode }) {
    const [mode, setModeState] = useState<WorkMode>(getStoredMode);
    const [messages, setMessages] = useState<Message[]>([]);
    const [unreadCount, setUnreadCount] = useState(0);
    const [unreadByCategory, setUnreadByCategory] = useState<UnreadByCategory>(defaultUnreadByCategory);
    const [isLoading, setIsLoading] = useState(false);
    const [isPanelOpen, setIsPanelOpen] = useState(false);

    const setMode = useCallback((newMode: WorkMode) => {
        setModeState(newMode);
        localStorage.setItem(STORAGE_KEY, newMode);
    }, []);

    const fetchMessages = useCallback(async () => {
        setIsLoading(true);
        try {
            const response = await fetch(`/api/messages?mode=${mode}`, {
                credentials: 'include',
            });
            if (response.ok) {
                const data = await response.json();
                setMessages(data);
            }
        } catch (error) {
            console.error('Failed to fetch messages:', error);
        } finally {
            setIsLoading(false);
        }
    }, [mode]);

    const fetchUnreadCount = useCallback(async () => {
        try {
            const response = await fetch(`/api/messages/unread-count?mode=${mode}`, {
                credentials: 'include',
            });
            if (response.ok) {
                const data = await response.json();
                setUnreadCount(data.count);
                setUnreadByCategory(data.by_category || defaultUnreadByCategory);
            }
        } catch (error) {
            console.error('Failed to fetch unread count:', error);
        }
    }, [mode]);

    const refreshMessages = useCallback(async () => {
        await Promise.all([fetchMessages(), fetchUnreadCount()]);
    }, [fetchMessages, fetchUnreadCount]);

    // Fetch on mode change
    useEffect(() => {
        refreshMessages();
    }, [mode, refreshMessages]);

    // Poll for updates every 30 seconds (will be replaced by WebSocket)
    useEffect(() => {
        const interval = setInterval(fetchUnreadCount, 30000);
        return () => clearInterval(interval);
    }, [fetchUnreadCount]);

    // Sync with localStorage changes from usePrivacyMode toggle (other tabs)
    useEffect(() => {
        const handleStorageChange = (event: StorageEvent) => {
            if (event.key === STORAGE_KEY && event.newValue) {
                const newMode = event.newValue as WorkMode;
                if (newMode === 'comptoir' || newMode === 'atelier') {
                    setModeState(newMode);
                }
            }
        };
        window.addEventListener('storage', handleStorageChange);
        return () => window.removeEventListener('storage', handleStorageChange);
    }, []);

    // Poll localStorage for changes within same tab (toggle doesn't trigger storage event in same tab)
    useEffect(() => {
        const checkModeSync = () => {
            const stored = localStorage.getItem(STORAGE_KEY);
            if (stored && (stored === 'comptoir' || stored === 'atelier') && stored !== mode) {
                setModeState(stored);
            }
        };
        const interval = setInterval(checkModeSync, 300);
        return () => clearInterval(interval);
    }, [mode]);

    const togglePanel = useCallback(() => {
        setIsPanelOpen(prev => !prev);
    }, []);

    const openPanel = useCallback(() => {
        setIsPanelOpen(true);
    }, []);

    const closePanel = useCallback(() => {
        setIsPanelOpen(false);
    }, []);

    const markMessageAsRead = useCallback(async (messageId: number) => {
        try {
            // Trouver le message pour connaître sa catégorie avant la requête
            const targetMessage = messages.find(m => m.id === messageId);

            const response = await fetch(`/api/messages/${messageId}/read`, {
                method: 'PATCH',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
            });
            if (response.ok) {
                setMessages(prev => prev.map(m =>
                    m.id === messageId ? { ...m, read_at: new Date().toISOString() } : m
                ));
                setUnreadCount(prev => Math.max(0, prev - 1));

                // Décrémenter le compteur de la catégorie correspondante
                if (targetMessage) {
                    setUnreadByCategory(prev => ({
                        ...prev,
                        [targetMessage.category]: Math.max(0, prev[targetMessage.category] - 1),
                    }));
                }
            }
        } catch (error) {
            console.error('Failed to mark message as read:', error);
        }
    }, [messages]);

    const markMessageAsResolved = useCallback(async (messageId: number) => {
        try {
            const response = await fetch(`/api/messages/${messageId}/resolve`, {
                method: 'PATCH',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
            });
            if (response.ok) {
                setMessages(prev => prev.map(m =>
                    m.id === messageId ? { ...m, status: 'resolu', resolved_at: new Date().toISOString() } : m
                ));
            }
        } catch (error) {
            console.error('Failed to mark message as resolved:', error);
        }
    }, []);

    const reopenMessage = useCallback(async (messageId: number) => {
        try {
            const response = await fetch(`/api/messages/${messageId}/reopen`, {
                method: 'PATCH',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
            });
            if (response.ok) {
                setMessages(prev => prev.map(m =>
                    m.id === messageId ? { ...m, status: 'ouvert', resolved_at: null } : m
                ));
            }
        } catch (error) {
            console.error('Failed to reopen message:', error);
        }
    }, []);

    const createMessage = useCallback(async (data: CreateMessageData): Promise<Message> => {
        const response = await fetch('/api/messages', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
            },
            body: JSON.stringify({
                author_mode: mode,
                ...data,
            }),
        });
        if (!response.ok) {
            throw new Error('Failed to create message');
        }
        const message = await response.json();
        setMessages(prev => [message, ...prev]);
        return message;
    }, [mode]);

    const createReply = useCallback(async (messageId: number, data: CreateReplyData) => {
        const response = await fetch(`/api/messages/${messageId}/replies`, {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
            },
            body: JSON.stringify({
                author_mode: mode,
                ...data,
            }),
        });
        if (!response.ok) {
            throw new Error('Failed to create reply');
        }
        const reply = await response.json();
        setMessages(prev => prev.map(m =>
            m.id === messageId
                ? { ...m, replies: [...m.replies, reply] }
                : m
        ));
    }, [mode]);

    const deleteMessage = useCallback(async (messageId: number) => {
        // Trouver le message avant suppression pour mettre à jour les compteurs si non lu
        const targetMessage = messages.find(m => m.id === messageId);

        const response = await fetch(`/api/messages/${messageId}`, {
            method: 'DELETE',
            credentials: 'include',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
            },
        });
        if (response.ok) {
            setMessages(prev => prev.filter(m => m.id !== messageId));

            // Si le message était non lu, décrémenter les compteurs
            if (targetMessage && !targetMessage.read_at) {
                setUnreadCount(prev => Math.max(0, prev - 1));
                setUnreadByCategory(prev => ({
                    ...prev,
                    [targetMessage.category]: Math.max(0, prev[targetMessage.category] - 1),
                }));
            }
        }
    }, [messages]);

    const markReplyAsRead = useCallback(async (replyId: number) => {
        try {
            const response = await fetch(`/api/replies/${replyId}/read`, {
                method: 'PATCH',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
            });
            if (response.ok) {
                setMessages(prev => prev.map(m => ({
                    ...m,
                    replies: m.replies.map(r =>
                        r.id === replyId ? { ...r, read_at: new Date().toISOString() } : r
                    ),
                })));
            }
        } catch (error) {
            console.error('Failed to mark reply as read:', error);
        }
    }, []);

    const value: MessagingContextValue = {
        mode,
        messages,
        unreadCount,
        unreadByCategory,
        isLoading,
        isPanelOpen,
        setMode,
        togglePanel,
        openPanel,
        closePanel,
        refreshMessages,
        markMessageAsRead,
        markMessageAsResolved,
        reopenMessage,
        createMessage,
        createReply,
        deleteMessage,
        markReplyAsRead,
    };

    return (
        <MessagingContext.Provider value={value}>
            {children}
        </MessagingContext.Provider>
    );
}

export function useMessaging(): MessagingContextValue {
    const context = useContext(MessagingContext);
    if (!context) {
        throw new Error('useMessaging must be used within a MessagingProvider');
    }
    return context;
}

export function getModeLabel(mode: WorkMode): string {
    return mode === 'comptoir' ? 'Nicolas' : 'Jonathan';
}
