<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Contrat de location — Les vélos d'Armor</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            font-size: 15px;
            line-height: 1.6;
            color: #1a1a1a;
            background: #f4f4f4;
            padding: 0;
        }
        .header {
            background: #1a1a1a;
            color: white;
            padding: 16px 20px;
            text-align: center;
        }
        .header h1 {
            font-size: 17px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        .header p {
            font-size: 12px;
            color: #aaa;
            margin-top: 2px;
        }
        .card {
            background: white;
            margin: 16px;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }
        .card h2 {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #888;
            margin-bottom: 12px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }
        .info-row:last-child { border-bottom: none; }
        .info-row__label { color: #666; }
        .info-row__value { font-weight: 500; text-align: right; max-width: 60%; }
        .accessories-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 4px;
        }
        .accessory-tag {
            background: #f0f0f0;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 13px;
        }
        .caution-box {
            background: #fff8e1;
            border: 1px solid #f0c040;
            border-radius: 10px;
            padding: 16px;
            text-align: center;
        }
        .caution-box__amount {
            font-size: 28px;
            font-weight: 700;
            color: #c07000;
        }
        .caution-box__label {
            font-size: 13px;
            color: #888;
            margin-top: 2px;
        }
        .legal-text {
            font-size: 12px;
            color: #666;
            line-height: 1.5;
        }
        .legal-text p { margin-bottom: 8px; }

        /* Signature form */
        .form-group { margin-bottom: 16px; }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #444;
            margin-bottom: 6px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-group input:focus { border-color: #1a1a1a; }
        .canvas-wrap {
            border: 1.5px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            background: #fafafa;
            position: relative;
        }
        #signature-canvas {
            display: block;
            width: 100%;
            touch-action: none;
        }
        .canvas-hint {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 13px;
            color: #bbb;
            pointer-events: none;
        }
        .btn-clear {
            background: none;
            border: none;
            color: #888;
            font-size: 13px;
            padding: 8px 0;
            cursor: pointer;
            text-decoration: underline;
        }
        .btn-sign {
            width: 100%;
            background: #1a1a1a;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 16px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 8px;
            transition: opacity 0.2s;
        }
        .btn-sign:disabled { opacity: 0.5; }
        .btn-sign:active { opacity: 0.8; }

        /* States */
        .state-card {
            text-align: center;
            padding: 40px 24px;
        }
        .state-icon { font-size: 48px; margin-bottom: 16px; }
        .state-title { font-size: 20px; font-weight: 700; margin-bottom: 8px; }
        .state-desc { font-size: 14px; color: #666; line-height: 1.6; }
        .btn-download {
            display: inline-block;
            margin-top: 20px;
            background: #1a1a1a;
            color: white;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
        }
        .error-msg {
            background: #fff0f0;
            border: 1px solid #f88;
            border-radius: 8px;
            padding: 12px;
            font-size: 13px;
            color: #c00;
            margin-top: 12px;
            display: none;
        }
        .success-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            z-index: 100;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: white;
            text-align: center;
            padding: 40px;
        }
        .success-overlay.visible { display: flex; }
        .success-overlay .icon { font-size: 64px; margin-bottom: 20px; }
        .success-overlay h2 { font-size: 22px; margin-bottom: 10px; }
        .success-overlay p { font-size: 15px; color: #ccc; line-height: 1.6; }
    </style>
</head>
<body>

<div class="header">
    <h1>Les vélos d'Armor</h1>
    <p>Contrat de location</p>
</div>

@php
    $data = $contract->contract_data;
    $accessories = $contract->accessories ?? [];
    $accessoryLabels = [
        'vae' => 'VAE', 'vtc' => 'VTC', 'casque' => 'Casque',
        'retroviseur' => 'Rétroviseur', 'sacoche' => 'Sacoche',
        'support_tel' => 'Support tél.', 'bequille' => 'Béquille',
        'lumiere' => 'Lumière', 'antivol' => 'Antivol',
        'siege_enfant' => 'Siège enfant', 'remorque' => 'Remorque',
    ];
    $activeAccessories = array_filter($accessories, fn($qty) => $qty > 0);
@endphp

@if($state === 'expired')
    <div class="card">
        <div class="state-card">
            <div class="state-icon">⏰</div>
            <div class="state-title">Lien expiré</div>
            <div class="state-desc">Ce lien n'est plus valide.<br>Demandez à votre loueur de générer un nouveau QR code.</div>
        </div>
    </div>

@elseif($state === 'signed')
    <div class="card">
        <div class="state-card">
            <div class="state-icon">✅</div>
            <div class="state-title">Contrat signé</div>
            <div class="state-desc">Vous avez signé ce contrat le {{ $contract->signed_at->format('d/m/Y à H:i') }}.<br>Un email de confirmation vous a été envoyé.</div>
            <a href="{{ route('contract.pdf', $contract->token) }}" class="btn-download">Télécharger le PDF</a>
        </div>
    </div>

@else
    {{-- Résumé --}}
    <div class="card">
        <h2>Votre location</h2>
        <div class="info-row">
            <span class="info-row__label">Client</span>
            <span class="info-row__value">{{ $data['client_name'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-row__label">Début</span>
            <span class="info-row__value">{{ $data['date_reservation'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-row__label">Fin</span>
            <span class="info-row__value">{{ $data['date_retour'] }}</span>
        </div>
        <div class="info-row">
            <span class="info-row__label">Retour avant</span>
            <span class="info-row__value">{{ $contract->return_time_text }}</span>
        </div>
    </div>

    @if(count($activeAccessories) > 0)
    <div class="card">
        <h2>Équipements remis</h2>
        <div class="accessories-list">
            @foreach($activeAccessories as $key => $qty)
            <span class="accessory-tag">
                {{ $accessoryLabels[$key] ?? $key }}@if($qty > 1) ×{{ $qty }}@endif
            </span>
            @endforeach
        </div>
    </div>
    @endif

    <div class="card">
        <div class="caution-box">
            <div class="caution-box__amount">{{ number_format($contract->caution_amount / 100, 0, ',', ' ') }} €</div>
            <div class="caution-box__label">Caution engagée</div>
        </div>
    </div>

    {{-- Points clés du contrat --}}
    <div class="card">
        <h2>Conditions importantes</h2>
        <div class="legal-text">
            <p>En signant, vous reconnaissez être responsable de l'utilisation et de la garde des vélos loués pendant toute la durée de la location.</p>
            <p>Vous êtes responsable de tout dommage, vol ou perte des vélos loués. Le loueur décline toute responsabilité pour tout accident ou dommage résultant de l'utilisation des vélos.</p>
            <p>En cas de retard de retour, des frais supplémentaires pourront être facturés.</p>
            <p>La caution indiquée ci-dessus sera restituée à la fin de la location, déduction faite des éventuels frais ou dommages.</p>
        </div>
    </div>

    {{-- Formulaire de signature --}}
    <div class="card">
        <h2>Signature</h2>
        <div class="form-group">
            <label for="signer-name">Votre nom complet *</label>
            <input type="text" id="signer-name" value="{{ $data['client_name'] }}" autocomplete="name" placeholder="Prénom Nom">
        </div>
        <div class="form-group">
            <label>Votre signature *</label>
            <div class="canvas-wrap">
                <canvas id="signature-canvas" height="180"></canvas>
                <span class="canvas-hint" id="canvas-hint">Signez ici avec votre doigt</span>
            </div>
            <button type="button" class="btn-clear" id="btn-clear">Effacer</button>
        </div>
        <div id="error-msg" class="error-msg"></div>
        <button type="button" class="btn-sign" id="btn-sign">Je signe et j'accepte le contrat</button>
    </div>

    <div style="height: 40px;"></div>

    <div class="success-overlay" id="success-overlay">
        <div class="icon">✅</div>
        <h2>Contrat signé !</h2>
        <p>Merci. Vous allez recevoir un email de confirmation avec votre contrat en pièce jointe.</p>
    </div>
@endif

@if($state === 'pending')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
(function () {
    const canvas = document.getElementById('signature-canvas');
    const hint = document.getElementById('canvas-hint');
    const btnClear = document.getElementById('btn-clear');
    const btnSign = document.getElementById('btn-sign');
    const errorMsg = document.getElementById('error-msg');
    const overlay = document.getElementById('success-overlay');

    // Ajuste la résolution du canvas
    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.height * ratio;
        canvas.getContext('2d').scale(ratio, ratio);
        pad.clear();
    }

    const pad = new SignaturePad(canvas, {
        backgroundColor: 'rgba(250, 250, 250, 0)',
        penColor: '#1a1a1a',
    });

    resizeCanvas();

    pad.addEventListener('beginStroke', () => {
        hint.style.display = 'none';
    });

    btnClear.addEventListener('click', () => {
        pad.clear();
        hint.style.display = 'block';
    });

    btnSign.addEventListener('click', async () => {
        const name = document.getElementById('signer-name').value.trim();

        if (!name) {
            showError('Veuillez saisir votre nom complet.');
            return;
        }
        if (pad.isEmpty()) {
            showError('Veuillez dessiner votre signature.');
            return;
        }

        hideError();
        btnSign.disabled = true;
        btnSign.textContent = 'Envoi en cours...';

        try {
            const res = await fetch('{{ route('contract.sign', $contract->token) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    signer_name: name,
                    signature_image: pad.toDataURL('image/png'),
                }),
            });

            if (res.ok) {
                overlay.classList.add('visible');
            } else {
                const json = await res.json().catch(() => ({}));
                showError(json.message || 'Une erreur est survenue. Veuillez réessayer.');
                btnSign.disabled = false;
                btnSign.textContent = 'Je signe et j\'accepte le contrat';
            }
        } catch {
            showError('Erreur réseau. Vérifiez votre connexion et réessayez.');
            btnSign.disabled = false;
            btnSign.textContent = 'Je signe et j\'accepte le contrat';
        }
    });

    function showError(msg) {
        errorMsg.textContent = msg;
        errorMsg.style.display = 'block';
    }
    function hideError() {
        errorMsg.style.display = 'none';
    }
})();
</script>
@endif

</body>
</html>
