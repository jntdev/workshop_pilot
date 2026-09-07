# Arbitrages — Feature 27

## 1. Scan caméra plutôt que douchette dédiée

**Constat** : une douchette de code-barres (Bluetooth/USB) résout aussi ce besoin, mais impose une dépendance matérielle et un coût d'achat.

**Décision** : scan via la caméra du navigateur (librairie JS, ex. `@zxing/library` pour compatibilité iOS/Android/desktop — `BarcodeDetector` natif exclut Safari/iOS). N'importe quel smartphone équipé d'un navigateur devient utilisable, au comptoir comme en tournée d'inventaire dans les rayons.

**Validé par l'utilisateur** : *"je ne cherche pas a investir dans une douchette car on peut s'en servir en inventaire comme au comptoir, je prefere rester flexible et ne pas etre dépendant d'un appareil"*.

## 2. Création de stock, pas correction d'écart

**Constat initial (écarté après clarification)** : un premier cadrage envisageait un inventaire classique (comparer stock théorique vs quantité physique comptée, ajuster dans les deux sens). Ce modèle suppose un stock théorique déjà fiable — or le stock atelier réel est aujourd'hui vide, la quasi-totalité des ~15 000 articles CGN n'ayant jamais eu de mouvement.

**Décision** : le scan crée directement un mouvement d'entrée (`manual_in`) sur la quantité comptée, sans notion d'écart ni de correction. C'est un peuplement initial du stock virtuel, pas un audit de cohérence.

**Validé par l'utilisateur** : *"voici le besoin reel : je cherche a vendre au comptoir, et je cherche aussi a créer notre stock virtuel. avec cette fonctionnalité, facile pour moi d'aller scanner tout mes produits."*

## 3. Écran séparé, mobile-first, accessible depuis la page Stock

**Constat** : la page Stock actuelle (grille de cartes, feature 26 bis) est pensée pour une consultation/gestion sur poste fixe ou mobile, mais pas pour un scan caméra en continu.

**Décision** : nouvel écran dédié, plein écran caméra, pensé mobile-first — pas une modale ou un panneau superposé à la page Stock existante. Accessible via un bouton bien visible sur mobile depuis `/stock`.

**Validé par l'utilisateur** : *"ecran séparé dédié a cet usage [...] un bouton accessible dans la page stock (bien visible sur mobile) puisque la fonctionnalité est mobile first."*

## 4. Fonctionnalité "Inventaire" comme base à enrichir, pas figée

**Constat** : le besoin exprimé aujourd'hui est volontairement restreint (scan → quantité → mouvement), mais l'utilisateur anticipe des usages futurs sur ce même écran.

**Décision** : cette feature documente et implémente strictement le périmètre actuel (voir `00-contexte.md`, hors périmètre), sans construire par anticipation des mécanismes non demandés (pas de session persistée, pas de mode correction d'écart). L'architecture (route de lookup dédiée, composant scanner réutilisable) reste simple à étendre sans sur-ingénierie prématurée.

**Validé par l'utilisateur** : *"on pourrait créer une fonctionnalité 'inventaire' a enrichir plus tard."*

## 5. Code-barres inconnu : création à la volée avec photo, pas ignoré silencieusement

**Constat** : un produit physiquement présent en magasin mais absent du catalogue CGN (achat direct, occasion, article non référencé fournisseur) ne doit pas bloquer la boucle de scan.

**Décision** : proposer une création rapide avec capture photo caméra + formulaire minimal, incluant la sélection de sous-catégorie et — de façon optionnelle — les attributs de filtre pertinents, pour que l'article nouvellement créé reste trouvable comme le reste du catalogue (mêmes filtres dynamiques, feature 26).

**Validé par l'utilisateur** : *"oui, dans le cas d'un code barre non catalogué, on peut prendre une photo, et renseigner le produit. il faudra les filtres de la categories du produit afin de rendre le produit 'trouvable'"*.

## 6. Champs obligatoires à la création rapide : photo, désignation, sous-catégorie, marque, prix de vente TTC, prix d'achat HT, quantité

**Constat** : un article créé à la volée sans prix ni classification resterait une fiche incomplète, invendable et improbable à retrouver plus tard.

**Décision** : rendre obligatoires les six champs listés initialement, **plus la quantité comptée** (voir arbitrage 10, révision suite au feedback) — suffisant pour que l'article soit immédiatement vendable (prix connus des deux côtés, marge calculable), classé (sous-catégorie, marque), et réellement présent dans le stock virtuel dès sa création. Les attributs de filtre (diamètre, pratique...) restent optionnels — un produit reste trouvable par désignation/sous-catégorie même sans eux, et forcer leur saisie ralentirait la création sans bénéfice proportionné pour un cas qui devrait rester marginal (la majorité du catalogue vient de l'import CGN, pas du scan).

