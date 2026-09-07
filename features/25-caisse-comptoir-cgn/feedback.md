# Feedback - Feature 25

Revue du 2026-09-02.

## Verdict

La feature 25 n'est pas complete dans le depot.

Les retours precedents ont ete majoritairement traites dans les documents de specification, mais aucune implementation applicative n'existe encore dans `app/`, `database/`, `resources/`, `routes` ou `tests` pour :

- `Sale`
- `SaleLine`
- `SaleStatus`
- `PaymentMethod` PHP
- `ArticleAttribute`
- `AttributeExtractor`
- `ImportCgnCatalogue`
- `SaleController`
- route `/vente/caisse`
- page `Vente/Caisse`

Verification effectuee :

```bash
rg --files app database resources routes tests | rg 'Sale|sale|Caisse|caisse|ArticleAttribute|AttributeExtractor|ImportCgn|PaymentMethod'
php artisan route:list --path=vente
php artisan route:list --path=sales
```

Les deux commandes `route:list` ne retournent aucune route correspondante.

Conclusion : la specification est proche d'un etat implementable, mais la feature n'est pas livree ni testable fonctionnellement.

## Points precedents correctement traites dans la spec

- `sales.reference` est nullable pendant le statut `draft`.
- Le format de reference est stabilise sur `VC-{YYYYMMDD}-{sale_id}`.
- `Article::attributeValue()` remplace bien l'idee dangereuse de `Article::getAttribute()`.
- `PaymentMethod` PHP est explicitement demande cote serveur.
- `sale_return` est prevu pour les annulations, separe de `sale_consumption`.
- `sale_lines.quantity` est en decimal et la migration de `stock_movements.quantity` vers `decimal(10,2)` est maintenant prevue.
- La marge reste en centimes, sans division par 100.
- La deduplication panier repose maintenant sur une contrainte unique DB `(sale_id, article_id)`.
- Le lookup EAN exact retourne une ambiguite explicite si plusieurs articles partagent le code.
- La re-extraction des attributs supprime les cles obsoletes avant reinsertion.
- `supplier_status` est supprime si une fiche anciennement `EPUISE` redevient active.
- `extract()` ne retourne plus de valeurs `null`.
- Les nombres CSV doivent passer par un helper unique `parseDecimal()`.
- Le brouillon de vente est cree au premier article ajoute, pas au chargement de la page.
- Les KPI `vente` et le justificatif imprimable/email sont hors v1.

## Bloquants restants

### 1. Mapping CSV 0-based faux pour `weight_kg` et `image_url`

Le ticket 25.2 dit que le mapping est 0-indexe, puis mappe :

- `12` -> `weight_kg`
- `13` -> `image_url`

Or le fichier reel montre :

```text
11: 0,00000
12: https://www.cgnfrance-pro.com/PartageWeb/Produits/2703_0.jpg
13:
14: 0.02300
15: 0.01200
16: 0.01400
17: 0.00000
18: 0.00000
```

Donc, en 0-based :

- `11` = poids fournisseur (`weight_kg`)
- `12` = URL image (`image_url`)
- `13` = description/details fournisseur, actuellement ignoree
- `14` a `18` = dimensions/volume, actuellement ignores

Correction obligatoire dans `02-tickets-backend.md` :

- remplacer `12` par `11` pour `weight_kg` ;
- remplacer `13` par `12` pour `image_url`.

Sans cette correction, l'import stockera l'URL dans `weight_kg` et perdra l'image.

### 2. Migration `stock_movements.quantity` : les tests existants ne peuvent pas rester "sans modification" si le type expose un float/string

La spec demande de migrer `stock_movements.quantity` vers `decimal(10,2)` et d'ajuster `Article::stock_quantity`.

Probleme : les tests actuels verifient des entiers stricts, par exemple :

- `tests/Feature/Catalogue/StockMovementTest.php`
- `tests/Feature/Catalogue/ArticleTest.php`

Exemples existants :

```php
$this->assertSame(11, $article->fresh()->stock_quantity);
$this->assertSame(7, $article->stock_quantity);
```

Si `stock_quantity` devient `float` (`11.0`) ou `decimal:2` Eloquent (`"11.00"`), ces assertions strictes echoueront. Le ticket 25.1ter dit pourtant que les tests existants doivent passer "sans modification de leurs assertions".

Correction obligatoire, choisir une seule strategie :

