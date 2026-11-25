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

    /*
    |--------------------------------------------------------------------------
    | Dossier des journaux lus par GET /api/ai/logs
    |--------------------------------------------------------------------------
    |
    | Fichiers laravel*.log de ce dossier (canal single et canal daily).
    |
    */

    'logs_path' => storage_path('logs'),

];
