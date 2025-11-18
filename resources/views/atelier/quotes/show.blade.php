<x-layouts.main>
    <div class="page-header">
        <div class="breadcrumb">
            <a href="{{ route('atelier.index') }}">Atelier</a>
            <span>&gt;</span>
            <a href="{{ route('atelier.quotes.index') }}">Devis</a>
            <span>&gt;</span>
            <span>{{ $quote->reference }}</span>
        </div>
        <h1>Devis {{ $quote->reference }}</h1>
    </div>

    <div class="quote-show">
        <section class="quote-show__section">
            <h2 class="quote-show__section-title">Informations client</h2>
            <div class="quote-show__info">
                <div class="quote-show__info-row">
                    <span class="quote-show__label">Nom</span>
                    <span class="quote-show__value">{{ $quote->client->prenom }} {{ $quote->client->nom }}</span>
                </div>
                @if($quote->client->email)
                    <div class="quote-show__info-row">
                        <span class="quote-show__label">Email</span>
                        <span class="quote-show__value">{{ $quote->client->email }}</span>
                    </div>
                @endif
                @if($quote->client->telephone)
                    <div class="quote-show__info-row">
                        <span class="quote-show__label">Téléphone</span>
                        <span class="quote-show__value">{{ $quote->client->telephone }}</span>
                    </div>
                @endif
                @if($quote->client->adresse)
                    <div class="quote-show__info-row">
                        <span class="quote-show__label">Adresse</span>
                        <span class="quote-show__value">{{ $quote->client->adresse }}</span>
                    </div>
                @endif
            </div>
        </section>

        <section class="quote-show__section">
            <h2 class="quote-show__section-title">Prestations</h2>
            <div class="quote-show__lines">
                <table class="quote-show__table">
                    <thead>
                        <tr>
                            <th>Intitulé</th>
                            <th>Réf.</th>
                            @if($quote->status->canShowPurchasePrice())
                                <th>PA HT</th>
                            @endif
                            <th>PV HT</th>
                            @if($quote->status->showMargins())
                                <th>Marge €</th>
                                <th>Marge %</th>
                            @endif
                            <th>TVA %</th>
                            <th>PV TTC</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quote->lines as $line)
                            <tr>
                                <td>{{ $line->title }}</td>
                                <td>{{ $line->reference }}</td>
                                @if($quote->status->canShowPurchasePrice())
                                    <td>
                                        @if($line->purchase_price_ht !== null)
                                            {{ number_format((float)$line->purchase_price_ht, 2, ',', ' ') }} €
                                        @else
                                            <span style="color: #ff9800; font-weight: 600;">À définir</span>
                                        @endif
                                    </td>
                                @endif
                                <td>{{ number_format((float)$line->sale_price_ht, 2, ',', ' ') }} €</td>
                                @if($quote->status->showMargins())
                                    <td>
                                        @if($line->margin_amount_ht !== null)
                                            {{ number_format((float)$line->margin_amount_ht, 2, ',', ' ') }} €
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($line->margin_rate !== null)
                                            {{ number_format((float)$line->margin_rate, 2, ',', ' ') }} %
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endif
                                <td>{{ number_format((float)$line->tva_rate, 2, ',', ' ') }} %</td>
                                <td>{{ number_format((float)$line->sale_price_ttc, 2, ',', ' ') }} €</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="quote-show__section">
            <h2 class="quote-show__section-title">Résumé</h2>
            <div class="quote-show__totals">
                <div class="quote-show__totals-row">
                    <span class="quote-show__label">Total HT</span>
                    <span class="quote-show__value">{{ number_format((float)$quote->total_ht, 2, ',', ' ') }} €</span>
                </div>
                <div class="quote-show__totals-row">
                    <span class="quote-show__label">TVA</span>
                    <span class="quote-show__value">{{ number_format((float)$quote->total_tva, 2, ',', ' ') }} €</span>
                </div>
                <div class="quote-show__totals-row quote-show__totals-row--total">
                    <span class="quote-show__label">Total TTC</span>
                    <span class="quote-show__value">{{ number_format((float)$quote->total_ttc, 2, ',', ' ') }} €</span>
                </div>
                @if($quote->status->showMargins())
                    <div class="quote-show__totals-row">
                        <span class="quote-show__label">Marge totale</span>
                        <span class="quote-show__value">{{ number_format((float)$quote->margin_total_ht, 2, ',', ' ') }} €</span>
                    </div>
                @endif
            </div>

            <div class="quote-show__meta">
                <div class="quote-show__info-row">
                    <span class="quote-show__label">Statut</span>
                    <span class="quote-show__value">
                        <span class="badge badge--{{ $quote->status->value }}">{{ $quote->status->label() }}</span>
                    </span>
                </div>
                <div class="quote-show__info-row">
                    <span class="quote-show__label">Date de validité</span>
                    <span class="quote-show__value">{{ $quote->valid_until->format('d/m/Y') }}</span>
                </div>
            </div>
        </section>

        <div class="quote-show__actions">
            <a href="{{ route('atelier.quotes.index') }}" class="quote-show__btn quote-show__btn--secondary">
                Retour
            </a>
            <a href="{{ route('atelier.quotes.edit', $quote) }}" class="quote-show__btn quote-show__btn--primary">
                Modifier
            </a>
        </div>
    </div>
</x-layouts.main>
