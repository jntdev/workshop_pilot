# Tickets Backend — Feature 22

---

## Ticket 22.1 : Migrations et modèles du catalogue

**Type** : Backend / Data
**Priorité** : Haute
**Estimation** : 1h30

### Description

Créer les tables `article_categories`, `article_subcategories`, `articles` et `stock_movements`, ainsi que les modèles Eloquent correspondants.

### Tâches

- [ ] Migration `create_article_categories_table`
  - `id`, `name` (string 100, unique), `sort_order` (int default 0), `timestamps`
- [ ] Migration `create_article_subcategories_table`
  - `id`, `article_category_id` (FK cascade delete), `name` (string 100), `sort_order` (int default 0), `timestamps`
  - Index unique sur `(article_category_id, name)`
- [ ] Migration `create_articles_table`
  - `id`, `article_subcategory_id` (FK nullable, set null on delete), `reference` (string 100, unique), `designation` (string 255), `purchase_price_ht` (int, centimes), `sale_price_ht` (int, centimes), `tva_rate` (decimal 5,2, default 20.00), `unit` (string 50, default 'pièce'), `supplier` (string 255, nullable), `notes` (text, nullable), `sort_order` (int default 0), `timestamps`
- [ ] Migration `create_stock_movements_table`
  - `id`, `article_id` (FK cascade delete), `quantity` (int, positif = entrée / négatif = sortie), `type` (enum : `manual_in`, `manual_out`, `quote_consumption`, `maintenance_consumption`), `source_type` (string nullable), `source_id` (bigint nullable), `unit_price_ht` (int nullable, centimes), `note` (text nullable), `timestamps`
  - Index sur `(source_type, source_id)` et `(article_id)`
- [ ] Modèle `ArticleCategory` :
  - `$fillable` : name, sort_order
  - `hasMany(ArticleSubcategory)`
  - Scope `ordered()` : orderBy sort_order
- [ ] Modèle `ArticleSubcategory` :
  - `$fillable` : article_category_id, name, sort_order
  - `belongsTo(ArticleCategory)`
  - `hasMany(Article)`
  - Scope `ordered()` : orderBy sort_order
- [ ] Modèle `Article` :
  - `$fillable` : article_subcategory_id, reference, designation, purchase_price_ht, sale_price_ht, tva_rate, unit, supplier, notes, sort_order
  - Casts : purchase_price_ht (int), sale_price_ht (int), tva_rate (float)
  - `belongsTo(ArticleSubcategory)` (nullable)
  - `hasMany(StockMovement)`
  - Méthode `stockQuantity(): int` — sum des mouvements
  - `$appends = ['stock_quantity']` avec `getStockQuantityAttribute()`
  - Scope `ordered()` : orderBy sort_order
- [ ] Modèle `StockMovement` :
  - `$fillable` : article_id, quantity, type, source_type, source_id, unit_price_ht, note
  - Cast type en enum `StockMovementType`
  - `belongsTo(Article)`
  - `morphTo('source')` pour la relation polymorphique
- [ ] Enum `StockMovementType` : `ManualIn`, `ManualOut`, `QuoteConsumption`, `MaintenanceConsumption`
- [ ] Factories `ArticleCategoryFactory`, `ArticleSubcategoryFactory`, `ArticleFactory`, `StockMovementFactory`
- [ ] Seeder minimal avec quelques catégories réalistes (Pneus, Transmission, Freins, Éclairage, Accessoires) et sous-catégories (ex. Pneus > Chambre à air, Pneus > Pneu VTT, Transmission > Chaîne 7v, Transmission > Chaîne 11v, Transmission > Cassette)

### Critères d'acceptation

- Les migrations tournent sans erreur (`php artisan migrate`)
- Le stock d'un article est calculé via `sum(quantity)` sur ses mouvements, jamais stocké
- La relation polymorphique `source` est fonctionnelle sur `StockMovement`
- Les factories permettent de créer des données de test cohérentes

---

## Ticket 22.2 : API catalogue — catégories et sous-catégories

**Type** : Backend / API
**Priorité** : Haute
**Estimation** : 1h

### Description

Exposer les endpoints CRUD pour gérer les catégories et sous-catégories d'articles. Ces endpoints alimenteront la page de paramétrage du catalogue.

### Tâches

- [ ] `ArticleCategoryController` (API) avec méthodes :
  - `index()` : liste ordonnée avec sous-catégories imbriquées (`with('subcategories')`)
  - `store()` : création avec validation
  - `update(int $id)` : mise à jour
  - `destroy(int $id)` : suppression (refus si sous-catégories ou articles associés)
  - `reorder(Request $request)` : mise à jour des sort_order en masse
