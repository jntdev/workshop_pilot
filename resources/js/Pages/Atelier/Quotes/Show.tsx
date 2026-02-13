import { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import { QuoteShowPageProps } from '@/types';

function formatCurrency(value: string | number): string {
    const num = typeof value === 'string' ? parseFloat(value) : value;
    return new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(num) + ' €';
}

function formatDate(dateString: string): string {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR');
}

function formatTime(minutes: number | null): string {
    if (minutes === null || minutes === undefined) return '-';
    const hours = minutes / 60;
    return hours.toFixed(2) + ' h';
}

export default function QuoteShow({ quote }: QuoteShowPageProps) {
    const title = `${quote.is_invoice ? 'Facture' : 'Devis'} ${quote.reference}`;
    const [showEmailModal, setShowEmailModal] = useState(false);
    const [emailTo, setEmailTo] = useState(quote.client.email || '');
    const [isSending, setIsSending] = useState(false);
    const [emailSent, setEmailSent] = useState(false);
    const [emailError, setEmailError] = useState('');

    const handleSendEmail = async () => {
        if (!emailTo) {
            setEmailError('Veuillez saisir une adresse email');
            return;
        }

        setIsSending(true);
        setEmailError('');

        try {
            const response = await fetch(`/api/quotes/${quote.id}/send-email`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({ email: emailTo }),
            });

            if (response.ok) {
                setEmailSent(true);
                setTimeout(() => {
                    setShowEmailModal(false);
                    setEmailSent(false);
                }, 2000);
            } else {
                const data = await response.json();
                setEmailError(data.message || 'Erreur lors de l\'envoi');
            }
        } catch (error) {
            setEmailError('Erreur de connexion');
        } finally {
            setIsSending(false);
        }
    };

    return (
        <MainLayout>
            <Head title={title} />

            <div className="page-header">
                <div className="breadcrumb">
                    <Link href="/atelier">Atelier</Link>
                    <span>&gt;</span>
                    <Link href="/atelier">Devis</Link>
                    <span>&gt;</span>
                    <span>{quote.reference}</span>
                </div>
                <h1>{quote.is_invoice ? 'Facture' : 'Devis'} {quote.reference}</h1>
            </div>

            <div className="quote-show">
                <section className="quote-show__section">
                    <h2 className="quote-show__section-title">Informations client</h2>
                    <div className="quote-show__info">
                        <div className="quote-show__info-row">
                            <span className="quote-show__label">Nom</span>
                            <span className="quote-show__value">
                                {quote.client.prenom} {quote.client.nom}
                            </span>
                        </div>
                        {quote.client.email && (
                            <div className="quote-show__info-row">
                                <span className="quote-show__label">Email</span>
                                <span className="quote-show__value">{quote.client.email}</span>
                            </div>
                        )}
                        {quote.client.telephone && (
                            <div className="quote-show__info-row">
                                <span className="quote-show__label">Téléphone</span>
                                <span className="quote-show__value">{quote.client.telephone}</span>
                            </div>
                        )}
                        {quote.client.adresse && (
                            <div className="quote-show__info-row">
                                <span className="quote-show__label">Adresse</span>
                                <span className="quote-show__value">{quote.client.adresse}</span>
                            </div>
                        )}
                    </div>
                </section>

                {(quote.bike_description || quote.reception_comment) && (
                    <section className="quote-show__section">
                        <h2 className="quote-show__section-title">Identification du vélo</h2>
                        <div className="quote-show__info">
                            {quote.bike_description && (
                                <div className="quote-show__info-row">
                                    <span className="quote-show__label">Description</span>
                                    <span className="quote-show__value">{quote.bike_description}</span>
                                </div>
                            )}
                            {quote.reception_comment && (
                                <div className="quote-show__info-row">
                                    <span className="quote-show__label">Motif</span>
                                    <span className="quote-show__value">{quote.reception_comment}</span>
                                </div>
                            )}
                        </div>
                    </section>
                )}

                <section className="quote-show__section">
                    <h2 className="quote-show__section-title">Prestations</h2>
                    <div className="quote-show__lines">
                        <table className="quote-show__table">
                            <thead>
                                <tr>
                                    <th>Intitulé</th>
                                    <th>Réf.</th>
                                    <th>PA HT</th>
                                    <th>TVA %</th>
                                    <th>PV TTC</th>
                                    <th>Qté</th>
                                    <th>Total PA</th>
                                    <th>Marge €</th>
                                    <th>Marge %</th>
                                    <th>Total HT</th>
                                    <th>Total TTC</th>
                                </tr>
                            </thead>
                            <tbody>
                                {quote.lines.map((line) => (
                                    <tr key={line.id}>
                                        <td>{line.title}</td>
                                        <td>{line.reference || '-'}</td>
                                        <td>{formatCurrency(line.purchase_price_ht)}</td>
                                        <td>{parseFloat(line.tva_rate).toFixed(0)} %</td>
                                        <td>{formatCurrency(line.sale_price_ttc)}</td>
                                        <td>{Math.round(parseFloat(line.quantity))}</td>
                                        <td>{line.line_purchase_ht ? formatCurrency(line.line_purchase_ht) : '-'}</td>
                                        <td>{line.line_margin_ht ? formatCurrency(line.line_margin_ht) : '-'}</td>
                                        <td>
                                            {line.line_total_ht && line.line_margin_ht && parseFloat(line.line_total_ht) > 0
                                                ? ((parseFloat(line.line_margin_ht) / parseFloat(line.line_total_ht)) * 100).toFixed(1)
                                                : '-'} %
                                        </td>
                                        <td>{line.line_total_ht ? formatCurrency(line.line_total_ht) : '-'}</td>
                                        <td>{line.line_total_ttc ? formatCurrency(line.line_total_ttc) : '-'}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </section>

                <section className="quote-show__section">
                    <h2 className="quote-show__section-title">Résumé</h2>
                    <div className="quote-show__summary-row">
                        <div className="quote-show__remarks">
                            <h3 className="quote-show__subsection-title">Remarques</h3>
                            {quote.remarks ? (
                                <div className="quote-show__remarks-content">
                                    {quote.remarks}
                                </div>
                            ) : (
                                <div className="quote-show__remarks-empty">
                                    Aucune remarque
                                </div>
                            )}
                        </div>
                        <div className="quote-show__totals-wrapper">
                            <div className="quote-show__totals">
                                <div className="quote-show__totals-row">
                                    <span className="quote-show__label">Total HT</span>
                                    <span className="quote-show__value">{formatCurrency(quote.total_ht)}</span>
                                </div>
                                <div className="quote-show__totals-row">
                                    <span className="quote-show__label">TVA</span>
                                    <span className="quote-show__value">{formatCurrency(quote.total_tva)}</span>
                                </div>
                                <div className="quote-show__totals-row quote-show__totals-row--total">
                                    <span className="quote-show__label">Total TTC</span>
                                    <span className="quote-show__value">{formatCurrency(quote.total_ttc)}</span>
                                </div>
                                <div className="quote-show__totals-row">
                                    <span className="quote-show__label">Marge totale</span>
                                    <span className="quote-show__value">{formatCurrency(quote.margin_total_ht)}</span>
                                </div>
                            </div>

                            <div className="quote-show__time-section">
                                <h3 className="quote-show__subsection-title">Temps (interne)</h3>
                                <div className="quote-show__info-row">
                                    <span className="quote-show__label">Temps estimé total</span>
                                    <span className="quote-show__value">{formatTime(quote.total_estimated_time_minutes)}</span>
                                </div>
                                <div className="quote-show__info-row">
                                    <span className="quote-show__label">Temps réel</span>
                                    <span className="quote-show__value">{formatTime(quote.actual_time_minutes)}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="quote-show__meta">
                        <div className="quote-show__info-row">
                            <span className="quote-show__label">Type</span>
                            <span className="quote-show__value">
                                {quote.is_invoice ? (
                                    <span className="badge badge--invoice">Facture</span>
                                ) : (
                                    <span className="badge badge--quote">Devis</span>
                                )}
                            </span>
                        </div>
                        <div className="quote-show__info-row">
                            <span className="quote-show__label">Date de validité</span>
                            <span className="quote-show__value">{formatDate(quote.valid_until)}</span>
                        </div>
                        {quote.is_invoice && quote.invoiced_at && (
                            <div className="quote-show__info-row">
                                <span className="quote-show__label">Date de facturation</span>
                                <span className="quote-show__value">{formatDate(quote.invoiced_at)}</span>
                            </div>
                        )}
                    </div>
                </section>

                <div className="quote-show__actions">
                    <Link href="/atelier" className="quote-show__btn quote-show__btn--secondary">
                        Retour
                    </Link>
                    <a
                        href={'/atelier/devis/' + quote.id + '/pdf'}
                        className="quote-show__btn quote-show__btn--secondary"
                    >
                        Télécharger PDF
                    </a>
                    <button
                        type="button"
                        onClick={() => setShowEmailModal(true)}
                        className="quote-show__btn quote-show__btn--secondary"
                    >
                        Envoyer par email
                    </button>
                    {quote.can_edit && (
                        <Link
                            href={'/atelier/devis/' + quote.id + '/modifier'}
                            className="quote-show__btn quote-show__btn--primary"
                        >
                            Modifier
                        </Link>
                    )}
                </div>

                {showEmailModal && (
                    <div className="modal-overlay" onClick={() => !isSending && setShowEmailModal(false)}>
                        <div className="modal" onClick={(e) => e.stopPropagation()}>
                            <div className="modal__header">
                                <h3 className="modal__title">
                                    Envoyer {quote.is_invoice ? 'la facture' : 'le devis'} par email
                                </h3>
                                <button
                                    type="button"
                                    className="modal__close"
                                    onClick={() => !isSending && setShowEmailModal(false)}
                                >
                                    ×
                                </button>
                            </div>
                            <div className="modal__body">
                                {emailSent ? (
                                    <div className="modal__success">
                                        Email envoyé avec succès !
                                    </div>
                                ) : (
                                    <>
                                        <div className="modal__field">
                                            <label htmlFor="email-to" className="modal__label">
                                                Adresse email du destinataire
                                            </label>
                                            <input
                                                id="email-to"
                                                type="email"
                                                value={emailTo}
                                                onChange={(e) => setEmailTo(e.target.value)}
                                                className="modal__input"
                                                placeholder="email@exemple.com"
                                                disabled={isSending}
                                            />
                                        </div>
                                        {emailError && (
                                            <div className="modal__error">{emailError}</div>
                                        )}
                                    </>
                                )}
                            </div>
                            {!emailSent && (
                                <div className="modal__footer">
                                    <button
                                        type="button"
                                        className="quote-show__btn quote-show__btn--secondary"
                                        onClick={() => setShowEmailModal(false)}
                                        disabled={isSending}
                                    >
                                        Annuler
                                    </button>
                                    <button
                                        type="button"
                                        className="quote-show__btn quote-show__btn--primary"
                                        onClick={handleSendEmail}
                                        disabled={isSending}
                                    >
                                        {isSending ? 'Envoi...' : 'Envoyer'}
                                    </button>
                                </div>
                            )}
                        </div>
                    </div>
                )}
            </div>
        </MainLayout>
    );
}
