<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Jeton d'accès à l'API AI
    |--------------------------------------------------------------------------
    |
    | Jeton secret attendu en « Authorization: Bearer » sur les routes
    | /api/ai/*. Sans valeur, ces routes refusent toutes les requêtes.
    |
    */

    'token' => env('AI_API_TOKEN'),

];
