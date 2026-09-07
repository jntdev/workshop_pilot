import { useEffect, useRef, useState } from 'react';
import { BrowserMultiFormatReader } from '@zxing/library';

interface Props {
    onDetected: (code: string) => void;
    isPaused?: boolean;
}

export default function BarcodeScanner({ onDetected, isPaused = false }: Props) {
    const videoRef = useRef<HTMLVideoElement>(null);
    const readerRef = useRef<BrowserMultiFormatReader | null>(null);
    const onDetectedRef = useRef(onDetected);
    const isPausedRef = useRef(isPaused);
    const [error, setError] = useState<string | null>(null);

    onDetectedRef.current = onDetected;
    isPausedRef.current = isPaused;

    useEffect(() => {
        const reader = new BrowserMultiFormatReader();
        readerRef.current = reader;
        let isCancelled = false;

        reader
            .decodeFromConstraints(
                { video: { facingMode: 'environment' } },
                videoRef.current!,
                (result, err) => {
                    if (isCancelled || isPausedRef.current || !result) {
                        return;
                    }
                    onDetectedRef.current(result.getText());
                },
            )
            .catch(() => {
                if (!isCancelled) {
                    setError('Impossible d\'accéder à la caméra. Vérifiez les permissions ou utilisez une connexion sécurisée (HTTPS).');
                }
            });

        return () => {
            isCancelled = true;
            reader.reset();
        };
    }, []);

    return (
        <div className="barcode-scanner">
            {error ? (
                <div className="barcode-scanner__error">{error}</div>
            ) : (
                <video ref={videoRef} className="barcode-scanner__video" muted playsInline />
            )}
        </div>
    );
}