- garder une representation entiere quand la somme est entiere, ce qui complique le type expose ;
- ou accepter que le contrat JSON/modele change et mettre a jour les tests existants ;
- ou exposer explicitement `stock_quantity` comme string decimal partout (`"11.00"`) et adapter frontend/tests.

Recommandation : exposer un `number` cote API/frontend, retourner un `float` cote modele, et mettre a jour les tests stricts pour attendre `11.0` ou utiliser une assertion numerique adaptee.

### 3. Frontend : critere d'acceptation ambigu sur la creation de `Sale`

Le ticket frontend dit :

> Taper une reference CGN existante dans le champ code-barres puis Entree l'ajoute au panier sans creer de vente tant qu'aucun article n'a ete ajoute

Cette phrase est contradictoire : l'action de l'ajouter au panier est justement le premier ajout d'article, donc c'est le moment ou la vente doit etre creee selon l'arbitrage.

Correction obligatoire :

- "Charger la page ne cree aucune `Sale`."
- "Une recherche/ambiguite non confirmee ne cree aucune `Sale`."
- "Le premier ajout confirme cree la `Sale`, puis cree la premiere ligne."

### 4. Route d'annulation exposee mais ticket 25.7 en priorite moyenne

Le ticket 25.4 rend `cancel()` disponible cote API. Le ticket 25.7 ajoute l'interface "ventes recentes et annulation", mais il est en priorite moyenne.

Probleme : si 25.7 est reporte, l'annulation existe techniquement mais n'est pas utilisable au comptoir. Ce n'est pas forcement bloquant pour l'encaissement, mais c'est bloquant pour une v1 qui promet une annulation accessible.

Correction obligatoire :

- soit passer le ticket 25.7 en priorite haute et dans le perimetre v1 ;
- soit declarer explicitement que l'annulation v1 est API/admin seulement, sans UI comptoir.

## Points a surveiller pendant implementation

- La migration `change_quantity_to_decimal_on_stock_movements_table` utilise `->change()` : verifier la compatibilite Laravel/SQLite/MySQL du projet avant de coder, surtout si `doctrine/dbal` n'est pas une dependance directe.
- `resources/js/Components/Stock/ArticleStockPanel.tsx` utilise actuellement `parseInt(form.quantity)` ; il faudra passer a `parseFloat` pour ne pas tronquer les mouvements manuels decimaux.
- `Article::getStockQuantityAttribute()` retourne actuellement `int` et caste la somme en `(int)` ; il faudra supprimer cette troncature.
- `StockMovementController` et les composants stock affichent directement `quantity`; verifier l'affichage de `2.50` vs `2.5` selon le contrat choisi.
- Les routes API statiques `/api/sales/lookup-article` et `/api/sales/recent` doivent rester declarees avant `/api/sales/{sale}`.
- Les tests doivent couvrir le cas reel de poids CSV avec virgule (`0,32000`) et l'image en colonne 12 0-based.

## Validation minimale avant livraison

Backend :

- migrations SQLite et MySQL OK ;
- tests unitaires des extracteurs OK ;
- tests API caisse OK ;
- tests import CGN OK ;
- tests existants catalogue/stock OK apres migration decimal ;
- `php artisan catalogue:import-cgn StockNouveautesCgn022252.csv` OK sur le fichier reel.

Frontend :

- route Inertia `/vente/caisse` OK ;
- build TypeScript/Vite OK ;
- QA clavier : scan, saisie manuelle, resultats ambigus, code inconnu, refocus, modification quantite/prix, finalisation, annulation.

Commandes minimales :

```bash
php artisan test
npm run build
php artisan catalogue:import-cgn StockNouveautesCgn022252.csv
```

Le rapport d'import du CSV reel doit retrouver ces chiffres de controle :

- 14 970 lignes ;
- 19 colonnes par ligne ;
- 75 lignes sans EAN ;
- 1 groupe d'EAN duplique, 2 lignes ;
- 21 lignes sans marque ;
- 22 lignes `EPUISE` ;
- 159 prix d'achat a zero ;
- 191 prix de vente conseille a zero.

## Conclusion

Les principaux retours de conception sont maintenant traites dans la spec. Il reste quatre corrections documentaires avant implementation, dont une critique : le mapping CSV 0-based de `weight_kg` et `image_url` est faux.

Apres ces corrections, la feature pourra passer en implementation. Elle ne doit pas etre consideree complete tant que le code, les migrations, les routes, l'ecran et les tests n'existent pas.
