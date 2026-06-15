# Modèle de données — Feature 22

## Tables

### `article_categories`
| Colonne      | Type         | Notes                  |
|-------------|-------------|------------------------|
| id          | bigint PK   |                        |
| name        | string(100) | unique                 |
| sort_order  | integer     | default 0              |
| timestamps  |             |                        |

### `article_subcategories`
| Colonne              | Type         | Notes                        |
|---------------------|-------------|------------------------------|
| id                  | bigint PK   |                              |
| article_category_id | FK          | cascade delete               |
| name                | string(100) |                              |
| sort_order          | integer     | default 0                    |
| timestamps          |             |                              |

Index unique : `(article_category_id, name)`

### `articles`
| Colonne                  | Type           | Notes                                         |
|-------------------------|---------------|-----------------------------------------------|
| id                      | bigint PK     |                                               |
| article_subcategory_id  | FK nullable   | null = article non catégorisé                 |
| reference               | string(100)   | unique                                        |
| designation             | string(255)   |                                               |
| purchase_price_ht       | integer       | en centimes                                   |
| sale_price_ht           | integer       | en centimes                                   |
| tva_rate                | decimal(5,2)  | default 20.00                                 |
| unit                    | string(50)    | ex. pièce, paire, kit, litre — default pièce  |
| supplier                | string(255)   | nullable, texte libre                         |
| notes                   | text          | nullable                                      |
| sort_order              | integer       | default 0                                     |
| timestamps              |               |                                               |

### `stock_movements`
| Colonne         | Type         | Notes                                                                                   |
|----------------|-------------|-----------------------------------------------------------------------------------------|
| id             | bigint PK   |                                                                                         |
| article_id     | FK          | cascade delete                                                                          |
| quantity       | integer     | positif = entrée, négatif = sortie                                                      |
| type           | enum        | `manual_in`, `manual_out`, `quote_consumption`, `maintenance_consumption`               |
| source_type    | string      | nullable — classe Eloquent source (ex. App\Models\QuoteLine)                           |
| source_id      | bigint      | nullable — ID de la source                                                              |
| unit_price_ht  | integer     | nullable, en centimes — prix unitaire au moment du mouvement                            |
| note           | text        | nullable                                                                                |
| created_at     | timestamp   |                                                                                         |
| updated_at     | timestamp   |                                                                                         |

Index : `(source_type, source_id)`, `(article_id)`

### Modifications sur tables existantes

**`quote_lines`** : ajout de `article_id` (FK nullable vers `articles`, set null on delete)

**`bike_maintenance_logs`** : ajout de `article_id` (FK nullable vers `articles`, set null on delete)

## Relations Eloquent

```
ArticleCategory  hasMany  ArticleSubcategory
ArticleSubcategory  belongsTo  ArticleCategory
ArticleSubcategory  hasMany  Article
Article  belongsTo  ArticleSubcategory (nullable)
Article  hasMany  StockMovement
Article  stockQuantity()  → computed: sum(stock_movements.quantity)
StockMovement  belongsTo  Article
StockMovement  morphTo  source (QuoteLine | BikeMaintenanceLog | null)
QuoteLine  belongsTo  Article (nullable)
BikeMaintenanceLog  belongsTo  Article (nullable)
```

## Règle sur le stock

Le stock courant d'un article n'est jamais stocké directement. Il est toujours calculé :

```php
// Sur le modèle Article
public function stockQuantity(): int
{
    return $this->stockMovements()->sum('quantity');
}
```

Cela garantit que l'historique est la source de vérité, et que l'API fournisseur pourra créer des mouvements sans migration supplémentaire.
