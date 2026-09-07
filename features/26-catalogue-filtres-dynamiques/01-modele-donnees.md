# Modèle de données — Feature 26

## Aucune migration de structure de données

Cette feature ne modifie ni ne crée de colonne sur `articles`, `article_attributes`, `article_categories` ou `article_subcategories`. Les filtres "Diamètre de roue"/"Largeur" et "Petit pignon"/"Grand pignon" sont des **vues de présentation** calculées à la volée à partir des valeurs existantes de `etrto_size` et `tooth_range` — jamais stockées comme colonnes ou clés `article_attributes` séparées.

## Migration additive recommandée (performance, non bloquante)

`database/migrations/XXXX_add_key_index_to_article_attributes_table.php` :
```php
Schema::table('article_attributes', function (Blueprint $table) {
    $table->index('key');
});
```

Justification : `article_attributes` a aujourd'hui un index unique `(article_id, key)` — la colonne `key` en seconde position n'est pas exploitable seule pour un `GROUP BY key` ou un `WHERE key = ?` isolé, motif de requête qui devient plus fréquent avec cette feature (`filterOptions()` et le filtrage étendu de `index()`). Non indispensable au volume actuel (~10 000 lignes, quelques millisecondes de scan complet même sans index), mais peu coûteuse et anticipe la croissance du catalogue à chaque réimport CGN. N'affecte pas l'index unique existant.

## Nouvelle ressource API (pas une table) : options de filtre par sous-catégorie

`GET /api/article-subcategories/{id}/filter-options`

### Requête d'agrégation des attributs

```php
ArticleAttribute::query()
    ->join('articles', 'articles.id', '=', 'article_attributes.article_id')
    ->where('articles.article_subcategory_id', $id)
    ->select('article_attributes.key', 'article_attributes.value')
    ->distinct()
    ->get()
    ->groupBy('key');
```

Un `join` direct est préféré à un `whereHas('article', ...)` : permet de sélectionner explicitement `(key, value)` de la table jointe avec un `DISTINCT` net, plus direct qu'une sous-requête `EXISTS` pour ce cas de dédoublonnage.

### Liste blanche des clés filtrables

```php
private const FILTERABLE_KEYS = [
    'practice_type', 'valve_type', 'chainring_diameter_mm', 'tooth_count',
    'speed_count', 'crank_length_mm', 'side', 'speed_compat', 'axle_type',
    'position', 'power_source',
    // etrto_size et tooth_range sont décomposés, jamais exposés bruts (voir ci-dessous)
];
```

Clés explicitement exclues, jamais renvoyées comme filtre : `supplier_status` (statut fournisseur, pas une caractéristique produit), `size_inches` (redondant avec `etrto_size`).

### Décomposition des attributs composites

Constat empirique déterminant (vérifié en base, pas supposé) : `size_inches` a un format différent selon la sous-catégorie.
- **Pneus** : format `"{diamètre}X{largeur}"` (ex. `"700X28C"`, `"26X1.75"`, `"27.5X2.10"`) — diamètre et largeur dans le même champ, séparés par `X`.
- **Chambres** : le champ **est** directement le diamètre, sans largeur (ex. `"26\""`, `"27.5\""`, `"20\" A 29\""` pour les chambres multi-compatibles) — pas de `X` à parser.

