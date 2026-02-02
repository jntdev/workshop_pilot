import { useState, useCallback, useEffect, useRef } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import { ClientSearch, QuoteLinesTable, QuoteTotals, ConvertModal } from '@/Components/Atelier/QuoteForm';
import { QuoteFormPageProps, QuoteLine, QuoteTotals as QuoteTotalsType, Client } from '@/types';

const getCsrfToken = (): string => {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
};

const apiHeaders = (): HeadersInit => ({
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'X-XSRF-TOKEN': getCsrfToken(),
});

interface ClientFormData {
    id: number | null;
    prenom: string;
    nom: string;
    email: string;
    telephone: string;
    adresse: string;
}

const emptyLine = (): QuoteLine => ({
    title: '',
    reference: null,
    quantity: '',
    purchase_price_ht: '',
    sale_price_ht: '',
    sale_price_ttc: '',
    margin_amount_ht: '',
    margin_rate: '',
    tva_rate: '20',
    line_purchase_ht: '',
    line_margin_ht: '',
    line_total_ht: '',
    line_total_ttc: '',
    position: 0,
});

const emptyTotals = (): QuoteTotalsType => ({
    total_ht: '0.00',
    total_tva: '0.00',
    total_ttc: '0.00',
    margin_total_ht: '0.00',
});

