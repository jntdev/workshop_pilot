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

export default function QuoteShow({ quote }: QuoteShowPageProps) {
    const title = `${quote.is_invoice ? 'Facture' : 'Devis'} ${quote.reference}`;

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
                                    <th>Qté</th>
                                    <th>PA HT</th>
                                    <th>PV HT</th>
                                    <th>Marge €</th>
                                    <th>Marge %</th>
                                    <th>TVA %</th>
                                    <th>PV TTC</th>
                                    <th>Total HT</th>
                                    <th>Total TTC</th>
                                </tr>
                            </thead>
                            <tbody>
                                {quote.lines.map((line) => (
                                    <tr key={line.id}>
                                        <td>{line.title}</td>
                                        <td>{line.reference || '-'}</td>
                                        <td>{parseFloat(line.quantity).toFixed(2)}</td>
                                        <td>{formatCurrency(line.purchase_price_ht)}</td>
                                        <td>{formatCurrency(line.sale_price_ht)}</td>
                                        <td>{formatCurrency(line.margin_amount_ht)}</td>
                                        <td>{parseFloat(line.margin_rate).toFixed(2)} %</td>
                                        <td>{parseFloat(line.tva_rate).toFixed(0)} %</td>
                                        <td>{formatCurrency(line.sale_price_ttc)}</td>
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
                    {quote.can_edit && (
                        <Link
                            href={'/atelier/devis/' + quote.id + '/modifier'}
                            className="quote-show__btn quote-show__btn--primary"
                        >
                            Modifier
                        </Link>
                    )}
                </div>
            </div>
        </MainLayout>
    );
}
