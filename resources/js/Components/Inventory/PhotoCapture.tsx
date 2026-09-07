import { useEffect, useRef, useState } from 'react';

interface Props {
    onCaptured: (file: File) => void;
    onCancel: () => void;
}

export default function PhotoCapture({ onCaptured, onCancel }: Props) {
    const videoRef = useRef<HTMLVideoElement>(null);
    const streamRef = useRef<MediaStream | null>(null);
    const [error, setError] = useState<string | null>(null);
    const [previewUrl, setPreviewUrl] = useState<string | null>(null);
    const [capturedFile, setCapturedFile] = useState<File | null>(null);

    useEffect(() => {
        let isCancelled = false;

        navigator.mediaDevices
            .getUserMedia({ video: { facingMode: 'environment' } })
            .then(stream => {
                if (isCancelled) {
                    stream.getTracks().forEach(track => track.stop());
                    return;
                }
                streamRef.current = stream;
                if (videoRef.current) {
                    videoRef.current.srcObject = stream;
                }
            })
            .catch(() => {
                if (!isCancelled) {
                    setError('Impossible d\'accéder à la caméra. Vérifiez les permissions ou utilisez une connexion sécurisée (HTTPS).');
                }
            });

        return () => {
            isCancelled = true;
            streamRef.current?.getTracks().forEach(track => track.stop());
            streamRef.current = null;
        };
    }, []);

    const stopStream = () => {
        streamRef.current?.getTracks().forEach(track => track.stop());
        streamRef.current = null;
    };

    const capture = () => {
        const video = videoRef.current;
        if (!video) { return; }

        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        if (!ctx) { return; }
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        canvas.toBlob(blob => {
            if (!blob) { return; }
            const file = new File([blob], `photo-${Date.now()}.jpg`, { type: 'image/jpeg' });
            setCapturedFile(file);
            setPreviewUrl(URL.createObjectURL(blob));
            stopStream();
        }, 'image/jpeg', 0.9);
    };

    const retake = () => {
        if (previewUrl) { URL.revokeObjectURL(previewUrl); }
        setPreviewUrl(null);
        setCapturedFile(null);
        setError(null);

        navigator.mediaDevices
            .getUserMedia({ video: { facingMode: 'environment' } })
            .then(stream => {
                streamRef.current = stream;
                if (videoRef.current) {
                    videoRef.current.srcObject = stream;
                }
            })
            .catch(() => setError('Impossible d\'accéder à la caméra. Vérifiez les permissions ou utilisez une connexion sécurisée (HTTPS).'));
    };

    const confirm = () => {
        if (!capturedFile) { return; }
        stopStream();
        if (previewUrl) { URL.revokeObjectURL(previewUrl); }
        onCaptured(capturedFile);
    };

    const cancel = () => {
        stopStream();
        if (previewUrl) { URL.revokeObjectURL(previewUrl); }
        onCancel();
    };

    return (
        <div className="photo-capture">
            {error ? (
                <div className="photo-capture__error">{error}</div>
            ) : previewUrl ? (
                <div className="photo-capture__preview-wrap">
                    <img src={previewUrl} alt="Photo capturée" className="photo-capture__preview" />
                </div>
            ) : (
                <video ref={videoRef} className="photo-capture__video" autoPlay muted playsInline />
            )}

            <div className="photo-capture__actions">
                {previewUrl ? (
                    <>
                        <button type="button" className="photo-capture__btn" onClick={retake}>Reprendre</button>
                        <button type="button" className="photo-capture__btn photo-capture__btn--primary" onClick={confirm}>Confirmer</button>
                    </>
                ) : (
                    <>
                        <button type="button" className="photo-capture__btn" onClick={cancel}>Annuler</button>
                        <button type="button" className="photo-capture__btn photo-capture__btn--primary" onClick={capture} disabled={!!error}>Capturer</button>
                    </>
                )}
            </div>
        </div>
    );
}
