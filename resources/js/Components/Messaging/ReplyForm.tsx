import { useState } from 'react';
import { useMessaging, getModeLabel } from '@/Contexts/MessagingContext';
import { WorkMode } from '@/types';

interface ReplyFormProps {
    messageId: number;
    onClose: () => void;
}

export default function ReplyForm({ messageId, onClose }: ReplyFormProps) {
    const { mode, createReply } = useMessaging();

    const [content, setContent] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const otherMode: WorkMode = mode === 'comptoir' ? 'atelier' : 'comptoir';

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
                recipient_mode: otherMode,
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
                <span>Repondre en tant que {getModeLabel(mode)}</span>
                <button
                    type="button"
                    className="reply-form__close"
                    onClick={onClose}
                >
                    &times;
                </button>
            </div>

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
