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
            padding: 5px 20px;
            padding-bottom: 150px;
        }
        .header {
            text-align: center;
            margin-bottom: 5px;
        }
        .header img {
            max-height: 80px;
            max-width: 200px;
            margin-bottom: 10px;
        }
        .header h1 {
            margin: 0 0 3px 0;
            font-size: 24px;
            color: #333;
        }
        .header .reference {
            font-size: 14px;
            color: #666;
            margin-top: 3px;
        }
        .section {
            margin-bottom: 15px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
            margin-bottom: 10px;
        }
        .info-row {
            padding: 3px 0;
        }
        .info-label {
            font-weight: bold;
            color: #555;
            display: inline;
        }
        .info-value {
            color: #333;
            display: inline;
            margin-left: 5px;
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
        .summary-row {
            width: 100%;
            margin-top: 15px;
        }
        .summary-row:after {
            content: "";
            display: table;
            clear: both;
        }
        .remarks {
            float: left;
            width: 55%;
        }
        .remarks-title {
            font-size: 12px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }
        .remarks-content {
            background-color: #f8f9fa;
            padding: 10px 12px;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            font-size: 11px;
            line-height: 1.5;
            color: #555;
            white-space: pre-wrap;
        }
        .remarks-empty {
            color: #999;
            font-style: italic;
            font-size: 11px;
        }
        .totals {
            float: right;
            width: 38%;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }
        .totals-table td {
            padding: 8px;
            border: none;
            border-bottom: 1px solid #ddd;
            font-size: 12px;
        }
        .totals-table .totals-label {
            text-align: left;
        }
        .totals-table .totals-value {
            text-align: right;
        }
        .totals-table .totals-row--total td {
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
            margin-bottom: 15px;
            padding: 10px 0 8px 0;
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
            padding: 15px 20px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            color: #888;
            text-align: center;
            opacity: 0.7;
            line-height: 1.5;
            background-color: white;
        }
        .legal-mentions p {
            margin: 4px 0;
        }
        .legal-mentions strong {
            color: #666;
        }
        .legal-info-block {
            margin-top: 15px;
            font-size: 8px;
            color: #999;
        }
        .info-box {
            margin-bottom: 15px;
            padding: 10px 12px;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
        }
        .info-box-row {
            width: 100%;
        }
        .info-box-row:after {
            content: "";
            display: table;
            clear: both;
        }
        .info-box-col {
            float: left;
            font-size: 11px;
        }
        .info-box-col--bike {
            width: 28%;
        }
        .info-box-col--motif {
            width: 32%;
            padding: 0 2%;
        }
        .info-box-col--dates {
            width: 34%;
            text-align: right;
        }
        .info-box-col .info-row {
            padding: 2px 0;
        }
        .info-box-col .info-label {
            font-weight: bold;
            color: #555;
            display: inline;
        }
        .info-box-col .info-value {
            color: #333;
            display: inline;
            margin-left: 5px;
        }
        .info-box h3 {
            margin: 0 0 6px 0;
            font-size: 12px;
            font-weight: bold;
            color: #333;
        }
        .info-box p {
            margin: 4px 0;
            font-size: 11px;
            color: #555;
        }
        .info-box .label {
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

    <div class="info-box">
        <div class="info-box-row">
            <div class="info-box-col info-box-col--bike">
                @if($quote->bike_description)
                    <h3>Vélo concerné</h3>
                    <p>{{ $quote->bike_description }}</p>
                @endif
            </div>
            <div class="info-box-col info-box-col--motif">
                @if($quote->reception_comment)
                    <h3>Motif</h3>
                    <p>{{ $quote->reception_comment }}</p>
                @endif
            </div>
            <div class="info-box-col info-box-col--dates">
                @if(!$quote->isInvoice())
                    <div class="info-row">
                        <span class="info-label">Émis le :</span>
                        <span class="info-value">{{ $quote->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Valide jusqu'au :</span>
                        <span class="info-value">{{ $quote->valid_until->format('d/m/Y') }}</span>
                    </div>
                @else
                    <div class="info-row">
                        <span class="info-label">Facturé le :</span>
                        <span class="info-value">{{ $quote->invoiced_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Paiement :</span>
                        <span class="info-value">{{ config('company.payment_terms_text') }}</span>
                    </div>
                @endif
            </div>
        </div>
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

    <div class="summary-row">
        <div class="remarks">
            <div class="remarks-title">Remarques</div>
            @if($quote->remarks)
                <div class="remarks-content">{{ $quote->remarks }}</div>
            @else
                <div class="remarks-empty">Aucune remarque</div>
            @endif
        </div>
        <div class="totals">
            <table class="totals-table">
                <tr>
                    <td class="totals-label">Total HT</td>
                    <td class="totals-value">{{ number_format((float)$quote->total_ht, 2, ',', ' ') }} €</td>
                </tr>
                <tr>
                    <td class="totals-label">TVA</td>
                    <td class="totals-value">{{ number_format((float)$quote->total_tva, 2, ',', ' ') }} €</td>
                </tr>
                <tr class="totals-row--total">
                    <td class="totals-label">Total TTC</td>
                    <td class="totals-value">{{ number_format((float)$quote->total_ttc, 2, ',', ' ') }} €</td>
                </tr>
            </table>
        </div>
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
