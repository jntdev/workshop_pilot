# Ticket 3 – Frontend : AgendaStore (instance + localStorage)

## Objectif
Créer le service `AgendaStore` qui fournit les données de l’agenda selon la hiérarchie Instance → LocalStorage → Réseau.

## Étapes
1. **Structure**
   - Nouveau fichier `resources/js/Stores/agendaStore.ts`.
   - Exporter un singleton avec :
     - `load(serverVersion: number, initialProps: LocationPageProps): Promise<AgendaData>`
     - `snapshot(): AgendaSnapshot`
     - `setState(data: AgendaData, version: number): void`
     - `rollback(snapshot: AgendaSnapshot): void`
2. **Sources**
   - **Instance** : champs privés `currentData`, `currentVersion`.
   - **LocalStorage** : clé `agenda_snapshot_v1` (`{ version, saved_at, data }`).
   - **Réseau** : `fetch('/api/location/full')`.
   - La méthode `load` renvoie aussi la provenance (`'instance' | 'localStorage' | 'network'`) pour du logging.
3. **Gestion du périmètre**
   - Si `agenda_snapshot_v1` dépasse 5 Mo, limiter les données sérialisées à une fenêtre (ex : J‑90/+90) ou compresser (`JSON.stringify` + `lz-string`).
   - Exposer une option `store.setWindow({ start, end })` pour faciliter la logique future.
4. **Intégration LocationIndex**
   - Au lieu d’utiliser directement `initialReservations`, appeler `agendaStore.load(pageProps.agenda_version, pageProps)`.
   - Afficher les données retournées par le store (mêmes structures que précédemment).
5. **Gestion d’erreurs**
   - Si le fetch réseau échoue, laisser remonter l’erreur après avoir affiché un message “Impossible de synchroniser les données”.
   - `localStorage` doit être try/catch (Safari privé, quota, etc.).
6. **Tests**
   - Tests unitaires (Jest) sur le store : chargement depuis instance, localStorage, réseau.
   - Test e2e Cypress : recharger la page, vérifier que la seconde ouverture lit bien depuis le cache (moins de 50 ms).

## Acceptance Criteria
- `Location/Index` s’hydrate depuis `AgendaStore` et fonctionne même en mode offline tant que la version en cache correspond ; dès que le réseau revient, une vérification est effectuée.
- La clé `agenda_snapshot_v1` est écrite après chaque fetch réseau, en respectant la limite de taille (<5 Mo) ou en réduisant la fenêtre stockée.
- Les logs réseau montrent qu’on ne consomme `/api/location/full` qu’en cas de mismatch de version.
