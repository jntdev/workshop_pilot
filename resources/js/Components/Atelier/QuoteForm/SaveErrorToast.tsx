import type { SaveErrorFeedback } from '@/utils/quoteSaveErrors';

interface SaveErrorToastProps {
    feedback: SaveErrorFeedback;
    onClose: () => void;
    onShowLine: (lineKey: string) => void;
}

/**
 * Rappel affiché juste au-dessus des boutons d'enregistrement quand le devis n'a pas pu
 * être enregistré. Il reste visible en bas de l'écran pendant qu'on remonte corriger.
 */
export default function SaveErrorToast({ feedback, onClose, onShowLine }: SaveErrorToastProps) {
    return (
        <div className="quote-save-toast" role="alert">
            <span className="quote-save-toast__icon" aria-hidden="true">!</span>
            <div className="quote-save-toast__body">
                <p className="quote-save-toast__title">{feedback.title}</p>
                <ul className="quote-save-toast__list">
                    {feedback.items.map((item, index) => (
                        <li key={index} className="quote-save-toast__item">
                            {item.lineKey ? (
                                <button
                                    type="button"
                                    className="quote-save-toast__link"
                                    onClick={() => onShowLine(item.lineKey as string)}
                                    title="Aller à la ligne"
                                >
                                    {item.text}
                                </button>
                            ) : (
                                item.text
                            )}
                        </li>
                    ))}
                </ul>
            </div>
            <button type="button" className="quote-save-toast__close" onClick={onClose} aria-label="Fermer">
                ×
            </button>
        </div>
    );
}
