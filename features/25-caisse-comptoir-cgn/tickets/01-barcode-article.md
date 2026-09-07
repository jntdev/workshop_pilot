# Ticket 01 : Colonnes catalogue additionnelles sur Article

**Type** : Backend / Data
**Priorité** : Haute
**Estimation** : 45min

## Tâches

- [ ] Migration `add_supplier_columns_to_articles_table` : `barcode` string(64) nullable `after('reference')` avec index simple (pas de contrainte unique), `image_url` string(500) nullable, `weight_kg` decimal(8,3) nullable
- [ ] Ajouter `barcode`, `image_url`, `weight_kg` à `$fillable` sur `Article`
- [ ] Étendre `Article::scopeSearch()` pour inclure `orWhere('barcode', 'like', "%{$term}%")`
- [ ] Ajouter `Article::scopeByBarcode(Builder $query, string $barcode): Builder` — `where('barcode', $barcode)` exact
- [ ] Ajouter les règles `barcode` (nullable, string, max:64, **sans unique**), `image_url` (nullable, string, max:500), `weight_kg` (nullable, numeric, min:0) dans `StoreArticleRequest` et `UpdateArticleRequest`
- [ ] Ajouter `barcode`, `image_url`, `weight_kg` sur l'interface `Article` dans `resources/js/types/index.d.ts`
- [ ] Ajouter ces champs optionnels dans `ArticleFactory` (`fake()->optional()->ean13()` pour `barcode`)

## Critères d'acceptation

- Deux articles peuvent partager le même `barcode` sans erreur (pas de contrainte unique en base)
- `Article::byBarcode('3528701009261')->first()` retourne l'article exact
- `Article::search('352870')->get()` retrouve aussi un article par fragment de code-barres
