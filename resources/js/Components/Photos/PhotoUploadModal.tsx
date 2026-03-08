import { useEffect, useRef, useState } from 'react';
import QRCode from 'qrcode';

function getCsrfToken(): string {
    const cookies = document.cookie.split(';');
    for (const cookie of cookies) {
        const [name, value] = cookie.trim().split('=');
        if (name === 'XSRF-TOKEN') {
            return decodeURIComponent(value);
        }
    }
    return '';
}

interface Photo {
    id: number;
    url: string;
    thumb_url: string;
    name: string;
}

interface PhotoUploadModalProps {
    contextType: 'message' | 'message_reply';
    contextId: number | null;
    isOpen: boolean;
    onClose: () => void;
    onPhotosReceived: (photos: Photo[]) => void;
}

export default function PhotoUploadModal({
    contextType,
    contextId,
    isOpen,
    onClose,
    onPhotosReceived,
}: PhotoUploadModalProps) {
    const [token, setToken] = useState<string | null>(null);
    const [uploadUrl, setUploadUrl] = useState<string | null>(null);
    const [qrDataUrl, setQrDataUrl] = useState<string | null>(null);
    const [photos, setPhotos] = useState<Photo[]>([]);
    const [error, setError] = useState<string | null>(null);
    const [loading, setLoading] = useState(false);
    const canvasRef = useRef<HTMLCanvasElement>(null);
    const pollIntervalRef = useRef<ReturnType<typeof setInterval> | null>(null);

    useEffect(() => {
        if (isOpen) {
            generateToken();
        } else {
            cleanup();
        }

        return () => cleanup();
    }, [isOpen]);

    const cleanup = () => {
        if (pollIntervalRef.current) {
            clearInterval(pollIntervalRef.current);
            pollIntervalRef.current = null;
        }
        setToken(null);
        setUploadUrl(null);
        setQrDataUrl(null);
        setPhotos([]);
        setError(null);
    };

    const generateToken = async () => {
        setLoading(true);
        setError(null);

        try {
            const response = await fetch('/api/upload-tokens', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-XSRF-TOKEN': getCsrfToken(),
                },
                credentials: 'include',
                body: JSON.stringify({
                    context_type: contextType,
                    context_id: contextId,
                }),
            });

            if (!response.ok) {
                throw new Error('Erreur lors de la generation du token');
            }

            const data = await response.json();
            setToken(data.token);
            setUploadUrl(data.url);

            // Generate QR code
            const qrUrl = await QRCode.toDataURL(data.url, {
                width: 250,
                margin: 2,
                color: {
                    dark: '#333333',
                    light: '#ffffff',
                },
            });
            setQrDataUrl(qrUrl);

            // Start polling for photos
            startPolling(data.token);
        } catch (err) {
            setError('Impossible de generer le QR code');
            console.error(err);
        } finally {
            setLoading(false);
        }
    };

    const startPolling = (tokenValue: string) => {
        pollIntervalRef.current = setInterval(async () => {
            try {
                const response = await fetch(`/api/upload-tokens/${tokenValue}/photos`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-XSRF-TOKEN': getCsrfToken(),
                    },
                    credentials: 'include',
                });

                if (response.ok) {
                    const data = await response.json();
                    setPhotos(data.photos);

                    if (!data.is_valid) {
                        if (pollIntervalRef.current) {
                            clearInterval(pollIntervalRef.current);
                            pollIntervalRef.current = null;
                        }
                    }
                }
            } catch (err) {
                console.error('Polling error:', err);
            }
        }, 2000);
    };

    const handleDeletePhoto = async (photoId: number) => {
        try {
            const response = await fetch(`/api/photos/${photoId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-XSRF-TOKEN': getCsrfToken(),
                },
                credentials: 'include',
            });

            if (response.ok) {
                setPhotos(photos.filter((p) => p.id !== photoId));
            }
        } catch (err) {
            console.error('Delete error:', err);
        }
    };

    const handleClose = () => {
        onPhotosReceived(photos);
        onClose();
    };

    if (!isOpen) return null;

    return (
        <div className="photo-upload-modal__overlay" onClick={handleClose}>
            <div
                className="photo-upload-modal"
                onClick={(e) => e.stopPropagation()}
            >
                <div className="photo-upload-modal__header">
                    <h2>Ajouter des photos</h2>
                    <button
                        className="photo-upload-modal__close"
                        onClick={handleClose}
                    >
                        &times;
                    </button>
                </div>

                <div className="photo-upload-modal__content">
                    {loading && (
                        <div className="photo-upload-modal__loading">
                            Chargement...
                        </div>
                    )}

                    {error && (
                        <div className="photo-upload-modal__error">{error}</div>
                    )}

                    {qrDataUrl && (
                        <>
                            <div className="photo-upload-modal__qr">
                                <img
                                    src={qrDataUrl}
                                    alt="QR Code"
                                    className="photo-upload-modal__qr-image"
                                />
                            </div>

                            <p className="photo-upload-modal__instructions">
                                Scannez ce QR code avec votre telephone pour
                                envoyer des photos
                            </p>
                        </>
                    )}

                    {photos.length > 0 && (
                        <div className="photo-upload-modal__photos">
                            <p className="photo-upload-modal__count">
                                {photos.length} photo(s) recue(s)
                            </p>
                            <div className="photo-upload-modal__grid">
                                {photos.map((photo) => (
                                    <div
                                        key={photo.id}
                                        className="photo-upload-modal__photo"
                                    >
                                        <img
                                            src={photo.thumb_url}
                                            alt={photo.name}
                                        />
                                        <button
                                            className="photo-upload-modal__photo-delete"
                                            onClick={() =>
                                                handleDeletePhoto(photo.id)
                                            }
                                        >
                                            &times;
                                        </button>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}
                </div>

                <div className="photo-upload-modal__footer">
                    <button
                        className={`photo-upload-modal__done ${photos.length === 0 ? 'photo-upload-modal__done--disabled' : ''}`}
                        onClick={handleClose}
                    >
                        {photos.length > 0
                            ? `Terminer (${photos.length} photos)`
                            : 'Terminer'}
                    </button>
                </div>
            </div>
        </div>
    );
}
