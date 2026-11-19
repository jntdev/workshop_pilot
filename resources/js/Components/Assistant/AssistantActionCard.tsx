import { Link, router } from '@inertiajs/react';
import { useState } from 'react';
import { ApiError, apiPost } from '@/utils/api';
import type { AssistantAction } from './types';

type Verb = 'confirm' | 'cancel' | 'undo';

interface AssistantActionCardProps {
    action: AssistantAction;
    onChange: (action: AssistantAction) => void;
}

/**
 * Carte d'une action proposée par l'assistant. Rien n'est modifié tant que l'utilisateur
 * n'a pas cliqué sur « Confirmer » : le texte affiché vient du serveur, pas de l'assistant.
 */
export default function AssistantActionCard({ action, onChange }: AssistantActionCardProps) {
    const [busy, setBusy] = useState<Verb | null>(null);
    const [errorText, setErrorText] = useState<string | null>(null);

    const run = async (verb: Verb) => {
        if (busy !== null) {
            return;
        }

        setBusy(verb);
        setErrorText(null);
        try {
            const updated = await apiPost<AssistantAction>(`/api/assistant/actions/${action.id}/${verb}`);
            onChange(updated);

            if (verb !== 'cancel') {
                // Les pages construites par Inertia se mettent à jour ; les autres se rafraîchissent via le lien « Voir ».
                router.reload();
            }
        } catch (error) {
            if (error instanceof ApiError) {
                const current = (error.data as { action?: AssistantAction }).action;
                if (current) {
                    onChange(current);
                }
                setErrorText(error.message);
            } else {
                setErrorText('L’action a échoué, réessaie.');
            }
        } finally {
            setBusy(null);
        }
    };

    const isOpenProposal = action.status === 'proposed';

    return (
        <div className={`assistant-action assistant-action--${action.status}`}>
            <div className="assistant-action__head">
                <span className="assistant-action__type">{action.type_label}</span>
                <span className="assistant-action__status">{statusLabel(action)}</span>
            </div>

            <p className="assistant-action__summary">{action.summary}</p>

            {action.warnings.length > 0 && isOpenProposal && (
                <ul className="assistant-action__warnings">
                    {action.warnings.map((warning) => (
                        <li key={warning}>{warning}</li>
                    ))}
                </ul>
            )}

            {(errorText ?? (action.status === 'failed' ? action.error : null)) && (
                <p className="assistant-action__error">{errorText ?? action.error}</p>
            )}

            <div className="assistant-action__buttons">
                {isOpenProposal && (
                    <>
                        <button
                            type="button"
                            className="assistant-action__confirm"
                            disabled={busy !== null}
                            onClick={() => void run('confirm')}
                        >
                            {busy === 'confirm' ? 'En cours…' : 'Confirmer'}
                        </button>
                        <button
                            type="button"
                            className="assistant-action__cancel"
                            disabled={busy !== null}
                            onClick={() => void run('cancel')}
                        >
                            Annuler
                        </button>
                    </>
                )}

                {action.status === 'executed' && action.can_undo && (
                    <button
                        type="button"
                        className="assistant-action__cancel"
                        disabled={busy !== null}
                        onClick={() => void run('undo')}
                    >
                        {busy === 'undo' ? 'En cours…' : 'Défaire'}
                    </button>
                )}

                {action.link && (action.status === 'executed' || isOpenProposal) && (
                    <Link href={action.link} className="assistant-chat__ref">
                        Voir
                    </Link>
                )}
            </div>
        </div>
    );
}

function statusLabel(action: AssistantAction): string {
    switch (action.status) {
        case 'proposed':
            return 'À confirmer';
        case 'executed':
            return '✓ Fait';
        case 'confirming':
            return 'En cours…';
        case 'undone':
            return 'Défait';
        case 'cancelled':
            return 'Annulé';
        case 'expired':
            return 'Expiré';
        case 'failed':
            return 'Échec';
        default:
            return action.status_label;
    }
}
