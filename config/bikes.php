<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Flotte de vélos disponibles à la location
    |--------------------------------------------------------------------------
    |
    | Configuration de la flotte de vélos. Chaque vélo est défini par :
    | - id : identifiant unique stable (utilisé comme clé de colonne)
    | - category : type de vélo (VAE = Vélo à Assistance Électrique, VTC)
    | - size : taille du cadre (S, M, L, XL)
    | - frame_type : type de cadre (b = bas, h = haut)
    | - label : libellé affiché dans l'en-tête de colonne
    | - status : état du vélo (OK = opérationnel, HS = hors service)
    | - notes : informations complémentaires (optionnel)
    |
    | Total : 22 VAE + 25 VTC = 47 vélos
    |
    */

    'fleet' => [
        // =====================================================================
        // VAE - Vélos à Assistance Électrique (22 unités)
        // =====================================================================

        // VAE Taille S (4 unités)
        ['id' => 'vae-s-01', 'category' => 'VAE', 'size' => 'S', 'frame_type' => 'b', 'label' => 'VAE S-1', 'status' => 'OK', 'notes' => 'Autonomie ~80km'],
        ['id' => 'vae-s-02', 'category' => 'VAE', 'size' => 'S', 'frame_type' => 'b', 'label' => 'VAE S-2', 'status' => 'OK', 'notes' => 'Autonomie ~80km'],
        ['id' => 'vae-s-03', 'category' => 'VAE', 'size' => 'S', 'frame_type' => 'h', 'label' => 'VAE S-3', 'status' => 'HS', 'notes' => 'Batterie défectueuse'],
        ['id' => 'vae-s-04', 'category' => 'VAE', 'size' => 'S', 'frame_type' => 'h', 'label' => 'VAE S-4', 'status' => 'OK', 'notes' => 'Autonomie ~80km'],

        // VAE Taille M (6 unités)
        ['id' => 'vae-m-01', 'category' => 'VAE', 'size' => 'M', 'frame_type' => 'b', 'label' => 'VAE M-1', 'status' => 'OK', 'notes' => 'Autonomie ~80km'],
        ['id' => 'vae-m-02', 'category' => 'VAE', 'size' => 'M', 'frame_type' => 'b', 'label' => 'VAE M-2', 'status' => 'OK', 'notes' => 'Autonomie ~80km'],
        ['id' => 'vae-m-03', 'category' => 'VAE', 'size' => 'M', 'frame_type' => 'b', 'label' => 'VAE M-3', 'status' => 'OK', 'notes' => 'Autonomie ~80km'],
        ['id' => 'vae-m-04', 'category' => 'VAE', 'size' => 'M', 'frame_type' => 'h', 'label' => 'VAE M-4', 'status' => 'HS', 'notes' => 'En réparation'],
        ['id' => 'vae-m-05', 'category' => 'VAE', 'size' => 'M', 'frame_type' => 'h', 'label' => 'VAE M-5', 'status' => 'OK', 'notes' => 'Autonomie ~80km'],
        ['id' => 'vae-m-06', 'category' => 'VAE', 'size' => 'M', 'frame_type' => 'h', 'label' => 'VAE M-6', 'status' => 'OK', 'notes' => 'Autonomie ~80km'],

        // VAE Taille L (8 unités)
        ['id' => 'vae-l-01', 'category' => 'VAE', 'size' => 'L', 'frame_type' => 'b', 'label' => 'VAE L-1', 'status' => 'OK', 'notes' => 'Autonomie ~80km'],
        ['id' => 'vae-l-02', 'category' => 'VAE', 'size' => 'L', 'frame_type' => 'b', 'label' => 'VAE L-2', 'status' => 'OK', 'notes' => 'Autonomie ~80km'],
        ['id' => 'vae-l-03', 'category' => 'VAE', 'size' => 'L', 'frame_type' => 'b', 'label' => 'VAE L-3', 'status' => 'OK', 'notes' => 'Autonomie ~80km'],
        ['id' => 'vae-l-04', 'category' => 'VAE', 'size' => 'L', 'frame_type' => 'b', 'label' => 'VAE L-4', 'status' => 'OK', 'notes' => 'Autonomie ~80km'],
        ['id' => 'vae-l-05', 'category' => 'VAE', 'size' => 'L', 'frame_type' => 'h', 'label' => 'VAE L-5', 'status' => 'OK', 'notes' => 'Autonomie ~80km'],
        ['id' => 'vae-l-06', 'category' => 'VAE', 'size' => 'L', 'frame_type' => 'h', 'label' => 'VAE L-6', 'status' => 'OK', 'notes' => 'Autonomie ~80km'],
        ['id' => 'vae-l-07', 'category' => 'VAE', 'size' => 'L', 'frame_type' => 'h', 'label' => 'VAE L-7', 'status' => 'OK', 'notes' => 'Autonomie ~80km'],
        ['id' => 'vae-l-08', 'category' => 'VAE', 'size' => 'L', 'frame_type' => 'h', 'label' => 'VAE L-8', 'status' => 'OK', 'notes' => 'Autonomie ~80km'],

        // VAE Taille XL (4 unités)
        ['id' => 'vae-xl-01', 'category' => 'VAE', 'size' => 'XL', 'frame_type' => 'b', 'label' => 'VAE XL-1', 'status' => 'OK', 'notes' => 'Autonomie ~80km'],
        ['id' => 'vae-xl-02', 'category' => 'VAE', 'size' => 'XL', 'frame_type' => 'b', 'label' => 'VAE XL-2', 'status' => 'OK', 'notes' => 'Autonomie ~80km'],
        ['id' => 'vae-xl-03', 'category' => 'VAE', 'size' => 'XL', 'frame_type' => 'h', 'label' => 'VAE XL-3', 'status' => 'OK', 'notes' => 'Autonomie ~80km'],
        ['id' => 'vae-xl-04', 'category' => 'VAE', 'size' => 'XL', 'frame_type' => 'h', 'label' => 'VAE XL-4', 'status' => 'OK', 'notes' => 'Autonomie ~80km'],

        // =====================================================================
        // VTC - Vélos Tout Chemin (25 unités)
        // =====================================================================

        // VTC Taille S (5 unités)
        ['id' => 'vtc-s-01', 'category' => 'VTC', 'size' => 'S', 'frame_type' => 'b', 'label' => 'VTC S-1', 'status' => 'OK', 'notes' => null],
        ['id' => 'vtc-s-02', 'category' => 'VTC', 'size' => 'S', 'frame_type' => 'b', 'label' => 'VTC S-2', 'status' => 'OK', 'notes' => null],
        ['id' => 'vtc-s-03', 'category' => 'VTC', 'size' => 'S', 'frame_type' => 'b', 'label' => 'VTC S-3', 'status' => 'OK', 'notes' => null],
        ['id' => 'vtc-s-04', 'category' => 'VTC', 'size' => 'S', 'frame_type' => 'h', 'label' => 'VTC S-4', 'status' => 'HS', 'notes' => 'Roue voilée'],
        ['id' => 'vtc-s-05', 'category' => 'VTC', 'size' => 'S', 'frame_type' => 'h', 'label' => 'VTC S-5', 'status' => 'OK', 'notes' => null],

        // VTC Taille M (7 unités)
        ['id' => 'vtc-m-01', 'category' => 'VTC', 'size' => 'M', 'frame_type' => 'b', 'label' => 'VTC M-1', 'status' => 'OK', 'notes' => null],
        ['id' => 'vtc-m-02', 'category' => 'VTC', 'size' => 'M', 'frame_type' => 'b', 'label' => 'VTC M-2', 'status' => 'OK', 'notes' => null],
        ['id' => 'vtc-m-03', 'category' => 'VTC', 'size' => 'M', 'frame_type' => 'b', 'label' => 'VTC M-3', 'status' => 'OK', 'notes' => null],
        ['id' => 'vtc-m-04', 'category' => 'VTC', 'size' => 'M', 'frame_type' => 'h', 'label' => 'VTC M-4', 'status' => 'OK', 'notes' => null],
        ['id' => 'vtc-m-05', 'category' => 'VTC', 'size' => 'M', 'frame_type' => 'h', 'label' => 'VTC M-5', 'status' => 'OK', 'notes' => null],
        ['id' => 'vtc-m-06', 'category' => 'VTC', 'size' => 'M', 'frame_type' => 'h', 'label' => 'VTC M-6', 'status' => 'OK', 'notes' => null],
        ['id' => 'vtc-m-07', 'category' => 'VTC', 'size' => 'M', 'frame_type' => 'h', 'label' => 'VTC M-7', 'status' => 'OK', 'notes' => null],

        // VTC Taille L (8 unités)
        ['id' => 'vtc-l-01', 'category' => 'VTC', 'size' => 'L', 'frame_type' => 'b', 'label' => 'VTC L-1', 'status' => 'OK', 'notes' => null],
        ['id' => 'vtc-l-02', 'category' => 'VTC', 'size' => 'L', 'frame_type' => 'b', 'label' => 'VTC L-2', 'status' => 'OK', 'notes' => null],
        ['id' => 'vtc-l-03', 'category' => 'VTC', 'size' => 'L', 'frame_type' => 'b', 'label' => 'VTC L-3', 'status' => 'OK', 'notes' => null],
        ['id' => 'vtc-l-04', 'category' => 'VTC', 'size' => 'L', 'frame_type' => 'b', 'label' => 'VTC L-4', 'status' => 'OK', 'notes' => null],
        ['id' => 'vtc-l-05', 'category' => 'VTC', 'size' => 'L', 'frame_type' => 'h', 'label' => 'VTC L-5', 'status' => 'HS', 'notes' => 'Freins à changer'],
        ['id' => 'vtc-l-06', 'category' => 'VTC', 'size' => 'L', 'frame_type' => 'h', 'label' => 'VTC L-6', 'status' => 'OK', 'notes' => null],
        ['id' => 'vtc-l-07', 'category' => 'VTC', 'size' => 'L', 'frame_type' => 'h', 'label' => 'VTC L-7', 'status' => 'OK', 'notes' => null],
        ['id' => 'vtc-l-08', 'category' => 'VTC', 'size' => 'L', 'frame_type' => 'h', 'label' => 'VTC L-8', 'status' => 'OK', 'notes' => null],

        // VTC Taille XL (5 unités)
        ['id' => 'vtc-xl-01', 'category' => 'VTC', 'size' => 'XL', 'frame_type' => 'b', 'label' => 'VTC XL-1', 'status' => 'OK', 'notes' => null],
        ['id' => 'vtc-xl-02', 'category' => 'VTC', 'size' => 'XL', 'frame_type' => 'b', 'label' => 'VTC XL-2', 'status' => 'OK', 'notes' => null],
        ['id' => 'vtc-xl-03', 'category' => 'VTC', 'size' => 'XL', 'frame_type' => 'h', 'label' => 'VTC XL-3', 'status' => 'OK', 'notes' => null],
        ['id' => 'vtc-xl-04', 'category' => 'VTC', 'size' => 'XL', 'frame_type' => 'h', 'label' => 'VTC XL-4', 'status' => 'OK', 'notes' => null],
        ['id' => 'vtc-xl-05', 'category' => 'VTC', 'size' => 'XL', 'frame_type' => 'h', 'label' => 'VTC XL-5', 'status' => 'OK', 'notes' => null],
    ],

];
