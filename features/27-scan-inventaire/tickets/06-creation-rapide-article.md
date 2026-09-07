# Ticket 06 : Création rapide d'article inconnu

**Type** : Frontend + Backend
**Priorité** : Haute
**Estimation** : 4h

**Dépend de** : Ticket 02, Ticket 04, Ticket 05

## Tâches backend

- [ ] Route dans `routes/api.php` :
  ```php
  Route::post('/inventory/articles', [InventoryController::class, 'storeArticle']);
  ```
- [ ] `php artisan make:request Api/StoreInventoryArticleRequest --no-interaction` — validation stricte isolée de `StoreArticleRequest`, voir `../04-arbitrages.md` (arbitrages 11, 14, 15) et `../01-modele-donnees.md` pour les règles exactes. **Pas de champ `reference` dans les règles de validation** — `barcode` est le seul champ d'entrée pour l'identifiant produit, `reference` est calculée par le contrôleur (voir arbitrage 7 révisé). `image_url` porte la règle `starts_with:/storage/articles/` (arbitrage 15). `attributes.*` n'a plus de règle de validation Laravel générique — le filtrage se fait par liste blanche explicite dans le contrôleur (arbitrage 14), pas dans la Form Request
- [ ] `InventoryController::storeArticle(StoreInventoryArticleRequest $request): JsonResponse`, dans une transaction :
  1. Crée l'`Article` avec `reference = $validated['barcode']` et `barcode = $validated['barcode']` (même valeur, calculée une seule fois, jamais reçue en double depuis le client — voir arbitrage 7 révisé), `tva_rate` 20.00, `unit` 'pièce' en dur
  2. Filtre les `attributes` reçus contre `ACCEPTED_ATTRIBUTE_KEYS` (liste blanche, arbitrage 14) avant tout traitement — toute clé hors liste ou valeur vide/nulle est ignorée silencieusement
  3. Traduit les clés virtuelles restantes et persiste en `ArticleAttribute` — voir `../01-modele-donnees.md`, section "Attributs filtrables pour saisie libre" pour le détail exact de la traduction (`wheel_diameter`/`wheel_width_mm` → `etrto_size`, `wheel_diameter`/`wheel_width_inches` → `size_inches`, `tooth_range_min`/`tooth_range_max` → `tooth_range`, valeur partielle jamais persistée)
  4. Crée le mouvement de stock (`type: manual_in`, `quantity` validée) — **obligatoire dans le même appel**, voir `../04-arbitrages.md` (arbitrage 10) : ne jamais créer un article sans son mouvement de stock associé
- [ ] Réponse : article complet créé (avec relations chargées), cohérent avec le format déjà utilisé ailleurs dans `ArticleController`

## Tâches frontend

- [ ] Depuis l'écran Inventaire (ticket 05), quand `result: "not_found"` : afficher le formulaire de création rapide plutôt que de bloquer la boucle de scan
- [ ] Capture photo via `PhotoCapture.tsx` (ticket 04) — composant séparé, pas de réutilisation du flux vidéo du scanner (voir `../04-arbitrages.md`, révision de l'arbitrage initial)
- [ ] Upload immédiat de la photo capturée via `POST /api/articles/upload-photo` (ticket 02), récupère `image_url`, affiche un aperçu avant de continuer le formulaire
- [ ] Formulaire, champs obligatoires (voir `../04-arbitrages.md`, arbitrage 6) :
  - `designation` (texte)
  - `article_subcategory_id` (sélection — `<select>` simple sur `GET /api/article-categories`, pas besoin de l'arbre complet `ArticleCategoryTree` pour ce contexte mobile)
  - `brand_id` (sélection sur `GET /api/brands`)
  - `purchase_price_ht`, `sale_price_ttc` (saisie en euros, conversion centimes comme `ArticleForm.tsx`)
  - **`quantity`** (quantité comptée — champ toujours vide, même règle que l'arbitrage 8 ; respecter `quantityStep(unit)` avec `unit = 'pièce'` fixe ici, donc pas de saisie décimale)
- [ ] Champs optionnels : attributs de filtre, chargés dynamiquement via `GET /api/article-subcategories/{id}/filter-options` **une fois la sous-catégorie choisie** (réutilise `ATTRIBUTE_LABELS`/`orderedAttributeEntries`, feature 26) — ces attributs sont déjà spécifiques à la sous-catégorie choisie par construction, jamais un mélange d'attributs d'autres familles de produits (voir `../01-modele-donnees.md`)
- [ ] À la soumission, payload vers `POST /api/inventory/articles` — **pas de champ `reference`**, uniquement `barcode` (voir arbitrage 7 révisé, la référence est calculée côté serveur) :
  ```typescript
  const payload = {
      barcode: scannedCode,
      designation,
      article_subcategory_id,
      brand_id,
      purchase_price_ht: eurosToCents(purchase_price_ht),
      sale_price_ttc: eurosToCents(sale_price_ttc),
      quantity,
      image_url,
      attributes: selectedAttributes, // clés virtuelles incluses telles quelles, filtrage + traduction faits côté serveur
  };
  ```
- [ ] Après création réussie : incrémente le compteur de progression (ticket 05), relance immédiatement le scan — même comportement que pour un article déjà connu

## Critères d'acceptation

- Scanner un code-barres inconnu propose la capture photo puis le formulaire, jamais un blocage silencieux
- Impossible de soumettre sans photo, désignation, sous-catégorie, marque, prix d'achat HT, prix de vente TTC, quantité (validation stricte côté serveur via `StoreInventoryArticleRequest`, pas seulement frontend)
- L'article créé a bien `reference === barcode === code scanné`, **même si un payload malveillant tentait d'envoyer une `reference` différente** — impossible puisque le champ n'existe pas dans les règles de validation ni dans le payload envoyé
- Une `image_url` ne commençant pas par `/storage/articles/` (ex. une URL externe arbitraire) est rejetée avec une erreur 422
- Un `attributes` contenant une clé hors de `ACCEPTED_ATTRIBUTE_KEYS` (ex. `supplier_status`, ou une clé inventée) est silencieusement ignoré, ne provoque pas d'erreur 422, n'apparaît jamais dans `article_attributes` après création
- L'article créé a un mouvement de stock `manual_in` correspondant à la quantité saisie, créé dans le même appel que la création de l'article (vérifiable : `stock_quantity` de l'article fraîchement créé est non nul immédiatement)
- Scanner ce même code-barres une seconde fois après création retrouve l'article (`result: "found"`), ne propose plus la création
- Les attributs de filtre saisis sous forme de clés virtuelles (diamètre, largeur) sont bien persistés sous leurs vraies clés composites (`etrto_size`, `size_inches`) — jamais les clés virtuelles elles-mêmes dans `article_attributes`
- Une valeur d'attribut composite partiellement saisie (ex. diamètre seul, sans largeur) n'est pas persistée sous une forme inventée
- L'article créé apparaît dans "Catalogue complet" et "Stock atelier" (page Stock) dès sa création, sans action supplémentaire
