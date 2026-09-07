# Ticket 01 : Route de lookup par code-barres

**Type** : Backend / API
**Priorité** : Haute
**Estimation** : 1h

**Dépend de** : —

## Tâches

- [ ] Route dans `routes/api.php`, groupe Catalogue — Articles (nouveau sous-groupe `Inventory`, pas dans `SaleController` — contexte différent, voir `../01-modele-donnees.md`) :
  ```php
  Route::get('/inventory/lookup-barcode', [InventoryController::class, 'lookupBarcode']);
  ```
- [ ] `php artisan make:controller Api/InventoryController --no-interaction`
- [ ] `InventoryController::lookupBarcode(Request $request): JsonResponse` :
  ```php
  public function lookupBarcode(Request $request): JsonResponse
  {
      $code = $request->string('code')->toString();

      if ($code === '') {
          return response()->json(['result' => 'not_found', 'article' => null]);
      }

      $articles = Article::byBarcode($code)->with(['subcategory.category', 'brand', 'supplier'])->get();

      if ($articles->count() === 1) {
          return response()->json(['result' => 'found', 'article' => $articles->first()]);
      }

      if ($articles->count() > 1) {
          return response()->json(['result' => 'ambiguous', 'articles' => $articles]);
      }

      return response()->json(['result' => 'not_found', 'article' => null]);
  }
  ```
  Réutilise `Article::scopeByBarcode()` déjà existant (feature 22/25) — pas de nouvelle logique de résolution EAN.
- [ ] Cas `ambiguous` : ne jamais choisir un article arbitrairement, même règle que `SaleController::lookupArticle()` (voir `../01-modele-donnees.md`) — le frontend devra présenter un choix à l'utilisateur (hors périmètre détaillé de ce ticket, traité au ticket 05).

## Critères d'acceptation

- `GET /api/inventory/lookup-barcode?code=3528701009261` sur un EAN existant en base retourne `{ result: "found", article: {...} }` avec l'article complet (relations chargées)
- Un EAN partagé par deux articles (cas autorisé en base, voir arbitrage 7 de la feature 22) retourne `{ result: "ambiguous", articles: [...] }`
- Un code-barres absent de la base retourne `{ result: "not_found", article: null }`
- Un code vide (`code=`) retourne `not_found` sans erreur 500
