# Résultat ergonomique

## Accès
- Header de la page Location : bouton primaire centré « Voir aujourd’hui ».  
- Clic → transition Inertia vers la page Planning, avec retour possible via breadcrumb “← Retour à la grille”.

## Planning journalier
- Layout 2 colonnes :
  - **Départs** (à gauche)  
  - **Retours** (à droite)
- Chaque colonne est séparée en sections :  
  1. Livraison (badge camion)  
  2. Remise sur place (badge boutique)

### Carte réservation
- Bande colorée (même couleur que sur la grille) + statut.  
- Nom client + téléphone + icône “appel”.  
- Liste des vélos : `label court – taille/cadre`, regroupée si plusieurs exemplaires.  
- Bloc logistique :  
  - Livraison → adresse + créneau.  
  - Sur place → “Préparer au comptoir” + éventuels commentaires.  
- Boutons d’action rapides : “Voir fiche” (ouvre la réservation dans le panneau droit), “Marquer comme prêt” (future évolution).

## Sélecteur de date
- Barre sticky en haut avec :  
  - Boutons `J-1`, `Aujourd'hui`, `J+1`.  
  - Input `type="date"` pour sélectionner une date précise.  
  - Tag “X départs · Y retours”.

## États particuliers
- Aucune réservation : carte vide “Pas de départ/retour ce jour-là”.  
- Réservations en attente d’acompte : badge orange “Acompte ?” pour attirer l’attention.  
- Réservation en retard (date retour = aujourd’hui mais statut ≠ retourné) : badge rouge “Suivi”.
