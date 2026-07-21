<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre contrat de location</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 16px; line-height: 1.6; color: #333; margin: 0; padding: 20px; background-color: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
        .logo { width: 75%; max-width: 300px; height: auto; }
        .summary { background: #f8f9fa; border-radius: 6px; padding: 16px 20px; margin: 20px 0; }
        .summary p { margin: 4px 0; font-size: 14px; }
        .signature { margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; }
        a { color: #2196F3; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ $message->embed(public_path('images/logo-email.png')) }}" alt="Les Vélos d'Armor" class="logo">
        </div>

        @php $data = $contract->contract_data; @endphp

        <p>Bonjour {{ $data['client_name'] }},</p>

        <p>Merci d'avoir signé votre contrat de location. Vous trouverez ci-joint votre exemplaire en PDF.</p>

        <div class="summary">
            <p><strong>Période :</strong> du {{ $data['date_reservation'] }} au {{ $data['date_retour'] }}</p>
            <p><strong>Retour attendu :</strong> {{ $contract->return_time_text }}</p>
            <p><strong>Caution :</strong> {{ number_format($contract->caution_amount / 100, 0, ',', ' ') }} €</p>
            <p><strong>Remis par :</strong> {{ $contract->operator_name }}</p>
        </div>

        <p>
            Pour toute question, n'hésitez pas à nous contacter au
            <a href="tel:+33636196175">06 36 19 61 75</a>
            ou par mail à <a href="mailto:contact@lesvelosdarmor.bzh">contact@lesvelosdarmor.bzh</a>.
        </p>

        <div class="signature">
            <p>Bonne location,</p>
            <p><strong>Les vélos d'Armor</strong></p>
        </div>
    </div>
</body>
</html>
