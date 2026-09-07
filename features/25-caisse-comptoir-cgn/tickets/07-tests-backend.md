# Ticket 07 : Tests backend

**Type** : QA / Tests
**Priorité** : Haute
**Estimation** : 2h

**Dépend de** : Tickets 01 à 06

## Tests à écrire

**Import CGN** (`tests/Feature/Console/ImportCgnCatalogueTest.php`, CSV de test fabriqué en Windows-1252)
- [ ] Import d'une ligne normale crée un `Article` avec la bonne référence préfixée, le bon prix d'achat, le bon prix de vente, le bon code-barres
- [ ] Un EAN dupliqué (fiche normale + fiche "épuisée") ne produit qu'un seul `Article`
- [ ] Une ligne sans EAN est importée avec `barcode = null`
- [ ] Une marque déjà existante n'est pas dupliquée
- [ ] Une nouvelle sous-catégorie est créée à la volée
- [ ] Un prix au format `"16,50"` est correctement converti en centimes
- [ ] Un poids au format `"0,32000"` (virgule, format réel de la colonne dans le CSV CGN) est correctement converti en `weight_kg = 0.32`, pas `0`
- [ ] Une désignation avec préfixe `µ` et suffixe `EPUISE` est nettoyée avant stockage
- [ ] Rejouer l'import sur le même fichier ne crée pas de doublons (upsert) et compte 0 "créés" / N "mis à jour" au second passage (`wasRecentlyCreated`)
- [ ] Le nombre de requêtes SQL pour résoudre marque/catégorie/sous-catégorie reste borné sur un CSV de test avec plusieurs lignes partageant les mêmes valeurs (cache en mémoire effectif, pas un `firstOrCreate` par ligne)
- [ ] Une fiche `EPUISE` sans doublon EAN obtient l'attribut `supplier_status = discontinued`, sa désignation stockée ne contient plus le marqueur `EPUISE`
- [ ] Un article importé une première fois avec `EPUISE` (donc `supplier_status = discontinued`), puis réimporté sans la mention (fichier de test modifié pour simuler un retour en stock fournisseur), n'a plus l'attribut `supplier_status` après le second import
- [ ] Une ligne avec `purchase_price_ht = 0` ou prix conseillé à 0 dans le CSV de test est importée normalement (pas ignorée), comptée dans le rapport
- [ ] Une ligne sans marque (colonne vide) n'appelle jamais `Brand::create()`, l'article a `brand_id = null`
- [ ] Un pneu du CSV de test obtient ses `article_attributes` (`practice_type`, `size_inches`, `etrto_size`) après import
- [ ] Un article d'une sous-catégorie non couverte par un extracteur (ex. `OUTILLAGE`) n'obtient aucun `article_attributes`, sans erreur

**Caisse** (`tests/Feature/Api/SaleControllerTest.php`)
- [ ] Lookup par code-barres exact retourne le bon article
- [ ] Lookup par référence exacte (`CGN-*`) retourne le bon article quand aucun EAN ne correspond
- [ ] Lookup en recherche texte avec plusieurs résultats retourne la liste complète, pas un seul résultat choisi arbitrairement
- [ ] Ajout d'une ligne au panier persiste `purchase_price_ht` et `unit_price_ttc` depuis l'article
- [ ] Ajout d'une ligne libre (sans `article_id`) exige `designation` et `purchase_price_ht` dans la requête, échoue en validation si absents
- [ ] Scanner deux fois le même `article_id` sur la même vente incrémente la quantité d'une ligne existante, ne crée pas de deuxième ligne
- [ ] Modification de quantité recalcule `line_total_ttc` et les totaux de la vente
- [ ] Suppression d'une ligne recalcule les totaux
- [ ] Modifier/supprimer une ligne appartenant à une autre vente (`sale_id` différent) retourne 404
- [ ] Toute mutation (`addLine`, `updateLine`, `removeLine`, `complete`, `cancel`) sur une vente déjà `completed` ou `cancelled` est refusée
- [ ] `complete()` sur un panier vide est refusé
- [ ] `complete()` crée un `StockMovement(sale_consumption)` par ligne avec article, décrémente le stock
- [ ] `complete()` sans `payment_method` échoue en validation
- [ ] Deux appels à `complete()` immédiatement successifs (simulant un double-clic) ne créent qu'un seul jeu de mouvements de stock et une seule référence
- [ ] `cancel()` sur une vente complétée crée des `StockMovement(sale_return)` positifs et réintègre le stock, sans supprimer les mouvements `sale_consumption` d'origine
- [ ] `cancel()` appelé deux fois sur la même vente ne réintègre le stock qu'une seule fois (idempotence)
- [ ] `Sale::marginHt()` retourne le calcul attendu sur un panier à plusieurs lignes avec des prix d'achat différents

**Migration enum** (`tests/Feature/`)
- [ ] La migration ajoutant `sale_consumption`/`sale_return` à `stock_movements.type` s'applique sans erreur sous SQLite (driver de test)
