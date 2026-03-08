import { useState } from 'react';
import { useMessaging, getModeLabel } from '@/Contexts/MessagingContext';
import { Message, WorkMode, Photo } from '@/types';
import ReplyForm from './ReplyForm';
import PhotoGallery from '../Photos/PhotoGallery';
import PhotoUploadModal from '../Photos/PhotoUploadModal';

interface MessageDetailProps {
    message: Message;
    onClose: () => void;
}

function formatDate(dateString: string): string {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'long',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function formatRelative(dateString: string): string {
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

export default function MessageDetail({ message, onClose }: MessageDetailProps) {
    const {
        mode,
        markMessageAsRead,
        markMessageAsResolved,
        reopenMessage,
        deleteMessage,
        markReplyAsRead,
    } = useMessaging();

    const [showReplyForm, setShowReplyForm] = useState(false);
    const [isDeleting, setIsDeleting] = useState(false);
    const [showPhotoModal, setShowPhotoModal] = useState(false);

    const isPersonalNote = message.recipient_mode === null;
    const isUnread = !message.read_at && !isPersonalNote;
    const isResolved = message.status === 'resolu';
    const hasContact = message.contact_name || message.contact_phone || message.contact_email;

    // Pas de bouton "Info lue" pour les notes perso
    const canMarkAsRead = !isPersonalNote && message.recipient_mode === mode;

    const isAuthorViewingOthersRead = message.author_mode === mode &&
        message.recipient_mode !== null &&
        message.recipient_mode !== mode &&
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
            onClose();
        }
    };

    const handleReadReply = async (replyId: number) => {
        await markReplyAsRead(replyId);
    };

    return (
        <div className="message-detail">
            <div className="message-detail__header">
                <div className="message-detail__title">
                    <span className="message-detail__author">{message.author_label}</span>
                    {message.recipient_label && (
                        <>
                            <span className="message-detail__arrow">&rarr;</span>
                            <span className="message-detail__recipient">{message.recipient_label}</span>
                        </>
                    )}
                    {!message.recipient_mode && (
                        <span className="message-detail__self">(note perso)</span>
                    )}
                </div>
                <button
                    type="button"
                    className="message-detail__close"
                    onClick={onClose}
                >
                    &times;
                </button>
            </div>

            <div className="message-detail__meta">
                <span className="message-detail__date">{formatDate(message.created_at)}</span>
                {isResolved && (
                    <span className="message-detail__status">Resolu</span>
                )}
            </div>

            {hasContact && (
                <div className="message-detail__contact">
                    {message.contact_name && (
                        <span className="message-detail__contact-name">{message.contact_name}</span>
                    )}
                    {message.contact_phone && (
                        <a href={`tel:${message.contact_phone}`} className="message-detail__contact-phone">
                            {message.contact_phone}
                        </a>
                    )}
                    {message.contact_email && (
                        <a href={`mailto:${message.contact_email}`} className="message-detail__contact-email">
                            {message.contact_email}
                        </a>
                    )}
                </div>
            )}

            <div className="message-detail__content">{message.content}</div>

            {message.photos && message.photos.length > 0 && (
                <PhotoGallery photos={message.photos} />
            )}

            <div className="message-detail__actions">
                {isUnread && canMarkAsRead && (
                    <button
                        type="button"
                        className="message-detail__action message-detail__action--read"
                        onClick={handleMarkRead}
                    >
                        Info lue
                    </button>
                )}
                {isAuthorViewingOthersRead && (
                    <span className="message-detail__read-status">
                        Lu {message.read_at && formatRelative(message.read_at)}
                    </span>
                )}
                {!isResolved ? (
                    <button
                        type="button"
                        className="message-detail__action message-detail__action--resolve"
                        onClick={handleResolve}
                    >
                        Resolu
                    </button>
                ) : (
                    <button
                        type="button"
                        className="message-detail__action message-detail__action--reopen"
                        onClick={handleReopen}
                    >
                        Reouvrir
                    </button>
                )}
                <button
                    type="button"
                    className="message-detail__action message-detail__action--delete"
                    onClick={handleDelete}
                    disabled={isDeleting}
                >
                    Supprimer
                </button>
            </div>

            <div className="message-detail__replies-section">
                <div className="message-detail__replies-header">
                    <h3>Reponses ({message.replies.length})</h3>
                </div>

                {message.replies.length === 0 ? (
                    <div className="message-detail__no-replies">
                        Aucune reponse pour le moment
                    </div>
                ) : (
                    <div className="message-detail__replies">
                        {message.replies.map(reply => {
                            // Réponse reçue non lue (l'autre m'a envoyé, je n'ai pas lu)
                            const isReplyReceivedUnread = reply.author_mode !== mode && !reply.read_at;
                            // Réponse envoyée par moi, en attente de lecture
                            const isReplyPendingRead = reply.author_mode === mode && !reply.read_at;
                            // Réponse envoyée par moi, lue par l'autre
                            const isReplyReadByOther = reply.author_mode === mode && reply.read_at !== null;

                            const replyClassNames = [
                                'message-detail__reply',
                                isReplyReceivedUnread && 'message-detail__reply--unread',
                                isReplyPendingRead && 'message-detail__reply--pending-read',
                                isReplyReadByOther && 'message-detail__reply--read-by-other',
                            ].filter(Boolean).join(' ');

                            return (
                                <div key={reply.id} className={replyClassNames}>
                                    <div className="message-detail__reply-header">
                                        <span className="message-detail__reply-author">{reply.author_label}</span>
                                        {reply.recipient_label && (
                                            <>
                                                <span className="message-detail__arrow">&rarr;</span>
                                                <span className="message-detail__reply-recipient">{reply.recipient_label}</span>
                                            </>
                                        )}
                                        <span className="message-detail__reply-date">{formatRelative(reply.created_at)}</span>
                                        {isReplyReadByOther && (
                                            <span className="message-detail__reply-read-status">Lu</span>
                                        )}
                                    </div>
                                    <div className="message-detail__reply-content">{reply.content}</div>
                                    {reply.photos && reply.photos.length > 0 && (
                                        <PhotoGallery photos={reply.photos} />
                                    )}
                                    {isReplyReceivedUnread && (
                                        <button
                                            type="button"
                                            className="message-detail__action message-detail__action--read"
                                            onClick={() => handleReadReply(reply.id)}
                                        >
                                            Lu
                                        </button>
                                    )}
                                </div>
                            );
                        })}
                    </div>
                )}

                <div className="message-detail__reply-footer">
                    {showReplyForm ? (
                        <ReplyForm
                            messageId={message.id}
                            onClose={() => setShowReplyForm(false)}
                        />
                    ) : (
                        <div className="message-detail__reply-actions">
                            <button
                                type="button"
                                className="message-detail__reply-btn"
                                onClick={() => setShowReplyForm(true)}
                            >
                                + Repondre
                            </button>
                            <button
                                type="button"
                                className="message-detail__photo-btn"
                                onClick={() => setShowPhotoModal(true)}
                            >
                                + Photo mobile
                            </button>
                        </div>
                    )}
                </div>
            </div>

            <PhotoUploadModal
                contextType="message"
                contextId={message.id}
                isOpen={showPhotoModal}
                onClose={() => setShowPhotoModal(false)}
                onPhotosReceived={(photos) => {
                    // Les photos sont automatiquement attachées au message côté serveur
                    // On pourrait refresh les messages ici si nécessaire
                }}
            />
        </div>
    );
}
