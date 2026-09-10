interface FieldConflictBannerProps {
    theirsValue: string | number | null;
    onApply: () => void;
    onDismiss: () => void;
}

export default function FieldConflictBanner({ theirsValue, onApply, onDismiss }: FieldConflictBannerProps) {
    return (
        <div className="field-conflict-banner">
            <span className="field-conflict-banner__text">
                Valeur entrante : <strong>{String(theirsValue ?? '(vide)')}</strong>
            </span>
            <span className="field-conflict-banner__actions">
                <button type="button" className="field-conflict-banner__apply" onClick={onApply}>
                    Appliquer
                </button>
                <button type="button" className="field-conflict-banner__dismiss" onClick={onDismiss}>
                    Ignorer
                </button>
            </span>
        </div>
    );
}
