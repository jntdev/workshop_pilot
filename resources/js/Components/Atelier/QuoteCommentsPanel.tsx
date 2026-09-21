import { useState, useEffect, useCallback } from 'react';
import type { QuoteComment, QuoteCommentRecipient } from '@/types';

interface Props {
    quoteId: number;
}

const RECIPIENTS: { value: QuoteCommentRecipient; label: string }[] = [
    { value: 'nikal', label: 'Pour Nikal' },
    { value: 'jal', label: 'Pour Jal' },
];

function csrfToken(): string {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function formatDateTime(dateString: string): string {
    return new Date(dateString).toLocaleString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    });
}

export default function QuoteCommentsPanel({ quoteId }: Props) {
    const [recipient, setRecipient] = useState<QuoteCommentRecipient>('nikal');
    const [comments, setComments] = useState<QuoteComment[]>([]);
    const [isResolved, setIsResolved] = useState(true);
    const [isLoading, setIsLoading] = useState(true);
    const [content, setContent] = useState('');
    const [isSending, setIsSending] = useState(false);
    const [isResolving, setIsResolving] = useState(false);

    const load = useCallback(async () => {
        setIsLoading(true);
        const res = await fetch(`/api/quotes/${quoteId}/comments`, { headers: { Accept: 'application/json' } });
        const data = await res.json();
        setComments(data.comments);
        setIsResolved(data.is_resolved);
        setIsLoading(false);
    }, [quoteId]);

    useEffect(() => {
        load();
    }, [load]);

    const handleSend = useCallback(async () => {
        if (!content.trim()) { return; }

        setIsSending(true);
        const res = await fetch(`/api/quotes/${quoteId}/comments`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken() },
            body: JSON.stringify({ recipient_label: recipient, content: content.trim() }),
        });

        if (res.ok) {
            const comment = await res.json();
            setComments(prev => [...prev, comment]);
            setIsResolved(false);
            setContent('');
        }
        setIsSending(false);
    }, [quoteId, recipient, content]);

    const handleToggleResolve = useCallback(async () => {
        setIsResolving(true);
        const endpoint = isResolved ? 'reopen' : 'resolve';
        await fetch(`/api/quotes/${quoteId}/comments/${endpoint}`, {
            method: 'PATCH',
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken() },
        });
        setIsResolved(prev => !prev);
        setIsResolving(false);
    }, [quoteId, isResolved]);

    return (
        <section className="quote-comments">
            <h2 className="quote-comments__title">Commentaires</h2>

            <div className="quote-comments__list">
                {isLoading ? (
                    <div className="quote-comments__loading">Chargement...</div>
                ) : comments.length === 0 ? (
                    <div className="quote-comments__empty">Aucun commentaire</div>
                ) : (
                    comments.map(comment => (
                        <div key={comment.id} className="quote-comments__item">
                            <div className="quote-comments__item-header">
                                <span className="quote-comments__recipient">{comment.recipient_display}</span>
                                <span className="quote-comments__date">{formatDateTime(comment.created_at)}</span>
                            </div>
                            <p className="quote-comments__content">{comment.content}</p>
                        </div>
                    ))
                )}
            </div>

            <div className="quote-comments__form">
                <div className="quote-comments__recipient-picker">
                    {RECIPIENTS.map(r => (
                        <button
                            key={r.value}
                            type="button"
                            className={`quote-comments__recipient-btn ${recipient === r.value ? 'quote-comments__recipient-btn--active' : ''}`}
                            onClick={() => setRecipient(r.value)}
                        >
                            {r.label}
                        </button>
                    ))}
                </div>
                <div className="quote-comments__input-row">
                    <textarea
                        className="quote-comments__input"
                        value={content}
                        onChange={e => setContent(e.target.value)}
                        onKeyDown={e => {
                            if (e.key === 'Enter' && (e.metaKey || e.ctrlKey)) {
                                e.preventDefault();
                                handleSend();
                            }
                        }}
                        placeholder={`Écrire un commentaire ${RECIPIENTS.find(r => r.value === recipient)?.label.toLowerCase()}...`}
                        rows={2}
                    />
                    <div className="quote-comments__actions">
                        <button
                            type="button"
                            className={`quote-comments__resolve-btn ${isResolved ? 'quote-comments__resolve-btn--resolved' : ''}`}
                            onClick={handleToggleResolve}
                            disabled={isResolving}
                        >
                            {isResolved ? 'Rouvrir' : 'Traité'}
                        </button>
                        <button type="button" className="quote-comments__send" onClick={handleSend} disabled={isSending || !content.trim()}>
                            Envoyer
                        </button>
                    </div>
                </div>
            </div>
        </section>
    );
}
