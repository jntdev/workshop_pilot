# Modèle de données — Feature 25

## Modification sur table existante

### `articles` (ajout)
| Colonne     | Type          | Notes                                                        |
|-------------|---------------|---------------------------------------------------------------|
| barcode     | string(64)    | nullable, `after('reference')`, index simple **non-unique**  |
| image_url   | string(500)   | nullable — URL photo produit fournie par le CSV (colonne 12 en 0-indexé, ex. `https://www.cgnfrance-pro.com/PartageWeb/Produits/435447_0.jpg`) |
| weight_kg   | decimal(8,3)  | nullable — poids brut fournisseur (colonne 11 en 0-indexé, virgule décimale ex. `0,33000`), conservé même sans usage UI immédiat |

Pas de contrainte unique sur `barcode` : le CSV fournisseur CGN contient des EAN vides (75 lignes sur l'export observé) et des EAN dupliqués entre deux fiches produit (variante « épuisée » du même article). Une contrainte unique bloquerait l'import.

Les autres colonnes CSV non mappées (code numérique colonne 6, date occasionnelle colonne 9, dimensions colonnes 15-19) restent ignorées : aucun usage identifié à ce jour, on ne les stocke pas pour ne pas alourdir le modèle sans besoin concret (contrairement à `image_url`/`weight_kg`, directement exploitables en UI caisse/catalogue).

### `stock_movements` (modification de type, impact feature 22 existante)

`sale_lines.quantity` est en `decimal(10,2)` (arbitrage : vendre au mètre/litre au comptoir comme `QuoteLine` le permet pour les devis). Or `stock_movements.quantity` est aujourd'hui un `integer` — une vente de `0.5` mètre de guidoline ne peut pas produire un mouvement de stock exact avec ce type. **Décision : migrer `stock_movements.quantity` de `integer` vers `decimal(10,2)`.**

Cette migration touche la feature 22 existante, déjà en production sur `develop` — pas seulement du code nouveau de la feature 25 :
- `database/migrations/*_create_stock_movements_table.php` : colonne `quantity` créée en `integer`, nouvelle migration `change_quantity_to_decimal_on_stock_movements_table` nécessaire (`$table->decimal('quantity', 10, 2)->change();`, avec la même précaution MySQL/SQLite que pour l'extension de l'enum `type` si le driver l'exige — à vérifier en implémentation, `decimal` est en principe supporté nativement par `doctrine/dbal` sous les deux drivers contrairement à `ENUM`)
- `app/Models/StockMovement.php` : cast `'quantity' => 'integer'` → `'quantity' => 'decimal:2'`
- `app/Models/Article.php` : `getStockQuantityAttribute()` a un type de retour `: int` explicite — devient `: float` (ou `string` selon la représentation choisie pour un `decimal:2` Eloquent ; à trancher en implémentation en cohérence avec le cast retenu), et `$appends = ['stock_quantity']` expose désormais un nombre décimal au frontend
- `app/Http/Requests/Api/StoreStockMovementRequest.php` : règle `'quantity' => ['required', 'integer', 'min:1']` → `['required', 'numeric', 'min:0.01']` (les mouvements manuels de la feature 22 pourront eux aussi accepter des quantités décimales, cohérent avec `Article::unit` qui autorise déjà `mètre`/`litre`)
- `resources/js/types/index.d.ts` : tout typage TypeScript de `stock_quantity`/`StockMovement.quantity` en `number` reste valide (JS ne distingue pas int/float), mais l'affichage catalogue existant (feature 22) doit être vérifié pour ne pas tronquer les décimales à l'affichage (ex. `Math.floor` implicite quelque part)

Aucun test ni donnée existante n'est cassé par ce changement de type (`integer` → `decimal(10,2)` est un élargissement, toute valeur entière existante reste représentée à l'identique), mais les fichiers ci-dessus doivent être audités et ajustés en implémentation, pas seulement les nouveaux fichiers de la feature 25.

## Attributs structurés filtrables (`article_attributes`)

### Constat qui motive ce choix
La désignation CGN (`Article.designation`) est du texte libre fournisseur. Une analyse sous-catégorie par sous-catégorie (échantillons réels du CSV, pas une supposition a priori) montre que sa structure interne est **hétérogène** : certaines sous-catégories suivent une grammaire positionnelle régulière et fiable, d'autres mélangent plusieurs familles de produits différentes sous un même libellé CGN (ex. `JEUX DE PEDALIERS` mélange boîtiers classiques à filetage et boîtiers intégrés d'une marque spécifique, avec une grammaire propre à chacun — cette sous-catégorie a été examinée puis écartée du périmètre v1, voir plus bas), d'autres enfin sont du texte libre sans patron exploitable.

