interface AgendaDriftBannerProps {
    isVisible: boolean;
    onDismiss?: () => void;
}

/**
 * Banner displayed when agenda version drift is detected (another tab modified data).
 */
export default function AgendaDriftBanner({ isVisible, onDismiss }: AgendaDriftBannerProps) {
    if (!isVisible) return null;

    return (
        <div className="agenda-drift-banner">
            <div className="agenda-drift-banner__content">
                <span className="agenda-drift-banner__icon">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        strokeWidth="2"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                    >
                        <path d="M21 12a9 9 0 1 1-9-9c2.52 0 4.85.83 6.72 2.24" />
                        <path d="M21 3v9h-9" />
                    </svg>
                </span>
                <span className="agenda-drift-banner__text">
                    L'agenda a ete modifie dans un autre onglet. Synchronisation en cours...
                </span>
            </div>
            {onDismiss && (
                <button className="agenda-drift-banner__dismiss" onClick={onDismiss}>
                    &times;
                </button>
            )}
        </div>
    );
}
