# Feature 20 : Suivi des pieces a commander

## Contexte

Aujourd'hui, pour savoir quelles pieces doivent etre commandees, il faut rouvrir tous les devis atelier en cours et relire chaque ligne une par une.

Ce fonctionnement pose trois problemes :
- la charge mentale est trop forte au moment de passer commande
- le risque d'oubli est eleve, surtout si plusieurs devis sont ouverts en parallele
- il n'existe aucun suivi centralise entre "piece a commander", "piece commandee" et "piece recue"

## Objectif produit

Ajouter un suivi simple d'approvisionnement directement sur les lignes de devis atelier :
- une ligne de devis peut etre marquee "a commander"
- toutes les lignes marquees remontent dans une vue centralisee de type "panier"
- depuis ce panier, l'utilisateur peut suivre l'etat de chaque piece : a commander, commandee, recue

## Decision de perimetre

La feature 20 ne doit pas demarrer par un vrai stock atelier.

On traite d'abord un besoin operationnel de suivi de commande rattache aux devis, pas un besoin de gestion d'inventaire.

### Pourquoi ne pas faire un stock maintenant
- le besoin exprime est "ne rien oublier au moment de commander", pas "connaitre un stock theorique"
- un stock apporte tout de suite des questions plus lourdes : entrees/sorties, corrections manuelles, inventaire, seuils, valorisation, casse, pieces utilisees hors devis
- tant que le flux de commande n'est pas stabilise, un stock risque d'ajouter de la complexite sans resoudre le probleme principal

### Ce que l'on prepare implicitement
- un vocabulaire clair sur les statuts d'approvisionnement
- des donnees propres par ligne de devis
- une base exploitable plus tard si une feature de stock devient pertinente

## Hors perimetre

- gestion d'un catalogue fournisseur
- gestion des bons de commande fournisseur
- suivi de prix d'achat historique
- decrement/increment de stock atelier
- reservation de pieces deja en stock
- alertes de stock minimum
