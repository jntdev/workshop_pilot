# Feedback — Feature 26

## Statut

La feature est implémentée et les points principaux fonctionnent côté backend :

- route `GET /api/article-subcategories/{id}/filter-options` ;
- endpoint `ArticleController::filterOptions()` ;
- filtrage `GET /api/articles` via `attribute[...]` ;
- décomposition et exposition des filtres roue/plage de dents ;
- marques contextualisées par sous-catégorie ;
- migration d'index sur `article_attributes.key` ;
- refonte de la modale catalogue ;
- intégration des filtres dynamiques dans la page Stock ;
- tests backend couvrant les principaux cas.

Je ne vois pas de bug backend évident dans la logique de filtrage elle-même. Les problèmes restants sont surtout des risques runtime/frontend et performance.

## Points problématiques

### 1. Réponses réseau obsolètes possibles côté React

Les `fetch` qui chargent les filtres et les articles ne sont ni annulés ni protégés contre les réponses arrivées dans le désordre.

Cas possible :

1. l'utilisateur sélectionne une sous-catégorie A ;
2. un appel `/api/article-subcategories/A/filter-options` part ;
3. l'utilisateur sélectionne rapidement une sous-catégorie B ;
4. l'appel B revient et affiche les bons filtres ;
5. l'appel A revient ensuite et écrase `filterOptions` avec les filtres de A.

Le même problème peut arriver sur les listes d'articles si plusieurs chargements successifs sont déclenchés par recherche, filtre, marque ou sous-catégorie.

Fichiers concernés :

- `resources/js/Components/Stock/CataloguePickerModal.tsx`
- `resources/js/Components/Stock/ArticleList.tsx`
- `resources/js/Pages/Stock/Index.tsx`

Attendu : protéger les chargements asynchrones avec `AbortController`, un compteur de requête, ou une vérification que la réponse correspond encore à l'état courant avant d'appeler `setState`.

### 2. La modale catalogue dépend de `categories[0]`

`CataloguePickerModal.tsx` construit sa liste de sous-catégories avec :

```tsx
setSubcategories(data.categories?.[0]?.subcategories ?? [])
```

Cela suppose que la catégorie utile est toujours la première catégorie retournée par `/api/article-categories`.

Or l'API retourne les catégories triées par `sort_order` puis `name`. Selon l'historique de la base, les anciennes catégories du seeder ou des catégories créées manuellement peuvent précéder `Pièces Cycles`.

Conséquence : la modale peut afficher les sous-catégories d'une mauvaise catégorie, même si la feature est pensée autour de la constante métier "Pièces Cycles".

Attendu : sélectionner explicitement la catégorie racine attendue, par exemple par nom normalisé (`Pièces Cycles` / `Pieces Cycles`) ou via un endpoint dédié qui retourne directement les sous-catégories catalogue utiles.

### 3. Risque de N+1 queries sur les listes d'articles

`Article` expose deux attributs appendés :

- `stock_quantity`
- `is_discontinued`

Chaque article sérialisé déclenche potentiellement :

- une requête `sum(quantity)` sur `stock_movements` pour `stock_quantity` ;
- une requête sur `article_attributes` pour `supplier_status` via `is_discontinued`.

Comme `ArticleController::index()` pagine 50 articles, chaque chargement peut générer beaucoup de requêtes supplémentaires. Ce risque concerne aussi les autres endpoints ou composants qui sérialisent des listes d'articles.

Ce n'est pas forcément bloquant au volume actuel, mais avec le catalogue CGN il faut surveiller ce point.

Attendu : envisager des agrégats/eager loads adaptés pour les listes (`withSum`, sous-requête, relation préchargée pour `supplier_status`, ou DTO de réponse explicite) si la page devient lente.

### 4. Gestion d'erreur frontend limitée sur les appels API

Plusieurs appels frontend font directement `res.json()` sans vérifier `res.ok` et sans `try/catch`.

Cas problématiques :

- session expirée ;
- erreur 500 ;
- réponse HTML au lieu de JSON ;
- coupure réseau ;
- endpoint indisponible.

Conséquence : l'UI peut rester en chargement, conserver des données anciennes ou échouer silencieusement côté console.

Fichiers concernés :

- `resources/js/Components/Stock/CataloguePickerModal.tsx`
- `resources/js/Components/Stock/ArticleList.tsx`
- `resources/js/Pages/Stock/Index.tsx`

Attendu : gérer explicitement les erreurs réseau/API et remettre `isLoading` à `false` dans un `finally`.

## Points validés

- Le filtrage backend `attribute[...]` applique bien un AND entre attributs différents.
- Les marques de `filterOptions()` sont bien contextualisées par sous-catégorie.
- Les clés internes/brutes non souhaitées ne sont pas exposées comme filtres directs.
- Les valeurs bruitées ou malformées ne font pas planter `filterOptions()`.
- Les anciens points de feedback sur `filterOptions` non vidé et l'affichage `VTC/Urbain` ont été traités.

## Vérifications effectuées

- `php artisan test --filter=ArticleTest` : OK, 39 tests passent.
- `php artisan test` : OK, 288 tests passent.
- `npm run build` : OK, avec avertissements Sass existants.
- `npx tsc --noEmit` : KO sur erreurs TypeScript hors feature 26 déjà présentes (`MessageCard`, `Dashboard`, `Location/Index`).

## Avis global

La feature semble solide côté backend et les tests automatisés couvrent bien la logique métier principale.

Avant de pousser plus loin le fonctionnel, je corrigerais en priorité les deux risques frontend suivants :

1. protéger les `fetch` contre les réponses obsolètes ;
2. supprimer la dépendance implicite à `categories[0]` pour trouver la catégorie racine catalogue.

Les points N+1 et gestion d'erreur frontend peuvent suivre, mais ils deviendront importants si le catalogue CGN complet est utilisé quotidiennement.
