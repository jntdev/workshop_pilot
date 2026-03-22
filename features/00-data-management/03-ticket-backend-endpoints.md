# Ticket 2 – Backend : Exposition de la version & payload complet

## Objectif
Rendre `agenda_version` disponible partout et fournir un endpoint de dump complet pour le front.

## Étapes
1. **Props Inertia**
   - Dans `routes/web.php` (handler Location), ajouter `agenda_version` (via `AgendaVersioner::current()`) au tableau passé à `Inertia::render`.
2. **Endpoint version**
   - Route `GET /api/location/version` → JSON `{ "version": <int> }`.
   - Protégée par `auth:sanctum` ou middleware `auth`.
3. **Endpoint full dump**
   - `GET /api/location/full` → renvoie exactement les données actuelles de la page (`bikes`, `bikeCategories`, `bikeSizes`, `reservations`) + `version`.
   - Réutiliser les mêmes transformers que dans le handler Inertia pour éviter la divergence.
4. **Endpoints existants**
   - Ajouter `agenda_version` aux réponses JSON suivantes :
     - `/api/reservations/window`
     - `/api/location/planning`
     - Toute autre route touchée par l’agenda.
5. **Tests**
   - Vérifier que `GET /api/location/version` retourne le même entier que `AgendaVersioner::current()`.
   - `GET /api/location/full` retourne un payload complet et que les données sont identiques à celles de la page.
   - Tests sur `/api/reservations/window` pour s’assurer que la clé `version` existe toujours.

## Acceptance Criteria
- Tous les points d’entrée renvoient une clé `version`.
- Les nouvelles routes sont protégées et documentées.
- Les payloads sont sérialisés en moins de 1s pour la fenêtre de 45 jours actuelle (sinon ajouter pagination).