Conséquence : pas d'extraction générique. Un extracteur dédié par sous-catégorie (ou sous-famille au sein d'une sous-catégorie si elle est hétérogène), appliqué uniquement là où un patron a été vérifié sur des échantillons réels, jamais deviné a priori.

### Point de vocabulaire important
Le libellé CGN `JEUX DE PEDALIERS` désigne les **boîtiers de pédalier** (axe/roulement central du cadre), pas l'ensemble manivelles + plateaux qu'on appelle communément « pédalier » à l'atelier. Ce dernier correspond aux sous-catégories CGN `PEDALIERS` (ensemble complet manivelles+plateaux monté) et `MANIVELLES` (manivelles seules). Distinction vérifiée par examen des désignations réelles, à garder en tête pour ne pas confondre les deux lors du développement des extracteurs.

### Table `article_attributes`
| Colonne     | Type            | Notes                                            |
|-------------|-----------------|---------------------------------------------------|
| id          | bigint PK       |                                                     |
| article_id  | FK              | `cascadeOnDelete`                                  |
| key         | string(50)      | ex. `practice_type`, `size_inches`, `etrto_size`, `valve_type`, `position`, `power_source`, `speed_count`, `speed_compat`, `tooth_range`, `tooth_count`, `chainring_diameter_mm`, `chainring_count`, `side`, `axle_type`, `supplier_status` |
| value       | string(255)     |                                                     |
| timestamps  |                 |                                                     |

Index unique sur `(article_id, key)` — un article n'a qu'une seule valeur par clé d'attribut. Table clé/valeur plutôt que colonnes dédiées sur `Article` : le catalogue couvrira à terme des familles de produits très différentes, une table générique évite une migration à chaque nouveau type d'attribut.

### Portée de l'extraction v1 — par sous-catégorie, avec un niveau de confiance différent

Périmètre validé, vérifié par échantillonnage réel du CSV (pas par supposition) :

Volumes vérifiés directement sur `StockNouveautesCgn022252.csv` (14 970 lignes au total, hors en-tête puisqu'il n'y en a pas). Les chiffres évolueront à chaque nouvel export fournisseur — le rapport de la commande d'import (ticket 25.2) doit toujours recalculer la couverture réelle plutôt que de se fier à ce tableau, qui documente l'état constaté au moment de la conception.

