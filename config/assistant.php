<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Jetons de l'assistant de l'atelier
    |--------------------------------------------------------------------------
    |
    | Trois jetons distincts, jamais ceux de l'API AI :
    | - read : lecture seule des données de l'atelier (routes GET /api/assistant-read/*),
    |   utilisé par l'outil que Claude appelle sur le Mac ;
    | - propose : enregistrer une proposition d'action (POST /api/assistant-propose/actions),
    |   utilisé par le même outil. Il ne modifie rien : l'utilisateur doit confirmer dans le chat ;
    | - relay : relève les questions et renvoie les réponses (/api/assistant-relay/*),
    |   utilisé uniquement par le petit programme du Mac, jamais par Claude.
    | Sans valeur, les routes correspondantes refusent toutes les requêtes.
    |
    */

    'read_token' => env('ASSISTANT_READ_TOKEN'),

    'propose_token' => env('ASSISTANT_PROPOSE_TOKEN'),

    'relay_token' => env('ASSISTANT_RELAY_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Délais
    |--------------------------------------------------------------------------
    |
    | processing_timeout_seconds : une question prise par le Mac et restée sans
    | réponse plus longtemps est marquée en échec.
    | relay_online_seconds : le Mac est considéré hors ligne s'il n'a pas
    | interrogé le serveur depuis ce délai.
    | max_unfinished_per_user : questions simultanées en attente par utilisateur.
    |
    */

    'processing_timeout_seconds' => 300,

    'relay_online_seconds' => 30,

    'max_unfinished_per_user' => 3,

    'question_max_length' => 2000,

    /*
    |--------------------------------------------------------------------------
    | Actions proposées par l'assistant
    |--------------------------------------------------------------------------
    |
    | action_ttl_minutes : une proposition non confirmée expire après ce délai.
    | max_actions_per_message : propositions possibles pour une même question.
    |
    */

    'action_ttl_minutes' => 30,

    'max_actions_per_message' => 5,

];