- [ ] `ArticleSubcategoryController` (API) avec méthodes :
  - `store()` : création (avec `article_category_id`)
  - `update(int $id)` : mise à jour
  - `destroy(int $id)` : suppression (refus si articles associés)
  - `reorder(Request $request)` : mise à jour des sort_order en masse
- [ ] Form Requests : `StoreArticleCategoryRequest`, `UpdateArticleCategoryRequest`, `StoreArticleSubcategoryRequest`, `UpdateArticleSubcategoryRequest`
- [ ] Routes dans `routes/api.php` (groupe auth) :
  ```
  GET    /api/article-categories
  POST   /api/article-categories
  POST   /api/article-categories/reorder       ← avant {id}
  PUT    /api/article-categories/{id}
  DELETE /api/article-categories/{id}
  POST   /api/article-subcategories
  POST   /api/article-subcategories/reorder    ← avant {id}
  PUT    /api/article-subcategories/{id}
  DELETE /api/article-subcategories/{id}
  ```

### Critères d'acceptation

- `GET /api/article-categories` retourne les catégories avec leurs sous-catégories imbriquées, ordonnées
- La suppression d'une catégorie avec des sous-catégories existantes retourne une erreur 422 claire
- La suppression d'une sous-catégorie avec des articles existants retourne une erreur 422 claire
- Le reorder fonctionne par échange de `sort_order` comme pour les bike-categories

---

## Ticket 22.3 : API catalogue — articles (CRUD + recherche)

**Type** : Backend / API
**Priorité** : Haute
**Estimation** : 1h30

### Description

Exposer les endpoints pour gérer les articles du catalogue, ainsi qu'un endpoint de recherche optimisé pour l'autocomplete dans les devis.

### Tâches

- [ ] `ArticleController` (API) avec méthodes :
  - `index(Request $request)` : liste paginée, filtrable par `subcategory_id` et `search` (référence ou désignation)
  - `show(int $id)` : détail d'un article avec `stock_quantity`
  - `store()` : création
  - `update(int $id)` : mise à jour
  - `destroy(int $id)` : suppression (avec avertissement si article lié à des QuoteLine ou BikeMaintenanceLog, pas de blocage)
  - `search(Request $request)` : endpoint dédié à l'autocomplete — retourne max 10 résultats sur `reference` LIKE ou `designation` LIKE, avec `stock_quantity`
- [ ] Form Requests : `StoreArticleRequest`, `UpdateArticleRequest`
  - Validation `reference` unique (sauf sur update)
  - `purchase_price_ht` et `sale_price_ht` : entiers en centimes, min 0
  - `tva_rate` : decimal entre 0 et 100
- [ ] Routes dans `routes/api.php` :
  ```
  GET    /api/articles/search          ← avant /articles/{id}
  GET    /api/articles
  POST   /api/articles
  GET    /api/articles/{id}
  PUT    /api/articles/{id}
  DELETE /api/articles/{id}
  ```

### Format de réponse `search`

```json
[
  {
    "id": 12,
    "reference": "CH-700-PR",
    "designation": "Chambre à air 700x23-25 Presta",
    "purchase_price_ht": 280,
    "sale_price_ht": 590,
    "tva_rate": 20.00,
    "unit": "pièce",
    "stock_quantity": 8,
    "subcategory": { "id": 2, "name": "Chambre à air" },
    "category": { "id": 1, "name": "Pneus" }
  }
]
```

### Critères d'acceptation

- La recherche `?q=CH-7` retourne les articles dont la référence ou la désignation contient "CH-7" (insensible à la casse)
- Le `stock_quantity` est toujours calculé depuis les mouvements, jamais stocké
- Un article peut être supprimé même s'il est lié à des lignes de devis (le `article_id` passe à null sur ces lignes)

---

## Ticket 22.4 : API stock — mouvements

**Type** : Backend / API
**Priorité** : Haute
**Estimation** : 1h

### Description

Exposer les endpoints pour créer des mouvements de stock manuels et consulter l'historique d'un article.

### Tâches

- [ ] `StockMovementController` (API) avec méthodes :
  - `index(int $articleId)` : historique des mouvements d'un article, du plus récent au plus ancien
  - `store(Request $request, int $articleId)` : créer un mouvement manuel (`manual_in` ou `manual_out` uniquement — les autres types sont créés automatiquement)
  - `destroy(int $id)` : suppression d'un mouvement manuel uniquement (bloquer si type `quote_consumption` ou `maintenance_consumption`)