| Sous-catégorie CGN      | Volume | Couverture mesurée du patron | Grammaire observée (résumé) | Attributs extraits (`key`) |
|--------------------------|--------|----------------------|-------------------------------|-------------------------------|
| PNEUS VELO                | 1306   | 1242/1306 (95%) | `PNEU [pratique] [taille pouces] [TR\|TS] ... [TT\|TLR] [couleur] (ETRTO)` — `etrto_size` : regex `/(\d+)-(\d+)/` cherchée sans ancrage de fin de chaîne (beaucoup de désignations ont un suffixe libre après la parenthèse, ex. `VAE/EBIKE 25 KM/H`) | `practice_type`, `size_inches`, `etrto_size` |
| CHAMBRES VELO              | 339    | 334/339 (98.5%) | `CHAMBRE A AIR VELO [taille pouces] (ETRTO) [VS\|VP] [marque]` — même regex non ancrée ; notation dominante en plage `n/n-n` (ex. `44/62-507`), seule la borne `62-507` est capturée en v1 | `size_inches`, `etrto_size`, `valve_type` |
| PLATEAUX                   | 931    | 914/931 (98%) | `PLATEAU [pratique] [MONO\|DOUBLE\|TRIPLE] DIAM [n] [INTER\|EXTER\|INTERM] [n]DTS ... [n]V` | `practice_type`, `chainring_diameter_mm`, `tooth_count`, `speed_count` |
| PEDALIERS                  | 194    | 169/194 (87%) préfixées `PEDALIER` — le reste (`INTRAVIS`, `CAPTEUR DE VITESSE`...) est hors patron par construction, `supports()` doit filtrer sur le préfixe | `PEDALIER [pratique] [MONO\|DOUBLE\|TRIPLE] [dents]D L[longueur] [marque] [n]V` | `practice_type`, `tooth_range`, `crank_length_mm`, `speed_count` |
| MANIVELLES                 | 45     | Moyenne (bruit : vis/plaquettes mal classées dans cette sous-catégorie, `supports()` doit filtrer sur le préfixe `MANIVELLE`) | `MANIVELLE [GAUCHE\|DROITE\|(PAIRE)] [pratique] L[longueur] [marque] ...` | `side`, `practice_type`, `crank_length_mm` |
| ROUE-LIBRES/CASSETTES — famille `CASSETTE` (préfixe, inclut `CASSETTE ET CHAINE`) | 303 | Haute (quelques lignes sans `tooth_range`, ex. `CASSETTE 9V. ROUTE MICHE PRIMATO ADAPT. CAMPA` sans plage de dents indiquée) | `CASSETTE [n]V. [pratique] [marque] - [n]-[n]DTS` | `speed_count`, `practice_type`, `tooth_range` |
| ROUE-LIBRES/CASSETTES — famille `ROUE LIBRE` | 32 | Haute (2 sous-formes : multi-vitesses avec plage, mono-vitesse BMX avec valeur unique) | `ROUE LIBRE [n V.\|n DTS MONOVITESSE] [marque] [n-nDTS]` | `speed_count`, `tooth_range` (multi-vitesses) ou `tooth_count` (mono-vitesse) |
| ROUE-LIBRES/CASSETTES — famille `CORPS CASSETTE` (ou `CORPS DE CASSETTE`) | 42 | Haute | `CORPS [DE] CASSETTE SHIMANO [modèle] [n[/n/n]V] POUR AXE [QR\|TRAVERSANT]` | `speed_compat`, `axle_type` |
| ECLAIRAGE                  | 238    | 154/155 (99%) des lignes **réellement préfixées `ECLAIRAGE VELO`/`ECLAIRAGE FRONTAL`** — sur les 238 lignes de la sous-catégorie, seulement 155 (65%) ont ce préfixe : les 83 autres sont des accessoires connexes (supports, autocollants, chargeurs, piles, câbles) mal classés. `supports()` doit impérativement filtrer sur le préfixe de désignation, pas seulement la sous-catégorie, sous peine de 35% de faux positifs | `ECLAIRAGE VELO [AV\|AR\|AV+AR] [DYNAMO\|PILE\|RECHARG.] [marque]` | `position`, `power_source` |

