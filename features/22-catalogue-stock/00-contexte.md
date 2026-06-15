# Feature 22 : Catalogue articles et gestion du stock

## Contexte

Aujourd'hui, chaque ligne de devis atelier est saisie manuellement : titre, référence, prix d'achat, prix de vente. Rien n'est partagé entre les devis. Deux employés qui commandent le même pneu peuvent saisir des désignations différentes, des prix différents, des références différentes.

Le même problème existe côté maintenance flotte : quand un mécanicien ajoute une pièce sur un log de maintenance, il repart de zéro à chaque fois.

Ce fonctionnement pose trois problèmes :
- les marges varient selon l'employé qui fait le devis, sans règle commune
- il est impossible de savoir combien de pièces ont été achetées, consommées, ou restent en stock
- former un nouvel employé à faire un devis correct prend du temps, parce que les prix ne sont nulle part

## Objectif produit

Créer un catalogue normalisé de pièces et consommables utilisés par l'atelier. Ce catalogue sert de base commune pour :
- les lignes de devis atelier (QuoteLine)
- les logs de maintenance flotte (BikeMaintenanceLog)
- la gestion du stock atelier

Quand un employé crée une ligne dans un devis, il peut chercher dans le catalogue : la référence, la désignation, le prix d'achat et le prix de vente se pré-remplissent automatiquement. Il peut les ajuster si le cas particulier le justifie.

Les pièces hors catalogue restent possibles : saisie libre sans article_id. C'est le comportement actuel, conservé pour les commandes ponctuelles.

## Décisions d'architecture

### Catalogue = Articles avec 2 niveaux de catégories
- `ArticleCategory` : niveau 1 (ex. Pneus, Transmission, Freins, Éclairage)
- `ArticleSubcategory` : niveau 2, rattachée à une catégorie (ex. Pneus > Chambre à air, Pneus > Pneu route)
- `Article` : la pièce elle-même, rattachée à une sous-catégorie

### Stock = mouvements tracés
Le stock d'un article n'est pas un champ : c'est la somme de ses mouvements (`stock_movements`). Chaque entrée ou sortie est enregistrée avec sa source (polymorphique). Cela permet plus tard de brancher une API fournisseur sans refonte du modèle.

Types de mouvements :
- `manual_in` : entrée manuelle (réception physique, correction d'inventaire)
- `manual_out` : sortie manuelle (casse, perte, correction)
- `quote_consumption` : sortie lors de la finalisation d'un devis
- `maintenance_consumption` : sortie lors d'un log de maintenance

### Liaison avec les flux existants
- `QuoteLine` : ajout d'un `article_id` nullable. Si renseigné, les champs sont pré-remplis depuis l'article mais restent modifiables dans le devis.
- `BikeMaintenanceLog` : même logique, ajout d'un `article_id` nullable.

### Navigation dans le catalogue
Deux modes d'accès depuis une ligne :
1. **Autocomplete inline** : saisie de 3 caractères → liste déroulante des articles correspondants (référence ou désignation)
2. **Modale catalogue** : bouton dédié ouvre une modale pleine largeur avec arborescence catégorie/sous-catégorie à gauche, liste d'articles à droite, recherche en tête

## Hors périmètre (v1)

- Gestion des fournisseurs (table Supplier) — un champ texte libre `supplier` sur l'article suffit pour l'instant
- Bons de commande fournisseur formels
- Seuils de réapprovisionnement et alertes
- Multi-entrepôt
- Historique des prix d'achat
- API fournisseur (préparée structurellement, pas implémentée)
