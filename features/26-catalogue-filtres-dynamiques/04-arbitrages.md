# Arbitrages — Feature 26

## 1. Catégorie racine implicite, jamais un niveau de navigation

**Constat** : le magasin ne vend que dans une seule catégorie ("Pièces Cycles"). Faire choisir cette catégorie à l'utilisateur à chaque ouverture du catalogue n'apporte aucune information — c'est un clic obligatoire sans décision réelle derrière.

**Décision** : la nav gauche part directement de la liste plate des sous-catégories de "Pièces Cycles" (`GET /api/article-categories`, réutilisé tel quel, `data.categories?.[0]?.subcategories`). La catégorie n'est affichée nulle part dans l'UI.

**Validé par l'utilisateur** : *"nous pouvons dors et deja affirmer que nous seront toujours dans la categories cycles. c'est meme pas un filtre a selectionner, c'est une constante."*

## 2. Marque : filtre secondaire contextualisé, jamais une liste globale

**Constat initial** : la modale actuelle propose la liste de toutes les marques du catalogue, y compris celles sans aucun article dans la sous-catégorie regardée — mène à des sélections qui retournent zéro résultat.

**Décision** : la marque n'est proposée qu'après le choix d'une sous-catégorie, et uniquement les marques ayant au moins un article dans cette sous-catégorie (`Brand::whereHas('articles', fn ($q) => $q->where('article_subcategory_id', $id))`).

**Validé par l'utilisateur** : *"marque peut etre un filtre une fois qu'on est dans une categories"*, confirmé par le choix explicite "Marque + recherche texte uniquement (Recommandé)" pour les sous-catégories sans attributs structurés.

## 3. Valeurs de filtre calculées dynamiquement, jamais une liste figée en dur

**Constat** : une liste de valeurs codée en dur (ex. `['ROUTE', 'VTT', 'GRAVEL']` pour `practice_type`) se désynchronise silencieusement du catalogue réel à chaque réimport CGN — une nouvelle valeur fournisseur n'apparaîtrait jamais comme option de filtre, sans erreur visible.

**Décision** : `filterOptions()` calcule les valeurs disponibles par une requête `DISTINCT` sur `article_attributes` au moment de l'appel, jamais une constante applicative.

**Validé par l'utilisateur** : "Dynamique depuis la base (Recommandé)".

## 4. Décomposition des attributs à 2 dimensions plutôt qu'exposition brute

**Constat vérifié empiriquement** (pas supposé — voir tableau de cardinalité dans [00-contexte.md](00-contexte.md)) : `etrto_size` combine largeur et diamètre dans une seule chaîne (134 valeurs distinctes sur Pneus, 69 sur Chambres) ; `tooth_range` combine petit et grand pignon (58 valeurs sur Cassettes). Exposés bruts, ces attributs produiraient des `<select>` à plus de 60 options — inutilisables et sans rapport avec la façon dont un vendeur pense réellement une taille de pneu ou une plage de cassette (par diamètre de roue, puis largeur ; par petit pignon, puis grand pignon).

