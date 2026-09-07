# Ticket 08 : Tests backend

**Type** : Tests
**Priorité** : Haute
**Estimation** : 2h30

**Dépend de** : Ticket 01, Ticket 02, Ticket 06

## Tâches

- [ ] `php artisan make:test --phpunit Api/InventoryControllerTest --no-interaction`
- [ ] Tests `lookupBarcode()` :
  - Code EAN correspondant à un seul article → `found`, article complet retourné
  - Code EAN partagé par deux articles → `ambiguous`, jamais de sélection arbitraire
  - Code absent de la base → `not_found`
  - Code vide → `not_found`, pas d'erreur 500
- [ ] Tests `uploadPhoto()` (dans `ArticleTest.php` ou un fichier dédié, vérifier l'existant avant de créer) :
  - Upload d'une image valide → `image_url` retournée, fichier réellement présent sur le disque `public`
  - Fichier non-image → 422
  - Fichier trop volumineux (> 5120 Ko) → 422
- [ ] Tests `storeArticle()` (`POST /api/inventory/articles`) :
  - Payload complet valide (sans champ `reference`, uniquement `barcode`) → article créé avec `reference === barcode === code fourni`, **et** un mouvement `manual_in` créé pour la quantité fournie (vérifier `$article->fresh()->stock_quantity` non nul immédiatement)
  - **Test dédié au point 1 du feedback de préparation** : envoyer explicitement un champ `reference` différent de `barcode` dans le payload brut de la requête HTTP (contournement direct de la Form Request, pas juste via le payload frontend normal) → vérifier que l'article créé a bien `reference === barcode` malgré cette tentative, jamais la valeur falsifiée envoyée
  - Chaque champ obligatoire manquant individuellement (`designation`, `article_subcategory_id`, `brand_id`, `purchase_price_ht`, `sale_price_ttc`, `quantity`, `image_url`, `barcode`) → 422 avec message clair, testés un par un
  - Un second appel avec le même `barcode` échoue proprement (422, contrainte unique sur `reference`) — vérifier qu'aucun article ni mouvement partiel n'est créé (transaction cohérente)
  - `image_url` ne commençant pas par `/storage/articles/` (ex. `https://example.com/photo.jpg`, ou un chemin `/storage/autre-dossier/x.jpg`) → 422
  - `image_url` commençant bien par `/storage/articles/` → acceptée
  - Attributs simples fournis (ex. `practice_type`) → persistés tels quels dans `article_attributes`
  - Attributs virtuels fournis (`wheel_diameter` + `wheel_width_mm`) → persistés sous la vraie clé composite `etrto_size` avec la valeur recomposée exacte, jamais les clés virtuelles elles-mêmes
  - Un seul des deux attributs d'une paire composite fourni (ex. `wheel_diameter` seul) → aucune valeur inventée persistée pour `etrto_size`/`size_inches`
  - Une clé hors de `ACCEPTED_ATTRIBUTE_KEYS` fournie dans `attributes` (ex. `supplier_status`, ou une clé arbitraire type `foo`) → requête acceptée (200/201, pas de 422), mais la clé n'apparaît jamais dans `article_attributes` après création
  - Article créé avec `quantity` → apparaît immédiatement avec `has_movements=true` via `GET /api/articles?has_movements=1` (bascule Stock atelier déjà garantie par le mécanisme existant, feature 26 bis — non-régression à vérifier ici)
- [ ] Test du parcours `ambiguous` → validation (point 2 du feedback de préparation), dans `InventoryControllerTest` ou un test frontend si l'outillage du projet le permet — a minima, vérifier côté backend que `POST /api/articles/{id}/stock-movements` fonctionne identiquement que l'`id` provienne d'un `found` direct ou d'un article choisi dans une liste `ambiguous` (le comportement serveur est déjà le même dans les deux cas puisque c'est le même endpoint appelé avec le même `id` — ce test garantit surtout la non-régression sur ce point)
- [ ] `php artisan test --filter=InventoryControllerTest` puis les fichiers réellement créés — tous verts avant de continuer
- [ ] Restaurer le catalogue CGN et les seeders après exécution (`php artisan catalogue:import-cgn StockNouveautesCgn022252.csv` puis `php artisan db:seed --force`) — voir mémoire projet, protocole de restauration complet après tout `php artisan test`

## Critères d'acceptation

- Tous les tests listés ci-dessus passent
- Aucune régression sur les tests existants (`php artisan test --filter=ArticleController`, `php artisan test --filter=SaleController`)
- Catalogue CGN et emails autorisés réimportés/reseedés et vérifiés non vides après la session de tests