**Validé par l'utilisateur** : validation explicite de "Photo + désignation + sous-catégorie" puis ajout explicite de "Prix de vente TTC" et "Marque", puis confirmation que le prix d'achat HT est *"aussi obligatoire"* pour garantir un calcul de marge correct immédiatement.

## 7. Référence = code-barres scanné, dérivée côté serveur (jamais un champ du payload)

**Constat** : `StoreArticleRequest` exige une `reference` unique et obligatoire, mais un article créé depuis le scan n'a pas de référence fournisseur CGN.

**Décision initiale** : le `barcode` scanné est dupliqué comme `reference` — identifiant déjà unique par nature (EAN), lisible, sans risque de collision avec les références CGN existantes (format numérique différent des EAN à 13 chiffres).

**Révision (feedback de préparation, point 1)** : la version initiale prévoyait que le frontend envoie `reference` et `barcode` séparément dans le payload, tous deux validés indépendamment par `StoreInventoryArticleRequest` — rien n'empêchait alors un payload avec `reference !== barcode`, en contradiction directe avec les critères d'acceptation du ticket 06. Décision corrigée : **`reference` n'est jamais un champ d'entrée**. Le frontend n'envoie que `barcode` ; `InventoryController::storeArticle()` calcule `reference = $validated['barcode']` avant de créer l'`Article`. Élimine la classe d'erreur entière plutôt que de la valider après coup (`same:barcode` aurait aussi fonctionné, mais laisse un champ inutile transiter pour rien).

**Validé par l'utilisateur** : "Le code-barres scanné lui-même (Recommandé)".

## 8. Quantité toujours vide au scan, jamais de valeur par défaut

**Constat** : pré-remplir la quantité à 1 optimiserait la vitesse pour le cas fréquent d'un seul exemplaire en rayon, mais introduit un risque de valider par erreur une quantité fausse (double-clic, confirmation trop rapide).

**Décision** : le champ quantité reste vide à chaque scan, saisie manuelle systématique.

**Validé par l'utilisateur** : "Champ vide, saisie manuelle à chaque scan (Recommandé)".

## 9. Photo stockée en fichier simple, pas via `spatie/media-library`

**Constat** : `Message`/`MessageReply` utilisent déjà `spatie/media-library` (`InteractsWithMedia`) pour leurs pièces jointes, mais `Article.image_url` est aujourd'hui une simple colonne texte (URLs CGN externes), sans cette dépendance.

**Décision** : la photo capturée est uploadée via une route dédiée simple, stockée sur disque (`storage/app/public/articles/`), et son URL résultante remplit directement `image_url` — pas d'introduction de `media-library` sur le modèle `Article` pour un besoin qui n'exige qu'une seule photo par article.

**Validé par l'utilisateur** : "Upload simple vers le disque local, image_url = chemin résultant (Recommandé)".

## 10. Mouvement de stock obligatoire dans le flux de création rapide, pas une étape séparée

**Constat** (relevé par le feedback de préparation) : le ticket 05 initial décrivait la création d'article inconnu sans préciser si une quantité était demandée ni si un mouvement `manual_in` était créé après. Risque identifié : un article créé, le compteur de progression incrémenté, mais aucun stock virtuel réellement peuplé pour ce cas — contradiction directe avec l'objectif produit (voir `00-contexte.md`).

