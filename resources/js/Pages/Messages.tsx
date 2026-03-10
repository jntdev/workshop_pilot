import { useState } from 'react';
import MainLayout from '@/Layouts/MainLayout';
import { Head } from '@inertiajs/react';
import { Message, MessageCategory } from '@/types';
import MessageListItem from '@/Components/Messaging/MessageListItem';
import MessageDetail from '@/Components/Messaging/MessageDetail';
import NewMessageForm from '@/Components/Messaging/NewMessageForm';
import ThemeToggle from '@/Components/Messaging/ThemeToggle';
import { useMessaging } from '@/Contexts/MessagingContext';
import { ThemeProvider, useTheme } from '@/Contexts/ThemeContext';

const CATEGORY_LABELS: Record<MessageCategory, string> = {
    accueil: 'Accueil',
    atelier: 'Atelier',
    location: 'Location',
    autre: 'Autre',
};

const CATEGORIES: MessageCategory[] = ['accueil', 'atelier', 'location', 'autre'];

function MessagesContent() {
    const { isStarcraft } = useTheme();
    const {
        currentUserId,
        users,
        messages,
        unreadCount,
        unreadByCategory,
        isLoading,
        refreshMessages,
    } = useMessaging();

    const currentUserName = users.find(u => u.id === currentUserId)?.name ?? '';

    const [showNewForm, setShowNewForm] = useState(false);
    const [categoryFilter, setCategoryFilter] = useState<MessageCategory | 'all'>('all');
    const [statusFilter, setStatusFilter] = useState<'all' | 'open' | 'resolved'>('all');
    const [selectedMessageId, setSelectedMessageId] = useState<number | null>(null);

    const filteredMessages = messages.filter(m => {
        // Filtre catégorie
        if (categoryFilter !== 'all' && m.category !== categoryFilter) return false;
        // Filtre statut
        if (statusFilter === 'open') return m.status === 'ouvert';
        if (statusFilter === 'resolved') return m.status === 'resolu';
        return true;
    });

    // Compteurs pour les filtres de statut (dans la catégorie sélectionnée)
    const messagesInCategory = categoryFilter === 'all'
        ? messages
        : messages.filter(m => m.category === categoryFilter);

    const selectedMessage = messages.find(m => m.id === selectedMessageId) || null;

    const handleSelectMessage = (messageId: number) => {
        setSelectedMessageId(messageId);
    };

    const handleCloseDetail = () => {
        setSelectedMessageId(null);
    };

    return (
        <>
            <Head title="Messages" />

            <div className={`messages-page ${isStarcraft ? 'theme-starcraft' : ''}`}>
                <div className="messages-page__header">
                    <div className="messages-page__title-row">
                        <h1 className="messages-page__title">Messages</h1>
                        {currentUserName && (
                            <span className="messages-page__mode">
                                {currentUserName}
                            </span>
                        )}
                        {unreadCount > 0 && (
                            <span className="messages-page__badge">{unreadCount} non lu{unreadCount > 1 ? 's' : ''}</span>
                        )}
                    </div>
                    {currentUserName && (
                        <p className="messages-page__subtitle">
                            Vous consultez les messages en tant que <strong>{currentUserName}</strong>.
                        </p>
                    )}
                </div>

                <div className="messages-page__toolbar messages-page__toolbar--categories">
                    <div className="messages-page__filters">
                        <button
                            type="button"
                            className={`messages-page__filter messages-page__filter--category ${categoryFilter === 'all' ? 'messages-page__filter--active' : ''}`}
                            onClick={() => setCategoryFilter('all')}
                        >
                            Toutes
                            {unreadCount > 0 && (
                                <span className="messages-page__filter-badge">{unreadCount}</span>
                            )}
                        </button>
                        {CATEGORIES.map(cat => {
                            const unread = unreadByCategory[cat] || 0;
                            return (
                                <button
                                    key={cat}
                                    type="button"
                                    className={`messages-page__filter messages-page__filter--category ${categoryFilter === cat ? 'messages-page__filter--active' : ''}`}
                                    onClick={() => setCategoryFilter(cat)}
                                >
                                    {CATEGORY_LABELS[cat]}
                                    {unread > 0 && (
                                        <span className="messages-page__filter-badge">{unread}</span>
                                    )}
                                </button>
                            );
                        })}
                    </div>
                    <div className="messages-page__actions">
                        <ThemeToggle />
                        <button
                            type="button"
                            className="messages-page__refresh"
                            onClick={() => refreshMessages()}
                        >
                            Actualiser
                        </button>
                        <button
                            type="button"
                            className="messages-page__new-btn"
                            onClick={() => setShowNewForm(true)}
                        >
                            + Nouveau message
                        </button>
                    </div>
                </div>

                <div className="messages-page__toolbar">
                    <div className="messages-page__filters">
                        <button
                            type="button"
                            className={`messages-page__filter ${statusFilter === 'all' ? 'messages-page__filter--active' : ''}`}
                            onClick={() => setStatusFilter('all')}
                        >
                            Tous ({messagesInCategory.length})
                        </button>
                        <button
                            type="button"
                            className={`messages-page__filter ${statusFilter === 'open' ? 'messages-page__filter--active' : ''}`}
                            onClick={() => setStatusFilter('open')}
                        >
                            Ouverts ({messagesInCategory.filter(m => m.status === 'ouvert').length})
                        </button>
                        <button
                            type="button"
                            className={`messages-page__filter ${statusFilter === 'resolved' ? 'messages-page__filter--active' : ''}`}
                            onClick={() => setStatusFilter('resolved')}
                        >
                            Resolus ({messagesInCategory.filter(m => m.status === 'resolu').length})
                        </button>
                    </div>
                </div>

                {showNewForm && (
                    <div className="messages-page__form-container">
                        <NewMessageForm onClose={() => setShowNewForm(false)} />
                    </div>
                )}

                <div className="messages-page__columns">
                    <div className="messages-page__left">
                        {isLoading ? (
                            <div className="messages-page__loading">Chargement...</div>
                        ) : filteredMessages.length === 0 ? (
                            <div className="messages-page__empty">
                                {statusFilter === 'all'
                                    ? 'Aucun message'
                                    : statusFilter === 'open'
                                        ? 'Aucun message ouvert'
                                        : 'Aucun message resolu'}
                            </div>
                        ) : (
                            <div className="messages-page__list">
                                {filteredMessages.map(message => (
                                    <MessageListItem
                                        key={message.id}
                                        message={message}
                                        isSelected={message.id === selectedMessageId}
                                        onClick={() => handleSelectMessage(message.id)}
                                    />
                                ))}
                            </div>
                        )}
                    </div>

                    <div className="messages-page__right">
                        {selectedMessage ? (
                            <MessageDetail
                                message={selectedMessage}
                                onClose={handleCloseDetail}
                            />
                        ) : (
                            <div className="messages-page__no-selection">
                                Selectionnez un message pour voir les details et reponses
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}

export default function Messages() {
    return (
        <ThemeProvider>
            <MainLayout>
                <MessagesContent />
            </MainLayout>
        </ThemeProvider>
    );
}
