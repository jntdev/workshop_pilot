# Modèle de données — Feature 27

## Aucune nouvelle table

Cette feature ne crée ni ne modifie de table. Elle réutilise :
- `articles` (déjà toutes les colonnes nécessaires : `reference`, `barcode`, `designation`, `article_subcategory_id`, `brand_id`, `purchase_price_ht`, `sale_price_ttc`, `tva_rate`, `unit`, `image_url`)
- `stock_movements` (mouvement `manual_in`, type déjà existant)
- `article_attributes` (attributs de filtre optionnels, mêmes clés que celles retournées par `filterOptions()`, feature 26)

## Lookup par code-barres : réutilisation de la logique existante

`SaleController::lookupArticle()` (feature 25) implémente déjà la priorité EAN exact → référence exacte → recherche texte, avec gestion explicite du cas ambigu (plusieurs articles partageant le même `barcode`). Cette feature réutilise **le même mécanisme de résolution EAN exact** (`Article::byBarcode($code)`), sans réinventer une nouvelle route de lookup dédiée — un nouvel appel simple à cette scope suffit :

```php
$articles = Article::byBarcode($scannedCode)->get();

if ($articles->count() === 1) {
    // article trouvé, retourne sa fiche
} elseif ($articles->count() > 1) {
    // cas ambigu — même traitement que lookupArticle() : ne jamais choisir arbitrairement
} else {
    // aucun résultat — code-barres inconnu, proposer la création
}
```