**`wheel_diameter`** (nouvelle clé virtuelle, remplace l'ancien `etrto_diameter_mm`) :
- Pour Pneus : `explode('X', strtoupper($value))[0]` (préfixe avant le premier `X`).
- Pour Chambres : la valeur brute nettoyée (guillemets et espaces retirés) — pas de split par `X`, sauf si un `X` est présent (certaines valeurs Chambres suivent aussi le format Pneus lorsqu'un article est saisi de façon incohérente ; le split reste appliqué uniformément, il est simplement no-op quand il n'y a pas de `X`).
- Filtrage par liste blanche de diamètres commerciaux vélo réels (constante `COMMERCIAL_WHEEL_DIAMETERS`), établie à partir des valeurs effectivement observées en base sur Pneus et Chambres :
  ```php
  private const COMMERCIAL_WHEEL_DIAMETERS = [
      '10', '12', '14', '16', '18', '20', '22', '24', '26', '27', '27.5', '28', '29',
      '350', '400', '400A', '450', '450A', '500', '500A', '550', '550A', '600A',
      '650', '650A', '650B', '700', '700C',
  ];
  ```
  Toute valeur dont le préfixe extrait n'est pas dans cette liste après nettoyage (guillemets, espaces) est ignorée silencieusement (bruit fournisseur : `TR`, `TRAINER`, `URBAIN`, `/`, `/URBAIN`, plages multi-diamètres type `"20\" A 29\""`, ou un mot sans chiffre en tête).
- Les suffixes de lettre ne sont **jamais fusionnés** : `700` et `700C` restent deux entrées distinctes de `wheel_diameter`, de même que `650`/`650A`/`650B` (décision explicite, voir `04-arbitrages.md`).
- `etrto_size` n'est plus utilisé pour le diamètre dans cette feature (raison : `622mm` regroupe à la fois "700" et "29"", et des valeurs BSD voisines comme 630/635 sont des tailles commerciales différentes de 700 malgré leur proximité numérique en mm — voir `00-contexte.md`). Il reste en base tel que produit par la feature 25, et redevient la source de la largeur en mm (voir ci-dessous).

**`wheel_width_mm`** (largeur en millimètres, nouvelle clé virtuelle, source `etrto_size`) :
- Premier nombre de `etrto_size` (format `"{largeur}-{diametre_mm}"`), ex. `28` dans `"28-622"`.
- Logique de décomposition identique à l'ancien `etrto_width_mm` (même code, renommage uniquement pour clarifier qu'il s'agit de la largeur exprimée en mm, à distinguer de `wheel_width_inches` ci-dessous). Ensemble complet retourné, sans restriction croisée avec le diamètre sélectionné (inchangé, voir arbitrage v1).

**`wheel_width_inches`** (largeur en pouces/notation commerciale, nouvelle clé virtuelle, source `size_inches`) :
- Suffixe après le premier `X` de `size_inches` (Pneus uniquement — Chambres n'a pas de `X` dans ce champ, donc pas de `wheel_width_inches` pour cette sous-catégorie).
- Filtré par un format simple accepté : `/^\d+(\.\d+)?[A-Z]?$/` après `strtoupper(trim($value))` — un nombre (entier ou décimal), avec au plus une lettre de talon en suffixe (`28`, `28C`, `1.75`, `35B`). Toute valeur ne correspondant pas à ce format (plages `"1.50-2.40"`, fractions `"1 3/8"`, virgule décimale `"2,30"`) est ignorée silencieusement — hors périmètre v1, voir `04-arbitrages.md`.
- Ensemble complet retourné, sans restriction croisée avec le diamètre ou la largeur mm sélectionnés (même logique v1 simple que les autres filtres).

Le filtre "Largeur" affiche donc **deux `<select>` indépendants** côte à côte : Largeur (mm) et Largeur (pouces), chacun lisant sa propre source, aucune conversion calculée entre les deux (voir `04-arbitrages.md`).

**`tooth_range`** (format `"{min}-{max}"`, ex. `"11-32"`) → :
- `tooth_range_min` : ensemble des petites valeurs distinctes, triées numériquement.
- `tooth_range_max` : ensemble des grandes valeurs distinctes, triées numériquement.

### Tri des valeurs

Toutes les valeurs numériques (`chainring_diameter_mm`, `tooth_count`, `speed_count`, `crank_length_mm`, `etrto_diameter_mm`, `etrto_width_mm`, `tooth_range_min`, `tooth_range_max`) sont triées numériquement en PHP après récupération (cast `(int)`/`(float)` puis `sort()`) — le volume par groupe (quelques dizaines de valeurs maximum) ne justifie pas un tri SQL avec `CAST`. Les valeurs textuelles (`practice_type`, `side`, `valve_type`, `axle_type`, `position`, `power_source`, `speed_compat`) sont triées alphabétiquement, sauf `practice_type` qui passe par une étape de normalisation d'affichage avant tri (voir ticket dédié).

### Requête des marques contextualisées

```php
Brand::query()
    ->whereHas('articles', fn ($q) => $q->where('article_subcategory_id', $id))
    ->ordered()
    ->get(['id', 'name', 'sort_order']);
```

### Format de réponse

```json
{
  "attributes": {
    "practice_type": ["ROUTE", "VTT", "VTC/Urbain", "GRAVEL"],
    "etrto_diameter_mm": ["406", "559", "584", "622"],
    "etrto_width_mm": ["23", "25", "28", "32", "35"]
  },
  "brands": [
    {"id": 3, "name": "Continental"},
    {"id": 7, "name": "Michelin"}
  ]
}
```

Sous-catégorie sans `article_attributes` : `attributes` vaut `{}`, `brands` reste peuplé normalement.

## Extension du filtrage : `GET /api/articles`

Nouveau paramètre `attribute[key]=value` (tableau associatif, parsing natif Laravel de la query string). Traduction Eloquent : un `whereHas('attributes', fn ($q) => $q->where('key', $key)->where('value', $value))` **par paire clé/valeur**, jamais un seul `whereHas` groupé — condition nécessaire pour garantir un AND logique correct entre attributs différents (chaque attribut est une ligne séparée dans `article_attributes` ; un seul `whereHas` avec `whereIn` sur plusieurs clés/valeurs accepterait à tort des combinaisons croisées portées par des lignes différentes du même article).

Pour les filtres décomposés (`attribute[etrto_diameter_mm]`, `attribute[etrto_width_mm]`, `attribute[tooth_range_min]`, `attribute[tooth_range_max]`), le contrôleur reconstruit la condition sur la valeur composite réellement stockée :
```php
// attribute[etrto_diameter_mm]=622 & attribute[etrto_width_mm]=28
// → whereHas('attributes', fn ($q) => $q->where('key', 'etrto_size')->where('value', '28-622'))
```
Si un seul des deux (diamètre ou largeur) est fourni sans l'autre, utiliser un `LIKE` sur le fragment connu (`value LIKE '28-%'` ou `value LIKE '%-622'`) plutôt qu'exiger les deux systématiquement.
