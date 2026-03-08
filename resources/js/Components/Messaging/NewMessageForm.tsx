import { useState } from 'react';
import { useMessaging, getModeLabel } from '@/Contexts/MessagingContext';
import { WorkMode, MessageCategory } from '@/types';

const CATEGORY_LABELS: Record<MessageCategory, string> = {
    accueil: 'Accueil',
    atelier: 'Atelier',
    location: 'Location',
    autre: 'Autre',
};

const CATEGORIES: MessageCategory[] = ['accueil', 'atelier', 'location', 'autre'];

interface NewMessageFormProps {
    onClose: () => void;
}

export default function NewMessageForm({ onClose }: NewMessageFormProps) {
    const { mode, createMessage } = useMessaging();

    const [recipientMode, setRecipientMode] = useState<WorkMode | 'self'>('self');
    const [category, setCategory] = useState<MessageCategory>('autre');
    const [contactName, setContactName] = useState('');
    const [contactPhone, setContactPhone] = useState('');
    const [contactEmail, setContactEmail] = useState('');
    const [content, setContent] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const otherMode: WorkMode = mode === 'comptoir' ? 'atelier' : 'comptoir';

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!content.trim()) {
            setError('Le message ne peut pas etre vide');
            return;
        }

        setIsSubmitting(true);
        setError(null);

        try {
            await createMessage({
                recipient_mode: recipientMode === 'self' ? null : recipientMode,
                category,
                contact_name: contactName || undefined,
                contact_phone: contactPhone || undefined,
                contact_email: contactEmail || undefined,
                content: content.trim(),
            });
            onClose();
        } catch (err) {
            setError('Erreur lors de la creation du message');
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <form className="new-message-form" onSubmit={handleSubmit}>
            <div className="new-message-form__header">
                <h3>Nouveau message</h3>
                <button
                    type="button"
                    className="new-message-form__close"
                    onClick={onClose}
                >
                    &times;
                </button>
            </div>

            <div className="new-message-form__row">
                <div className="new-message-form__field">
                    <label>Destinataire</label>
                    <div className="new-message-form__radio-group">
                        <label className="new-message-form__radio">
                            <input
                                type="radio"
                                name="recipient"
                                value="self"
                                checked={recipientMode === 'self'}
                                onChange={() => setRecipientMode('self')}
                            />
                            <span>Note pour moi ({getModeLabel(mode)})</span>
                        </label>
                        <label className="new-message-form__radio">
                            <input
                                type="radio"
                                name="recipient"
                                value={otherMode}
                                checked={recipientMode === otherMode}
                                onChange={() => setRecipientMode(otherMode)}
                            />
                            <span>Pour {getModeLabel(otherMode)}</span>
                        </label>
                    </div>
                </div>

                <div className="new-message-form__field">
                    <label>Categorie *</label>
                    <div className="new-message-form__radio-group">
                        {CATEGORIES.map(cat => (
                            <label key={cat} className="new-message-form__radio">
                                <input
                                    type="radio"
                                    name="category"
                                    value={cat}
                                    checked={category === cat}
                                    onChange={() => setCategory(cat)}
                                />
                                <span>{CATEGORY_LABELS[cat]}</span>
                            </label>
                        ))}
                    </div>
                </div>
            </div>

            <div className="new-message-form__contact">
                <div className="new-message-form__field new-message-form__field--small">
                    <label>Nom du contact</label>
                    <input
                        type="text"
                        value={contactName}
                        onChange={(e) => setContactName(e.target.value)}
                        placeholder="Client, fournisseur..."
                    />
                </div>
                <div className="new-message-form__field new-message-form__field--small">
                    <label>Telephone</label>
                    <input
                        type="tel"
                        value={contactPhone}
                        onChange={(e) => setContactPhone(e.target.value)}
                        placeholder="06 12 34 56 78"
                    />
                </div>
                <div className="new-message-form__field new-message-form__field--small">
                    <label>Email</label>
                    <input
                        type="email"
                        value={contactEmail}
                        onChange={(e) => setContactEmail(e.target.value)}
                        placeholder="email@exemple.com"
                    />
                </div>
            </div>

            <div className="new-message-form__field">
                <label>Message *</label>
                <textarea
                    value={content}
                    onChange={(e) => setContent(e.target.value)}
                    placeholder="Contenu du message..."
                    rows={4}
                    required
                />
            </div>

            {error && <div className="new-message-form__error">{error}</div>}

            <div className="new-message-form__actions">
                <button
                    type="button"
                    className="new-message-form__cancel"
                    onClick={onClose}
                >
                    Annuler
                </button>
                <button
                    type="submit"
                    className="new-message-form__submit"
                    disabled={isSubmitting}
                >
                    {isSubmitting ? 'Envoi...' : 'Envoyer'}
                </button>
            </div>
        </form>
    );
}