Nouvelle route dédiée à ce contexte plutôt que de surcharger `SaleController` (contexte différent — pas une vente, un scan d'inventaire) :

```
GET /api/inventory/lookup-barcode?code={code}
```

Réponse alignée sur le format déjà utilisé par `lookupArticle()` (`{ result: "found"|"ambiguous"|"not_found", article|articles }`), pour rester cohérent avec un pattern déjà éprouvé plutôt qu'en inventer un nouveau.

## Mouvement d'entrée sur article déjà existant

Réutilise directement `POST /api/articles/{id}/stock-movements` (existant, feature 22/25), avec `type: manual_in` et la quantité saisie — **aucune nouvelle route nécessaire** pour ce cas. Le pas de saisie respecte `quantityStep(article.unit)` (feature 26 bis, entier pour "pièce"/"paire"/"kit", décimal pour "litre"/"kg").

## Création rapide d'un article inconnu

### Champs obligatoires (décision explicite)
`photo`, `designation`, `article_subcategory_id`, `brand_id`, `purchase_price_ht`, `sale_price_ttc`, **`quantity`** (quantité comptée — voir arbitrage 10, la création d'article doit produire un mouvement de stock, pas seulement une fiche). Le `barcode` scanné devient à la fois `barcode` et `reference` de l'article — **`reference` n'est pas un champ du payload envoyé par le frontend, elle est dérivée côté serveur depuis `barcode`** (voir `04-arbitrages.md`, arbitrage 7 révisé) : élimine toute possibilité d'incohérence plutôt que de faire confiance à un client pour envoyer deux fois la même valeur.

### Champs optionnels
`tva_rate` (défaut 20.00, cohérent avec la commande d'import CGN), `unit` (défaut `pièce`), attributs de filtre (`article_attributes`, saisie libre selon les clés retournées par `filterOptions()` **pour la sous-catégorie choisie dans ce même formulaire** — jamais obligatoires, l'article reste trouvable par désignation/sous-catégorie même sans eux).

### Nouvel endpoint dédié de création rapide, validation stricte isolée

**Décision (arbitrage 11)** : `StoreArticleRequest` reste inchangé (permissif, utilisé par `ArticleForm.tsx` pour la création/édition manuelle classique). La création rapide depuis le scan passe par un endpoint et une validation dédiés, pour garantir côté serveur les obligations propres à ce flux sans risquer de durcir — ou de complexifier avec un mode conditionnel — le formulaire existant :

```
POST /api/inventory/articles
```

**`reference` n'est jamais un champ d'entrée validé** — elle est calculée par le contrôleur à partir de `barcode` déjà validé, avant la création de l'`Article`. Ça élimine complètement le risque qu'un client envoie `reference !== barcode` (voir arbitrage 7 révisé) : il n'y a tout simplement pas de champ `reference` à falsifier dans la requête.

`php artisan make:request Api/StoreInventoryArticleRequest` :
```php
public function rules(): array
{
    return [
        'barcode' => ['required', 'string', 'max:64', 'unique:articles,reference'],
        'designation' => ['required', 'string', 'max:255'],
        'article_subcategory_id' => ['required', 'integer', 'exists:article_subcategories,id'],
        'brand_id' => ['required', 'integer', 'exists:brands,id'],
        'purchase_price_ht' => ['required', 'integer', 'min:0'],
        'sale_price_ttc' => ['required', 'integer', 'min:0'],
        'quantity' => ['required', 'numeric', 'min:0.01'],
        'image_url' => ['required', 'string', 'max:500', 'starts_with:/storage/articles/'],
        'attributes' => ['sometimes', 'array'],
    ];
}
```
La contrainte `unique:articles,reference` s'applique ici sur la colonne `barcode` validée (pas une colonne `reference` du payload) — Laravel vérifie l'unicité de la *valeur soumise* contre la colonne `reference` de la table `articles`, peu importe le nom du champ d'entrée. `image_url` doit commencer par `/storage/articles/` (préfixe produit exclusivement par `POST /api/articles/upload-photo`, ticket 02) — garantit côté serveur que la photo provient réellement de ce flux d'upload, pas une URL arbitraire fournie par le client (voir feedback, point 4 : validation stricte retenue plutôt que la dette acceptée).

`attributes.*` n'a plus de règle générique `string` — voir la liste blanche stricte dans la section suivante, qui remplace cette validation trop permissive (voir feedback, point 3).

`InventoryController::storeArticle(StoreInventoryArticleRequest $request): JsonResponse` :
1. Crée l'`Article` avec `reference = $validated['barcode']`, `barcode = $validated['barcode']`, `tva_rate` 20.00, `unit` 'pièce' en dur (comme la commande d'import CGN)
2. Traduit et persiste les `attributes` fournis en `ArticleAttribute`, après filtrage par liste blanche (voir section suivante)
3. Crée immédiatement le mouvement de stock (`$article->stockMovements()->create(['quantity' => $validated['quantity'], 'type' => StockMovementType::ManualIn])`) — dans la même requête, pas une étape séparée que le frontend pourrait oublier d'appeler
4. Les étapes 1-3 dans une transaction (`DB::transaction`) pour éviter un article créé sans son mouvement en cas d'échec partiel

### Nouvelle route d'upload photo

```
POST /api/articles/upload-photo
```
Requête `multipart/form-data`, champ `photo` (image, taille max raisonnable — reprendre la même contrainte que `MobileUploadController::upload()` : `image|mimes:jpeg,png,webp,heic|max:5120`). Stocke le fichier dans `storage/app/public/articles/`, retourne l'URL publique (`Storage::url(...)`) à injecter dans `image_url` lors de la création de l'article. Route indépendante de la création d'article — la photo est uploadée d'abord (dès la capture caméra, retour immédiat pour prévisualisation), puis son URL résultante est intégrée au payload de `POST /api/inventory/articles` envoyé ensuite. **Dette acceptée en v1** : si l'utilisateur annule le formulaire après upload ou si la création échoue, le fichier reste orphelin sur le disque — pas de nettoyage automatique prévu à ce stade (voir arbitrage 12).

## Attributs filtrables pour saisie libre : traduction des clés virtuelles

Réutilisation de `GET /api/article-subcategories/{id}/filter-options` (feature 26) pour peupler dynamiquement les champs d'attributs optionnels **une fois la sous-catégorie choisie dans le formulaire de création rapide** — les attributs proposés sont donc déjà spécifiques à cette sous-catégorie par construction (`filterOptions()` est appelé avec cet id précis), jamais un mélange d'attributs d'autres familles de produits. Un pneu ne se verra jamais proposer "Type de valve" (Chambres) ou "Nombre de dents" (Plateaux).

### Liste blanche stricte, jamais une clé arbitraire acceptée

**Décision (arbitrage 14, feedback point 3)** : `InventoryController::storeArticle()` ignore silencieusement toute clé d'`attributes` qui n'appartient pas à l'ensemble suivant — union de `ArticleController::FILTERABLE_KEYS` (feature 26) et des 5 clés virtuelles traduisibles :

```php
private const ACCEPTED_ATTRIBUTE_KEYS = [
    // clés brutes (ArticleController::FILTERABLE_KEYS, feature 26)
    'practice_type', 'valve_type', 'chainring_diameter_mm', 'tooth_count',
    'speed_count', 'crank_length_mm', 'side', 'speed_compat', 'axle_type',
    'position', 'power_source',
    // clés virtuelles traduisibles (voir décomposition ci-dessous)
    'wheel_diameter', 'wheel_width_mm', 'wheel_width_inches',
    'tooth_range_min', 'tooth_range_max',
];
```

Toute clé hors de cette liste (y compris une clé qui existerait dans `article_attributes` pour d'autres besoins non liés au filtrage, ex. `supplier_status`) est rejetée avant traduction — jamais persistée, jamais silencieusement acceptée comme une clé brute nouvelle. Les valeurs vides ou nulles ne sont jamais persistées non plus (cohérent avec le comportement déjà appliqué aux valeurs composites partielles, voir plus bas).

Les clés virtuelles de présentation (`wheel_diameter`, `wheel_width_mm`, `wheel_width_inches`, `tooth_range_min`, `tooth_range_max`) ne sont **jamais persistées telles quelles** dans `article_attributes` — elles sont traduites vers leurs vraies clés composites avant sauvegarde, côté `InventoryController::storeArticle()` :

- `wheel_diameter` + `wheel_width_mm` saisis → si les deux sont présents, reconstruit `etrto_size = "{wheel_width_mm}-{wheel_diameter}"` (même ordre que produit par les extracteurs de la feature 25, largeur-diamètre). Si un seul des deux est saisi, **ne pas persister de valeur partielle/inventée** — ignorer silencieusement plutôt que stocker une donnée fausse.
- `wheel_width_inches` saisi seul (sans diamètre) → pas de recomposition possible vers `size_inches` (qui a besoin du diamètre en préfixe) ; ignoré si `wheel_diameter` est absent.
- `wheel_diameter` + `wheel_width_inches` saisis → reconstruit `size_inches = "{wheel_diameter}X{wheel_width_inches}"`.
- `tooth_range_min` + `tooth_range_max` saisis → reconstruit `tooth_range = "{tooth_range_min}-{tooth_range_max}"` ; un seul des deux fourni → ignoré, même logique que ci-dessus.
- Toutes les autres clés (`practice_type`, `valve_type`, `chainring_diameter_mm`, `tooth_count`, `speed_count`, `crank_length_mm`, `side`, `speed_compat`, `axle_type`, `position`, `power_source`) sont des clés brutes déjà stockées telles quelles par les extracteurs — persistées directement sans traduction.

Cette traduction garde le modèle de données cohérent : un article créé depuis le scan reste indistinguable, du point de vue de `article_attributes`, d'un article importé depuis le CSV CGN — il est filtrable exactement de la même façon.
