interface LocationSyncOverlayProps {
    isVisible: boolean;
    message?: string;
}

/**
 * Overlay displayed during agenda synchronization.
 */
export default function LocationSyncOverlay({ isVisible, message }: LocationSyncOverlayProps) {
    if (!isVisible) return null;

    return (
        <div className="location-sync-overlay">
            <div className="location-sync-overlay__content">
                <div className="location-sync-overlay__spinner" />
                <span className="location-sync-overlay__text">
                    {message || 'Synchronisation des données location...'}
                </span>
            </div>
        </div>
    );
}