**Décision** : le formulaire de création rapide inclut un champ quantité obligatoire (même règle qu'un article déjà connu — voir arbitrage 8, jamais de valeur par défaut). `InventoryController::storeArticle()` crée l'article **et** le mouvement de stock correspondant dans le même appel serveur (idéalement une transaction), pas deux étapes distinctes que le frontend pourrait enchaîner de façon incohérente en cas d'échec partiel.

## 11. Endpoint dédié pour la création rapide, validation stricte isolée de `StoreArticleRequest`

**Constat** (relevé par le feedback de préparation) : `StoreArticleRequest` (utilisé par `ArticleForm.tsx` pour la création/édition manuelle classique) accepte `article_subcategory_id`, `brand_id` en `nullable` — ne garantit donc pas côté serveur les obligations propres au flux de scan (sous-catégorie et marque obligatoires). Durcir directement `StoreArticleRequest` casserait la création manuelle classique, qui a de bonnes raisons de rester plus permissive.

**Décision** : nouvel endpoint `POST /api/inventory/articles` avec son propre `StoreInventoryArticleRequest`, imposant strictement les 7 champs obligatoires de l'arbitrage 6. `StoreArticleRequest` et `ArticleController::store()` restent inchangés.

**Validé par l'utilisateur** : "Nouvel endpoint dédié avec sa propre validation stricte (Recommandé)".

## 12. Uploads photo orphelins : dette acceptée en v1

**Constat** (relevé par le feedback de préparation) : la photo est uploadée avant la création de l'article (retour immédiat pour prévisualisation). Si l'utilisateur annule le formulaire ensuite, ou si `POST /api/inventory/articles` échoue après upload réussi, le fichier reste sur le disque sans article associé.

**Décision** : dette assumée explicitement pour la v1 — pas de nettoyage automatique (ni job de purge, ni suppression immédiate en cas d'échec). Le volume attendu (création manuelle, ponctuelle, pas un flux de masse) rend ce risque mineur à ce stade. À reprendre si le volume de fichiers orphelins devient un problème réel constaté, pas anticipé par précaution.

## 13. Attributs virtuels traduits vers les vraies clés stockées avant persistance

**Constat** (relevé par le feedback de préparation) : `filterOptions()` (feature 26) expose des clés virtuelles de présentation (`wheel_diameter`, `wheel_width_mm`, `wheel_width_inches`, `tooth_range_min`, `tooth_range_max`) qui ne correspondent à aucune clé brute de `article_attributes` — les persister telles quelles romprait le modèle de données et rendrait l'article filtrable différemment des articles importés du CSV CGN.

**Décision** : `InventoryController::storeArticle()` traduit les clés virtuelles vers leurs vraies clés composites (`etrto_size`, `size_inches`, `tooth_range`) avant sauvegarde, en recomposant la valeur complète à partir des deux champs saisis (ex. diamètre + largeur mm → `etrto_size`). Une valeur partielle (un seul des deux champs renseigné) n'est jamais persistée sous une forme inventée — elle est ignorée silencieusement plutôt que de stocker une donnée fausse. Voir `01-modele-donnees.md` pour le détail exact de la traduction par clé.

Point de vigilance levé par l'utilisateur, déjà garanti par construction : les attributs proposés dans le formulaire ne concernent que la sous-catégorie choisie (`filterOptions(id)` appelé avec cet id précis) — jamais un mélange d'attributs d'autres familles de produits qui ne correspondraient pas du tout à l'article en cours de création.

**Validé par l'utilisateur** : *"ce qui m'inquiete c'est d'avoir des attributs a renseigner qui ne correspondent pas du tout au produit, sinon, je prefere la proposition 1"* (traduire les clés virtuelles vers les vraies clés avant sauvegarde).

## 14. Liste blanche stricte des clés d'attributs acceptées côté serveur

**Constat** (relevé par le feedback de préparation, point 3) : la première version de `StoreInventoryArticleRequest` validait `attributes.*` uniquement comme `nullable|string|max:255`, sans restreindre les *clés* acceptées — un client pouvait donc envoyer des clés arbitraires ou hors sujet pour la sous-catégorie choisie, polluant `article_attributes` et rendant les filtres de la feature 26 incohérents à terme, malgré le fait que l'interface ne propose que des attributs pertinents (le contrat client n'est pas une garantie serveur).

**Décision** : `InventoryController::storeArticle()` filtre les `attributes` reçus contre une liste blanche stricte (`ACCEPTED_ATTRIBUTE_KEYS`, union de `FILTERABLE_KEYS` et des 5 clés virtuelles traduisibles) avant tout traitement — toute clé hors de cette liste est silencieusement ignorée, jamais persistée, jamais source d'erreur bloquante (cohérent avec l'esprit "ne jamais bloquer la boucle de scan" de la feature). Les valeurs vides/nulles ne sont pas non plus persistées. Voir `01-modele-donnees.md` pour la liste exacte.

## 15. `image_url` validée strictement contre le préfixe de l'upload dédié

**Constat** (relevé par le feedback de préparation, point 4) : la validation initiale de `image_url` (`required|string|max:500`) accepte n'importe quelle chaîne — un client pourrait fournir une URL arbitraire sans avoir réellement utilisé `POST /api/articles/upload-photo`, ce qui contredirait l'obligation produit "photo capturée obligatoire" (arbitrage 6) en la réduisant à une garantie purement frontend.

**Décision** : ajout de la règle `starts_with:/storage/articles/` sur `image_url` dans `StoreInventoryArticleRequest` — ce préfixe n'est produit que par la route d'upload dédiée (ticket 02), donc sa présence prouve côté serveur que la photo est bien passée par ce flux. Choix de la validation stricte plutôt que la dette acceptée, par cohérence avec les arbitrages 11 et 14 (déjà tranchés en faveur de garanties serveur explicites pour ce même endpoint).
