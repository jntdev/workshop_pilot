# Résultat ergonomique

## Section “Suivi des paiements”
- Placée dans “Finances & Statut”, juste après les champs TTC/Acompte.  
- Contenu :
  - Tag `Total encaissé : X € / Total TTC : Y €`
  - Bouton `Ajouter un paiement`
  - Tableau :
    | Date | Montant | Mode | Payé par | Notes | Actions |
    |------|---------|------|---------|-------|---------|
  - Chaque ligne peut être éditée ou supprimée (icône crayon/poubelle).

## Création d’une ligne
- Modal ou inline form :
  - Montant (input numeric)  
  - Mode (select : CB, Espèces, Chèque, Virement, Autre)  
  - Date/heure (`paid_at`)  
  - Payé par (texte libre)  
  - Commentaire (facultatif)
- Bouton `Ajouter` → la ligne apparaît instantanément.

## Calcul automatique
- `totalEncaisse` = somme des montants.  
- `resteDu = prix_total_ttc - totalEncaisse`.  
- Badge couleur :
  - Vert si total encaissé == TTC.  
  - Orange si partiel.  
  - Rouge si dépassement (rare, mais on bloque l’ajout d’un montant > reste dû, sauf override futur).

## Interaction avec le statut
- Si l’utilisateur bascule `statut = Payé` mais `totalEncaisse < TTC`, afficher un message bloquant “Il manque X € pour passer en payé”.  
- Lorsque le total atteint TTC, montrer un callout “Tous les paiements sont enregistrés – vous pouvez passer en Payé”.

## Mode Comptoir (feature 14)
- En mode public, masquer la table détaillée et afficher seulement :  
  - `Total encaissé / Reste dû`  
  - Badge “Détails disponibles en mode Atelier/Admin”.

## Impression / export
- Prévoir un export PDF/print (v2) listant les paiements afin que la compta puisse suivre les encaisses.
