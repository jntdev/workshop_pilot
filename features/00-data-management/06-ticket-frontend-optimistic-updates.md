# Ticket 5 – Frontend : Optimistic update + Rollback

## Objectif
Appliquer immédiatement les modifications côté UI/Store tout en restant capable de revenir en arrière si l’API échoue.

## Étapes
1. **Snapshot**
   - Ajouter `agendaStore.snapshot()` qui retourne `{ version, data }`.
   - Utilisé avant tout appel mutateur (création, update, delete, changement de couleur, etc.).
2. **OptimisticApply**
   - Chaque action Redux-like (ex : `useReservationDraft`) doit disposer d’une fonction `applyOptimisticChange(payload)` qui met à jour `agendaStore` (instance + localStorage) et déclenche un re-render.
   - Le visuel fait confiance à ces données tant que l’API n’a pas répondu.
3. **API Response**
   - Sur succès : remplacer `agendaStore` par les données officielles renvoyées (ainsi que la version `N+1`).
   - Sur échec :
     - `agendaStore.rollback(snapshot)`.
     - Toast erreur : `Échec de l’enregistrement. Vos données ont été restaurées.`
     - Revenir à l’écran précédent (fermer formulaire si besoin).
4. **Gestion des erreurs 409**
   - Si l’API indique un conflit/version dépassée, forcer une resynchronisation complète (`agendaStore.forceRefresh()`).
5. **Tests**
   - Tests unitaires : simulateur d’échec → rollback effectif.
   - Cypress : créer une réservation en interceptant l’appel API pour renvoyer 500 → vérifier que l’UI revient à l’état initial.

## Acceptance Criteria
- L’utilisateur voit instantanément les modifications sans attendre la réponse serveur.
- En cas d’erreur, l’état revient exactement à celui d’avant action.
- La version locale n’est modifiée qu’après confirmation serveur.
