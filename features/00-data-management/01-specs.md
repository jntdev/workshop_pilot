# 00 - Data Management (Agenda Location)

Spécification détaillée de la stratégie de mise en cache/versionning pour l’agenda Location. Cette feature définit comment réduire les téléchargements massifs tout en garantissant la cohérence des données entre les onglets et sessions.

## 1. Objectifs

- **Éviter les rechargements complets** tant que la version de l’agenda (côté serveur) n’a pas évolué.
- **Partager l’état** entre onglets : priorité à l’instance en mémoire (si déjà hydratée), sinon lecture depuis `localStorage`, sinon téléchargement.
- **Gérer un fallback fiable** : toute erreur serveur annule la mutation côté client et affiche un message explicite.
- **Visibilité utilisateur** : loader global lors des synchronisations complètes et indication si un rollback survient.

## 2. Nouveaux concepts

| Nom | Description |
| --- | --- |
| `agenda_version` | Entier maintenu côté backend, incrémenté à chaque mutation impactant l’agenda (création/maj/suppression de réservation, modification d’un vélo influençant l’affichage, etc.). |
| `AgendaStore` | Service front (singleton) qui gère les données + version en suivant l’ordre de priorité Instance → LocalStorage → Réseau. |
| `AgendaSnapshot` | Structure sérialisée dans `localStorage` (`agenda_snapshot_v1`) contenant `{ version, timestamp, reservations, bikes, categories, sizes }`. |

## 3. Flux de chargement

1. Au boot de `Location/Index`, on interroge `AgendaStore.getState()`.
2. `AgendaStore` applique l’ordre suivant :
   - **Instance** : si une instance a déjà été hydratée ET `agenda_version` local == `agenda_version` envoyé dans les props Inertia, on renvoie immédiatement les données, sans I/O supplémentaire.
   - **LocalStorage** : sinon, on lit `agenda_snapshot_v1`. Si la version correspond à celle des props, on hydrate l’instance avec ces données et on évite le réseau.
   - **Réseau** : si aucune des sources n’est valide (absente ou version différente), on affiche le loader pleine largeur et on fetch l’ensemble des datas (endpoint dédié). Une fois reçu, on hydrate instance + localStorage et on masque le loader.
3. L’état final (instance + localStorage) stocke toujours la version utilisée, ce qui permet de détecter rapidement un décalage lors des prochains boots.

### Loader

- Loader overlay sur la zone agenda (table + panneaux). Texte recommandé : `Synchronisation des données location…`.
- S’affiche tant que `AgendaStore` est en phase « fetch complet ».
- Désactivé si lecture mémoire ou localStorage (latence quasi nulle).

## 4. API / Backend

1. **Expose `agenda_version`** :
   - Ajout à la prop Inertia `Location/Index`.
   - Ajout dans `GET /api/location/version` (endpoint ultra léger pour vérif périodique).
   - Chaque réponse JSON majeure (`/api/reservations/window`, `/api/location/planning`, futures mutations) doit inclure la version courante pour permettre la détection de drift.
2. **Incrément de version** :
   - Hook central (ex: observer Eloquent sur `Reservation` + `Bike`) ou service `AgendaVersioner::bump()` déclenché dans les commandes/mutations existantes.
3. **Endpoint “full dump”** :
   - `GET /api/location/full` → retourne `agenda_version` + toutes les données initiales nécessaires (équivalent du payload actuel de la page).
   - Authentification identique au reste des endpoints `/api`.

## 5. Gestion locale (AgendaStore)

### Priorité des sources

1. **Instance singleton** (ex: module TS exportant `const agendaStore = new AgendaStore()`).
2. **`localStorage`** : clé `agenda_snapshot_v1`. On stocke aussi `saved_at` pour debug (non bloquant).
3. **Réseau** : via `fetch('/api/location/full')`.

### Responsabilités

- `getState(serverVersion, initialProps)` :
  - Compare `serverVersion` (prop Inertia) à `instance.version`.
  - Si mismatch → check localStorage.version.
  - Si mismatch → fetch complet.
  - Retourne `{ data, source }` où `source ∈ { 'instance', 'localStorage', 'network' }` pour du logging éventuel.
