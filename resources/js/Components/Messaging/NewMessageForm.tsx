import { useState, useEffect } from 'react';
import { useMessaging } from '@/Contexts/MessagingContext';

interface NewMessageFormProps {
    onClose: () => void;
}

export default function NewMessageForm({ onClose }: NewMessageFormProps) {
    const { currentUserId, users, categories, createMessage } = useMessaging();

    const otherUsers = users.filter(u => u.id !== currentUserId);
    const currentUserName = users.find(u => u.id === currentUserId)?.name ?? '';

    // Default to the first non-default category, or the default one
    const defaultCategory = categories.find(c => !c.is_default) || categories.find(c => c.is_default);

    const [recipientUserId, setRecipientUserId] = useState<number | 'self'>('self');
    const [categoryId, setCategoryId] = useState<number | null>(defaultCategory?.id ?? null);
    const [contactName, setContactName] = useState('');
    const [contactPhone, setContactPhone] = useState('');
    const [contactEmail, setContactEmail] = useState('');
    const [content, setContent] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    // Update categoryId when categories are loaded
    useEffect(() => {
        if (!categoryId && categories.length > 0) {
            const defaultCat = categories.find(c => !c.is_default) || categories.find(c => c.is_default);
            if (defaultCat) {
                setCategoryId(defaultCat.id);
            }
        }
    }, [categories, categoryId]);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!content.trim()) {
            setError('Le message ne peut pas etre vide');
            return;
        }
        if (!categoryId) {
            setError('Veuillez selectionner une categorie');
            return;
        }

        setIsSubmitting(true);
        setError(null);

        try {
            await createMessage({
                recipient_user_id: recipientUserId === 'self' ? null : recipientUserId,
                category_id: categoryId,
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
                                checked={recipientUserId === 'self'}
                                onChange={() => setRecipientUserId('self')}
                            />
                            <span>Note pour moi ({currentUserName})</span>
                        </label>
                        {otherUsers.map(u => (
                            <label key={u.id} className="new-message-form__radio">
                                <input
                                    type="radio"
                                    name="recipient"
                                    value={u.id}
                                    checked={recipientUserId === u.id}
                                    onChange={() => setRecipientUserId(u.id)}
                                />
                                <span>Pour {u.name}</span>
                            </label>
                        ))}
                    </div>
                </div>

                <div className="new-message-form__field">
                    <label>Categorie *</label>
                    <div className="new-message-form__radio-group">
                        {categories.map(cat => (
                            <label key={cat.id} className="new-message-form__radio">
                                <input
                                    type="radio"
                                    name="category"
                                    value={cat.id}
                                    checked={categoryId === cat.id}
                                    onChange={() => setCategoryId(cat.id)}
                                />
                                <span
                                    className="new-message-form__category-label"
                                    style={{ display: 'flex', alignItems: 'center', gap: '0.375rem' }}
                                >
                                    <span
                                        style={{
                                            width: 8,
                                            height: 8,
                                            borderRadius: '50%',
                                            backgroundColor: cat.color,
                                        }}
                                    />
                                    {cat.label}
                                </span>
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
