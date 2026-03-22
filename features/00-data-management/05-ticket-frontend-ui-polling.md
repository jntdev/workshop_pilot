# Ticket 4 – Frontend : Loader, bandeau info & détection de drift

## Objectif
Informer l’utilisateur pendant les synchronisations et détecter automatiquement les décalages de version (multi‑onglets).

## Étapes
1. **Loader global**
   - Ajouter un composant `LocationSyncOverlay` affiché au-dessus de la table quand `agendaStore` est en mode `loading`.
   - Texte : `Synchronisation des données location…`.
   - Bloquer les interactions (overlay semi-transparent).
2. **Bandeau drift**
   - Banner (type `alert-info`) qui apparaît quand on détecte une version distante > version locale.
   - Message : `L’agenda a été modifié dans un autre onglet. Synchronisation en cours…`.
3. **Polling / Event**
   - Hook `useAgendaVersionWatcher` :
     - Sur `visibilitychange` + intervalle (60 s).
     - Appelle `GET /api/location/version`.
     - Si version > locale → déclenche `agendaStore.forceRefresh()` (fetch + loader).
4. **Logs & métriques**
   - Ajouter `console.info` (en dev seulement) pour indiquer la source des données (`instance/localStorage/network`) et les drifts détectés.
5. **Tests**
   - Cypress : ouvrir deux onglets, modifier dans A, vérifier que B affiche le bandeau puis se rafraîchit.
   - Snapshot test du loader/bandeau.

## Acceptance Criteria
- Loader visible uniquement lors des fetchs réseau complets.
- Drift détecté sous 60 s OU dès que l’onglet revient en focus.
- L’utilisateur est informé pendant toute la durée de la resynchronisation.
