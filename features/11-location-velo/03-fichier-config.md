# 3 - Fichier de configuration des vélos

- **Emplacement** : `features/bikeRentals/bikes.config.ts` (nouveau module dédié à la flotte).
- **Contenu** : export d'un tableau typé `BikeDefinition[]` avec les clés suivantes :
  - `id` : identifiant interne stable (`vae-s`, `vtc-m`).
  - `category` : `"VAE" | "VTC"` (ou futur type étendu).
  - `size` : `"S" | "M" | "L" | "XL"`.
  - `label` : libellé présentable dans l'entête de colonne (`"VAE • Taille M"`).
  - `notes?` : champ optionnel pour stocker une précision (autonomie, accessoires) affichable en tooltip ultérieurement.
- **Règles** : aucune donnée business sensible (tarifs, réservations) dans ce fichier ; uniquement la structure de la flotte pour piloter dynamiquement les colonnes.
- **Évolutivité** : ajout/suppression de vélos par simple modification du tableau + re-build, sans impact sur les composants.
