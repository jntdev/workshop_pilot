# Ticket 03 : Quantité décimale sur `stock_movements` (impact feature 22 existante)

**Type** : Backend / Data
**Priorité** : Haute
**Estimation** : 1h

## Description

`sale_lines.quantity` doit être décimal pour vendre au mètre/litre au comptoir. Comme le stock est décrémenté via `stock_movements` (colonne `quantity` actuellement `integer`), ce type doit être élargi en `decimal(10,2)` avant que la caisse puisse créer des mouvements exacts pour une vente à quantité non entière. **Ce ticket modifie du code de la feature 22 déjà en production sur `develop`**, pas seulement du code nouveau — à traiter avec la même rigueur qu'une migration de production (tests de non-régression sur l'existant, pas seulement sur la caisse).

## Contrat de retour — figer le type plutôt que de casser les tests existants

Le cast Eloquent `decimal:2` a un effet de bord important : il fait retourner à PHP une **chaîne** formatée (`"11.00"`), pas un `float`. Deux conséquences vérifiées dans le code réel avant de trancher :
- `tests/Feature/Catalogue/StockMovementTest.php:107` utilise `assertSame(11, $article->fresh()->stock_quantity)` — `assertSame` est strict sur le type, `assertSame(11, "11.00")` échouerait même si la valeur logique est correcte
- `resources/js/types/index.d.ts:544` déclare `stock_quantity: number` côté TypeScript, consommé par `Components/Stock/ArticleList.tsx`, `ArticleStockPanel.tsx`, `ArticleAutocomplete.tsx`, `CataloguePickerModal.tsx` — une chaîne renvoyée en JSON casserait ce contrat silencieusement (pas d'erreur TypeScript à la compilation, juste un comportement runtime erroné, ex. `stock_quantity + 1` concaténerait au lieu d'additionner)

**Décision : ne jamais laisser une chaîne remonter jusqu'aux tests ni au JSON.** `Article::getStockQuantityAttribute()` caste explicitement le résultat en `float` avant de le retourner (`(float) $this->stockMovements()->sum('quantity')`), et son type de retour PHP passe de `: int` à `: float`. Ce choix déplace la responsabilité de conversion dans l'accesseur lui-même, qui reste la seule source consultée par les tests et par le JSON — la colonne `stock_movements.quantity` peut rester un cast `decimal:2` sans que cela ne fuite plus loin.

## Tâches

- [ ] Migration `change_quantity_to_decimal_on_stock_movements_table` : `$table->decimal('quantity', 10, 2)->change()` — vérifier en implémentation si `doctrine/dbal` (requis par `->change()`) est déjà installé dans le projet, sinon l'ajouter (`composer require doctrine/dbal --dev` ou en dépendance de prod selon l'usage ailleurs dans le projet)
- [ ] `app/Models/StockMovement.php` : cast `'quantity' => 'integer'` → `'quantity' => 'decimal:2'`
- [ ] `app/Models/Article.php` : `getStockQuantityAttribute(): float` — `return (float) $this->stockMovements()->sum('quantity');` (conversion explicite pour ne jamais exposer la chaîne du cast `decimal:2` sous-jacent)
- [ ] `app/Http/Requests/Api/StoreStockMovementRequest.php` : règle `quantity` — `['required', 'integer', 'min:1']` → `['required', 'numeric', 'min:0.01']`
- [ ] `resources/js/types/index.d.ts:544` : `stock_quantity: number` reste inchangé — le contrat JSON garantit un nombre, pas une chaîne, grâce à la conversion explicite côté `Article`
- [ ] `tests/Feature/Catalogue/StockMovementTest.php` : **aucune modification requise** sur les assertions de valeur (`assertSame(11, ...)`, `assertJsonPath('stock_quantity', 5)`) — `getStockQuantityAttribute()` continue de retourner un nombre PHP natif comparable tel quel. Seule vérification à ajouter : un test avec des mouvements à quantité décimale (`2.5`) pour couvrir le nouveau cas, sans toucher aux tests entiers existants

## Critères d'acceptation

- Un mouvement manuel de quantité `2.5` (ex. mètres de guidoline) est accepté et stocké exactement, sans troncature
- Tous les tests existants de la feature 22 (`tests/Feature/Catalogue/StockMovementTest.php`) passent **sans modification de leurs assertions** — vérifié en exécutant la suite telle quelle après la migration, pas en l'adaptant
- `Article::stock_quantity` reflète correctement une somme incluant des mouvements décimaux, toujours exposé comme `float` PHP / `number` JSON, jamais comme chaîne
