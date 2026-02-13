# 3 — Sélection des vélos & accessoires

## Principes généraux
- Bloc présenté sous forme d'accordéon "Vélos & accessoires" (ouvert par défaut) composé de deux sous-sections : choix des vélos et accessoires associés.
- Toutes les données proviennent de `features/bikeRentals/bikes.config.ts` (mêmes définitions que pour le tableau). Ce fichier gagne deux propriétés supplémentaires :
  - `accessoires`: liste d'objets `{ id, label, compatibleSizes?, defaultQuantity }`.
  - `maxQuantity`: nombre de vélos disponibles simultanément pour ce modèle.

## Sélection des vélos
- Liste scrollable des modèles (triée par catégorie puis taille). Chaque carte contient :
  - `label` (ex. "VAE • Taille M") + badges `cadre bas/haut`, `status`.
  - Input numérique `quantité demandée` (min 0, max `maxQuantity`). Valeur initiale 0.
  - Tag rappelant les dates sélectionnées pour vérifier les chevauchements.
- Lorsque la quantité passe >0, une puce récapitulative s'ajoute en haut du formulaire (ex. `2× VAE M`).

## Accessoires
- Section inférieure affichant une carte par type d'accessoire louable. Les cartes sont générées à partir de la propriété `accessoires` du modèle sélectionné :
  - Si un accessoire est compatible avec plusieurs tailles, il apparaît une seule fois avec un sélecteur de modèle (multi-select) ou un label "compatible tous modèles".
  - Input numérique par accessoire (`min 0`, `default 0`), aligné horizontalement pour une saisie rapide.
  - Rappel du stock disponible (si configuré) pour éviter les sur-réservations.
- Le module calcule automatiquement le total de vélos + accessoires sélectionnés et pourra servir à remplir le devis ultérieurement.

## UX/validations
- Tant qu’aucun vélo n’est choisi, un message d’aide reste visible (« Sélectionnez au moins un vélo pour enregistrer la réservation »).
- Les changements mettent à jour une structure `reservationItems` (tableau d’objets) stockée localement et envoyée au backend lors de la sauvegarde.
