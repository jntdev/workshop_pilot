# 7 — Collaboration multi‑utilisateurs (UX)

Objectif : permettre à deux opérateurs (ou plus) de gérer les réservations en simultané sans se parasiter, uniquement via des mécanismes d’interface (aucun verrouillage côté backend pour l’instant).

## 1. Mise à jour en temps quasi‑réel
- Intégrer un canal de diffusion (`reservation.updated`, `reservation.created`, `reservation.deleted`) via Laravel Echo + Redis/Pusher.  
- Chaque événement transporte le même payload que la page principale (`LoadedReservation`).  
- Côté front, `LocationIndex` remplace/ajoute/supprime l’entrée correspondante et régénère les index (`reservedCellsIndex`, `reservationsById`) sans recharger la page.  
- Afficher un timestamp “Dernière mise à jour HH:MM” et un bouton “Rafraîchir” manuel pour rassurer les opérateurs.

## 2. Indication d’édition en cours (verrou UX)
- Lorsqu’un utilisateur ouvre le formulaire en mode édition, émettre `reservation.editing.started` avec `{ reservation_id, user_name, started_at }`.  
- Les autres clients affichent un badge/tooltip “En cours d’édition par Alice (depuis 14:32)” à côté de la réservation concernée et empêchent seulement l’accès rapide (message d’info, pas de blocage).  
- Quand l’utilisateur ferme le formulaire ou après un timeout (2 minutes d’inactivité), diffuser `reservation.editing.ended` pour retirer l’indicateur.

## 3. Draft partagé optionnel
- À l’ouverture d’un nouveau draft, diffuser `reservation.draft.started { draft_id, user_name, color, selected_cells }`.  
- Les autres voient les cellules en surbrillance translucide + tooltip “Sélection par Alice — non confirmée”.  
- À chaque modification de sélection, envoyer un delta léger (`added_cells`, `removed_cells`).  
- Quand le draft est confirmé ou annulé, diffuser `reservation.draft.ended` pour nettoyer les surbrillances.  
- Cette brique est optionnelle mais recommandée pour éviter d’annoncer une dispo erronée pendant qu’un collègue choisit les mêmes vélos.

## 4. Feedback utilisateur
- Bandeau haut :  
  - “Vous êtes en ligne” (vert) / “Connexion perdue — rechargement recommandé” (rouge).  
  - Dernière synchro.  
- Notifications légères (toast) quand une réservation est créée/modifiée par un autre opérateur : “Bob vient d’ajouter VTC L‑3 du 5 au 8 avril”.  
- Bouton global “Mettre à jour maintenant” qui force `router.reload({ only: ['reservations'] })` si l’utilisateur doute de son état.

## 5. Plan technique
1. **Backend** :  
   - Configurer broadcasting (Redis ou Pusher).  
   - Émettre les événements `ReservationCreated`, `ReservationUpdated`, `ReservationDeleted`, `ReservationEditingStarted/Ended`, `ReservationDraftStarted/Updated/Ended`.  
   - Mettre en place un job qui purge les verrous d’édition expirés (failsafe).
2. **Frontend** :  
   - Créer un hook `useReservationChannel()` responsable de l’abonnement, de la fusion des données et de la gestion des indicateurs.  
   - Étendre `useReservationDraft` pour accepter les événements “externes” (drafts d’autres utilisateurs).  
   - Ajouter un store léger pour les états réseau (online/offline, timestamp).

## 6. Tests & validations
- Tests d’intégration backend : vérifier que chaque action déclenche l’événement attendu.  
- Tests unitaires front : fusion de réservations, suppression, gestion de drafts “externes”.  
- Session QA multi‑navigateurs (ex. 2 fenêtres locale + 1 sur un autre poste) pour vérifier la fluidité et les messages.  
- Critère d’acceptation clé : “Alice réserve VAE M‑02 → l’événement apparaît côté Bob en <2s, la grille se met à jour sans reload, Bob sait que la dispo n’existe plus.”

## Évolutions possibles
- Ajout ultérieur d’un verrouillage pessimiste côté backend (colonne `locked_by`) si les conflits deviennent critiques.  
- Historique des actions (log) pour suivre qui a modifié quoi et quand.
