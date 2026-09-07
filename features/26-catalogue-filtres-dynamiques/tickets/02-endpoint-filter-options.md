# Ticket 02 : Endpoint `filterOptions()` par sous-catégorie

**Type** : Backend / API
**Priorité** : Haute
**Estimation** : 3h

**Dépend de** : Ticket 01

## Tâches

- [ ] Route dans `routes/api.php`, groupe Catalogue — Articles, avant les routes `/articles/{id}` (même convention que `/articles/search`) :
  ```php
  Route::get('/article-subcategories/{id}/filter-options', [ArticleController::class, 'filterOptions']);
  ```
- [ ] `ArticleController::filterOptions(int $id): JsonResponse` — pas de route model binding (cohérent avec `show()`/`update()`/`destroy()` du même contrôleur qui utilisent tous `int $id` + résolution manuelle)
- [ ] Constante `FILTERABLE_KEYS` en liste blanche explicite :
  ```php
  private const FILTERABLE_KEYS = [
      'practice_type', 'valve_type', 'chainring_diameter_mm', 'tooth_count',
      'speed_count', 'crank_length_mm', 'side', 'speed_compat', 'axle_type',
      'position', 'power_source',
  ];
  ```
  `supplier_status` et `size_inches` n'y figurent jamais — voir `../04-arbitrages.md` (arbitrage 4).
- [ ] Requête d'agrégation via jointure (pas `whereHas`, voir `../01-modele-donnees.md`) :
  ```php
  ArticleAttribute::query()
      ->join('articles', 'articles.id', '=', 'article_attributes.article_id')
      ->where('articles.article_subcategory_id', $id)
      ->select('article_attributes.key', 'article_attributes.value')
      ->distinct()
      ->get()
      ->groupBy('key');
  ```
- [ ] Filtrer le résultat groupé sur `FILTERABLE_KEYS`, sauf `etrto_size` et `tooth_range` qui ne sont jamais exposées telles quelles (voir décomposition ci-dessous) — les ignorer dans la boucle de construction de `attributes`, jamais les inclure brutes même en complément
- [ ] Décomposition `etrto_size` (format `"{largeur}-{diametre_mm}"`) :
  - Parser chaque valeur distincte en `[largeur, diametre]` par `explode('-', $value)`
  - `etrto_diameter_mm` : ensemble des diamètres distincts, triés numériquement croissant
  - `etrto_width_mm` : ensemble des largeurs distinctes, triés numériquement croissant
  - Ignorer silencieusement (ne pas planter) toute valeur ne matchant pas le format attendu (donnée fournisseur imprévue) — logguer un `warning` via `Log::warning()` avec la valeur en cause plutôt que de laisser échouer toute la requête
- [ ] Décomposition `tooth_range` (format `"{min}-{max}"`) : même logique → `tooth_range_min`, `tooth_range_max`
- [ ] Tri numérique en PHP (cast puis `sort()`) pour toutes les clés numériques : `chainring_diameter_mm`, `tooth_count`, `speed_count`, `crank_length_mm`, `etrto_diameter_mm`, `etrto_width_mm`, `tooth_range_min`, `tooth_range_max`
- [ ] Tri alphabétique pour les clés textuelles restantes
- [ ] Normalisation cosmétique de `practice_type` à l'affichage uniquement (voir `../04-arbitrages.md`, arbitrage 5) : regrouper toute valeur contenant `VTC` sous un seul libellé affiché `VTC/Urbain`, dédupliquer le résultat. La valeur brute d'origine (non normalisée) est celle qui doit être renvoyée dans le tableau `attributes.practice_type` pour rester utilisable directement comme paramètre de `attribute[practice_type]=...` au ticket 03 — la normalisation ne doit donc **pas** changer la valeur retournée par l'API à ce stade, seulement dédupliquer les entrées équivalentes (garder une seule occurrence par groupe, avec la valeur brute la plus fréquente ou la première rencontrée)
- [ ] Marques contextualisées :
  ```php
  Brand::query()
      ->whereHas('articles', fn ($q) => $q->where('article_subcategory_id', $id))
      ->ordered()
      ->get(['id', 'name', 'sort_order']);
  ```
- [ ] `ArticleSubcategory::findOrFail($id)` en amont pour garantir un 404 propre si l'id n'existe pas, avant toute requête d'agrégation
- [ ] Réponse :
  ```json
  {
    "attributes": { "practice_type": ["ROUTE", "VTT"], "etrto_diameter_mm": ["559", "622"] },
    "brands": [{ "id": 3, "name": "Continental" }]
  }
  ```
  Sous-catégorie sans `article_attributes` → `attributes: {}`, `brands` reste peuplé normalement.

## Critères d'acceptation

- `GET /api/article-subcategories/{id}/filter-options` sur la sous-catégorie Pneus retourne `etrto_diameter_mm` et `etrto_width_mm` (jamais `etrto_size` brut), avec des ensembles de quelques valeurs (pas 134)
- Idem sur Cassettes avec `tooth_range_min`/`tooth_range_max` (jamais `tooth_range` brut)
- La réponse ne contient jamais les clés `supplier_status` ni `size_inches`
- Sur une sous-catégorie sans `article_attributes` (ex. une des ~66 restantes), `attributes` est un objet vide et `brands` contient au moins une marque si des articles existent
- Un id de sous-catégorie inexistant retourne 404
- Les valeurs numériques de chaque groupe sont triées croissant
- Une valeur `article_attributes` malformée sur `etrto_size`/`tooth_range` (ex. sans tiret) n'interrompt pas la requête et est simplement ignorée
