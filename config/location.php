<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Coordonnées bancaires Location
    |--------------------------------------------------------------------------
    |
    | Ces informations sont utilisées dans les emails de demande d'acompte
    | pour permettre aux clients de procéder au virement.
    |
    */

    'rib' => [
        'iban' => env('LOCATION_RIB_IBAN', ''),
        'bic' => env('LOCATION_RIB_BIC', ''),
        'titulaire' => env('LOCATION_RIB_TITULAIRE', ''),
        'banque' => env('LOCATION_RIB_BANQUE', ''),
    ],

];
