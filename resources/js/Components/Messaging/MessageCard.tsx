import { useState } from 'react';
import { useMessaging } from '@/Contexts/MessagingContext';
import { Message } from '@/types';
import ReplyForm from './ReplyForm';

interface MessageCardProps {
    message: Message;
}

function formatDate(dateString: string): string {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now.getTime() - date.getTime();
    const diffHours = diffMs / (1000 * 60 * 60);

    if (diffHours < 1) {
        const mins = Math.floor(diffMs / (1000 * 60));
        return `Il y a ${mins} min`;
    }
    if (diffHours < 24) {
        return `Il y a ${Math.floor(diffHours)}h`;
    }
    if (diffHours < 48) {
        return 'Hier';
    }
    return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
}

export default function MessageCard({ message }: MessageCardProps) {
    const {
        currentUserId,
        markMessageAsRead,
        markMessageAsResolved,
        reopenMessage,
        deleteMessage,
        markReplyAsRead,
    } = useMessaging();

    const [showReplies, setShowReplies] = useState(false);
    const [showReplyForm, setShowReplyForm] = useState(false);
    const [isDeleting, setIsDeleting] = useState(false);

    const isUnread = !message.read_at;
    const isResolved = message.status === 'resolu';
    const hasContact = message.contact_name || message.contact_phone || message.contact_email;
    const unreadReplies = message.replies.filter(r => !r.read_at && r.author_user_id !== currentUserId);

    const canMarkAsRead = message.recipient_user_id === currentUserId ||
        (message.author_user_id === currentUserId && message.recipient_user_id === null);

    const isAuthorViewingOthersRead = message.author_user_id === currentUserId &&
        message.recipient_user_id !== null &&
        message.recipient_user_id !== currentUserId &&
        message.read_at !== null;

    const handleMarkRead = async () => {
        await markMessageAsRead(message.id);
    };

    const handleResolve = async () => {
        await markMessageAsResolved(message.id);
    };

    const handleReopen = async () => {
        await reopenMessage(message.id);
    };

    const handleDelete = async () => {
        if (confirm('Supprimer ce message ?')) {
            setIsDeleting(true);
            await deleteMessage(message.id);
        }
    };

    const handleReadReply = async (replyId: number) => {
        await markReplyAsRead(replyId);
    };

    return (
        <div className={`message-card ${isUnread ? 'message-card--unread' : ''} ${isResolved ? 'message-card--resolved' : ''}`}>
            <div className="message-card__header">
                <div className="message-card__meta">
                    <span className="message-card__author">{message.author_label}</span>
                    {message.recipient_label && (
                        <>
                            <span className="message-card__arrow">&rarr;</span>
                            <span className="message-card__recipient">{message.recipient_label}</span>
                        </>
                    )}
                    {!message.recipient_user_id && (
                        <span className="message-card__self">(note perso)</span>
                    )}
                </div>
                <span className="message-card__date">{formatDate(message.created_at)}</span>
            </div>

            {hasContact && (
                <div className="message-card__contact">
                    {message.contact_name && (
                        <span className="message-card__contact-name">{message.contact_name}</span>
                    )}
                    {message.contact_phone && (
                        <a href={`tel:${message.contact_phone}`} className="message-card__contact-phone">
                            {message.contact_phone}
                        </a>
                    )}
                    {message.contact_email && (
                        <a href={`mailto:${message.contact_email}`} className="message-card__contact-email">
                            {message.contact_email}
                        </a>
                    )}
                </div>
            )}

            <div className="message-card__content">{message.content}</div>

            <div className="message-card__actions">
                {isUnread && canMarkAsRead && (
                    <button
                        type="button"
                        className="message-card__action message-card__action--read"
                        onClick={handleMarkRead}
                    >
                        Info lue
                    </button>
                )}
                {isAuthorViewingOthersRead && (
                    <span className="message-card__read-status">
                        Lu {message.read_at && formatDate(message.read_at)}
                    </span>
                )}
                {!isResolved ? (
                    <button
                        type="button"
                        className="message-card__action message-card__action--resolve"
                        onClick={handleResolve}
                    >
                        Resolu
                    </button>
                ) : (
                    <button
                        type="button"
                        className="message-card__action message-card__action--reopen"
                        onClick={handleReopen}
                    >
                        Reouvrir
                    </button>
                )}
                <button
                    type="button"
                    className="message-card__action message-card__action--reply"
                    onClick={() => setShowReplyForm(true)}
                >
                    Repondre
                </button>
                {message.replies.length > 0 && (
                    <button
                        type="button"
                        className="message-card__action message-card__action--toggle"
                        onClick={() => setShowReplies(!showReplies)}
                    >
                        {showReplies ? 'Masquer' : `Voir ${message.replies.length} reponse${message.replies.length > 1 ? 's' : ''}`}
                        {unreadReplies.length > 0 && (
                            <span className="message-card__badge">{unreadReplies.length}</span>
                        )}
                    </button>
                )}
                <button
                    type="button"
                    className="message-card__action message-card__action--delete"
                    onClick={handleDelete}
                    disabled={isDeleting}
                >
                    Supprimer
                </button>
            </div>

            {isResolved && (
                <div className="message-card__status">
                    Resolu {message.resolved_at && formatDate(message.resolved_at)}
                </div>
            )}

            {showReplyForm && (
                <ReplyForm
                    messageId={message.id}
                    onClose={() => setShowReplyForm(false)}
                />
            )}

            {showReplies && message.replies.length > 0 && (
                <div className="message-card__replies">
                    {message.replies.map(reply => (
                        <div
                            key={reply.id}
                            className={`message-card__reply ${!reply.read_at && reply.author_user_id !== currentUserId ? 'message-card__reply--unread' : ''}`}
                        >
                            <div className="message-card__reply-header">
                                <span className="message-card__reply-author">{reply.author_label}</span>
                                {reply.recipient_label && (
                                    <>
                                        <span className="message-card__arrow">&rarr;</span>
                                        <span className="message-card__reply-recipient">{reply.recipient_label}</span>
                                    </>
                                )}
                                <span className="message-card__reply-date">{formatDate(reply.created_at)}</span>
                            </div>
                            <div className="message-card__reply-content">{reply.content}</div>
                            {!reply.read_at && reply.author_user_id !== currentUserId && (
                                <button
                                    type="button"
                                    className="message-card__action message-card__action--read"
                                    onClick={() => handleReadReply(reply.id)}
                                >
                                    Lu
                                </button>
                            )}
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
