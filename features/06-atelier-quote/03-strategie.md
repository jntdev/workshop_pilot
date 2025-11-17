# 03 - Stratégie de réalisation

Approche : commencer par les modèles/données, enchaîner sur le composant Livewire côté atelier, terminer par l'UX/SCSS et les tests.

## 1. Modélisation & migrations
1. Migration `create_quotes_table`
   - `client_id` FK, `reference`, `status` (enum `draft`,`validated`), `valid_until`, `discount_type` (`amount`/`percent`), `discount_value`, `total_ht`, `total_tva`, `total_ttc`, `margin_total_ht`, timestamps, soft deletes.
2. Migration `create_quote_lines_table`
   - `quote_id` FK, `title`, `reference`, `purchase_price_ht`, `sale_price_ht`, `sale_price_ttc`, `margin_amount_ht`, `margin_rate`, `tva_rate`, `position`.
3. Modèles `Quote` et `QuoteLine` + relations `belongsTo`, `hasMany`.
4. Factories pour tests automatisés.

## 2. Services & calculs
- Créer un service `QuoteCalculator` (classe dédiée) gérant conversions HT/TTC/margesoù toutes les formules sont centralisées.
- Ce service sera utilisé par Livewire (pour recalcul instantané) et par les tests/contrôleurs (pour validations serveur).
- Règles d'arrondis : 
  - Utiliser `bcmath`/`strval` pour tous les calculs.
  - Montants financiers (PA, PV, marges, totaux) stockés et affichés avec 2 décimales (Decimal(10,2) côté base).
  - Taux TVA et marges (%) stockés avec 4 décimales (Decimal(7,4)) mais affichés à 2 décimales.
  - Les conversions TTC↔HT se font en conservant la pleine précision puis arrondies à 2 décimales uniquement au moment de l'enregistrement/affichage.

## 3. Livewire côté atelier
1. Composant `Atelier\Quotes\Form` :
   - Props : `$quoteId = null`, `$clientForm`, `$clientSearchTerm`, `$selectedClientId`, `Collection $lines`.
   - Méthodes : `mount($quoteId = null)`, `addLine()`, `removeLine($index)`, `updateLine($index, $field, $value)`, `save($stayOnPage = false)`.
   - Listener d’événement `clientSelected` (depuis composant de recherche) pour remplir les champs client.
2. Composant `Clients\Search` réutilisable (input + liste de résultats type dropdown), expose un event Livewire.
3. Vue Blade : structure split en sections (client, prestations, totaux) + boutons.
4. Tous les recalculs se font via Livewire (pas d'Alpine.js) pour rester homogène avec le reste du projet.
5. Lorsqu'un client existant est sélectionné puis modifié dans le formulaire, la mise à jour effective du modèle `Client` ne se fait qu'au moment de `save()` (validation du devis), jamais en direct.

## 4. Routes & pages
- Web routes :
  - `Route::get('/atelier/devis', ...)` (liste future, placeholder).
  - `Route::get('/atelier/devis/nouveau', ...)` → Livewire form (middleware `auth`).
  - `Route::get('/atelier/devis/{quote}', ...)` → page lecture Livewire.
- Bouton "Nouveau devis" ajouté sur la page `/atelier` (ou composant existant) qui pointe vers la route ci-dessus.

## 5. SCSS
- Créer `resources/scss/atelier/_quotes.scss` pour :
  - Layout de la page (grid 2 colonnes sur desktop, stack sur mobile).
  - Styles du tableau prestations (lignes zebra, inputs alignés à droite pour montants, badges TVA/marge).
  - Boutons d'action cohérents avec la charte.
- Importer le partial dans `app.scss`.

## 6. Tests
- **Backend** :
  - `tests/Feature/QuoteLines/CreateQuoteLineTest.php` : création/suppression d'une ligne, recalcul marges, respect arrondis.
  - `tests/Feature/Quotes/CreateQuoteTest.php` : création en base, calcul totaux, remise, association client et update client lors de la validation.
- **Livewire / Front fonctionnel** : `tests/Feature/Livewire/Atelier/QuoteFormTest.php` couvrant :
  - Sélection client existant remplit les champs.
  - Ajout/suppression de lignes.
  - Mise à jour d'un champ recalcul les montants.
  - Sauvegarde crée devis + lignes.
- **Service** : Test unitaire pour `QuoteCalculator` (cas PV TTC modifié, marge modifiée, arrondis, etc.).

## 7. Documentation & QA
- Mettre à jour `features/README.md` (section Atelier) pour mentionner l'outil devis.
- Ajouter captures/notes dans changelog si besoin.
- QA manuelle : cas client existant vs nouveau, modifications multiples de lignes, remise en %, navigation et feedback banner.
