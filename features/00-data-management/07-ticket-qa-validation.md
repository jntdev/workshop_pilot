# Ticket 6 – QA & Validation croisée

## Objectif
Vérifier l’ensemble de la chaîne (backend ↔ frontend ↔ cache) avant mise en production.

## Étapes
1. **Scénarios multi-onglets**
   - Ouvrir deux sessions authentifiées.
   - Créer/éditer/supprimer dans l’onglet A → vérifier que l’onglet B déclenche le bandeau et resynchronise.
2. **Cache local**
   - Charger l’agenda, fermer l’onglet, couper le réseau, rouvrir : l’agenda doit s’afficher grâce au cache (jusqu’à détection mismatch). Noter que tant que le réseau est coupé aucune vérification de version n’est possible.
   - Vider `localStorage` et recharger → fetch réseau déclenché automatiquement.
   - Vérifier la taille réelle du snapshot (<5 Mo). Si >5 Mo, créer un ticket pour restreindre la fenêtre stockée.
3. **Optimistic update**
   - Utiliser un proxy ou Cypress pour renvoyer 500/422 : vérifier rollback + toast.
4. **Performance**
   - Mesurer la taille de `agenda_snapshot_v1` (doit rester < 5 Mo).
   - Mesurer le temps de `GET /api/location/full` (objectif < 1 s).
5. **Sécurité**
   - Vérifier que les endpoints `/api/location/full` et `/api/location/version` sont bien protégés.

## Acceptance Criteria
- Tous les scénarios ci-dessus validés et documentés.
- Rapport QA signé avant merge.