- `setState(newData, version)` :
  - Met à jour l’instance en mémoire.
  - Sérialise les données dans `localStorage`.
- `rollback(snapshot)` :
  - Réinsère l’ancien snapshot (utilisé si mutation échoue).

## 6. Mutations & optimisme

### Processus

1. **Avant mutation** : `const previous = agendaStore.snapshot();`.
2. **Optimistic update** :
   - Met à jour l’instance + localStorage avec les modifications attendues.
   - L’UI reflète immédiatement le changement.
3. **Appel API** :
   - On reste en version `N`. Le serveur traite, incrémente en `N+1`.
   - La réponse inclut la nouvelle version.
4. **Succès** :
   - On déclenche `agendaStore.setState(response.data, response.version)`.
   - On affiche éventuellement un toast de confirmation.
5. **Erreur** :
   - `agendaStore.rollback(previous)`.
   - Toast d’erreur « Une erreur est survenue, les données ont été restaurées ».

### Cas spécifiques

- Si l’API échoue mais que la réponse contient tout de même une version plus récente (ex: validation partielle), on force quand même une resynchronisation complète pour éviter les états incertains.
- Pour les actions n’ayant pas d’impact visuel immédiat (ex: changement d’acompte), on peut rester en mode pessimiste mais on doit quand même mettre à jour la version locale après succès.

## 7. Détection de drift multi-onglets

- Poll léger (ex: `setInterval` 60 s) ou `visibilitychange` → appel `GET /api/location/version`.
- Si version distante > version locale, on déclenche le loader + fetch complet (même comportement que sur mismatch initial).
- L’onglet courant affiche un bandeau discret « Données mises à jour sur un autre onglet, synchronisation… » le temps du refresh.

## 8. UX & États

| Situation | Indicateur |
| --- | --- |
| Synchronisation complète | Loader plein écran agenda |
| Drift détecté | Banner info + loader |
| Rollback suite à erreur | Toast error `Impossible d’enregistrer – vos données n’ont pas changé.` |
| Lecture depuis cache local | Optionnel : badge subtil « Chargé depuis cache local (en cours de vérif) » jusqu’à confirmation de version. |

## 9. Checklist de livraison

1. Backend
   - [ ] Créer table/colonne ou config stockant `agenda_version`.
   - [ ] Incrémenter la version dans toutes les mutations pertinentes.
   - [ ] Exposer la version dans les props Inertia + endpoints.
   - [ ] Créer `GET /api/location/full` + `GET /api/location/version`.
2. Frontend
   - [ ] Implémenter `AgendaStore` (instance + localStorage).
   - [ ] Ajuster `Location/Index` pour consommer `AgendaStore`.
   - [ ] Ajouter loader + bandeau info + gestion des sources.
   - [ ] Implémenter optimistic updates + rollback.
   - [ ] Mettre en place le polling/version check.
3. QA
   - [ ] Tester multi-onglets (création dans A, B détecte).
   - [ ] Simuler erreurs API pour valider rollback.
   - [ ] Vérifier persistance localStorage + expiration (ex: vider storage → fetch).
   - [ ] Mesurer taille du snapshot pour s’assurer que localStorage (<5 Mo) suffit.

---

Cette feature formalise la couche de gestion des données afin de limiter les requêtes et garantir une UX cohérente, tout en préparant le terrain pour des évolutions futures (diff incrémental, synchronisation offline, etc.).

## 10. Points d’attention supplémentaires

- **Race conditions** : l’incrément de version doit utiliser une requête atomique (`UPDATE … RETURNING` ou lock pessimiste `SELECT … FOR UPDATE`) afin d’éviter deux bumps consécutifs qui produiraient la même valeur.
- **Périmètre du snapshot** : si le volume de données croît trop, ne pas hésiter à restreindre le cache à une fenêtre (ex : J‑90/+90) pour rester < 5 Mo, voire compresser les payloads JSON.
- **Invalidation hors agenda** : toute modification admin sur un vélo, une catégorie ou une taille déclenche aussi `AgendaVersioner::bump`.
- **Offline** : lorsqu’aucune requête réseau n’est possible, on autorise la lecture du cache local “tel quel”. Dès que la connectivité revient, on compare la dernière version connue et, en cas de différence, on force une synchronisation complète (loader obligatoire).
