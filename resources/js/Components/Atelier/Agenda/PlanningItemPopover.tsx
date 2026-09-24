import { useState, useEffect, useCallback } from 'react';
import type { AgendaItem, QuoteTask } from '@/types';

interface PlanningSummary {
    reception_comment: string | null;
    tasks: QuoteTask[];
}

const POPOVER_WIDTH_PX = 320;
const GAP_PX = 12;

interface Props {
    item: AgendaItem | null;
    anchorRect: DOMRect | null;
    onClose: () => void;
}

export default function PlanningItemPopover({ item, anchorRect, onClose }: Props) {
    const [summary, setSummary] = useState<PlanningSummary | null>(null);
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const load = useCallback(async (quoteId: number) => {
        setIsLoading(true);
        setError(null);
        setSummary(null);
        try {
            const res = await fetch(`/api/quotes/${quoteId}/planning-summary`, { headers: { Accept: 'application/json' } });
            if (res.ok) {
                setSummary(await res.json());
            } else {
                setError('Impossible de charger le devis.');
            }
        } catch {
            setError('Impossible de charger le devis.');
        } finally {
            setIsLoading(false);
        }
    }, []);

    useEffect(() => {
        if (item?.kind === 'quote') {
            load(item.quote_id);
        }
    }, [item, load]);

    if (!item || !anchorRect) return null;

    // Accolée à gauche de la carte cliquée, ancrée par le bas de la carte :
    // si la modale doit grandir (contenu long), elle s'étend vers le haut
    // plutôt que de déborder en bas de l'écran, sans jamais dépasser le haut.
    const bottomOffset = window.innerHeight - anchorRect.bottom;
    const style: React.CSSProperties = {
        right: window.innerWidth - anchorRect.left + GAP_PX,
        bottom: bottomOffset,
        width: POPOVER_WIDTH_PX,
        maxHeight: `calc(100vh - ${bottomOffset}px - 1.25rem)`,
    };

    return (
        <div className="agenda-planning-item-popover" style={style}>
            <div className="agenda-planning-item-popover__header">
                <h3 className="agenda-planning-item-popover__title">
                    {item.kind === 'quote' ? item.quote_reference : item.title}
                </h3>
                <button type="button" className="agenda-planning-item-popover__close" onClick={onClose} aria-label="Fermer">
                    ×
                </button>
            </div>

            {item.kind === 'event' ? (
                item.detail ? (
                    <p className="agenda-planning-item-popover__text">{item.detail}</p>
                ) : (
                    <p className="quotes-list__tasks-empty">Aucun détail renseigné.</p>
                )
            ) : (
                <>
                    {error && <div className="agenda__error">{error}</div>}
                    {isLoading ? (
                        <p className="quotes-list__tasks-loading">Chargement...</p>
                    ) : summary && (
                        <>
                            <div className="agenda-planning-item-popover__section">
                                <h4 className="agenda-planning-item-popover__section-title">Commentaire de réception</h4>
                                {summary.reception_comment ? (
                                    <p className="agenda-planning-item-popover__text">{summary.reception_comment}</p>
                                ) : (
                                    <p className="quotes-list__tasks-empty">Aucun commentaire.</p>
                                )}
                            </div>

                            <div className="agenda-planning-item-popover__section">
                                <h4 className="agenda-planning-item-popover__section-title">Travaux à faire</h4>
                                {summary.tasks.length === 0 ? (
                                    <p className="quotes-list__tasks-empty">Aucun travail renseigné.</p>
                                ) : (
                                    <ul className="quotes-list__tasks-list">
                                        {summary.tasks.map(task => (
                                            <li key={task.id}>
                                                {task.title}
                                                {task.quantity > 1 && ` (x${task.quantity})`}
                                            </li>
                                        ))}
                                    </ul>
                                )}
                            </div>
                        </>
                    )}
                </>
            )}
        </div>
    );
}