**Décision** : décomposition en filtres indépendants par attribut composite :
- `tooth_range` → **Petit pignon** / **Grand pignon**.
- `etrto_size` → **Largeur (mm)** (voir arbitrage 7 pour le diamètre, qui n'utilise plus `etrto_size`).

C'est une transformation de présentation dans `filterOptions()` et dans la traduction du filtre côté `index()` — `article_attributes` continue de stocker les valeurs composites telles que produites par les extracteurs de la feature 25. Aucune migration de données, aucune modification d'extracteur.

**Cas non décomposé, volontairement** : `tooth_count` (29 valeurs sur Plateaux) reste un filtre simple malgré son volume — c'est une valeur unique, pas une combinatoire à 2 dimensions, donc moins problématique en ergonomie qu'`etrto_size`/`tooth_range`.

**Validé par l'utilisateur** : réponse explicite retenant la décomposition `etrto_size`/`tooth_range` proposée en question de clarification, et l'exclusion de `supplier_status` comme clé filtrable (statut fournisseur interne, pas une caractéristique produit). **Révisé ensuite** : voir arbitrage 7, le diamètre ne vient plus de `etrto_size`.

## 5. Normalisation cosmétique de `practice_type`, jamais une correction de donnée

**Constat** : `practice_type` contient des variantes fragmentées issues du texte libre fournisseur (`VTC/URBAIN`, `VTC`, `VTC/`) qui, si exposées telles quelles, apparaîtraient comme options de filtre distinctes alors qu'elles désignent la même pratique pour l'utilisateur final.

**Décision** : le regroupement des variantes proches (ex. tout ce qui contient "VTC" → une seule option affichée "VTC/Urbain") se fait uniquement dans la couche de présentation du filtre (`filterOptions()`, à l'affichage). La valeur brute d'origine reste celle envoyée dans la requête de filtrage réelle vers `index()` — pas de retouche de `TireAttributeExtractor` ni de ré-import du catalogue CGN. Cet arbitrage garde le chantier dans son périmètre : améliorer l'exploitation des données existantes, pas revenir sur l'extraction déjà livrée et validée en feature 25.

## 6. Bug de pertinence de `ArticleController::search()` : documenté, non corrigé

**Constat** : taper "pneu" dans la recherche libre du catalogue ne retourne pas les pneus en tête de liste — la limite de 10 résultats combinée à un tri alphabétique (`ordered()`, pas par pertinence) noie les vrais pneus derrière des accessoires contenant incidemment le mot "pneu" (ex. "CLIQUET PNEUMATIQUE").

**Décision** : non corrigé dans cette feature. La nouvelle navigation par sous-catégorie contourne ce bug pour l'usage catalogue (on ne passe plus par la recherche texte pour trouver "les pneus", on clique sur la sous-catégorie Pneus). `ArticleAutocomplete.tsx` (saisie directe de référence en ligne de devis, composant distinct) reste affecté et n'est pas dans le périmètre de ce chantier.

**Pourquoi ne pas le corriger au passage** : périmètre déjà bien défini par l'utilisateur autour de la navigation catalogue ; corriger `search()` toucherait un composant et un contrôleur utilisés ailleurs dans l'application, avec un risque de régression hors du sujet traité ici. À reprendre séparément si l'utilisateur le demande.

## 7. Diamètre de roue : désignation commerciale (`size_inches`), jamais le BSD technique (`etrto_size`)

**Constat signalé par l'utilisateur en test réel** : le filtre "Diamètre de roue" initialement construit à partir de `etrto_size` (BSD en mm) affichait des valeurs comme 622/630/635mm côte à côte, sans repère commercial. Or `622mm` regroupe à la fois des pneus "700" (route/gravel) et "29"" (VTT), et `630`/`635` sont des tailles commercialement différentes de "700" malgré leur proximité numérique en mm. Un client qui demande "un pneu 700" ne doit pas se voir proposer un choix ambigu entre ces valeurs techniques.

**Décision** : le diamètre est désormais lu depuis `size_inches` (notation commerciale déjà extraite par les extracteurs de la feature 25), pas depuis `etrto_size`. Extraction :
- Pneus : préfixe avant le premier `X` (`size_inches` a le format `"{diamètre}X{largeur}"`).
- Chambres : la valeur nettoyée telle quelle (`size_inches` **est** directement le diamètre, sans `X`, format différent entre les deux sous-catégories vérifié empiriquement — voir `01-modele-donnees.md`).

Résultat filtré par une liste blanche de diamètres commerciaux vélo réels (`COMMERCIAL_WHEEL_DIAMETERS`), pas une extraction brute — le champ contient du bruit fournisseur (`TR`, `TRAINER`, `URBAIN`, plages multi-diamètres sur Chambres) qui doit être exclu silencieusement plutôt qu'affiché comme option de filtre.

**Suffixes non fusionnés** : `700` et `700C` restent deux options distinctes (de même `650`/`650A`/`650B`), sur demande explicite — la fusion aurait simplifié l'affichage mais masqué une information que l'utilisateur veut garder visible.

**Validé par l'utilisateur** : *"j'aimerai traité directement par diametre en mm ou en pouce, et que ca correspondent au reference de pneu que je cherche. par exemple si je cherche un 700x35, je souhait trouver 700 ou 28\""* ; confirmé "Liste blanche des diamètres vélo connus (Recommandé)" et "Garder les suffixes séparés".

## 8. Largeur : deux filtres indépendants (mm et pouces), jamais une conversion calculée

**Constat vérifié empiriquement** : chaque article Pneu porte simultanément une largeur exacte en mm (premier nombre de `etrto_size`) et une largeur commerciale en pouces/notation fournisseur (suffixe après le `X` dans `size_inches`) — ce sont deux mesures indépendantes du même pneu, pas une valeur convertible mathématiquement en l'autre (une conversion mm↔pouces généraliste serait fausse pour des pneus, la relation n'est pas un simple facteur linéaire selon le style de pneu).

**Décision** : deux `<select>` indépendants, **Largeur (mm)** (source `etrto_size`) et **Largeur (pouces)** (source `size_inches`, suffixe après `X`, format simple uniquement — `28`, `28C`, `1.75`, `35B` ; les plages `"1.50-2.40"` et fractions `"1 3/8"` sont ignorées silencieusement, hors périmètre v1). L'utilisateur choisit l'unité qu'il connaît (le client donne parfois l'un, parfois l'autre) sans conversion à faire de son côté.

**Validé par l'utilisateur** : *"j'aimerai bien avoir largeur en mm ou largeur en pouce comme choix [...] je n'ai pas envie de faire la conversion a chaque fois moi meme"* ; confirmé "Deux filtres séparés : Largeur (mm) et Largeur (pouces) (Recommandé)".
