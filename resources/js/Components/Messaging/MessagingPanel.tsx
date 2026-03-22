import { useState } from 'react';
import { useMessaging } from '@/Contexts/MessagingContext';
import { Message } from '@/types';
import MessageCard from './MessageCard';
import NewMessageForm from './NewMessageForm';

export default function MessagingPanel() {
    const {
        currentUserId,
        users,
        messages,
        unreadCount,
        isLoading,
        isPanelOpen,
        closePanel,
    } = useMessaging();

    const currentUserName = users.find(u => u.id === currentUserId)?.name ?? '';

    const [showNewForm, setShowNewForm] = useState(false);
    const [filter, setFilter] = useState<'all' | 'open' | 'resolved'>('all');

    if (!isPanelOpen) return null;

    const filteredMessages = messages.filter(m => {
        if (filter === 'open') return m.status === 'ouvert';
        if (filter === 'resolved') return m.status === 'resolu';
        return true;
    });

    return (
        <div className="messaging-panel">
            <div className="messaging-panel__overlay" onClick={closePanel} />
            <div className="messaging-panel__content">
                <header className="messaging-panel__header">
                    <div className="messaging-panel__title">
                        <h2>Messages</h2>
                        {currentUserName && (
                            <span className="messaging-panel__mode">
                                {currentUserName}
                            </span>
                        )}
                        {unreadCount > 0 && (
                            <span className="messaging-panel__badge">{unreadCount}</span>
                        )}
                    </div>
                    <button
                        type="button"
                        className="messaging-panel__close"
                        onClick={closePanel}
                        aria-label="Fermer"
                    >
                        &times;
                    </button>
                </header>

                <div className="messaging-panel__toolbar">
                    <div className="messaging-panel__filters">
                        <button
                            type="button"
                            className={`messaging-panel__filter ${filter === 'all' ? 'messaging-panel__filter--active' : ''}`}
                            onClick={() => setFilter('all')}
                        >
                            Tous
                        </button>
                        <button
                            type="button"
                            className={`messaging-panel__filter ${filter === 'open' ? 'messaging-panel__filter--active' : ''}`}
                            onClick={() => setFilter('open')}
                        >
                            Ouverts
                        </button>
                        <button
                            type="button"
                            className={`messaging-panel__filter ${filter === 'resolved' ? 'messaging-panel__filter--active' : ''}`}
                            onClick={() => setFilter('resolved')}
                        >
                            Resolus
                        </button>
                    </div>
                    <button
                        type="button"
                        className="messaging-panel__new-btn"
                        onClick={() => setShowNewForm(true)}
                    >
                        + Nouveau message
                    </button>
                </div>

                {showNewForm && (
                    <NewMessageForm onClose={() => setShowNewForm(false)} />
                )}

                <div className="messaging-panel__list">
                    {isLoading ? (
                        <div className="messaging-panel__loading">Chargement...</div>
                    ) : filteredMessages.length === 0 ? (
                        <div className="messaging-panel__empty">
                            Aucun message
                        </div>
                    ) : (
                        filteredMessages.map(message => (
                            <MessageCard key={message.id} message={message} />
                        ))
                    )}
                </div>
            </div>
        </div>
    );
}
