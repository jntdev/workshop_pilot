import { useState, useEffect, useRef, useCallback } from 'react';
import QRCode from 'qrcode';

interface AccessoryKey {
    key: string;
    label: string;
}

const ACCESSORIES: AccessoryKey[] = [
    { key: 'vae', label: 'VAE' },
    { key: 'vtc', label: 'VTC' },
    { key: 'casque', label: 'Casque' },
    { key: 'retroviseur', label: 'Rétroviseur' },
    { key: 'sacoche', label: 'Sacoche / Sac' },
    { key: 'support_tel', label: 'Support téléphone' },
    { key: 'bequille', label: 'Béquille' },
    { key: 'lumiere', label: 'Lumière' },
    { key: 'antivol', label: 'Antivol' },
    { key: 'siege_enfant', label: 'Siège enfant' },
    { key: 'remorque', label: 'Remorque enfant' },
];

const OPERATORS = ['Nicolas', 'Jonathan'];

interface Contract {
    id: number;
    token: string;
    operator_name: string;
    caution_amount: number;
    return_time_text: string;
    accessories: Record<string, number>;
    signed_at: string | null;
    signer_name: string | null;
    expires_at: string;
    is_pending: boolean;
    is_expired: boolean;
    is_signed: boolean;
}

interface ContractPanelProps {
    reservationId: number;
    clientEmail: string | null;
}

const CSRF = () =>
    document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