export default function QuoteForm({ quote }: QuoteFormPageProps) {
    const isEdit = !!quote;
    const isInvoice = quote?.is_invoice ?? false;
    const isReadOnly = isInvoice;

    const [client, setClient] = useState<ClientFormData>({
        id: quote?.client_id ?? null,
        prenom: quote?.client?.prenom ?? '',
        nom: quote?.client?.nom ?? '',
        email: quote?.client?.email ?? '',
        telephone: quote?.client?.telephone ?? '',
        adresse: quote?.client?.adresse ?? '',
    });

    const [bikeDescription, setBikeDescription] = useState(quote?.bike_description ?? '');
    const [receptionComment, setReceptionComment] = useState(quote?.reception_comment ?? '');
    const [validUntil, setValidUntil] = useState(
        quote?.valid_until ?? new Date(Date.now() + 15 * 24 * 60 * 60 * 1000).toISOString().split('T')[0]
    );
    const [discountType, setDiscountType] = useState<'amount' | 'percent'>(
        quote?.discount_type ?? 'percent'
    );
    const [discountValue, setDiscountValue] = useState(quote?.discount_value ?? '0');

    const [lines, setLines] = useState<QuoteLine[]>(
        quote?.lines?.length ? quote.lines : [emptyLine()]
    );

    const [totals, setTotals] = useState<QuoteTotalsType>(
        quote ? {
            total_ht: quote.total_ht,
            total_tva: quote.total_tva,
            total_ttc: quote.total_ttc,
            margin_total_ht: quote.margin_total_ht,
        } : emptyTotals()
    );

    const [isSaving, setIsSaving] = useState(false);
    const [isConverting, setIsConverting] = useState(false);
    const [showConvertModal, setShowConvertModal] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [message, setMessage] = useState<string | null>(null);

    // Ref to track if this is the initial render (skip totals calculation on mount)
    const isInitialMount = useRef(true);

    const handleClientSelect = (selectedClient: Client) => {
        setClient({
            id: selectedClient.id,
            prenom: selectedClient.prenom,
            nom: selectedClient.nom,
            email: selectedClient.email ?? '',
            telephone: selectedClient.telephone ?? '',
            adresse: selectedClient.adresse ?? '',
        });
    };

    const handleLineChange = (index: number, field: keyof QuoteLine, value: string) => {
        setLines(prev => {
            const newLines = [...prev];
            newLines[index] = { ...newLines[index], [field]: value };
            return newLines;
        });
    };

    const handleLineCalculate = useCallback(async (
        index: number,
        calculationType: string,
        value: string,
        currentLineValues?: { purchase_price_ht?: string; tva_rate?: string; quantity?: string }
    ) => {
        // Use provided values or fall back to state (for backward compatibility)
        const line = lines[index];
        const purchasePriceHt = currentLineValues?.purchase_price_ht ?? line.purchase_price_ht;
        const tvaRate = currentLineValues?.tva_rate ?? line.tva_rate;
        const quantity = currentLineValues?.quantity ?? line.quantity ?? '1';

        try {
            const response = await fetch('/api/quotes/calculate-line', {
                method: 'POST',
                headers: apiHeaders(),
                credentials: 'same-origin',
                body: JSON.stringify({
                    purchase_price_ht: purchasePriceHt,
                    tva_rate: tvaRate,
                    calculation_type: calculationType,
                    value: value,
                    quantity: quantity,
                }),
            });

            if (response.ok) {
                const result = await response.json();
                setLines(prev => {
                    const newLines = [...prev];
                    newLines[index] = {
                        ...newLines[index],
                        // Also update the input values if they were provided
                        ...(currentLineValues?.purchase_price_ht !== undefined && { purchase_price_ht: currentLineValues.purchase_price_ht }),
                        ...(currentLineValues?.tva_rate !== undefined && { tva_rate: currentLineValues.tva_rate }),
                        ...(currentLineValues?.quantity !== undefined && { quantity: currentLineValues.quantity }),
                        sale_price_ht: result.sale_price_ht,
                        sale_price_ttc: result.sale_price_ttc,
                        margin_amount_ht: result.margin_amount_ht,
                        margin_rate: result.margin_rate,
                        line_purchase_ht: result.line_purchase_ht,
                        line_margin_ht: result.line_margin_ht,
                        line_total_ht: result.line_total_ht,
                        line_total_ttc: result.line_total_ttc,
                    };
                    return newLines;
                });

                // Totals will be recalculated automatically via useEffect
            }
        } catch (error) {
            console.error('Calculation error:', error);
        }
    }, [lines]);

    // Auto-recalculate totals when lines or discount changes
    useEffect(() => {
        // Skip initial mount to avoid unnecessary API call
        if (isInitialMount.current) {
            isInitialMount.current = false;
            return;
        }

        const recalculate = async () => {
            try {
                const response = await fetch('/api/quotes/calculate-totals', {
                    method: 'POST',
                    headers: apiHeaders(),
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        lines: lines.map(l => ({
                            sale_price_ht: l.sale_price_ht,
                            sale_price_ttc: l.sale_price_ttc,
                            margin_amount_ht: l.margin_amount_ht,
                            line_total_ht: l.line_total_ht,
                            line_total_ttc: l.line_total_ttc,
                            line_margin_ht: l.line_margin_ht,
                        })),
                        discount_type: discountType,
                        discount_value: discountValue,
                    }),
                });

                if (response.ok) {
                    const result = await response.json();
                    setTotals(result);
                }
            } catch (error) {
                console.error('Totals calculation error:', error);
            }
        };

        // Debounce the calculation
        const timeoutId = setTimeout(recalculate, 100);
        return () => clearTimeout(timeoutId);
    }, [lines, discountType, discountValue]);

    const handleAddLine = () => {
        setLines(prev => [...prev, { ...emptyLine(), position: prev.length }]);
    };

    const handleRemoveLine = (index: number) => {
        if (lines.length === 1) return;
        setLines(prev => prev.filter((_, i) => i !== index));
    };

    const handleDiscountChange = (type: 'amount' | 'percent') => {
        setDiscountType(type);
    };

    const handleDiscountValueChange = (value: string) => {
        setDiscountValue(value);
    };

    const handleSave = async (stayOnPage: boolean = false) => {
        setIsSaving(true);
        setErrors({});
        setMessage(null);

        const payload = {
            client_id: client.id,
            client_prenom: client.prenom,
            client_nom: client.nom,
            client_email: client.email || null,
            client_telephone: client.telephone || null,
            client_adresse: client.adresse || null,
            bike_description: bikeDescription,
            reception_comment: receptionComment,
            valid_until: validUntil,
            discount_type: discountType,
            discount_value: discountValue || null,
            lines: lines.map((l, i) => ({ ...l, position: i })),
            totals: totals,
        };

        try {
            const url = isEdit ? '/api/quotes/' + quote.id : '/api/quotes';
            const method = isEdit ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method,
                headers: apiHeaders(),
                credentials: 'same-origin',
                body: JSON.stringify(payload),
            });

            if (response.ok) {
                const result = await response.json();
                setMessage('Devis enregistré avec succès.');

                if (!stayOnPage) {
                    router.visit('/atelier/devis/' + result.id);
                } else if (!isEdit) {
                    router.visit('/atelier/devis/' + result.id + '/modifier');
                }
            } else {
                const errorData = await response.json();
                if (errorData.errors) {
                    setErrors(errorData.errors);
                } else {
                    setMessage(errorData.message || 'Une erreur est survenue.');
                }
            }
        } catch (error) {
            console.error('Save error:', error);
            setMessage('Une erreur est survenue lors de la sauvegarde.');
        } finally {
            setIsSaving(false);
        }
    };

    const handleConvertToInvoice = async () => {
        if (!quote) return;

        setIsConverting(true);
        try {
            const response = await fetch('/api/quotes/' + quote.id + '/convert-to-invoice', {
                method: 'POST',
                headers: apiHeaders(),
                credentials: 'same-origin',
            });

            if (response.ok) {
                setShowConvertModal(false);
                router.visit('/atelier/devis/' + quote.id);
            } else {
                const errorData = await response.json();
                setMessage(errorData.message || 'Une erreur est survenue.');
            }
        } catch (error) {
            console.error('Convert error:', error);
            setMessage('Une erreur est survenue lors de la conversion.');
        } finally {
            setIsConverting(false);
        }
    };

    return (
        <MainLayout>
            <Head title={isEdit ? 'Modifier le devis' : 'Nouveau devis'} />

            <div className="page-header">
                <div className="breadcrumb">
                    <Link href="/atelier">Atelier</Link>
                    <span>&gt;</span>
                    <Link href="/atelier">Devis</Link>
                    <span>&gt;</span>
                    <span>{isEdit ? quote.reference : 'Nouveau'}</span>
                </div>
                <h1>{isEdit ? 'Modifier le devis' : 'Nouveau devis'}</h1>
            </div>

            <div className="quote-form">
                {isInvoice && (
                    <div className="quote-form__header">
                        <div className="quote-form__status-section">
                            <span className="quote-form__status-badge quote-form__status-badge--facturé">
                                Facture
                            </span>
                            <div className="quote-form__info-banner quote-form__info-banner--info">
                                Cette facture est en lecture seule et ne peut plus être modifiée.
                            </div>
                        </div>
                    </div>
                )}

                {message && (
                    <div className={`quote-form__alert ${message.includes('erreur') ? 'quote-form__alert--error' : 'quote-form__alert--success'}`}>
                        {message}
                    </div>
                )}

                <form onSubmit={(e) => { e.preventDefault(); handleSave(); }}>
                    {/* Section Client */}
                    <section className="quote-form__section">
                        <h2 className="quote-form__section-title">Informations client</h2>

                        {!isReadOnly && (
                            <div className="quote-form__tab-content">
                                <ClientSearch onClientSelect={handleClientSelect} disabled={isReadOnly} />
                            </div>
                        )}

                        <div className="quote-form__client-fields">
                            {client.id && (
                                <div className="quote-form__client-badge">
                                    Client sélectionné : {client.prenom} {client.nom}
                                </div>
                            )}

                            <div className="quote-form__grid">
                                <div className="quote-form__field">
                                    <label className="quote-form__label">Prénom *</label>
                                    <input
                                        type="text"
                                        value={client.prenom}
                                        onChange={(e) => setClient(prev => ({ ...prev, prenom: e.target.value }))}
                                        className="quote-form__input"
                                        required
                                        readOnly={isReadOnly}
                                    />
                                </div>
                                <div className="quote-form__field">
                                    <label className="quote-form__label">Nom *</label>
                                    <input
                                        type="text"
                                        value={client.nom}
                                        onChange={(e) => setClient(prev => ({ ...prev, nom: e.target.value }))}
                                        className="quote-form__input"
                                        required
                                        readOnly={isReadOnly}
                                    />
                                </div>
                                <div className="quote-form__field">
                                    <label className="quote-form__label">Email</label>
                                    <input
                                        type="email"
                                        value={client.email}
                                        onChange={(e) => setClient(prev => ({ ...prev, email: e.target.value }))}
                                        className="quote-form__input"
                                        readOnly={isReadOnly}
                                    />
                                </div>
                                <div className="quote-form__field">
                                    <label className="quote-form__label">Téléphone</label>
                                    <input
                                        type="tel"
                                        value={client.telephone}
                                        onChange={(e) => setClient(prev => ({ ...prev, telephone: e.target.value }))}
                                        className="quote-form__input"
                                        readOnly={isReadOnly}
                                    />
                                </div>
                                <div className="quote-form__field quote-form__field--full">
                                    <label className="quote-form__label">Adresse</label>
                                    <input
                                        type="text"
                                        value={client.adresse}
                                        onChange={(e) => setClient(prev => ({ ...prev, adresse: e.target.value }))}
                                        className="quote-form__input"
                                        readOnly={isReadOnly}
                                    />
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Section Vélo */}
                    <section className="quote-form__section">
                        <h2 className="quote-form__section-title">Identification du vélo</h2>
                        <div className="quote-form__grid">
                            <div className="quote-form__field quote-form__field--full">
                                <label className="quote-form__label">Description du vélo *</label>
                                <input
                                    type="text"
                                    value={bikeDescription}
                                    onChange={(e) => setBikeDescription(e.target.value)}
                                    className="quote-form__input"
                                    placeholder="Ex: Nakamura vert, VTT bleu avec roue blanche..."
                                    required
                                    readOnly={isReadOnly}
                                />
                            </div>
                            <div className="quote-form__field quote-form__field--full">
                                <label className="quote-form__label">Commentaire de réception *</label>
                                <textarea
                                    value={receptionComment}
                                    onChange={(e) => setReceptionComment(e.target.value)}
                                    className="quote-form__input"
                                    rows={4}
                                    placeholder="Ex: Devis révision, le client vient parce que..."
                                    required
                                    readOnly={isReadOnly}
                                />
                            </div>
                        </div>
                    </section>

                    {/* Section Prestations */}
                    <section className="quote-form__section">
                        <h2 className="quote-form__section-title">Prestations</h2>
                        <QuoteLinesTable
                            lines={lines}
                            onLineChange={handleLineChange}
                            onLineCalculate={handleLineCalculate}
                            onAddLine={handleAddLine}
                            onRemoveLine={handleRemoveLine}
                            disabled={isReadOnly}
                        />
                    </section>

                    {/* Section Totaux */}
                    <section className="quote-form__section">
                        <h2 className="quote-form__section-title">Résumé</h2>
                        <QuoteTotals
                            totals={totals}
                            discountType={discountType}
                            discountValue={discountValue}
                            validUntil={validUntil}
                            onDiscountTypeChange={handleDiscountChange}
                            onDiscountValueChange={handleDiscountValueChange}
                            onValidUntilChange={setValidUntil}
                            disabled={isReadOnly}
                        />
                    </section>

                    {/* Actions */}
                    <div className="quote-form__actions">
                        <Link href="/atelier" className="quote-form__btn quote-form__btn--secondary">
                            Retour
                        </Link>
                        {!isReadOnly && (
                            <>
                                {isEdit && (
                                    <a
                                        href={'/atelier/devis/' + quote.id + '/pdf'}
                                        className="quote-form__btn quote-form__btn--secondary"
                                    >
                                        Télécharger PDF
                                    </a>
                                )}
                                <button
                                    type="button"
                                    onClick={() => handleSave(true)}
                                    className="quote-form__btn quote-form__btn--secondary"
                                    disabled={isSaving}
                                >
                                    {isSaving ? 'Enregistrement...' : 'Enregistrer et continuer'}
                                </button>
                                <button
                                    type="submit"
                                    className="quote-form__btn quote-form__btn--primary"
                                    disabled={isSaving}
                                >
                                    {isSaving ? 'Enregistrement...' : 'Enregistrer le devis'}
                                </button>
                                {isEdit && (
                                    <button
                                        type="button"
                                        onClick={() => setShowConvertModal(true)}
                                        className="quote-form__btn quote-form__btn--warning"
                                    >
                                        Transformer en facture
                                    </button>
                                )}
                            </>
                        )}
                    </div>
                </form>

                <ConvertModal
                    isOpen={showConvertModal}
                    onClose={() => setShowConvertModal(false)}
                    onConfirm={handleConvertToInvoice}
                    isLoading={isConverting}
                />
            </div>
        </MainLayout>
    );
}