`JEUX DE PEDALIERS` (276 lignes, boîtiers de pédalier) a été examiné et **écarté du périmètre v1** : mélange au moins deux familles à grammaires distinctes (boîtiers classiques à filetage vs boîtiers intégrés d'une marque spécifique) sous le même libellé CGN — un extracteur unique produirait trop de ratés ou d'extractions fausses. Pourra faire l'objet d'un extracteur dédié plus tard si le besoin se confirme, en séparant les deux familles.

Note méthode : `ROUE-LIBRES/CASSETTES` illustre le même phénomène que `JEUX DE PEDALIERS` (une sous-catégorie CGN mélangeant plusieurs familles de produits), mais ici les trois familles ont chacune une grammaire assez régulière pour être couvertes par un extracteur dédié — contrairement aux boîtiers de pédalier où les deux familles étaient trop hétérogènes individuellement. D'où la décision de les couvrir toutes les trois plutôt que d'écarter la sous-catégorie entière.

Chaque extracteur : (1) n'agit que sur sa sous-catégorie (ou sous-famille identifiée), (2) `extract()` retourne un tableau associatif ne contenant que les clés effectivement reconnues (`['practice_type' => 'ROUTE']`) — **jamais de clé avec une valeur `null`**, une clé non reconnue est simplement absente du tableau retourné, (3) est testé unitairement sur un échantillon réel du CSV. La désignation brute (`Article.designation`) est toujours conservée telle quelle — l'extraction est une donnée dérivée, jamais un remplacement, et peut être rejouée plus tard sur les articles déjà en base sans re-parser le CSV.

Toute sous-catégorie hors de cette liste reste sans attribut structuré en v1 : recherche/filtre uniquement par texte libre sur `designation` (comportement actuel, inchangé).

## Nouvelles tables

### `sales`
| Colonne         | Type                  | Notes                                                      |
|-----------------|-----------------------|-------------------------------------------------------------|
| id              | bigint PK             |                                                             |
| reference       | string, nullable, unique | `null` tant que la vente est `draft` ; générée uniquement à `complete()`. **Format unique et définitif : `VC-{YYYYMMDD}-{sale_id}`** (ex. `VC-20260902-437`) — dérivé de `Sale::id`, jamais d'un compteur journalier recalculé (`count() + 1`), pour éliminer toute course de concurrence sur la génération de référence. Une colonne non-nullable ferait échouer `SaleController::store()`, qui crée un brouillon avant toute référence |
| client_id       | FK nullable           | `Client`, `nullOnDelete` — vente comptoir souvent anonyme  |
| user_id         | FK nullable           | `User`, `nullOnDelete` — caissier (`Auth::id()`)           |
| status          | string(20)            | default `draft` — voir enum `SaleStatus`                   |
| payment_method  | string(20) nullable   | valeurs du type `PaymentMethod` existant (`cb`, `liquide`, `cheque`, `virement`, `autre`) |
| total_ht        | integer               | centimes, default 0                                        |
| total_tva       | integer               | centimes, default 0                                        |
| total_ttc       | integer               | centimes, default 0                                        |
| completed_at    | timestamp nullable    |                                                             |
| cancelled_at    | timestamp nullable    |                                                             |
| timestamps      |                       |                                                             |

### `sale_lines`
| Colonne             | Type                  | Notes                                                          |
|---------------------|-----------------------|------------------------------------------------------------------|
| id                  | bigint PK             |                                                                   |
| sale_id             | FK                    | `cascadeOnDelete`                                                |
| article_id          | FK nullable           | `nullOnDelete` — ligne libre possible (saisie manuelle sans article catalogue), comme `QuoteLine` |
| designation         | string(255)           | dénormalisée, copiée depuis l'article à l'ajout, ou saisie manuellement si ligne libre |
| reference           | string(100) nullable  | dénormalisée                                                     |
| quantity            | decimal(10,2)         | default 1 — même type que `QuoteLine::quantity` (arbitrage : la caisse doit pouvoir vendre au mètre/litre comme les devis, ex. colle, guidoline) |
| purchase_price_ht   | integer               | centimes — copié depuis `Article::purchase_price_ht` à l'ajout si `article_id` renseigné ; **saisi manuellement et obligatoire dans `StoreSaleLineRequest` si ligne libre** (arbitrage : le vendeur doit connaître/déclarer le coût d'achat pour qu'une vente hors catalogue reste incluse dans le calcul de marge) |
| unit_price_ttc      | integer               | centimes, copié depuis `Article::sale_price_ttc` à l'ajout, modifiable avant finalisation ; saisi manuellement si ligne libre |
| tva_rate            | decimal(5,2)          | default 20.00                                                    |
| line_total_ttc      | integer               | centimes, `quantity * unit_price_ttc` arrondi, dénormalisé        |
| position            | unsignedInteger       | default 0                                                        |
| timestamps          |                       |                                                                   |

**Index unique** sur `(sale_id, article_id)` — vérifié en conditions réelles sur MySQL (`Schema::unique()`) : plusieurs lignes avec `article_id = null` coexistent sans erreur (MySQL, comme SQLite, exclut les `NULL` de l'évaluation d'une contrainte unique composite), donc cet index protège strictement les lignes catalogue (`article_id` non nul, un seul par vente) sans empêcher plusieurs lignes libres. C'est la contrainte DB elle-même qui garantit l'absence de doublon sous concurrence — le `firstOrNew()` applicatif (ticket 25.4) reste nécessaire pour l'expérience (incrémenter plutôt qu'échouer), mais ne suffirait pas seul : sans la contrainte, deux scans quasi simultanés pourraient passer la vérification applicative avant que l'un des deux n'ait committé sa transaction. En complément, `Sale::addLine()` doit intercepter l'exception d'intégrité (violation de contrainte unique) sur un insert concurrent et se rabattre sur un incrément de la ligne existante plutôt que de laisser l'erreur remonter au client.

### Modification sur `stock_movements`
Ajout des valeurs `sale_consumption` et `sale_return` à l'enum DB `type`. **La migration doit gérer séparément MySQL et SQLite** : `ALTER TABLE ... MODIFY ... ENUM(...)` n'est valide qu'en MySQL (le driver de production) et échoue sous SQLite (le driver utilisé par les tests, où la colonne est un simple TEXT non contraint nativement) — condition sur `DB::getDriverName()`, exécuter le `DB::statement` uniquement si `mysql`.

## Enums

