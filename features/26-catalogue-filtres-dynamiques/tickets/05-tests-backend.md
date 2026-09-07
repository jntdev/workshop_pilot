# Ticket 05 : Tests backend

**Type** : Tests
**Priorité** : Haute
**Estimation** : 2h

**Dépend de** : Ticket 01, Ticket 02, Ticket 03

## Tâches

- [ ] `php artisan make:test --phpunit Api/ArticleControllerFilterOptionsTest --no-interaction` (ou étendre un fichier de test `ArticleController` existant s'il y en a un — vérifier `tests/Feature/Api/` avant de créer un nouveau fichier)
- [ ] Tests `filterOptions()` :
  - Sous-catégorie avec attributs `etrto_size` variés → réponse contient `etrto_diameter_mm`/`etrto_width_mm`, jamais `etrto_size` brut
  - Sous-catégorie avec `tooth_range` → réponse contient `tooth_range_min`/`tooth_range_max`, jamais `tooth_range` brut
  - Réponse ne contient jamais `supplier_status` ni `size_inches` même si ces clés existent en base pour les articles de la sous-catégorie testée
  - Sous-catégorie sans aucun `article_attributes` → `attributes` est un tableau/objet vide, `brands` non vide si des articles existent
  - Marques renvoyées sont uniquement celles ayant un article dans la sous-catégorie testée (créer un article d'une autre marque dans une autre sous-catégorie, vérifier qu'elle n'apparaît pas)
  - Valeurs numériques triées croissant
  - Id de sous-catégorie inexistant → 404
  - Valeur `etrto_size` malformée (sans tiret) dans les données de test → ne fait pas échouer la requête, ignorée silencieusement
- [ ] Tests extension `index()` :
  - `attribute[practice_type]=VTT` filtre correctement
  - Deux `attribute[...]` combinés appliquent un AND, pas un OR (cas de l'article avec attributs contradictoires portés par des lignes différentes — vérifier l'exclusion explicitement, voir critère d'acceptation du ticket 03)
  - `attribute[etrto_diameter_mm]` + `attribute[etrto_width_mm]` combinés → correspondance exacte sur `etrto_size`
  - `attribute[etrto_diameter_mm]` seul → correspondance partielle par `LIKE`
  - `attribute[tooth_range_min]` + `attribute[tooth_range_max]` combinés → correspondance exacte sur `tooth_range`
  - Filtre sur une clé/valeur sans correspondance → liste vide, pas d'erreur 500
  - `subcategory_id` + `attribute[...]` combinés fonctionnent ensemble
- [ ] Utiliser les factories `Article`, `ArticleAttribute`, `Brand`, `ArticleSubcategory` existantes (vérifier les states disponibles avant d'instancier manuellement)
- [ ] `php artisan test --filter=ArticleControllerFilterOptionsTest` puis le nom réel du/des fichiers de test créés — tous verts avant de continuer
- [ ] Restaurer le catalogue CGN après exécution (`php artisan catalogue:import-cgn StockNouveautesCgn022252.csv`) — la base de test partage la base de dev (voir mémoire projet)

## Critères d'acceptation

- Tous les tests listés ci-dessus passent
- Aucune régression sur les tests existants du contrôleur Article (`php artisan test --filter=ArticleController`)
- Catalogue CGN réimporté et vérifié non vide après la session de tests
