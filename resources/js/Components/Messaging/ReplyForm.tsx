import { useState } from 'react';
import { useMessaging } from '@/Contexts/MessagingContext';

interface ReplyFormProps {
    messageId: number;
    isPersonalNote: boolean;
    onClose: () => void;
}

export default function ReplyForm({ messageId, isPersonalNote, onClose }: ReplyFormProps) {
    const { currentUserId, users, createReply } = useMessaging();

    const otherUsers = users.filter(u => u.id !== currentUserId);
    const currentUserName = users.find(u => u.id === currentUserId)?.name ?? '';

    const [recipientUserId, setRecipientUserId] = useState<number | null>(
        isPersonalNote ? null : (otherUsers[0]?.id ?? null)
    );
    const [content, setContent] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!content.trim()) {
            setError('La reponse ne peut pas etre vide');
            return;
        }

        setIsSubmitting(true);
        setError(null);

        try {
            await createReply(messageId, {
                recipient_user_id: recipientUserId,
                content: content.trim(),
            });
            onClose();
        } catch (err) {
            setError('Erreur lors de la creation de la reponse');
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <form className="reply-form" onSubmit={handleSubmit}>
            <div className="reply-form__header">
                <span>Repondre en tant que {currentUserName}</span>
                <button
                    type="button"
                    className="reply-form__close"
                    onClick={onClose}
                >
                    &times;
                </button>
            </div>

            {!isPersonalNote && (
                <div className="reply-form__recipient">
                    <label>Pour</label>
                    <div className="reply-form__radio-group">
                        {otherUsers.map(u => (
                            <label key={u.id} className="reply-form__radio">
                                <input
                                    type="radio"
                                    name="recipient"
                                    value={u.id}
                                    checked={recipientUserId === u.id}
                                    onChange={() => setRecipientUserId(u.id)}
                                />
                                <span>{u.name}</span>
                            </label>
                        ))}
                    </div>
                </div>
            )}

            <textarea
                className="reply-form__input"
                value={content}
                onChange={(e) => setContent(e.target.value)}
                placeholder="Votre reponse..."
                rows={3}
                required
                autoFocus
            />

            {error && <div className="reply-form__error">{error}</div>}

            <div className="reply-form__actions">
                <button
                    type="button"
                    className="reply-form__cancel"
                    onClick={onClose}
                >
                    Annuler
                </button>
                <button
                    type="submit"
                    className="reply-form__submit"
                    disabled={isSubmitting}
                >
                    {isSubmitting ? 'Envoi...' : 'Repondre'}
                </button>
            </div>
        </form>
    );
}
