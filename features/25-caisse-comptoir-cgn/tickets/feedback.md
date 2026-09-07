# Feedback - Feature 25 tickets

Revue du 2026-09-07, apres developpement de la feature.

## Verdict

La feature est largement developpee : modeles, migrations, API caisse, import CGN, page Inertia, ventes recentes, annulation et tests existent.

Validation executee :

```bash
php artisan test
npm run build
composer validate --no-check-publish
npx tsc --noEmit
```

Resultats :

- `php artisan test` : OK, 252 tests passent.
- `npm run build` : OK, warnings Sass de deprecation uniquement.
- `composer validate --no-check-publish` : OK.
- `npx tsc --noEmit` : KO sur des erreurs TypeScript existantes hors feature caisse apparente.

La feature ne doit pas encore etre consideree comme totalement correcte : il reste plusieurs ecarts fonctionnels et contractuels.

## Retours a traiter

### 1. `complete()` peut modifier une vente deja finalisee avant d'echouer

Fichier : `app/Http/Controllers/Api/SaleController.php`

Dans `complete()`, le controleur fait :

```php
$sale->update(['payment_method' => $request->validated('payment_method')]);
$sale->complete();
```

Probleme : si la vente est deja `completed` ou `cancelled`, `Sale::complete()` echoue ensuite, mais `payment_method` a deja ete modifie en base.

Impact : une vente finalisee n'est pas vraiment figee. Un appel API invalide peut changer son mode de paiement tout en retournant une erreur.

Correction attendue :

- verifier que la vente est encore `draft` avant toute mutation ;
- ou deplacer l'ecriture de `payment_method` dans la transaction/verrou de `Sale::complete()`.

Ajouter un test qui tente de finaliser une vente deja finalisee avec un autre `payment_method`, puis verifie que la valeur initiale n'a pas change.

### 2. Totaux affiches en caisse recalcules cote front avec une logique differente du serveur

Fichier : `resources/js/Components/Vente/CaisseForm.tsx`

Le total panier est calcule avec :

```ts
lines.reduce((sum, line) => sum + line.quantity * line.unit_price_ttc, 0)
```

et les lignes affichent aussi :

```ts
line.quantity * line.unit_price_ttc
```

Probleme : le serveur stocke `line_total_ttc = round(quantity * unit_price_ttc)`. Avec des quantites decimales et des montants en centimes, le front peut afficher un total different du total serveur final.

Impact : le vendeur peut voir un total different de celui enregistre.

Correction attendue :

- afficher `line.line_total_ttc` pour chaque ligne ;
- afficher `sale.total_ttc` ou un total derive de `line_total_ttc`, pas un recalcul flottant local divergent.

### 3. Le panneau stock tronque encore les quantites decimales

Fichier : `resources/js/Components/Stock/ArticleStockPanel.tsx`

Le formulaire envoie :

```ts
quantity: parseInt(form.quantity) || 0
```

Probleme : `2.5` devient `2`, alors que la feature migre `stock_movements.quantity` en decimal et que l'API accepte `min:0.01`.

Impact : les mouvements manuels decimaux sont acceptes par l'API mais impossibles a saisir correctement depuis l'UI existante.

Correction attendue :

- remplacer par une conversion decimale (`parseFloat`, avec gestion de la virgule si souhaitee) ;
- mettre l'input en `step="0.01"` et `min="0.01"`.

Le message de validation dans `app/Http/Requests/Api/StoreStockMovementRequest.php` dit encore "au moins 1" alors que la regle est `min:0.01`.

### 4. Contrats TypeScript incomplets pour les nouveaux types de mouvements

Fichier : `resources/js/types/index.d.ts`

`StockMovementType` vaut encore :

```ts
'manual_in' | 'manual_out' | 'quote_consumption' | 'maintenance_consumption'
```

Probleme : l'API peut maintenant retourner `sale_consumption` et `sale_return`.

Impact : le contrat TypeScript ne decrit plus toutes les valeurs reelles renvoyees par `StockMovementController`.

Correction attendue :

- ajouter `sale_consumption` et `sale_return` au type `StockMovementType`.

### 5. `weight_kg` risque d'etre expose comme string

Fichiers :

- `app/Models/Article.php`
- `resources/js/types/index.d.ts`

Le type front declare :

```ts
weight_kg: number | null
```

mais le modele `Article` ne caste pas `weight_kg`.

Probleme : sur une colonne SQL `decimal`, Eloquent expose souvent une chaine si aucun cast explicite ne convertit la valeur.

Impact : contrat JSON ambigu entre backend et frontend.

Correction attendue :

- ajouter un cast explicite pour `weight_kg`, par exemple `float` ou `decimal:3` selon le contrat voulu ;
- si `decimal:3` est choisi, adapter le type front en consequence. Recommandation : `float`, pour rester coherent avec `number | null`.

### 6. Le rapport d'import ne donne pas le taux de couverture ETRTO reel

Fichier : `app/Console/Commands/ImportCgnCatalogue.php`

Le ticket 04 demande un rapport avec le taux de couverture ETRTO reel pour pneus/chambres.

L'implementation affiche seulement un compteur global par cle extraite :

```php
$attributesExtractedByKey
```

Probleme : on obtient le nombre d'attributs `etrto_size`, mais pas le denominateur par famille (`PNEUS VELO`, `CHAMBRES VELO`) ni un taux exploitable.

Impact : impossible de verifier la couverture reelle annoncee par la spec lors d'un import fournisseur complet.

Correction attendue :

- compter les lignes eligibles par sous-categorie ;
- compter celles qui produisent `etrto_size` ;
- afficher un ratio/taux distinct pour pneus et chambres.

## Points non bloquants

- Les routes API caisse existent et les routes statiques `lookup-article` / `recent` sont declarees avant `/sales/{sale}`.
- L'annulation est bien exposee en UI via les ventes recentes.
- Le mapping CSV corrige est bien present : `weight_kg` colonne 11, `image_url` colonne 12.
- Les extracteurs sont separes par classe et couverts par tests unitaires.
- Les tests backend couvrent les principaux cas caisse : lookup, ambiguite EAN, lignes, finalisation, stock, annulation, idempotence, ventes recentes.