- [ ] Form Request `StoreStockMovementRequest` :
  - `type` : in `manual_in`, `manual_out`
  - `quantity` : integer, min 1 (la direction est donnée par le type, pas le signe)
  - `unit_price_ht` : nullable, integer min 0
  - `note` : nullable string
- [ ] Le controller signe la quantité : `manual_in` → positif, `manual_out` → négatif
- [ ] Routes dans `routes/api.php` :
  ```
  GET    /api/articles/{article}/stock-movements
  POST   /api/articles/{article}/stock-movements
  DELETE /api/stock-movements/{movement}
  ```

### Critères d'acceptation

- L'ajout d'un mouvement `manual_in` de quantité 5 augmente `stock_quantity` de 5
- L'ajout d'un mouvement `manual_out` de quantité 2 diminue `stock_quantity` de 2
- La suppression d'un mouvement `quote_consumption` est refusée avec un message clair
- L'historique est trié du plus récent au plus ancien

---

## Ticket 22.5 : Liaison QuoteLine → Article

**Type** : Backend / Data + API
**Priorité** : Haute
**Estimation** : 45min

### Description

Ajouter `article_id` sur `QuoteLine` et adapter l'API des devis pour accepter et retourner cet identifiant.

### Tâches

- [ ] Migration `add_article_id_to_quote_lines_table` :
  - `article_id` FK nullable vers `articles`, `onDelete('set null')`
- [ ] Ajouter `article_id` dans `$fillable` de `QuoteLine`
- [ ] Ajouter relation `belongsTo(Article)` sur `QuoteLine`
- [ ] Mettre à jour la validation dans `QuoteController` pour accepter `article_id` nullable sur chaque ligne
- [ ] Mettre à jour `formatQuote()` pour inclure `article_id` dans la réponse
- [ ] Mettre à jour les types TypeScript `QuoteLine` dans `resources/js/types/index.d.ts`

### Critères d'acceptation

- Une QuoteLine peut avoir un `article_id` renseigné ou null
- La mise à jour d'un devis avec `article_id` sur une ligne le persiste correctement
- Le `article_id` est retourné dans le payload du devis (pour que le frontend sache si la ligne est liée au catalogue)

---

## Ticket 22.6 : Liaison BikeMaintenanceLog → Article

**Type** : Backend / Data + API
**Priorité** : Moyenne
**Estimation** : 30min

### Description

Même logique que le ticket 22.5, appliquée à `BikeMaintenanceLog`.

### Tâches

- [ ] Migration `add_article_id_to_bike_maintenance_logs_table` :
  - `article_id` FK nullable vers `articles`, `onDelete('set null')`
- [ ] Ajouter `article_id` dans `$fillable` de `BikeMaintenanceLog`
- [ ] Ajouter relation `belongsTo(Article)` sur `BikeMaintenanceLog`
- [ ] Mettre à jour `BikeMaintenanceLogController` pour accepter et retourner `article_id`
- [ ] Mettre à jour les types TypeScript `BikeMaintenanceLog`

### Critères d'acceptation

- Un log de maintenance peut référencer un article du catalogue
- La relation est nullable : les logs existants ne sont pas affectés

---

## Ticket 22.7 : Tests backend

**Type** : QA / Tests
**Priorité** : Haute
**Estimation** : 2h

### Tests à écrire

**Catalogue**
- [ ] CRUD complet `ArticleCategory` (création, mise à jour, suppression avec et sans sous-catégories)
- [ ] CRUD complet `ArticleSubcategory` (avec et sans articles associés)
- [ ] CRUD complet `Article`
- [ ] Unicité de la référence article
- [ ] `GET /api/articles/search?q=CH` retourne les bons résultats (max 10, insensible à la casse)
- [ ] Suppression d'une catégorie avec sous-catégories retourne 422

**Stock**
- [ ] Mouvement `manual_in` augmente `stock_quantity`
- [ ] Mouvement `manual_out` diminue `stock_quantity`
- [ ] `stock_quantity` est la somme de tous les mouvements
- [ ] Suppression d'un mouvement `quote_consumption` est refusée
- [ ] Suppression d'un mouvement `manual_in` met à jour le stock

**Liaisons**
- [ ] Création d'une QuoteLine avec `article_id` valide
- [ ] Suppression d'un article passe `article_id` à null sur les QuoteLines liées
- [ ] Même test pour `BikeMaintenanceLog`
