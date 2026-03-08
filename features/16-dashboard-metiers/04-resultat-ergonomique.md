# Résultat ergonomique

```
| Vente   | Atelier | Location |
| ------- | ------- | -------- |
| CA      | CA      | CA       |
| Marge   | Marge   | Marge    |
| Panier  | Panier  | Panier   |
```

## Structure
- Trois colonnes de même largeur, chacune composée de :
  1. Header : titre + sous-texte (“Mois en cours”) + bouton “Voir détail”.
  2. Bloc KPI :  
     - CA (valeur principale, format monétaire, variation vs N-1 optionnelle).  
     - Marge brute (ligne secondaire).  
     - Panier moyen (ligne secondaire).
- Indicateur d’état : si les données sont indisponibles (ex. marge Location), afficher une mention “À venir (module achats)”.

## Interactions
- Cliquer sur le titre ou le bouton ouvre la page métier correspondante.  
- Survol d’un KPI affiche un tooltip précisant la période et la formule (ex. “Panier moyen = CA / # réservations confirmées”).  
- Layout responsive : stack vertical < 1024px (ordre Vente → Atelier → Location).

## Mode comptoir (Feature 14)
- Les colonnes restent visibles mais seules les valeurs “publics” s’affichent : CA global et Panier, pas de marge.  
- Message en bas : “Données sensibles masquées — repasser en mode Atelier/Admin pour voir les marges”.
