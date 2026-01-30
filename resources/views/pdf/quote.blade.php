<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $quote->isInvoice() ? 'Facture' : 'Devis' }} {{ $quote->reference }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            padding-bottom: 100px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            max-height: 80px;
            max-width: 200px;
            margin-bottom: 15px;
        }
        .header h1 {
            margin: 10px 0 5px 0;
            font-size: 24px;
            color: #333;
        }
        .header .reference {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            padding: 5px 0;
        }
        .info-label {
            width: 150px;
            font-weight: bold;
            color: #555;
        }
        .info-value {
            flex: 1;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        table td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        .totals {
            margin-top: 20px;
            float: right;
            width: 300px;
            page-break-inside: avoid;
        }
        .section--summary {
            page-break-inside: avoid;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .totals-row--total {
            font-weight: bold;
            font-size: 14px;
            background-color: #f5f5f5;
            border-top: 2px solid #333;
        }
        .meta {
            clear: both;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge--invoice {
            background-color: #4CAF50;
            color: white;
        }
        .badge--quote {
            background-color: #2196F3;
            color: white;
        }
        .contact-row {
            width: 100%;
            margin-bottom: 30px;
            padding: 15px 0 10px 0;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
        }
        .contact-row:after {
            content: "";
            display: table;
            clear: both;
        }
        .contact-row > div {
            width: 48%;
            font-size: 11px;
            float: left;
            min-height: 100px;
        }
        .contact-row > div:first-child {
            margin-right: 4%;
        }
        .contact-row h3 {
            margin: 0 0 8px 0;
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        .contact-row p {
            margin: 3px 0;
            color: #555;
        }
        .legal-mentions {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 8px 20px;
            border-top: 1px solid #ddd;
            font-size: 8px;
            color: #888;
            text-align: center;
            opacity: 0.7;
            line-height: 1.3;
            background-color: white;
        }
        .legal-mentions p {
            margin: 2px 0;
        }
        .legal-mentions strong {
            color: #666;
        }
        .legal-info-block {
            margin-top: 8px;
            font-size: 7px;
            color: #999;
        }
        .bike-info {
            margin-bottom: 25px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
        }
        .bike-info-header {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .bike-info-header h3 {
            display: table-cell;
            margin: 0;
            font-size: 14px;
            font-weight: bold;
            color: #333;
            vertical-align: middle;
        }
        .bike-info-header .dates {
            display: table-cell;
            text-align: right;
            font-size: 10px;
            color: #555;
            vertical-align: middle;
        }
        .bike-info-header .dates span {
            margin-left: 15px;
        }
        .bike-info p {
            margin: 5px 0;
            font-size: 11px;
            color: #555;
        }
        .bike-info .label {
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="header">
        {{-- Logo temporarily disabled due to memory issues with large PNG file --}}
        {{-- <img src="{{ public_path('images/logo.png') }}" alt="{{ config('company.name') }}"> --}}
        <h1>{{ $quote->isInvoice() ? 'FACTURE' : 'DEVIS' }}</h1>
        <div class="reference">{{ $quote->reference }}</div>
    </div>

    <div class="contact-row">
        <div>
            <h3>{{ config('company.name') }}</h3>
            <p>{{ config('company.address') }}</p>
            <p>{{ config('company.postal_code') }} {{ config('company.city') }}</p>
            <p>Tél : {{ config('company.phone') }}</p>
            <p>Email : {{ config('company.email') }}</p>
        </div>

        <div>
            <h3>{{ $quote->client->prenom }} {{ $quote->client->nom }}</h3>
            @if($quote->client->adresse)
                <p>{{ $quote->client->adresse }}</p>
            @endif
            @if($quote->client->email)
                <p>Email : {{ $quote->client->email }}</p>
            @endif
            @if($quote->client->telephone)
                <p>Tél : {{ $quote->client->telephone }}</p>
            @endif
        </div>
    </div>

    <div class="bike-info">
        <div class="bike-info-header">
            <h3>Vélo concerné</h3>
            <div class="dates">
                @if(!$quote->isInvoice())
                    <span><strong>Émis le :</strong> {{ $quote->created_at->format('d/m/Y') }}</span>
                    <span><strong>Valide jusqu'au :</strong> {{ $quote->valid_until->format('d/m/Y') }}</span>
                @else
                    <span><strong>Facturé le :</strong> {{ $quote->invoiced_at->format('d/m/Y') }}</span>
                    <span><strong>Paiement :</strong> {{ config('company.payment_terms_text') }}</span>
                @endif
            </div>
        </div>
        @if($quote->bike_description)
            <p><span class="label">Description :</span> {{ $quote->bike_description }}</p>
        @endif
        @if($quote->reception_comment)
            <p><span class="label">Motif :</span> {{ $quote->reception_comment }}</p>
        @endif
    </div>

    <div class="section">
        <h2 class="section-title">Prestations</h2>
        <table>
            <thead>
                <tr>
                    <th>Intitulé</th>
                    <th>Qté</th>
                    <th>PV HT</th>
                    <th>TVA %</th>
                    <th>PV TTC</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quote->lines as $line)
                    <tr>
                        <td>{{ $line->title }}</td>
                        <td>{{ number_format((float)$line->quantity, 2, ',', ' ') }}</td>
                        <td>{{ number_format((float)$line->sale_price_ht, 2, ',', ' ') }} €</td>
                        <td>{{ number_format((float)$line->tva_rate, 0, ',', ' ') }} %</td>
                        <td>{{ number_format((float)$line->sale_price_ttc, 2, ',', ' ') }} €</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section section--summary">
        <h2 class="section-title">Résumé</h2>
        <div class="totals">
            <div class="totals-row">
                <span>Total HT</span>
                <span>{{ number_format((float)$quote->total_ht, 2, ',', ' ') }} €</span>
            </div>
            <div class="totals-row">
                <span>TVA</span>
                <span>{{ number_format((float)$quote->total_tva, 2, ',', ' ') }} €</span>
            </div>
            <div class="totals-row totals-row--total">
                <span>Total TTC</span>
                <span>{{ number_format((float)$quote->total_ttc, 2, ',', ' ') }} €</span>
            </div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="legal-mentions">
        @if($quote->isInvoice())
            <p><strong>Conditions de règlement :</strong> {{ config('company.payment_terms_text') }}. Date d'échéance : {{ $quote->invoiced_at->addDays((int)config('company.payment_terms'))->format('d/m/Y') }}</p>
            <p>En cas de retard de paiement, seront exigibles, conformément à l'article L. 441-10 du code de commerce :</p>
            <p>Une indemnité calculée sur la base de {{ config('company.late_payment_penalty_rate') }}% par an - Indemnité forfaitaire pour frais de recouvrement de {{ config('company.late_payment_fixed_compensation') }} €</p>
        @else
            <p><strong>Validité :</strong> Ce devis est valable jusqu'au {{ $quote->valid_until->format('d/m/Y') }}.</p>
            <p>Les prix indiqués sont exprimés en euros et s'entendent TTC (Toutes Taxes Comprises).</p>
            <p>Toute commande implique l'acceptation sans réserve par l'acheteur et son adhésion pleine et entière aux présentes conditions générales de vente.</p>
        @endif

        <div class="legal-info-block">
            <p><strong>{{ config('company.legal_name') }}</strong> - {{ config('company.address') }}, {{ config('company.postal_code') }} {{ config('company.city') }}</p>
            <p>SIRET : {{ config('company.siret') }} - N° TVA : {{ config('company.tva_number') }} - {{ config('company.rcs') }} - Capital social : {{ config('company.capital') }} €</p>
        </div>
    </div>
</body>
</html>
