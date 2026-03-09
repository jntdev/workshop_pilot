import React, { createContext, useContext, useState, useEffect, useCallback, ReactNode } from 'react';
import { Message, MessagingState, UnreadByCategory, MessageCategory, MessagingUser } from '@/types';

interface MessagingContextValue extends MessagingState {
    togglePanel: () => void;
    openPanel: () => void;
    closePanel: () => void;
    refreshMessages: () => Promise<void>;
    markMessageAsRead: (messageId: number) => Promise<void>;
    markMessageAsResolved: (messageId: number) => Promise<void>;
    reopenMessage: (messageId: number) => Promise<void>;
    createMessage: (data: CreateMessageData) => Promise<Message>;
    createReply: (messageId: number, data: CreateReplyData) => Promise<void>;
    updateReply: (replyId: number, content: string) => Promise<void>;
    deleteReply: (replyId: number, messageId: number) => Promise<void>;
    deleteMessage: (messageId: number) => Promise<void>;
    markReplyAsRead: (replyId: number) => Promise<void>;
}

interface CreateMessageData {
    recipient_user_id: number | null;
    category: MessageCategory;
    contact_name?: string;
    contact_phone?: string;
    contact_email?: string;
    content: string;
}

interface CreateReplyData {
    recipient_user_id?: number | null;
    content: string;
}

interface MessagingProviderProps {
    children: ReactNode;
    currentUserId: number | null;
    users: MessagingUser[];
}

const MessagingContext = createContext<MessagingContextValue | null>(null);

function getCsrfToken(): string {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

const defaultUnreadByCategory: UnreadByCategory = {
    accueil: 0,
    atelier: 0,
    location: 0,
    autre: 0,
};

export function MessagingProvider({ children, currentUserId, users }: MessagingProviderProps) {
    const [messages, setMessages] = useState<Message[]>([]);
    const [unreadCount, setUnreadCount] = useState(0);
    const [unreadByCategory, setUnreadByCategory] = useState<UnreadByCategory>(defaultUnreadByCategory);
    const [isLoading, setIsLoading] = useState(false);
    const [isPanelOpen, setIsPanelOpen] = useState(false);

    const fetchMessages = useCallback(async () => {
        if (!currentUserId) { return; }
        setIsLoading(true);
        try {
            const response = await fetch('/api/messages', {
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
    }, [currentUserId]);

    const fetchUnreadCount = useCallback(async () => {
        if (!currentUserId) { return; }
        try {
            const response = await fetch('/api/messages/unread-count', {
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
    }, [currentUserId]);

    const refreshMessages = useCallback(async () => {
        await Promise.all([fetchMessages(), fetchUnreadCount()]);
    }, [fetchMessages, fetchUnreadCount]);

    useEffect(() => {
        refreshMessages();
    }, [currentUserId, refreshMessages]);

    // Poll for updates every 30 seconds (will be replaced by WebSocket)
    useEffect(() => {
        const interval = setInterval(fetchUnreadCount, 30000);
        return () => clearInterval(interval);
    }, [fetchUnreadCount]);

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
            body: JSON.stringify(data),
        });
        if (!response.ok) {
            throw new Error('Failed to create message');
        }
        const message = await response.json();
        setMessages(prev => [message, ...prev]);
        return message;
    }, []);

    const createReply = useCallback(async (messageId: number, data: CreateReplyData) => {
        const response = await fetch(`/api/messages/${messageId}/replies`, {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
            },
            body: JSON.stringify(data),
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
    }, []);

    const updateReply = useCallback(async (replyId: number, content: string) => {
        const response = await fetch(`/api/replies/${replyId}`, {
            method: 'PUT',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
            },
            body: JSON.stringify({ content }),
        });
        if (!response.ok) {
            throw new Error('Failed to update reply');
        }
        const updated = await response.json();
        setMessages(prev => prev.map(m => ({
            ...m,
            replies: m.replies.map(r => r.id === replyId ? updated : r),
        })));
    }, []);

    const deleteReply = useCallback(async (replyId: number, messageId: number) => {
        const response = await fetch(`/api/replies/${replyId}`, {
            method: 'DELETE',
            credentials: 'include',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
            },
        });
        if (response.ok) {
            setMessages(prev => prev.map(m =>
                m.id === messageId
                    ? { ...m, replies: m.replies.filter(r => r.id !== replyId) }
                    : m
            ));
        }
    }, []);

    const deleteMessage = useCallback(async (messageId: number) => {
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
        currentUserId,
        users,
        messages,
        unreadCount,
        unreadByCategory,
        isLoading,
        isPanelOpen,
        togglePanel,
        openPanel,
        closePanel,
        refreshMessages,
        markMessageAsRead,
        markMessageAsResolved,
        reopenMessage,
        createMessage,
        createReply,
        updateReply,
        deleteReply,
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
