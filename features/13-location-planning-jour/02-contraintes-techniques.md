# Contraintes techniques

1. **Chargement sélectif**  
   - La route `/location/planning` doit filtrer uniquement les réservations dont `date_reservation` ou `date_retour` correspond à la date demandée.  
   - Les réservations annulées sont exclues.  
   - Requêtes : `Reservation::with(['client', 'items.bikeType'])`.

2. **Données nécessaires**  
   - Client (nom, téléphone).  
   - Statut, commentaires internes.  
   - Logistique (livraison, adresse, créneau, remise sur place).  
   - Liste des vélos (via `items.bikeType` pour récupérer label/taille/cadre).  
   - Couleur (`reservation.color`) pour cohérence visuelle.

3. **Navigation Inertia**  
   - Le bouton « Voir aujourd’hui » reste sur la page Location (pas de reload complet).  
   - Sur la page Planning, le datepicker et les boutons J−1/J+1 déclenchent `router.get('/location/planning', { date })` tout en conservant l’état des filtres.

4. **Performance**  
   - Les listes quotidiennes restent petites (<50 réservations), pas de pagination nécessaire.  
   - Prévoir un cache court (ex. 5 minutes) si la route devient très sollicitée.

5. **Tests**  
   - Feature test pour la route (dates limites).  
   - Tests front pour le composant de navigation et le groupement Livraison/Sur place.
