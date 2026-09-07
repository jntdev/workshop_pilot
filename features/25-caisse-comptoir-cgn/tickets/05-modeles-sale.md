# Ticket 05 : Modèles et migrations Sale / SaleLine

**Type** : Backend / Data
**Priorité** : Haute
**Estimation** : 2h30

**Dépend de** : Ticket 03 (quantité décimale sur `stock_movements`)

## Tâches

- [ ] Migration `create_sales_table` (voir `../01-modele-donnees.md` — `reference` nullable jusqu'à finalisation)
- [ ] Migration `create_sale_lines_table` (voir `../01-modele-donnees.md` — `article_id` nullable pour les lignes libres, `quantity` en `decimal(10,2)`, index `(sale_id, article_id)`)
- [ ] Migration d'extension de l'enum DB `stock_movements.type` pour ajouter `sale_consumption` **et** `sale_return` (voir point dédié ci-dessous) : **la migration doit distinguer le driver**, car les tests tournent sous SQLite qui ne supporte pas `ALTER TABLE ... MODIFY ... ENUM`. Utiliser `if (DB::getDriverName() === 'mysql') { DB::statement(...) }` — sous SQLite, l'enum applicatif (cast PHP) suffit à contraindre les valeurs, la colonne SQLite sous-jacente est un simple TEXT sans contrainte native
- [ ] Enum PHP centralisé `app/Enums/PaymentMethod.php` — backed string enum avec les valeurs déjà utilisées côté front (`Cb = 'cb'`, `Liquide = 'liquide'`, `Cheque = 'cheque'`, `Virement = 'virement'`, `Autre = 'autre'`) + `label()`. **Le type `PaymentMethod` n'existe aujourd'hui que côté TypeScript** (`resources/js/types/index.d.ts:302`) — cet enum PHP est nouveau, pas une réutilisation, et doit rester la source de vérité des valeurs autorisées côté serveur (utilisé dans `CompleteSaleRequest`)
- [ ] Enum `app/Enums/SaleStatus.php` : `Draft`, `Completed`, `Cancelled` + `label()`
- [ ] Ajouter le cas `SaleConsumption` (`sale_consumption`) à `app/Enums/StockMovementType.php` + `label()`, exclu de `isManual()`
- [ ] Ajouter le cas `SaleReturn` (`sale_return`) à `app/Enums/StockMovementType.php` + `label()` ("Réintégration suite annulation vente"), exclu de `isManual()` — type dédié pour les mouvements créés par `Sale::cancel()`, distinct de `SaleConsumption` : un mouvement positif typé `sale_consumption` serait trompeur dans l'historique (le type suggérerait une sortie alors que la quantité est positive)
- [ ] Modèle `Sale` (`$fillable`, `casts()` incluant `status => SaleStatus::class` et `payment_method => PaymentMethod::class`, `belongsTo(Client)`, `belongsTo(User)`, `hasMany(SaleLine)->orderBy('position')`, `isDraft()`, `isCompleted()`, `recalculateTotals()`, `complete()`, `cancel()`)
- [ ] Modèle `SaleLine` (`$fillable`, `casts()` incluant `quantity => decimal:2`, `belongsTo(Sale)`, `belongsTo(Article, nullable)`)
- [ ] Factories `SaleFactory`, `SaleLineFactory` (calquées sur `QuoteFactory`/`QuoteLineFactory`)

## Formules de calcul (à documenter dans le code, pas seulement ici)

- `line_total_ttc = round(quantity * unit_price_ttc)`
- `line_total_ht = round(line_total_ttc / (1 + tva_rate / 100))`
- `line_total_tva = line_total_ttc - line_total_ht`
- `Sale::total_ttc = somme(SaleLine::line_total_ttc)`, `total_ht`/`total_tva` sommés de la même façon depuis les lignes
- Marge par vente (accessor `Sale::marginHt(): int`, calculé à la volée, pas stocké) — **tous les montants restent en centimes, jamais de division par 100** (`purchase_price_ht` est déjà en centimes comme `total_ht`, diviser mélangerait euros et centimes et fausserait la marge) :
  ```php
  $costHt = $sale->lines->sum(
      fn (SaleLine $line) => (int) round((float) $line->quantity * $line->purchase_price_ht)
  );
  $marginHt = $sale->total_ht - $costHt;
  ```
  L'arrondi explicite (`round()`) est nécessaire car `quantity` est décimal (`decimal(10,2)`) — `quantity * purchase_price_ht` peut produire un flottant non entier même si `purchase_price_ht` est un entier de centimes. Nécessaire pour tenir la promesse de "marge suivie dès la v1" (arbitrage 3), absente jusqu'ici du modèle malgré la colonne `purchase_price_ht`

## Concurrence et atomicité

- [ ] `Sale::complete()` : exécuter dans `DB::transaction()` avec `Sale::query()->lockForUpdate()->findOrFail($this->id)` (ou équivalent sur `$this`) en tout début de méthode, pour empêcher deux requêtes simultanées (double-clic, double scan de finalisation) de créer chacune leurs mouvements de stock. Revérifier `isDraft()` **après** l'acquisition du verrou, pas seulement avant l'appel HTTP
- [ ] `Sale::cancel()` : même principe de verrouillage, revérifier `isCompleted()` après le lock. Refuse (idempotent, pas d'exception si déjà annulée — retour silencieux ou exception explicite `AlreadyCancelledException`, à choisir en implémentation) une vente déjà `Cancelled`
- [ ] `Sale::complete()` refuse un panier vide (`lines()->count() === 0`) avec une exception claire — sans quoi une vente finalisée sans ligne créerait une référence et un encaissement nul
- [ ] Génération de la référence — **format unique et définitif : `VC-{YYYYMMDD}-{sale_id}`** (ex. `'VC-' . now()->format('Ymd') . '-' . $this->id`), jamais un compteur journalier recalculé (`count() + 1`, pattern à risque de collision identifié ailleurs dans le projet). L'id auto-incrémenté de `Sale` élimine toute course de concurrence sur la génération elle-même, au prix d'une référence un peu moins "lisible" qu'un compteur repartant de 1 chaque jour

## Tâches restantes

- [ ] Toutes les mutations du panier (`addLine`, `updateLine`, `removeLine`, en plus de `complete`/`cancel`) doivent vérifier `Sale::isDraft()` avant d'agir, et rejeter sinon (422 ou exception dédiée) — une vente déjà finalisée ou annulée est figée

## Critères d'acceptation

- Les migrations tournent sans erreur, **y compris en environnement de test SQLite**
- `Sale::complete()` refuse si le statut n'est pas `draft` (exception claire), y compris sous appel concurrent (test à deux appels simultanés ou séquentiels rapprochés simulant une race — pas de double mouvement de stock, pas de double référence)
- `Sale::complete()` refuse un panier vide
- `Sale::complete()` crée un `StockMovement(sale_consumption)` par ligne avec `article_id`, décrémente `stock_quantity` de l'article correspondant
- `Sale::cancel()` sur une vente complétée crée des `StockMovement(sale_return)` positifs (jamais de suppression de mouvement, jamais de réutilisation du type `sale_consumption` en positif)
- `Sale::cancel()` est idempotent — appeler deux fois ne réintègre pas le stock deux fois
- Pas de blocage si le stock passe en négatif
- `Sale::marginHt()` retourne un calcul cohérent avec les lignes de la vente
