<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Contrat de Location de Vélos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #222;
            margin: 0;
            padding: 20px 30px;
        }
        h1 {
            text-align: center;
            font-size: 16px;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .subtitle {
            text-align: center;
            font-size: 10px;
            color: #666;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 12px;
            margin: 14px 0 4px 0;
            border-bottom: 1px solid #ccc;
            padding-bottom: 2px;
        }
        p {
            margin: 4px 0;
        }
        .accessories-grid {
            width: 100%;
            margin: 6px 0;
        }
        .accessories-grid td {
            padding: 3px 6px;
            font-size: 11px;
            width: 33%;
        }
        .check {
            display: inline-block;
            min-width: 12px;
            height: 12px;
            padding: 0 2px;
            border: 1px solid #555;
            text-align: center;
            line-height: 12px;
            font-size: 9px;
            margin-right: 4px;
            vertical-align: middle;
        }
        .check--on {
            background: #222;
            color: white;
        }
        .sig-block {
            border: 1px solid #ccc;
            padding: 10px;
            min-height: 80px;
            margin-top: 20px;
        }
        .sig-block__label {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 6px;
            display: block;
        }
        .sig-block__name {
            font-size: 11px;
            margin-bottom: 4px;
        }
        .sig-block__date {
            font-size: 10px;
            color: #555;
        }
        .sig-image {
            max-width: 200px;
            max-height: 80px;
            display: block;
            margin-top: 6px;
        }
        .caution-block {
            border: 2px solid #222;
            padding: 10px 14px;
            margin: 10px 0;
            font-size: 12px;
        }
    </style>
</head>
<body>

@php
    $data = $contract->contract_data;
    $accessories = $contract->accessories;
    $cautionEuros = number_format($contract->caution_amount / 100, 0, ',', ' ');

    $allAccessories = [
        'vae'            => 'VAE',
        'vtc'            => 'VTC',
        'casque'         => 'Casque',
        'retroviseur'    => 'Rétroviseur',
        'sacoche'        => 'Sacoche / Sac',
        'support_tel'    => 'Support téléphone',
        'bequille'       => 'Béquille',
        'lumiere'        => 'Lumière',
        'antivol'        => 'Antivol',
        'siege_enfant'   => 'Siège enfant',
        'remorque'       => 'Remorque enfant',
    ];
@endphp

<h1>Contrat de Location de Vélos</h1>
<div class="subtitle">Les vélos d'Armor — Réservation n° {{ $data['reservation_id'] }}</div>

<h2>1. Objet du contrat</h2>
<p>Le Loueur s'engage à louer au Locataire les vélos décrits ci-dessous, et le Locataire accepte de les louer selon les conditions énoncées dans ce contrat.</p>

<h2>2. Vélos et équipements remis</h2>
<table class="accessories-grid">
    @foreach(array_chunk(array_keys($allAccessories), 3) as $row)
    <tr>
        @foreach($row as $key)
        @php $qty = $accessories[$key] ?? 0; @endphp
        <td>
            <span class="check {{ $qty > 0 ? 'check--on' : '' }}">{{ $qty > 1 ? $qty : ($qty > 0 ? 'X' : '') }}</span>
            {{ $allAccessories[$key] }}
        </td>
        @endforeach
    </tr>
    @endforeach
</table>

<h2>3. Durée de la location</h2>
<p><strong>Date de début :</strong> {{ $data['date_reservation'] }}</p>
<p><strong>Date de fin :</strong> {{ $data['date_retour'] }}</p>
<p><strong>Les vélos doivent être retournés :</strong> {{ $contract->return_time_text }}</p>

<h2>4. Tarifs et paiement</h2>
<p>(a) Le Locataire accepte de payer le montant total de la location selon les tarifs en vigueur au moment de la location.</p>
<p>(b) Le paiement doit être effectué avant la prise en charge des vélos.</p>
<p>(c) En cas de retard dans le retour des vélos, des frais supplémentaires pourront être facturés au Locataire.</p>

<h2>5. Responsabilités du Locataire</h2>
<p>(a) Le Locataire est responsable de l'utilisation et de la garde des vélos loués pendant la durée de la location.</p>
<p>(b) Le Locataire s'engage à utiliser les vélos conformément aux lois et règlements en vigueur, ainsi qu'à les utiliser de manière raisonnable et prudente.</p>
<p>(c) Le Locataire est responsable de tout dommage, vol ou perte des vélos loués pendant la durée de la location.</p>

<h2>6. Responsabilités du Loueur</h2>
<p>(a) Le Loueur s'engage à fournir des vélos en bon état de fonctionnement et conformes aux normes de sécurité applicables.</p>
<p>(b) En cas de défaillance mécanique des vélos pendant la location, le Loueur s'engage à réparer ou à remplacer les vélos dans les meilleurs délais.</p>

<h2>7. Dépôt de garantie</h2>
<p>(a) Le loueur peut exiger un dépôt de garantie au moment de la location pour couvrir les éventuels dommages, pertes ou vols des vélos loués.</p>
<p>(b) Le dépôt de garantie sera rendu au Locataire à la fin de la location, déduction faite des éventuels frais ou dommages constatés.</p>

<h2>8. Résiliation du contrat</h2>
<p>(a) Le Loueur se réserve le droit de résilier le contrat de location en cas de non-respect des termes et conditions énoncés.</p>
<p>(b) En cas de résiliation anticipée du contrat par le Locataire, aucun remboursement ne sera effectué pour la période restante de la location.</p>

<h2>9. Clause de non-responsabilité</h2>
<p>Le Loueur décline toute responsabilité pour tout accident, dommage corporel ou matériel résultant de l'utilisation des vélos loués. Le Locataire assume l'entière responsabilité de sa sécurité et de celle des tiers pendant la période de location.</p>

<h2>10. Caution</h2>
<div class="caution-block">
    <strong>{{ $contract->signer_name ?? $data['client_name'] }}</strong>
    s'engage à hauteur de <strong>{{ $cautionEuros }} €</strong>
    pour le retour des vélos et de leurs équipements.
</div>

<div class="sig-block">
    <span class="sig-block__label">Signature du locataire :</span>
    <div class="sig-block__name">{{ $contract->signer_name }}</div>
    <div class="sig-block__date">{{ $contract->signed_at?->format('d/m/Y H:i') }}</div>
    @if($contract->signature_image)
        <img class="sig-image" src="{{ $contract->signature_image }}" alt="Signature">
    @endif
</div>

</body>
</html>
