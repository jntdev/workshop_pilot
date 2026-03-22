interface ConvertModalProps {
    isOpen: boolean;
    onClose: () => void;
    onConfirm: () => void;
    isLoading?: boolean;
}

export default function ConvertModal({ isOpen, onClose, onConfirm, isLoading }: ConvertModalProps) {
    if (!isOpen) return null;

    return (
        <div className="quote-modal" style={{ display: 'flex' }}>
            <div className="quote-modal__overlay" onClick={onClose}></div>
            <div className="quote-modal__content">
                <h3 className="quote-modal__title">Transformer en facture</h3>
                <div className="quote-modal__body">
                    <p className="quote-modal__text">
                        Vous êtes sur le point de <strong>transformer ce devis en facture</strong>.
                    </p>
                    <p className="quote-modal__warning">
                        Cette action est <strong>irréversible</strong> : la facture sera verrouillée et ne pourra plus être modifiée.
                    </p>
                    <p className="quote-modal__text">
                        Voulez-vous vraiment continuer ?
                    </p>
                </div>
                <div className="quote-modal__actions">
                    <button
                        type="button"
                        onClick={onClose}
                        className="quote-modal__btn quote-modal__btn--secondary"
                        disabled={isLoading}
                    >
                        Annuler
                    </button>
                    <button
                        type="button"
                        onClick={onConfirm}
                        className="quote-modal__btn quote-modal__btn--danger"
                        disabled={isLoading}
                    >
                        {isLoading ? 'Conversion...' : 'Oui, transformer en facture'}
                    </button>
                </div>
            </div>
        </div>
    );
}
