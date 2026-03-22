# 6 — Limiter le chargement des réservations

## Contexte
Actuellement la route `/location` renvoie toutes les réservations de l’année en cours. Le volume va exploser et l’UI n’a besoin que des réservations proches de la date du jour. Nous voulons réduire la payload initiale sans perdre les réservations en cours qui chevauchent la période visible.

## Règles métier
1. **Fenêtre glissante**  
   - Charger les réservations dont `date_reservation` est comprise entre **J−15** et **J+30** (J = `today()`).  
   - Charger également toute réservation qui a commencé avant J−15 mais dont `date_retour` est ≥ J (donc toujours active ou à venir).  
   - Exclure les réservations terminées depuis plus de 15 jours et celles qui démarrent au-delà de +30 jours.
2. **Statut**  
   - Continuer d’exclure les réservations `annule`.  
   - Les autres statuts restent chargés.

## Tâches
1. **Adapter la requête Laravel**  
   - Calculer `startWindow = now()->subDays(15)` et `endWindow = now()->addMonth()` (ou 30 jours calendaires).  
   - Filtrer avec la condition composée :
     ```php
     Reservation::where('statut', '!=', 'annule')
         ->where(function ($query) use ($startWindow, $endWindow) {
             $query->whereBetween('date_reservation', [$startWindow, $endWindow])
                   ->orWhere(function ($q) use ($startWindow) {
                       $q->where('date_reservation', '<', $startWindow)
                         ->where('date_retour', '>=', now());
                   });
         });
     ```
   - Ajuster le mapping existant (client, items, selection) inchangé.
2. **Mettre à jour la doc / tests**  
   - Ajouter ce comportement dans `features/12-location-reservation/00-contexte.md` (section dépendances).  
   - Créer/adapter un test Feature qui seed des réservations aux bornes et vérifie que seules celles répondant à la règle sont présentes dans la réponse Inertia.

## Acceptance Criteria
1. En date du 16 février 2026 :  
   - Une réservation du 1er janvier → **non** chargée (car terminée depuis >15 jours).  
   - Une réservation du 10 février au 20 février → **chargée** (dans la fenêtre).  
   - Une réservation du 5 janvier au 25 février → **chargée** (terminaison future).  
   - Une réservation qui commence le 25 mars → **non** chargée (au-delà de +30 jours).  
2. Le front continue de recevoir les propriétés `reservations` sans changement de structure.  
3. Les requêtes Inertia affichent une taille de payload réduite (observé via Laravel debug bar).
