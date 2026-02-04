# 03 - Plan technique

## A. Modèle & bases de données
- `quote_lines` : ajouter `estimated_time_minutes` (nullable integer). Stockage en minutes pour éviter les imprécisions, conversion heures↔minutes dans la couche application.
- `quotes` : ajouter `total_estimated_time_minutes` (nullable integer) et `actual_time_minutes` (nullable integer). `actual_time_minutes` est éditable à tout moment (même si `invoiced_at` non null).
- Migrations + mise à jour des factories/seeds.

## B. Backend / API
- Validation `QuoteController@store/update` : accepter `lines.*.estimated_time_minutes` et un champ global `actual_time_minutes`.
- Calcul automatique du total estimé dans le service (somme des lignes), stocké dans `quotes.total_estimated_time_minutes`.
- Exposer ces propriétés dans les réponses Inertia/API (`routes/web.php`, `QuoteController::formatQuote`).
- Autoriser la mise à jour de `actual_time_minutes` pour les factures (exception à la règle de non-modification).

## C. Frontend / Inertia
- `QuoteLinesTable` : ajouter une colonne "Temps estimé (h)" (input number). Conversion UI heures décimales ↔ minutes côté payload.
- `QuoteTotals` : afficher "Temps estimé total" (lecture seule) et ajouter un champ "Temps réel (h)" editable (même en lecture/édition facture).
- `QuoteShow` : afficher les deux valeurs dans la section résumé (mention "interne").
- Persister l'information dans les pages React clients (formulaires, show).

## D. Tests & reporting
- Tests Feature API pour vérifier : création devis avec temps, conversion facture + mise à jour temps réel.
- Inertia tests pour vérifier que les props contiennent les nouveaux champs.
- Préparer un script d'export (future tâche) pour marges horaires.
