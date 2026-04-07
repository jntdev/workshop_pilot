import { useState } from 'react';
import MainLayout from '@/Layouts/MainLayout';
import { Head, router } from '@inertiajs/react';
import MessageListItem from '@/Components/Messaging/MessageListItem';
import MessageDetail from '@/Components/Messaging/MessageDetail';
import NewMessageForm from '@/Components/Messaging/NewMessageForm';
import { useMessaging } from '@/Contexts/MessagingContext';

function MessagesContent() {
    const {
        currentUserId,
        users,
        messages,
        categories,
        unreadCount,
        unreadByCategory,
        isLoading,
        refreshMessages,
    } = useMessaging();

    const currentUserName = users.find(u => u.id === currentUserId)?.name ?? '';

    const [showNewForm, setShowNewForm] = useState(false);
    const [categoryFilter, setCategoryFilter] = useState<number | 'all'>('all');
    const [statusFilter, setStatusFilter] = useState<'all' | 'open' | 'resolved'>('open');
    const [selectedMessageId, setSelectedMessageId] = useState<number | null>(null);

    const filteredMessages = messages.filter(m => {
        // Filtre catégorie
        if (categoryFilter !== 'all' && m.category_id !== categoryFilter) return false;
        // Filtre statut
        if (statusFilter === 'open') return m.status === 'ouvert';
        if (statusFilter === 'resolved') return m.status === 'resolu';
        return true;
    });

    // Compteurs pour les filtres de statut (dans la catégorie sélectionnée)
    const messagesInCategory = categoryFilter === 'all'
        ? messages
        : messages.filter(m => m.category_id === categoryFilter);

    const selectedMessage = messages.find(m => m.id === selectedMessageId) || null;

    const handleSelectMessage = (messageId: number) => {
        setSelectedMessageId(messageId);
    };

    const handleCloseDetail = () => {
        setSelectedMessageId(null);
    };

    const handleGoToSettings = () => {
        router.visit('/messages/settings');
    };

    return (
        <>
            <Head title="Messages" />

            <div className="messages-page">
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
                        {categories.map(cat => {
                            const unread = unreadByCategory[cat.id] || 0;
                            return (
                                <button
                                    key={cat.id}
                                    type="button"
                                    className={`messages-page__filter messages-page__filter--category ${categoryFilter === cat.id ? 'messages-page__filter--active' : ''}`}
                                    onClick={() => setCategoryFilter(cat.id)}
                                    style={{
                                        borderColor: categoryFilter === cat.id ? cat.color : undefined,
                                    }}
                                >
                                    <span
                                        className="messages-page__filter-dot"
                                        style={{ backgroundColor: cat.color }}
                                    />
                                    {cat.label}
                                    {unread > 0 && (
                                        <span className="messages-page__filter-badge">{unread}</span>
                                    )}
                                </button>
                            );
                        })}
                    </div>
                    <div className="messages-page__actions">
                        <button
                            type="button"
                            className="messages-page__settings-btn"
                            onClick={handleGoToSettings}
                            title="Reglages"
                        >
                            Reglages
                        </button>
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
        <MainLayout>
            <MessagesContent />
        </MainLayout>
    );
}
