# Feature 25 : Caisse comptoir + import catalogue fournisseur CGN

## Contexte

L'atelier reçoit régulièrement un export CSV du fournisseur CGN (`StockNouveautesCgn022252.csv`, ~15000 lignes, séparateur `;`, sans en-tête) contenant référence, désignation, prix d'achat, prix public conseillé et code-barres (EAN) de milliers de pièces cycles.

Aujourd'hui, vendre une pièce au comptoir oblige à fouiller ce fichier ou le catalogue à la main : aucun moyen rapide de retrouver le prix d'achat et le prix de vente conseillé d'un article à partir de son code-barres.

La feature 22 (`features/22-catalogue-stock/`) a posé un catalogue `Article` (référence, désignation, prix d'achat HT, prix de vente TTC, marque, fournisseur, catégorie/sous-catégorie, stock calculé par mouvements tracés). Il manque :
- un code-barres sur `Article`
- un import automatisé du CSV fournisseur CGN vers ce catalogue
- un vrai point de vente permettant de scanner/taper un code-barres, constituer un panier, et encaisser

Second problème, distinct mais lié : l'inventaire du magasin n'est aujourd'hui pas suivi du tout (aucun décompte, ni papier ni informatique). Impossible de savoir ce qu'il reste en stock sans aller vérifier physiquement.

## Objectif produit

Permettre à un vendeur de vendre une pièce au comptoir sans fouiller le catalogue : il tape (ou scanne, une douchette USB émulant un clavier) le code-barres, l'article et son prix apparaissent, il l'ajoute au panier, ajuste si besoin, encaisse. Chaque vente encaissée doit décompter automatiquement le stock, pour que le stock virtuel se rapproche progressivement du stock physique réel — sans jamais bloquer une vente si le stock affiché est insuffisant ou négatif (le magasin n'a pas d'inventaire de départ fiable ; le réalignement se fait dans la durée, pas d'un coup).

## Vision produit à terme (pour cadrer les décisions d'aujourd'hui)

Cette feature est la première brique d'un futur **gestionnaire de stock centralisé**, pas un outil de caisse isolé. À terme, la même notion de stock doit pouvoir servir :
- la vente comptoir (cette feature)
- la consommation de pièces sur les devis atelier et les logs de maintenance flotte (déjà en place, feature 22, mouvements `quote_consumption`/`maintenance_consumption` — non branchés automatiquement à ce jour)
- une future commande de réapprovisionnement directement auprès du fournisseur CGN, à partir des références déjà connues du catalogue

Conséquence directe sur les choix ci-dessous : pas de nouveau catalogue produit dédié à la caisse. Un seul catalogue (`Article`), une seule notion de stock (`stock_movements`), plusieurs usages qui y puisent. Éviter de dupliquer aujourd'hui ce qu'il faudrait fusionner demain.

## Nommage — point d'attention

Le mot **"Comptoir"** est déjà utilisé dans l'application pour un mode d'affichage sans rapport (masquage d'informations sensibles à l'accueil, toggle "Atelier / Comptoir" dans `MainLayout.tsx` / `usePrivacyMode`). Pour ne jamais entrer en collision avec ce concept existant :
- Nom produit / libellé UI : **Caisse**
- Identifiants techniques : modèle `Sale` / `SaleLine`, route `/vente/caisse`

## Décisions d'architecture

### Catalogue existant réutilisé tel quel
Aucune nouvelle table de « produit ». La caisse s'appuie sur `Article` (feature 22), en lui ajoutant `barcode`, `image_url`, `weight_kg`.

### Attributs structurés pour le filtrage, extraits là où c'est fiable
La désignation CGN est du texte libre fournisseur. Son analyse (échantillons réels du CSV, pas une supposition a priori) montre une grammaire assez régulière pour certaines familles de produits (pneus, chambres à air, plateaux, pédaliers, manivelles, cassettes/roue-libres, éclairage), et aucune structure exploitable pour d'autres (outillage, cadres...). En conséquence : une table générique `article_attributes` (clé/valeur), remplie par des extracteurs dédiés à chaque famille fiable, jamais par un parseur générique appliqué à l'aveugle sur tout le catalogue. Détail complet du périmètre v1 dans `01-modele-donnees.md`.

### CSV fournisseur = source d'alimentation du catalogue, pas une table à part
Le fichier CGN est importé via une commande Artisan qui crée/met à jour des `Article`. Pas de table `supplier_products` séparée : le catalogue reste la seule source de vérité pour la vente, cohérent avec l'esprit de la feature 22.

### Vente = mouvements de stock tracés, comme le reste du catalogue
La finalisation d'une vente comptoir crée des `StockMovement` (nouveau type `sale_consumption`), dans la continuité de l'arbitrage 1 de la feature 22 ("le stock n'est jamais un champ, toujours la somme des mouvements"). C'est la première fois qu'un mouvement automatique est câblé en v1 (la feature 22 avait volontairement repoussé cette automatisation — voir son arbitrage 6) : la caisse est précisément le cas d'usage qui la justifie.

### Montants en centimes (integer)
`Sale`/`SaleLine` stockent leurs montants en centimes (integer), cohérent avec `Article::purchase_price_ht`/`sale_price_ttc` — pas en décimal euros comme `Quote`/`QuoteLine`. Un panier multi-articles additionne des entiers, sans dérive d'arrondi flottant.

### Marge suivie dès la v1
Contrairement à `QuoteLine`, `SaleLine` porte un `purchase_price_ht` (copié depuis l'article au moment de la vente), pour permettre un calcul de marge par ticket dès la v1.

### Le catalogue pré-remplit, il ne verrouille pas (repris de la feature 22)
Le prix de vente proposé au panier vient de `Article::sale_price_ttc`, mais reste modifiable ligne par ligne avant finalisation (remise ponctuelle, prix négocié).

## Hors périmètre (v1)

- Douchette/scanner physique : aucune intégration matérielle spécifique. Un scanner USB « keyboard wedge » fonctionne nativement (il émule un clavier + Enter) sur un champ texte standard — pas de code dédié nécessaire.
- Récupération automatique du CSV via FTP : le fichier est lu depuis un chemin fixe à la racine du projet. Le FTP sera branché plus tard (commande déjà isolée pour ne pas avoir à la retoucher).
- Assignation automatique d'un `Supplier` "CGN" aux articles importés : non ; `supplier_id` reste `null` sur les articles créés par l'import.
- Table de mapping catégories/sous-catégories CGN maintenue en config : non ; création à la volée avec mise en forme automatique (`Str::title`).
- Paiement multi-moyens sur une même vente (split cash/carte) : un seul `payment_method` par vente en v1.
- Lien client sur la vente comptoir : `client_id` nullable, la plupart des ventes comptoir restent anonymes.
- **Intégration aux KPI mensuels `vente` du dashboard** : les KPI existants sont alimentés par les `Quote` transformées en facture, pas par `Sale`. Les ventes caisse n'apparaîtront pas dans le dashboard métier `vente` en v1 — explicitement hors périmètre, pour ne pas laisser croire que les statistiques affichées sont exhaustives. Une itération future pourra les relier.
- **Justificatif de vente (ticket imprimable/envoyable)** : aucune génération de reçu, impression ou envoi par email en v1. Seul l'écran "ventes récentes" (ticket 25.7) permet de retrouver une vente pour annulation, sans détail imprimable.
- **Lignes libres illimitées sans traçabilité catalogue** : les lignes libres restent possibles (`article_id = null`, arbitrage) mais n'alimentent aucun mouvement de stock ni statistique catalogue — elles ne concernent que des ventes hors catalogue exceptionnelles, pas un usage courant attendu.
