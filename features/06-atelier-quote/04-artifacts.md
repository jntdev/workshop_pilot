# 04 - Artifacts attendus

## Données & modèles
- Migrations `create_quotes_table` et `create_quote_lines_table` (cf. stratégie) appliquées.
- Colonnes monétaires en `decimal(10,2)` et taux en `decimal(7,4)` conformément aux règles d'arrondis.
- Modèles `App\Models\Quote` et `App\Models\QuoteLine` + relations.
- Seeder/factory pour `Quote` + `QuoteLine` (pour tests).
- Service `App\Services\Quotes\QuoteCalculator` avec méthodes :
  - `fromSalePriceHt(purchase, saleHt, tva)` → retourne marges + TTC.
  - `fromSalePriceTtc(purchase, saleTtc, tva)`.
  - `fromMarginAmount(purchase, marginAmount, tva)`.
  - `fromMarginRate(purchase, marginRate, tva)`.
  - `aggregateTotals(lines)` → total HT/TVA/TTC/marges.

## Livewire & vues
- Composant `App\Livewire\Atelier\Quotes\Form` + vue `resources/views/livewire/atelier/quotes/form.blade.php`.
- Composant `App\Livewire\Clients\Search` (ou extension du composant existant) + vue associée.
- Page Blade `resources/views/atelier/quotes/create.blade.php` contenant `<livewire:atelier.quotes.form />`.
- Page `resources/views/atelier/index.blade.php` mise à jour avec bouton `Nouveau devis`.
- Page lecture `resources/views/atelier/quotes/show.blade.php`.
- Logique `save()` qui met à jour un client existant uniquement au moment de la validation du devis si les champs ont été modifiés.

## Routes & contrôleurs
- Nouvelles routes dans `routes/web.php` protégées par `auth`.
- Optionnel : controller `Atelier\QuoteController` si nécessaire pour show/list, sinon closures dans routes.

## SCSS & assets
- Fichier `resources/scss/atelier/_quotes.scss` + import dans `resources/scss/app.scss`.
- Styles pour :
  - `quote-form` container.
  - `quote-lines-table` (flex table responsive).
  - `quote-totals` bloc.
  - Boutons d’action.

## Tests
- `tests/Feature/QuoteLines/CreateQuoteLineTest.php` vérifiant la création/suppression d'une prestation et la persistance des montants recalculés.
- `tests/Feature/Quotes/CreateQuoteTest.php` avec scénarios : client existant/nouveau, remise %, recalcul total, update client au moment de la validation.
- `tests/Feature/Livewire/Atelier/QuoteFormTest.php` (front fonctionnel) couvrant interactions (ajout/suppression, recalcul, sauvegarde, update client différé).
- `tests/Unit/Services/QuoteCalculatorTest.php` avec cas d’entrée différents (incluant arrondis).

## QA / vérifications manuelles
- Création d’un devis complet depuis `/atelier`.
- Sélection client via recherche + modification champs.
- Ajout de plusieurs lignes, suppression, recalcul correct des totaux/marges.
- Application d’une remise HT et vérification du Total TTC.
- Feedback banner présent après sauvegarde.
