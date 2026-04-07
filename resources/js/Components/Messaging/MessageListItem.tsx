import { useMessaging } from '@/Contexts/MessagingContext';
import { Message } from '@/types';

interface MessageListItemProps {
    message: Message;
    isSelected: boolean;
    onClick: () => void;
}

function formatDate(dateString: string): string {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now.getTime() - date.getTime();
    const diffHours = diffMs / (1000 * 60 * 60);

    if (diffHours < 1) {
        const mins = Math.floor(diffMs / (1000 * 60));
        return `${mins}min`;
    }
    if (diffHours < 24) {
        return `${Math.floor(diffHours)}h`;
    }
    if (diffHours < 48) {
        return 'Hier';
    }
    return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
}

export default function MessageListItem({ message, isSelected, onClick }: MessageListItemProps) {
    const { currentUserId } = useMessaging();

    const isPersonalNote = message.recipient_user_id === null;
    const isResolved = message.status === 'resolu';
    const hasReplies = message.replies.length > 0;
    const unreadReplies = message.replies.filter(r => !r.read_at && r.author_user_id !== currentUserId).length;

    // Déterminer l'état du message pour le style de bordure
    // - Bleu : message reçu non lu (destinataire = moi, pas encore lu)
    // - Jaune : message envoyé par moi, pas encore lu par l'autre
    // - Vert : message envoyé par moi, lu par l'autre
    const isReceivedUnread = !isPersonalNote && message.recipient_user_id === currentUserId && !message.read_at;
    const isSentPendingRead = !isPersonalNote && message.author_user_id === currentUserId && message.recipient_user_id !== currentUserId && !message.read_at;
    const isSentReadByOther = !isPersonalNote && message.author_user_id === currentUserId && message.recipient_user_id !== currentUserId && message.read_at !== null;

    // Truncate content for preview
    const preview = message.content.length > 80
        ? message.content.substring(0, 80) + '...'
        : message.content;

    const classNames = [
        'message-list-item',
        isSelected && 'message-list-item--selected',
        isReceivedUnread && 'message-list-item--unread',
        isSentPendingRead && 'message-list-item--pending-read',
        isSentReadByOther && 'message-list-item--read-by-other',
        isResolved && 'message-list-item--resolved',
    ].filter(Boolean).join(' ');

    return (
        <div
            className={classNames}
            onClick={onClick}
        >
            <div className="message-list-item__header">
                <div className="message-list-item__from">
                    <span className="message-list-item__author">{message.author_label}</span>
                    {message.recipient_label && (
                        <>
                            <span className="message-list-item__arrow">&rarr;</span>
                            <span className="message-list-item__recipient">{message.recipient_label}</span>
                        </>
                    )}
                    {!message.recipient_user_id && (
                        <span className="message-list-item__self">(perso)</span>
                    )}
                </div>
                <span className="message-list-item__date">{formatDate(message.created_at)}</span>
            </div>

            {message.contact_name && (
                <div className="message-list-item__contact">{message.contact_name}</div>
            )}

            <div className="message-list-item__preview">{preview}</div>

            <div className="message-list-item__footer">
                {message.category && (
                    <span className="message-list-item__category">
                        <span
                            className="message-list-item__category-dot"
                            style={{ backgroundColor: message.category.color }}
                        />
                        {message.category.label}
                    </span>
                )}
                {isReceivedUnread && (
                    <span className="message-list-item__status message-list-item__status--new">Nouveau</span>
                )}
                {isResolved && (
                    <span className="message-list-item__status message-list-item__status--resolved">Resolu</span>
                )}
                {hasReplies && (
                    <span className="message-list-item__replies">
                        {message.replies.length} reponse{message.replies.length > 1 ? 's' : ''}
                        {unreadReplies > 0 && (
                            <span className="message-list-item__badge">{unreadReplies}</span>
                        )}
                    </span>
                )}
            </div>
        </div>
    );
}
