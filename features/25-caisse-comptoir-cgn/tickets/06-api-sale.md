# Ticket 06 : API Sale — caisse

**Type** : Backend / API
**Priorité** : Haute
**Estimation** : 2h30

**Dépend de** : Ticket 05

## Tâches

- [ ] `SaleController` (Api) avec méthodes :
  - `lookupArticle(Request $request)` : `?q=` — priorité stricte, **jamais de résultat choisi arbitrairement** : (1) `Article::byBarcode($term)->get()` — EAN exact. **Zéro résultat** → passe à l'étape 2. **Un seul résultat** → le retourne directement (`{ "result": "found", "article": {...} }`). **Plusieurs résultats** (la base autorise des `barcode` dupliqués, cf. arbitrage 7 de la feature 22) → retourne `{ "result": "ambiguous", "articles": [...] }`, ne tente pas l'étape 2 ; (2) si aucun résultat à l'étape 1, `Article::where('reference', $term)->orWhere('reference', 'CGN-'.$term)->get()` — référence exacte, même logique zéro/un/plusieurs ; (3) si toujours aucun résultat, `Article::search($term)->ordered()->get()` (sans la contrainte de longueur minimale de `ArticleController::search`) — recherche texte, toujours renvoyée comme `{ "result": "choices", "articles": [...] }` même s'il n'y a qu'un seul résultat flou (la recherche texte n'est jamais une correspondance exacte, elle doit toujours passer par une confirmation utilisateur, contrairement aux étapes 1 et 2 qui peuvent auto-sélectionner un résultat unique car exact)
  - `store()` : crée une `Sale` en statut `draft`, `reference = null`
  - `show(Sale $sale)` : détail avec lignes
  - `addLine(Request $request, Sale $sale)` : refuse si `!$sale->isDraft()`. Si `article_id` fourni, tente l'insertion d'une nouvelle `SaleLine` ; si une ligne `(sale_id, article_id)` existe déjà, la **contrainte unique DB** (voir `../01-modele-donnees.md`) fait échouer l'insert avec une exception d'intégrité — intercepter cette exception (`try/catch QueryException`, ou vérifier au préalable via `firstOrNew` puis rattraper le cas résiduel de course) et incrémenter la `quantity` de la ligne existante à la place. La contrainte DB est ce qui garantit l'absence de doublon sous scans concurrents ; le code applicatif gère l'expérience (pas d'erreur visible, incrément transparent) — pas de déduplication pour les lignes libres (`article_id = null`, la contrainte unique ne s'y applique pas)
  - `updateLine(Request $request, Sale $sale, SaleLine $line)` : refuse si `!$sale->isDraft()` ou si `$line->sale_id !== $sale->id` (404, pas 403, pour ne pas révéler l'existence de la ligne) ; modifie quantité/prix, recalcule `line_total_ttc` et les totaux de la vente. Route en `PUT` (pas `PATCH`) : `resources/js/utils/api.ts` n'expose pas de helper `apiPatch` aujourd'hui (seulement `apiGet`/`apiPost`/`apiPut`/`apiDelete`), et le remplacement complet des champs modifiables de la ligne à chaque appel rend `PUT` sémantiquement correct sans avoir à ajouter un nouveau helper pour ce seul usage
  - `removeLine(Sale $sale, SaleLine $line)` : mêmes vérifications `isDraft()` et `sale_id`, supprime la ligne, recalcule les totaux
  - `complete(Request $request, Sale $sale)` : valide `payment_method` via `CompleteSaleRequest`, appelle `Sale::complete()`
  - `cancel(Sale $sale)` : appelle `Sale::cancel()`
- [ ] **Sécurité des routes imbriquées** : utiliser le scoped route model binding Laravel (`Route::apiResource` imbriqué ou `sale.lines,line` dans la déclaration de route) pour que Laravel vérifie nativement `line.sale_id === sale.id`, ou à défaut un contrôle explicite en début de méthode retournant 404 sinon — sans cela, une URL fabriquée (`/sales/1/lines/{id-d-une-autre-vente}`) permettrait de modifier une ligne d'une vente tierce
- [ ] Form Requests : `StoreSaleLineRequest` (`article_id` nullable ; si `article_id` fourni, `designation`/`purchase_price_ht`/`unit_price_ttc` optionnels car dérivés de l'article ; si `article_id` absent, `designation` et `purchase_price_ht` et `unit_price_ttc` deviennent `required` — ligne libre, voir arbitrage `../01-modele-donnees.md` ; `quantity` numeric min:0.01), `CompleteSaleRequest` (`payment_method` required, `Rule::enum(PaymentMethod::class)`)
- [ ] Réponses en JSON construit à la main dans le contrôleur (pas de `JsonResource`), cohérent avec `ArticleController::search`
- [ ] Routes dans `routes/api.php` (groupe auth) :
  ```
  GET    /api/sales/lookup-article
  GET    /api/sales/recent          ← avant /sales/{sale}, voir ticket 08
  POST   /api/sales
  GET    /api/sales/{sale}
  POST   /api/sales/{sale}/lines
  PUT    /api/sales/{sale}/lines/{line}
  DELETE /api/sales/{sale}/lines/{line}
  POST   /api/sales/{sale}/complete
  POST   /api/sales/{sale}/cancel
  ```

## Critères d'acceptation

- `GET /api/sales/lookup-article?q=3528701009261` retourne `{ result: "found", article }` quand un seul article a cet EAN
- Un EAN partagé par deux articles (cas autorisé en base) retourne `{ result: "ambiguous", articles: [...] }`, jamais un article choisi arbitrairement
- `GET /api/sales/lookup-article?q=CGN-435447` retourne l'article exact par référence
- `GET /api/sales/lookup-article?q=michelin` retourne `{ result: "choices", articles: [...] }` quand la recherche texte matche, même avec un seul résultat
- Scanner deux fois le même code-barres à la suite incrémente la quantité de la ligne existante, ne crée pas de doublon (y compris sous deux requêtes quasi simultanées — vérifié via la contrainte unique DB `(sale_id, article_id)`, pas seulement via la logique applicative)
- Modifier ou supprimer une ligne d'une vente en passant l'`id` d'une ligne appartenant à une autre vente retourne 404
- Toute mutation sur une vente non-`draft` (déjà complétée ou annulée) est refusée
- `POST /api/sales/{sale}/complete` sans `payment_method` retourne une erreur de validation 422
- `POST /api/sales/{sale}/complete` sur un panier vide est refusé
- `POST /api/sales/{sale}/complete` décrémente correctement le stock des articles vendus