### `PaymentMethod` (`app/Enums/PaymentMethod.php`) — nouveau
Backed string enum, **n'existe aujourd'hui que côté TypeScript** (`resources/js/types/index.d.ts:302`) : `Cb = 'cb'`, `Liquide = 'liquide'`, `Cheque = 'cheque'`, `Virement = 'virement'`, `Autre = 'autre'`. Devient la source de vérité serveur ; le type TypeScript existant reste tel quel côté front, les deux doivent rester synchronisés manuellement (pas de génération automatique dans ce projet).

### `SaleStatus` (`app/Enums/SaleStatus.php`)
Backed string enum, calqué sur `StockMovementType` :
- `Draft` = `draft`
- `Completed` = `completed`
- `Cancelled` = `cancelled`

Méthode `label(): string`.

### `StockMovementType` (extension)
- Ajout du cas `SaleConsumption` = `sale_consumption`, avec son `label()` ("Consommation vente caisse"). Exclu de `isManual()` — non supprimable manuellement via `StockMovementController::destroy`, comme `QuoteConsumption`/`MaintenanceConsumption`. Mouvement toujours négatif, créé par `Sale::complete()`.
- Ajout du cas `SaleReturn` = `sale_return`, avec son `label()` ("Réintégration suite annulation vente"). Exclu de `isManual()`. Mouvement toujours positif, créé par `Sale::cancel()` sur une vente précédemment complétée — type distinct de `SaleConsumption` pour que l'historique reste lisible (un mouvement positif portant le type "consommation" serait trompeur).

## Relations Eloquent

```
Article  scopeByBarcode(string $barcode)   → where('barcode', $barcode) exact
Article  scopeSearch(string $term)          → étendu pour inclure barcode LIKE

Sale  belongsTo  Client (nullable)
Sale  belongsTo  User (nullable)
Sale  hasMany  SaleLine (ordonné par position)
SaleLine  belongsTo  Sale
SaleLine  belongsTo  Article (nullable)

StockMovement  morphTo  source (QuoteLine | BikeMaintenanceLog | Sale | null)
```

## Règles de calcul

- `line_total_ttc = round(quantity * unit_price_ttc)` ; `line_total_ht = round(line_total_ttc / (1 + tva_rate / 100))` ; `line_total_tva = line_total_ttc - line_total_ht`
- `Sale::recalculateTotals()` : `total_ttc`/`total_ht`/`total_tva` = somme des valeurs correspondantes de chaque `SaleLine`
- `Sale::marginHt(): int` (accessor calculé, non stocké), tous les montants en centimes, **jamais de division par 100** (`purchase_price_ht` est déjà en centimes) : `$costHt = $sale->lines->sum(fn (SaleLine $line) => (int) round((float) $line->quantity * $line->purchase_price_ht)); $marginHt = $sale->total_ht - $costHt;` — l'arrondi est nécessaire car `quantity` est décimal, `quantity * purchase_price_ht` peut ne pas être un entier. Tient la promesse de "marge suivie dès la v1" (arbitrage 3)
- `Sale::complete()` : dans une transaction avec verrouillage (`lockForUpdate()`) pour empêcher tout double appel concurrent de produire un double effet. Refuse si le statut n'est pas `draft` (revérifié après acquisition du verrou) ou si le panier est vide. Génère la référence de façon non-ambiguë vis-à-vis de la concurrence — dérivée de `Sale::id` (déjà unique par nature) plutôt que d'un compteur journalier recalculé à la volée, ex. `'VC-' . now()->format('Ymd') . '-' . $this->id`. Pour chaque `SaleLine` ayant un `article_id`, crée un `StockMovement` : `quantity = -abs(quantity)`, `type = sale_consumption`, `source_type = Sale::class`, `source_id = $this->id`, `unit_price_ht = SaleLine.purchase_price_ht`. Marque `completed_at = now()`, `status = Completed`. Pas de blocage sur stock négatif (cohérent avec l'arbitrage 7 de la feature 22).
- `Sale::cancel()` : dans une transaction verrouillée, idempotent (n'agit pas si déjà `Cancelled`). Si la vente était `Completed`, crée des `StockMovement` de type `sale_return` (positifs, jamais `sale_consumption` réutilisé en positif — voir enum ci-dessus) en sens inverse des mouvements initiaux, jamais de suppression d'un mouvement existant. Marque `cancelled_at = now()`, `status = Cancelled`.
