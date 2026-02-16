# 3 — Vélos & accessoires

## Source de données
- **Grille** : colonnes construites à partir de `config/bikes.php` (ordre + statut).  
- **Formulaire** : types issus de la table `bike_types` (modèle `App\Models\BikeType`) pour connaître le label et la disponibilité théorique.
- Les deux sources doivent partager le même identifiant (`bike.id`). S’il y a divergence, afficher un warning dans la console pour faciliter le debug.

## Sélection des vélos
1. **Depuis le calendrier**  
   - Chaque cellule sélectionnée ajoute implicitement le vélo associé avec une plage `[minDate, maxDate]`.  
   - Dans le formulaire, la section “Vélos” liste ces vélos sous forme de cartes : `Label`, `Taille / cadre`, `Période`.
2. **Dans le formulaire**  
   - Les boutons +/- permettent d’ajuster la quantité par type de vélo (utile pour les réservations groupées).  
   - Retirer la quantité à 0 retire aussi toutes les cellules du calendrier correspondant à ce vélo (bidirectionnel).
3. **Récap**  
   - Les puces en haut du formulaire (`reservation-form__recap`) reflètent la sélection : `2× VAE M (3‑6 avr)`.

## Accessoires (hors périmètre 12.0)
- Les accessoires seront gérés via `items` (comme les vélos), en ajoutant des types dans `bike_types` si nécessaire.
- Pas de section Accessoires distincte pour le moment.

## Validations
- Tant qu’aucun vélo n’est sélectionné (donc aucune cellule active), afficher l’aide “Sélectionnez au moins un vélo…”.  
- Le backend conserve la validation existante : `items` ne peut pas être vide.
- Afficher un badge orange “HS” sur les cartes correspondant à des vélos marqués HS dans `config/bikes.php`.
