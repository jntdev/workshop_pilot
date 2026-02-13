# 2 — Dates & logistique

## Dates clés
- `date_contact` (date/heure du premier échange) : préremplie avec `today` à la création mais modifiable pour tracer les demandes arrivées plus tôt.
- `date_reservation` (début de location) et `date_retour` (fin prévue) : sélection via un double date-picker autonome, sans dépendance au tableau de gauche.
- Validation : `date_retour ≥ date_reservation`, avertissement si l'écart dépasse 30 jours (cas rares).

## Logistique livraison / récupération
- Deux checkboxes indépendantes : `Livraison nécessaire` et `Récupération nécessaire`.
- Lorsque `Livraison` est cochée :
  - Affichage d'un bloc champs requis : `adresse_livraison` (textarea courte) + `contact_sur_place` + `créneau souhaité`.
  - Ce bloc reste visible tant que la case est active.
- Lorsque `Récupération` est cochée :
  - Champs similaires (`adresse_récupération`, `contact_sur_place`, `créneau`), préremplis avec l'adresse de livraison pour gagner du temps.
- Si aucune case n'est cochée, on considère une remise sur place par défaut (information rappelée via un tag gris).

## Cohérence (hors périmètre)
- Aucune interaction avec le tableau n'est prévue dans cette itération. Toute logique de synchronisation sera définie plus tard côté produit.
