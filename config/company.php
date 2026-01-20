<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Company Information
    |--------------------------------------------------------------------------
    |
    | Information légale de l'entreprise utilisée dans les documents PDF
    | (devis, factures, etc.)
    |
    */

    'name' => env('COMPANY_NAME', 'Les vélos d\'Armor'),
    'legal_name' => env('COMPANY_LEGAL_NAME', 'Les vélos d\'Armor'),
    'address' => env('COMPANY_ADDRESS', '23 avenue chateaubriand'),
    'postal_code' => env('COMPANY_POSTAL_CODE', '22500'),
    'city' => env('COMPANY_CITY', 'Paimpol'),
    'country' => env('COMPANY_COUNTRY', 'France'),
    'phone' => env('COMPANY_PHONE', '06 36 19 61 75'),
    'email' => env('COMPANY_EMAIL', 'contact@lesvelosdarmor.bzh'),
    'siret' => env('COMPANY_SIRET', '984 374 736'),
    'tva_number' => env('COMPANY_TVA_NUMBER', 'FR57 984 374 736'),
    'rcs' => env('COMPANY_RCS', 'RCS Saint-Brieuc 984 374 736'),
    'capital' => env('COMPANY_CAPITAL', '1 000'),

    /*
    |--------------------------------------------------------------------------
    | Payment Terms
    |--------------------------------------------------------------------------
    |
    | Conditions de paiement par défaut
    |
    */

    'payment_terms' => env('COMPANY_PAYMENT_TERMS', '30'),
    'payment_terms_text' => env('COMPANY_PAYMENT_TERMS_TEXT', 'Paiement à 30 jours'),

    /*
    |--------------------------------------------------------------------------
    | Late Payment Penalties
    |--------------------------------------------------------------------------
    |
    | Pénalités de retard de paiement
    |
    */

    'late_payment_penalty_rate' => env('COMPANY_LATE_PAYMENT_PENALTY_RATE', '10'),
    'late_payment_fixed_compensation' => env('COMPANY_LATE_PAYMENT_FIXED_COMPENSATION', '40'),

    /*
    |--------------------------------------------------------------------------
    | Quote Validity
    |--------------------------------------------------------------------------
    |
    | Durée de validité par défaut des devis (en jours)
    |
    */

    'quote_validity_days' => env('COMPANY_QUOTE_VALIDITY_DAYS', '30'),

];
