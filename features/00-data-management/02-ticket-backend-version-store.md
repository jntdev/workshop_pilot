# Ticket 1 – Backend : Stockage & incrément de la version

## Objectif
Créer la source de vérité `agenda_version` côté backend et garantir son incrémentation automatique à chaque mutation des données impactant l’agenda.

## Étapes
1. **Schema**
   - Ajouter une table `agenda_meta` (id=1) ou une colonne `agenda_version` dans une table de configuration existante. Type `unsignedBigInteger`.
   - Migration initiale : version = `1`.
2. **Service**
   - Créer `App\Services\AgendaVersioner` avec :
     - `public function current(): int`
     - `public function bump(): int` (incrémente et retourne la nouvelle valeur).
   - Service stocke en cache (configurable) pour limiter les hits DB.
3. **Points d’incrément & verrouillage**
   - Appeler `AgendaVersioner::bump()` pour toute modification qui change le rendu de l’agenda :
     - `ReservationController@store/update/destroy`.
     - Toute commande/schedule qui modifie `Reservation`, `Bike` ou les référentiels utilisés par la grille.
     - Seeders/scripts d’import (option `--no-version` pour éviter l’incrément si besoin).
   - `bump()` doit exécuter une requête atomique :
     - Soit `update agenda_meta set agenda_version = agenda_version + 1 returning agenda_version;`
     - Soit `select … for update` suivi d’un update.
   - Empêche les race conditions lorsque plusieurs mutations arrivent en parallèle.
4. **Tests**
   - Feature test : créer une réservation => version++.
   - Test de concurrence (deux bumps successifs) pour vérifier l’absence de race condition (utiliser `update ... returning` ou `select for update`).

## Acceptance Criteria
- `AgendaVersioner::current()` retourne toujours un entier >=1.
- Après chaque mutation, `current()` augmente strictement, même en cas de requêtes simultanées.
- Le service est injectable et utilisable dans les futures couches (endpoints, events, etc.).
