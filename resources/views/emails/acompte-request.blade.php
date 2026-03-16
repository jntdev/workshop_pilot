<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande d'acompte - Réservation</title>
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
        .bank-details {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .bank-details h3 {
            margin: 0 0 15px 0;
            color: #333;
            font-size: 16px;
        }
        .bank-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .bank-details td {
            padding: 5px 0;
        }
        .bank-details td:first-child {
            font-weight: bold;
            width: 100px;
        }
        .highlight {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
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

            <p>
                Ce mail est envoyé automatiquement car votre réservation a été renseignée dans notre agenda.
                Si vous avez des questions, vous pouvez directement répondre à ce mail.
            </p>

            <p>
                Pour valider votre réservation, un acompte de <strong>{{ $montantAcompte }} €</strong> est nécessaire.
                Merci de procéder au virement sur le compte dont vous trouverez les coordonnées ci-dessous.
            </p>

            <div class="bank-details">
                <h3>Coordonnées bancaires</h3>
                <table>
                    <tr>
                        <td>IBAN</td>
                        <td>{{ $rib['iban'] }}</td>
                    </tr>
                    <tr>
                        <td>BIC</td>
                        <td>{{ $rib['bic'] }}</td>
                    </tr>
                    <tr>
                        <td>Titulaire</td>
                        <td>{{ $rib['titulaire'] }}</td>
                    </tr>
                    <tr>
                        <td>Banque</td>
                        <td>{{ $rib['banque'] }}</td>
                    </tr>
                </table>
            </div>

            <div class="highlight">
                <strong>Important :</strong> N'oubliez pas de mentionner <strong>"{{ $clientNom }}"</strong> dans le libellé du virement pour que nous puissions faire le rapprochement bancaire.
            </div>

            <p>Merci de procéder au virement dans un délai de 7 jours.</p>
        </div>

        <div class="signature">
            <p>Bien cordialement,</p>
            <p><strong>L'équipe Location des Vélos d'Armor</strong></p>
        </div>
    </div>
</body>
</html>
