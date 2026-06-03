<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche départs – {{ $date->isoFormat('dddd D MMMM YYYY') }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #222;
            padding: 16px 20px;
        }

        .page-header {
            display: flex;
            align-items: baseline;
            gap: 12px;
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 2px solid #222;
        }

        h1 {
            font-size: 17px;
            font-weight: bold;
            text-transform: capitalize;
        }

        .subtitle {
            font-size: 11px;
            color: #666;
        }

        .columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .col-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #555;
            border-bottom: 2px solid #222;
            padding-bottom: 4px;
            margin-bottom: 10px;
        }

        .card {
            border: 1px solid #ddd;
            border-left: 4px solid #3b82f6;
            border-radius: 3px;
            padding: 8px 10px;
            margin-bottom: 8px;
            break-inside: avoid;
        }

        .card__client {
            font-size: 13px;
            font-weight: bold;
        }

        .card__phone {
            font-size: 11px;
            color: #555;
            margin-bottom: 5px;
        }

        .card__bikes {
            margin: 4px 0;
        }

        .card__bike {
            font-size: 11px;
            background: #f3f4f6;
            border-radius: 3px;
            padding: 1px 5px;
            display: inline-block;
            margin: 1px 2px 1px 0;
        }

        .card__comment {
            font-size: 10px;
            color: #666;
            font-style: italic;
            border-left: 2px solid #ddd;
            padding-left: 5px;
            margin-top: 4px;
        }

        .card__logistics {
            margin-top: 5px;
            font-size: 11px;
            background: #f9fafb;
            border-radius: 3px;
            padding: 5px 7px;
        }

        .card__logistics-address {
            font-weight: 600;
        }

        .card__logistics-detail {
            color: #555;
        }

        .evening-divider {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #999;
            margin: 10px 0 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .evening-divider::before,
        .evening-divider::after {
            content: '';
            flex: 1;
            border-top: 1px dashed #ccc;
        }

        .empty {
            font-size: 11px;
            color: #aaa;
            font-style: italic;
        }

        .footer {
            margin-top: 20px;
            font-size: 10px;
            color: #aaa;
            border-top: 1px solid #eee;
            padding-top: 6px;
        }

        @media print {
            body { padding: 8px 12px; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="no-print" style="margin-bottom:12px;">
    <button onclick="window.print()" style="padding:5px 14px;font-size:12px;cursor:pointer;margin-right:6px;">Imprimer</button>
    <button onclick="window.close()" style="padding:5px 14px;font-size:12px;cursor:pointer;">Fermer</button>
</div>

<div class="page-header">
    <h1>Départs du {{ $date->isoFormat('dddd D MMMM YYYY') }}</h1>
    <span class="subtitle">{{ $departures->count() }} départ{{ $departures->count() > 1 ? 's' : '' }}</span>
</div>

@php
    $livraisons       = $departures->where('livraison_necessaire', true)->where('date_recuperation', null);
    $livraisonsVeille = $departures->where('livraison_necessaire', true)->whereNotNull('date_recuperation');
    $atelier          = $departures->where('livraison_necessaire', false)->where('date_recuperation', null);
    $atelierVeille    = $departures->where('livraison_necessaire', false)->whereNotNull('date_recuperation');
@endphp

<div class="columns">

    {{-- COLONNE À LIVRER --}}
    <div>
        <div class="col-title">À livrer</div>

        @if($livraisons->isEmpty() && $livraisonsVeille->isEmpty())
            <div class="empty">Aucune livraison ce jour.</div>
        @else
            @foreach($livraisons as $r)
                @include('print._depart-card', ['r' => $r, 'bikes' => $bikes])
            @endforeach

            @if($livraisonsVeille->isNotEmpty())
                <div class="evening-divider">Veille · après 18h</div>
                @foreach($livraisonsVeille as $r)
                    @include('print._depart-card', ['r' => $r, 'bikes' => $bikes])
                @endforeach
            @endif
        @endif
    </div>

    {{-- COLONNE À L'ATELIER --}}
    <div>
        <div class="col-title">À l'atelier</div>

        @if($atelier->isEmpty() && $atelierVeille->isEmpty())
            <div class="empty">Aucun départ atelier ce jour.</div>
        @else
            @foreach($atelier as $r)
                @include('print._depart-card', ['r' => $r, 'bikes' => $bikes])
            @endforeach

            @if($atelierVeille->isNotEmpty())
                <div class="evening-divider">Veille · après 18h</div>
                @foreach($atelierVeille as $r)
                    @include('print._depart-card', ['r' => $r, 'bikes' => $bikes])
                @endforeach
            @endif
        @endif
    </div>

</div>

<div class="footer">Imprimé le {{ now()->format('d/m/Y à H:i') }}</div>

</body>
</html>
