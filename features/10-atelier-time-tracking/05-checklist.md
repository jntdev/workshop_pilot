# 05 - Checklist d'exécution

## Préparation
- [ ] Brancher `feature/10-atelier-time-tracking` depuis `develop`.
- [ ] Relire les features `06-atelier-quote` et `09-migration-react` pour rester cohérent.
- [ ] Lancer `php artisan test` pour partir d'un état propre.

## Backend
- [ ] Créer la migration ajoutant `estimated_time_minutes` (quote_lines) et `total_estimated_time_minutes` / `actual_time_minutes` (quotes).
- [ ] Mettre à jour les modèles + factories (Quote, QuoteLine).
- [ ] Ajuster `QuoteController` (validation, calcul total, formatage, autorisation update post-facture).
- [ ] Mettre à jour les API/ressources (Inertia) pour exposer les champs.

## Frontend
- [ ] Ajouter la colonne "Temps estimé (h)" dans `QuoteLinesTable` avec conversion heures↔minutes.
- [ ] Afficher le total estimé dans `QuoteTotals` (lecture seule).
- [ ] Ajouter un champ "Temps réel (h)" editable dans `QuoteTotals` (et sur les pages Show si besoin).
- [ ] S'assurer que ces champs ne sont pas inclus dans les PDF.

## Tests & QA
- [ ] Écrire des tests Feature API (création devis avec temps, update facture avec temps réel).
- [ ] Mettre à jour / ajouter des tests Inertia si nécessaire (vérifier props nouveaux champs).
- [ ] QA manuelle : devis avec temps estimé, conversion facture, saisie du temps réel, rechargement pour vérifier la persistance.
- [ ] Vérifier que l'édition d'une facture ne permet que la modification du temps réel (pas des autres données verrouillées).

## Documentation & livraison
- [ ] Compléter ce dossier feature si besoin + ajouter un résumé dans `features/README.md`.
- [ ] Mettre à jour le changelog / release notes.
- [ ] Lancer `php artisan test` + tests front éventuels avant merge.
- [ ] QA & notation : relire tous les fichiers de `features/10-atelier-time-tracking`, cocher la checklist, calculer la note (100 % requis).
