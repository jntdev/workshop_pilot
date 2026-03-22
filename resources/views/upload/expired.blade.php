<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lien expire - Workshop Pilot</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            max-width: 400px;
            background: white;
            border-radius: 20px;
            padding: 32px 24px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            text-align: center;
        }

        .icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 20px;
            color: #ef5350;
        }

        h1 {
            font-size: 20px;
            color: #333;
            margin-bottom: 12px;
        }

        p {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>

        @if($reason === 'expired')
            <h1>Lien expire</h1>
            <p>Ce lien n'est plus valide. Veuillez demander un nouveau QR code depuis l'application.</p>
        @elseif($reason === 'limit_reached')
            <h1>Limite atteinte</h1>
            <p>Le nombre maximum de photos a ete atteint pour ce lien.</p>
        @else
            <h1>Lien invalide</h1>
            <p>Ce lien n'est pas reconnu. Veuillez scanner a nouveau le QR code.</p>
        @endif
    </div>
</body>
</html>
