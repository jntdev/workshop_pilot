# Arbitrages produit — Feature 25

## Arbitrage 1 : "Caisse", jamais "Comptoir"

Le mot "Comptoir" désigne déjà, dans cette application, un mode d'affichage sans rapport (masquage d'informations sensibles à l'accueil, toggle "Atelier / Comptoir" dans `MainLayout.tsx` / `usePrivacyMode`). Pour ne jamais créer d'ambiguïté dans le code ou l'UI entre les deux concepts, la vente rapide par code-barres s'appelle "Caisse" partout : modèle `Sale`, route `/vente/caisse`, libellés UI.

## Arbitrage 2 : Montants en centimes, pas en décimal euros

`Quote`/`QuoteLine` stockent leurs montants en `decimal(10,2)` euros. `Sale`/`SaleLine` stockent les leurs en `integer` centimes, cohérent avec `Article::purchase_price_ht`/`sale_price_ttc`. Raison : la caisse est directement adossée au catalogue (contrairement au devis, en partie saisie libre), et un panier multi-articles additionne mieux des entiers que des décimaux (pas de dérive d'arrondi flottant sur un total de plusieurs lignes).

## Arbitrage 3 : Marge suivie dès la v1

Contrairement à `QuoteLine`, `SaleLine` porte un `purchase_price_ht` copié depuis l'article au moment de la vente. Raison : le suivi de marge sur les ventes comptoir est un besoin exprimé dès le départ, pas une évolution différée.

## Arbitrage 4 : Mouvement de stock automatique dès la v1

La feature 22 avait explicitement repoussé la création automatique de mouvements de stock à la finalisation d'un devis ou d'un log de maintenance ("v2, une fois le flux de travail stabilisé" — voir son arbitrage 6). La caisse déroge à cette prudence dès la v1 : une vente comptoir *est* le cas d'usage qui justifie l'automatisation (sans décrémentation automatique, la fonctionnalité perd une grande partie de son intérêt pour le suivi de stock).

## Arbitrage 4bis : Décompte jamais bloquant, réalignement progressif

Le magasin n'a aujourd'hui aucun inventaire fiable (aucun décompte, ni papier ni informatique). `Sale::complete()` décrémente donc toujours le stock, y compris si `stock_quantity` est déjà à 0 ou négatif pour l'article — jamais de refus de vente pour cause de stock insuffisant. Raison : l'objectif n'est pas un contrôle de disponibilité strict, mais un réalignement progressif du stock virtuel sur le stock physique réel au fil des ventes ; bloquer la vente casserait le flux de travail du comptoir pour un stock de départ qu'on sait déjà faux. Cohérent avec l'arbitrage 7 de la feature 22 ("le système ne bloque pas si le stock passe en négatif").

## Arbitrage 5 (révisé en feature 26) : Supplier "CGN" auto-assigné

Décision initiale abandonnée. L'import assigne désormais `supplier_id` vers un `Supplier` "CGN" (créé/réutilisé via `firstOrCreate`) sur chaque article importé — voir arbitrage 8 révisé ci-dessous, décision liée. Raison de la révision : le préfixe `CGN-` sur la référence posait un problème de lisibilité produit ("je vois cgn devant la référence, c'est la vraie ref ?") ; le vrai code fournisseur brut est conservé tel quel comme référence, et l'origine CGN est portée par l'attribut fournisseur plutôt que par la référence elle-même.

## Arbitrage 6 : Catégories/sous-catégories CGN — fallback automatique, pas de mapping maintenu

Les libellés bruts du CSV CGN (ex. "PNEUS VELO", "LAMPES") sont transformés automatiquement (`Str::title(strtolower(...))`) et créés à la volée dans `ArticleCategory`/`ArticleSubcategory`, sans fichier de correspondance à maintenir. Raison : simplicité — l'UI de gestion du catalogue (feature 22) permet déjà de renommer/fusionner des catégories a posteriori si le résultat automatique ne convient pas.

## Arbitrage 7 : Pas de contrainte unique sur `articles.barcode`

Le CSV fournisseur CGN contient lui-même des EAN vides et des EAN dupliqués (variante "épuisée" d'un même article). Une contrainte unique en base bloquerait l'import. L'index reste simple (non-unique), et la commande d'import dédoublonne en amont (une seule fiche conservée par EAN, priorité à la fiche non "épuisée").

## Arbitrage 8 (révisé en feature 26) : Référence brute, sans préfixe `CGN-`

Décision initiale abandonnée. La référence CGN brute (ex. `435447`) est utilisée telle quelle comme `Article.reference` (unique), sans préfixe. L'identification de l'origine CGN (a posteriori, ou pour une future resynchronisation FTP) passe désormais par `supplier_id` (arbitrage 5 révisé) plutôt que par un marqueur dans la référence elle-même. Une collision de référence avec un article non-CGN déjà existant est détectée à l'import et la ligne concernée est ignorée silencieusement (voir `ImportCgnCatalogue::$referenceCollisions`) plutôt que bloquée par une contrainte unique en échec.

**Conséquence sur le ticket 06 (`SaleController::lookupArticle`)** : le fallback `orWhere('reference', 'CGN-'.$term)` initialement prescrit n'a plus lieu d'être — il n'existe plus de préfixe à essayer, la référence brute suffit et le lookup exact (`where('reference', $term)`) la trouve directement.

## Arbitrage 10 : Attributs structurés — extracteurs dédiés par famille, jamais de parseur générique

La désignation CGN est du texte libre fournisseur, sans grammaire universelle. L'analyse par échantillonnage réel du CSV montre que la fiabilité d'extraction varie fortement d'une sous-catégorie à l'autre, et que certaines sous-catégories mélangent en fait plusieurs familles de produits à grammaires distinctes sous un même libellé (`JEUX DE PEDALIERS` mélange boîtiers classiques à filetage et boîtiers intégrés d'une marque spécifique). Décision : un extracteur isolé et testé par famille de produits vérifiée fiable, jamais un parseur générique appliqué à l'aveugle sur tout le catalogue. Une famille dont la structure s'avère hétérogène à l'examen (comme `JEUX DE PEDALIERS`) est explicitement écartée plutôt que couverte par un extracteur approximatif qui produirait des attributs faux en silence.

## Arbitrage 11 : Vocabulaire — "pédalier" métier ≠ "JEUX DE PEDALIERS" CGN

Le libellé CGN `JEUX DE PEDALIERS` désigne les boîtiers de pédalier (axe/roulement central), pas l'ensemble manivelles + plateaux couramment appelé « pédalier » à l'atelier. Ce dernier correspond aux sous-catégories CGN `PEDALIERS` et `MANIVELLES`. Cette distinction, contre-intuitive au premier abord, a été vérifiée par examen des désignations réelles et doit être gardée à l'esprit lors du développement et du nommage du code (extracteurs, tests) pour ne pas mélanger les deux notions.

## Arbitrage 9 : Annulation = mouvement inverse, jamais suppression

`Sale::cancel()` sur une vente déjà complétée ne supprime aucun `StockMovement` existant : elle crée de nouveaux mouvements en sens inverse. Cohérent avec l'arbitrage 1 de la feature 22 ("le stock est une somme de mouvements, l'historique est la source de vérité").
