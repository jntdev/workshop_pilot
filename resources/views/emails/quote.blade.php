<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $quote->isInvoice() ? 'Facture' : 'Devis' }} {{ $quote->reference }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .logo {
            width: 75%;
            max-width: 300px;
            height: auto;
        }
        .content {
            margin-bottom: 20px;
        }
        .content p {
            margin: 0 0 15px 0;
        }
        a {
            color: #2196F3;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .signature {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ $message->embed(public_path('images/logo-email.png')) }}" alt="Les Vélos d'Armor" class="logo">
        </div>

        <div class="content">
            <p>Bonjour,</p>

            @if($quote->isInvoice())
                <p>
                    Veuillez trouver ci-joint la facture pour le <strong>{{ $quote->bike_description }}</strong> que vous nous avez confié à l'atelier.
                </p>
            @else
                <p>
                    Veuillez trouver ci-joint le devis pour le <strong>{{ $quote->bike_description }}</strong> que vous nous avez déposé à l'atelier.
                </p>
            @endif

            @php
                $clientName = trim($quote->client->prenom . ' ' . $quote->client->nom);
                $mailSubject = urlencode($clientName . ' - ' . $quote->bike_description);
            @endphp

            <p>
                Si vous avez la moindre question, n'hésitez pas à nous téléphoner au
                <a href="tel:+33636196175">06 36 19 61 75</a>
                ou à nous écrire par mail en
                <a href="mailto:contact@lesvelosdarmor.bzh?subject={{ $mailSubject }}">cliquant ici</a>.
            </p>
        </div>

        <div class="signature">
            <p>Bien cordialement,</p>
            <p><strong>L'atelier des Vélos d'Armor</strong></p>
        </div>
    </div>
</body>
</html>
