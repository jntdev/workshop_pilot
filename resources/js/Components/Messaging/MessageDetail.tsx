import { useState, useRef, useEffect } from 'react';
import { useMessaging } from '@/Contexts/MessagingContext';
import { Message, Photo } from '@/types';
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
        currentUserId,
        categories,
        markMessageAsRead,
        markMessageAsResolved,
        reopenMessage,
        deleteMessage,
        markReplyAsRead,
        updateReply,
        deleteReply,
        updateMessageCategory,
    } = useMessaging();

    const [showReplyForm, setShowReplyForm] = useState(false);
    const [isDeleting, setIsDeleting] = useState(false);
    const [showPhotoModal, setShowPhotoModal] = useState(false);
    const [editingReplyId, setEditingReplyId] = useState<number | null>(null);
    const [editingContent, setEditingContent] = useState('');
    const [showCategoryPicker, setShowCategoryPicker] = useState(false);
    const categoryPickerRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        if (!showCategoryPicker) { return; }
        const handleClickOutside = (e: MouseEvent) => {
            if (categoryPickerRef.current && !categoryPickerRef.current.contains(e.target as Node)) {
                setShowCategoryPicker(false);
            }
        };
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, [showCategoryPicker]);

    const isPersonalNote = message.recipient_user_id === null;
    const isUnread = !message.read_at && !isPersonalNote;
    const isResolved = message.status === 'resolu';
    const hasContact = message.contact_name || message.contact_phone || message.contact_email;

    const canMarkAsRead = !isPersonalNote && message.recipient_user_id === currentUserId;

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

    const handleCategoryChange = async (categoryId: number) => {
        setShowCategoryPicker(false);
        await updateMessageCategory(message.id, categoryId);
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

    const handleEditReply = (replyId: number, content: string) => {
        setEditingReplyId(replyId);
        setEditingContent(content);
    };

    const handleSaveReply = async (replyId: number) => {
        if (!editingContent.trim()) { return; }
        await updateReply(replyId, editingContent.trim());
        setEditingReplyId(null);
        setEditingContent('');
    };

    const handleDeleteReply = async (replyId: number) => {
        if (confirm('Supprimer cette reponse ?')) {
            await deleteReply(replyId, message.id);
        }
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
                    {!message.recipient_user_id && (
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

                <div className="message-detail__category-picker" ref={categoryPickerRef}>
                    <button
                        type="button"
                        className="message-detail__category-badge"
                        onClick={() => setShowCategoryPicker(prev => !prev)}
                    >
                        {message.category && (
                            <span
                                className="message-detail__category-dot"
                                style={{ backgroundColor: message.category.color }}
                            />
                        )}
                        <span>{message.category?.label ?? 'Sans catégorie'}</span>
                    </button>
                    {showCategoryPicker && (
                        <div className="message-detail__category-dropdown">
                            {categories.map(cat => (
                                <button
                                    key={cat.id}
                                    type="button"
                                    className={`message-detail__category-option${cat.id === message.category_id ? ' message-detail__category-option--active' : ''}`}
                                    onClick={() => handleCategoryChange(cat.id)}
                                >
                                    <span
                                        className="message-detail__category-dot"
                                        style={{ backgroundColor: cat.color }}
                                    />
                                    {cat.label}
                                </button>
                            ))}
                        </div>
                    )}
                </div>

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
                            const isReplyReceivedUnread = reply.author_user_id !== currentUserId && !reply.read_at;
                            const isReplyPendingRead = reply.author_user_id === currentUserId && !reply.read_at;
                            const isReplyReadByOther = reply.author_user_id === currentUserId && reply.read_at !== null;

                            const replyClassNames = [
                                'message-detail__reply',
                                isReplyReceivedUnread && 'message-detail__reply--unread',
                                isReplyPendingRead && 'message-detail__reply--pending-read',
                                isReplyReadByOther && 'message-detail__reply--read-by-other',
                            ].filter(Boolean).join(' ');

                            const isMyReply = reply.author_user_id === currentUserId;
                            const isEditing = editingReplyId === reply.id;

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
                                        {isMyReply && !isEditing && (
                                            <div className="message-detail__reply-owner-actions">
                                                <button
                                                    type="button"
                                                    className="message-detail__reply-edit"
                                                    onClick={() => handleEditReply(reply.id, reply.content)}
                                                >
                                                    Modifier
                                                </button>
                                                <button
                                                    type="button"
                                                    className="message-detail__reply-delete"
                                                    onClick={() => handleDeleteReply(reply.id)}
                                                >
                                                    Supprimer
                                                </button>
                                            </div>
                                        )}
                                    </div>
                                    {isEditing ? (
                                        <div className="message-detail__reply-edit-form">
                                            <textarea
                                                className="message-detail__reply-edit-input"
                                                value={editingContent}
                                                onChange={e => setEditingContent(e.target.value)}
                                                rows={3}
                                                autoFocus
                                            />
                                            <div className="message-detail__reply-edit-actions">
                                                <button
                                                    type="button"
                                                    className="message-detail__reply-edit-cancel"
                                                    onClick={() => setEditingReplyId(null)}
                                                >
                                                    Annuler
                                                </button>
                                                <button
                                                    type="button"
                                                    className="message-detail__reply-edit-save"
                                                    onClick={() => handleSaveReply(reply.id)}
                                                >
                                                    Enregistrer
                                                </button>
                                            </div>
                                        </div>
                                    ) : (
                                        <div className="message-detail__reply-content">{reply.content}</div>
                                    )}
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
                            isPersonalNote={isPersonalNote}
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