export default function ContractPanel({ reservationId, clientEmail }: ContractPanelProps) {
    const [contract, setContract] = useState<Contract | null>(null);
    const [loading, setLoading] = useState(true);
    const [step, setStep] = useState<'status' | 'form' | 'qr'>('status');

    const [accessories, setAccessories] = useState<Record<string, number>>(
        Object.fromEntries(ACCESSORIES.map(a => [a.key, 0]))
    );
    const [cautionAmount, setCautionAmount] = useState('');
    const [returnTimeText, setReturnTimeText] = useState('');
    const [operatorName, setOperatorName] = useState(OPERATORS[0]);
    const [emailInput, setEmailInput] = useState(clientEmail ?? '');
    const [generating, setGenerating] = useState(false);

    const [contractUrl, setContractUrl] = useState('');
    const qrCanvasRef = useRef<HTMLCanvasElement>(null);
    const pollRef = useRef<ReturnType<typeof setInterval> | null>(null);

    const fetchStatus = useCallback(async () => {
        try {
            const res = await fetch(`/api/reservations/${reservationId}/contract`, {
                headers: { Accept: 'application/json' },
            });
            const json = await res.json();
            setContract(json.contract);
        } finally {
            setLoading(false);
        }
    }, [reservationId]);

    useEffect(() => {
        fetchStatus();
    }, [fetchStatus]);

    useEffect(() => {
        if (step === 'qr' && contract && !contract.is_signed) {
            pollRef.current = setInterval(async () => {
                await fetchStatus();
            }, 3000);
        }
        return () => {
            if (pollRef.current) clearInterval(pollRef.current);
        };
    }, [step, contract, fetchStatus]);

    useEffect(() => {
        if (contract?.is_signed && pollRef.current) {
            clearInterval(pollRef.current);
        }
    }, [contract?.is_signed]);

    useEffect(() => {
        if (step === 'qr' && contractUrl && qrCanvasRef.current) {
            QRCode.toCanvas(qrCanvasRef.current, contractUrl, {
                width: 260,
                margin: 2,
                color: { dark: '#1a1a1a', light: '#ffffff' },
            });
        }
    }, [step, contractUrl]);

    const handleGenerate = async (e: React.FormEvent) => {
        e.preventDefault();
        setGenerating(true);
        try {
            const res = await fetch(`/api/reservations/${reservationId}/contract`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': CSRF(),
                },
                body: JSON.stringify({
                    accessories,
                    caution_amount: Math.round(parseFloat(cautionAmount || '0') * 100),
                    return_time_text: returnTimeText,
                    operator_name: operatorName,
                    client_email: emailInput || null,
                }),
            });
            if (res.ok) {
                const json = await res.json();
                setContract(json.contract);
                setContractUrl(json.url);
                setStep('qr');
            }
        } finally {
            setGenerating(false);
        }
    };

    const handleAccessoryChange = (key: string, delta: number) => {
        setAccessories(prev => ({
            ...prev,
            [key]: Math.max(0, (prev[key] || 0) + delta),
        }));
    };

    const openForm = () => {
        if (contract) {
            setAccessories(Object.fromEntries(
                ACCESSORIES.map(a => [a.key, contract.accessories[a.key] || 0])
            ));
            setCautionAmount(String(contract.caution_amount / 100));
            setReturnTimeText(contract.return_time_text);
            setOperatorName(contract.operator_name);
        }
        setStep('form');
    };

    if (loading) {
        return <div className="contract-panel__loading">Chargement...</div>;
    }

    // Contrat existant signé
    if (contract?.is_signed && step === 'status') {
        return (
            <div className="contract-panel">
                <div className="contract-panel__signed">
                    <span className="contract-panel__signed-icon">✓</span>
                    <div>
                        <div className="contract-panel__signed-label">Contrat signé</div>
                        <div className="contract-panel__signed-meta">
                            par {contract.signer_name} · {new Date(contract.signed_at!).toLocaleDateString('fr-FR')}
                        </div>
                    </div>
                    <a
                        href={`/api/location/contrat/${contract.token}/pdf`}
                        className="contract-panel__btn contract-panel__btn--ghost"
                        target="_blank"
                        rel="noreferrer"
                    >
                        PDF
                    </a>
                    <button
                        type="button"
                        className="contract-panel__btn contract-panel__btn--ghost"
                        onClick={openForm}
                    >
                        Regénérer
                    </button>
                </div>
            </div>
        );
    }

    // Modale : lecture du contrat (PDF) avec le QR code en bas
    if (step === 'qr' && contract) {
        return (
            <div className="contract-panel__modal-overlay">
                <div className="contract-panel__modal">
                    <div className="contract-panel__modal-header">
                        <span>Lecture du contrat</span>
                        <button
                            type="button"
                            className="contract-panel__modal-close"
                            onClick={() => setStep('status')}
                        >
                            &times;
                        </button>
                    </div>

                    <iframe
                        src={`/api/reservations/${reservationId}/contract/preview#toolbar=0&view=FitH`}
                        className="contract-panel__modal-pdf"
                        title="Aperçu du contrat"
                    />

                    <div className="contract-panel__modal-footer">
                        {contract.is_signed ? (
                            <div className="contract-panel__confirmed">
                                <div className="contract-panel__confirmed-icon">✅</div>
                                <div className="contract-panel__confirmed-title">Contrat signé !</div>
                                <div className="contract-panel__confirmed-meta">
                                    par {contract.signer_name}
                                </div>
                                <a
                                    href={`/api/location/contrat/${contract.token}/pdf`}
                                    className="contract-panel__btn contract-panel__btn--primary"
                                    target="_blank"
                                    rel="noreferrer"
                                >
                                    Télécharger le PDF
                                </a>
                            </div>
                        ) : (
                            <>
                                <p className="contract-panel__qr-hint">
                                    Lisez le contrat ensemble, puis faites scanner ce QR code par votre client pour qu'il signe sur son téléphone.
                                </p>
                                <canvas ref={qrCanvasRef} className="contract-panel__qr-canvas" />
                                <div className="contract-panel__qr-url">{contractUrl}</div>
                                <div className="contract-panel__qr-waiting">
                                    <span className="contract-panel__qr-dot" />
                                    En attente de signature…
                                </div>
                                <button
                                    type="button"
                                    className="contract-panel__btn contract-panel__btn--ghost"
                                    onClick={openForm}
                                >
                                    Modifier / Regénérer
                                </button>
                            </>
                        )}
                    </div>
                </div>
            </div>
        );
    }

    // Formulaire de préparation
    return (
        <div className="contract-panel">
            <div className="contract-panel__header">
                <span className="contract-panel__title">Préparer le contrat</span>
            </div>

            <form onSubmit={handleGenerate} className="contract-panel__form">
                {/* Email si manquant */}
                {!clientEmail && (
                    <div className="contract-panel__field">
                        <label className="contract-panel__label">Email client *</label>
                        <input
                            type="email"
                            className="contract-panel__input"
                            value={emailInput}
                            onChange={e => setEmailInput(e.target.value)}
                            placeholder="email@exemple.com"
                            required
                        />
                    </div>
                )}

                {/* Opérateur */}
                <div className="contract-panel__field">
                    <label className="contract-panel__label">Opérateur</label>
                    <div className="contract-panel__toggle-group">
                        {OPERATORS.map(op => (
                            <button
                                key={op}
                                type="button"
                                className={`contract-panel__toggle ${operatorName === op ? 'contract-panel__toggle--active' : ''}`}
                                onClick={() => setOperatorName(op)}
                            >
                                {op}
                            </button>
                        ))}
                    </div>
                </div>

                {/* Heure limite de retour */}
                <div className="contract-panel__field">
                    <label className="contract-panel__label">Retour avant</label>
                    <input
                        type="text"
                        className="contract-panel__input"
                        value={returnTimeText}
                        onChange={e => setReturnTimeText(e.target.value)}
                        placeholder="Ex : avant 18h, avant fermeture…"
                        required
                    />
                </div>

                {/* Caution */}
                <div className="contract-panel__field">
                    <label className="contract-panel__label">Caution (€)</label>
                    <input
                        type="number"
                        className="contract-panel__input"
                        value={cautionAmount}
                        onChange={e => setCautionAmount(e.target.value)}
                        placeholder="0"
                        min="0"
                        step="1"
                        required
                    />
                </div>

                {/* Accessoires */}
                <div className="contract-panel__field">
                    <label className="contract-panel__label">Accessoires remis</label>
                    <div className="contract-panel__accessories">
                        {ACCESSORIES.map(({ key, label }) => (
                            <div key={key} className="contract-panel__accessory">
                                <span className="contract-panel__accessory-label">{label}</span>
                                <div className="contract-panel__counter">
                                    <button
                                        type="button"
                                        className="contract-panel__counter-btn"
                                        onClick={() => handleAccessoryChange(key, -1)}
                                        disabled={!accessories[key]}
                                    >−</button>
                                    <span className="contract-panel__counter-val">{accessories[key] || 0}</span>
                                    <button
                                        type="button"
                                        className="contract-panel__counter-btn"
                                        onClick={() => handleAccessoryChange(key, 1)}
                                    >+</button>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>

                <button
                    type="submit"
                    className="contract-panel__btn contract-panel__btn--primary"
                    disabled={generating}
                >
                    {generating ? 'Génération…' : 'Générer le contrat'}
                </button>
            </form>
        </div>
    );
}
