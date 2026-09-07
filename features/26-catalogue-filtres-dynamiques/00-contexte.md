# Feature 26 : Catalogue par sous-catégorie avec filtres dynamiques d'attributs

## Contexte

Le catalogue actuel (`CataloguePickerModal.tsx`, utilisé depuis les lignes de devis atelier via le bouton 📋) navigue par catégorie → sous-catégorie → marque, sans lien avec les attributs structurés (`article_attributes`) extraits à l'import CGN (feature 25). Or le magasin ne vend que dans la catégorie "Pièces Cycles" — c'est une constante implicite, pas un choix à présenter à l'utilisateur. Ce niveau de navigation est donc superflu et alourdit l'usage.

Second problème constaté en testant la recherche libre : taper "pneu" dans le champ de recherche du catalogue ne retourne aucun vrai pneu pertinent. Investigation : `ArticleController::search()` retourne bien des résultats (ex. "CLIQUET PNEUMATIQUE", "BROSSE NETTOYAGE...PNEU"), mais la limite de 10 résultats combinée à un tri alphabétique (`ordered()`, pas un tri par pertinence) noie les vrais pneus (qui commencent tous par "PNEU ...") derrière des accessoires contenant incidemment le mot "pneu". **Ce bug n'est pas corrigé dans cette feature** — la nouvelle navigation par sous-catégorie le contourne pour l'usage catalogue, mais `ArticleAutocomplete.tsx` (autre composant, utilisé pour la saisie directe de référence en ligne de devis) reste affecté et devra être traité séparément si besoin.

## Objectif produit

Remplacer la navigation catégorie/sous-catégorie/marque par une liste plate des sous-catégories de "Pièces Cycles", puis afficher des filtres pertinents une fois une sous-catégorie choisie : les attributs spécifiques à cette sous-catégorie (déjà extraits à l'import CGN dans `article_attributes`) + un filtre Marque contextualisé (uniquement les marques ayant des articles dans cette sous-catégorie, pas toutes les marques du catalogue).

## Constat déterminant : cardinalité réelle des attributs

Avant de concevoir les filtres, la cardinalité réelle des valeurs a été vérifiée en base (pas supposée) sur les 7 sous-catégories couvertes par des extracteurs (feature 25) :

| Sous-catégorie | Attribut | Valeurs distinctes | Filtrable en `<select>` simple ? |
|---|---|---|---|
| Pneus | `practice_type` | 19 | Oui, avec normalisation d'affichage |
| Pneus | `size_inches` | 165 valeurs brutes → ~25 diamètres commerciaux après extraction du préfixe | Oui, pour le **diamètre commercial** (voir arbitrage 7) — pas pour la valeur brute complète |
| Pneus | `etrto_size` | 134 | **Non** — technique (BSD mm), ne correspond pas à ce que le client demande (ex. 622mm regroupe du 700 et du 29" ; 630/635mm sont des variantes proches de 700 mais commercialement distinctes) |
| Chambres | `size_inches` | 122 valeurs brutes → même traitement que Pneus | Oui, même logique |
| Chambres | `etrto_size` | 69 | **Non** — même raison |
| Chambres | `valve_type` | 2 | Oui |
| Plateaux | `practice_type` | 8 | Oui |
| Plateaux | `chainring_diameter_mm` | 17 | Oui |
| Plateaux | `tooth_count` | 29 | Oui (accepté malgré le volume, pas une combinatoire à 2 dimensions) |
| Plateaux | `speed_count` | 6 | Oui |
| Pédaliers | (attributs similaires, volumes comparables à Plateaux) | — | Oui |
| Manivelles | `crank_length_mm` | 4 | Oui |
| Manivelles | `practice_type` | 4 | Oui |
| Manivelles | `side` | 3 | Oui |
| Roue-Libres/Cassettes | `tooth_count` | 4 | Oui |
| Roue-Libres/Cassettes | `speed_count` | 8 | Oui |
| Roue-Libres/Cassettes | `tooth_range` | 58 | **Non** — combine 2 dimensions (min-max) |
| Roue-Libres/Cassettes | `practice_type` | 2 | Oui |
| Roue-Libres/Cassettes | `speed_compat` | 11 | Oui |
| Roue-Libres/Cassettes | `axle_type` | 2 | Oui |
| Éclairage | `position` | 4 | Oui |
| Éclairage | `power_source` | 3 | Oui |
| *(toutes sous-catégories)* | `supplier_status` | 1 | **Non filtrable** — statut fournisseur interne, pas une caractéristique produit |

Deux points de vocabulaire à ne pas confondre pendant l'implémentation (déjà tracés dans l'arbitrage 11 de la feature 25, rappelés ici car ce chantier les recroise) : `JEUX DE PEDALIERS` (CSV) = boîtiers de pédalier, distinct de `PEDALIERS` (CSV) = ensemble manivelles+plateaux au sens métier.

## Décisions d'architecture

### Liste blanche de clés filtrables, jamais "tout ce qui existe"
Le nouvel endpoint de filtres ne retourne que les clés explicitement autorisées. `supplier_status` est exclu car ce n'est pas une caractéristique produit.

### Diamètre de roue : désignation commerciale, jamais le diamètre ETRTO/BSD brut (arbitrage 7)
`etrto_size` (BSD en mm, ex. `622`) est une échelle technique qui ne correspond pas au vocabulaire du client : `622mm` regroupe à la fois du "700" (route/gravel) et du "29"" (VTT), et des valeurs voisines comme `630`/`635` sont commercialement distinctes de "700" bien que numériquement proches en mm. Un client qui demande "un pneu 700" ne doit jamais se voir proposer un choix entre 622/630/635 sans repère.

Le filtre "Diamètre de roue" est donc construit à partir de `size_inches` (notation commerciale déjà extraite par `TireAttributeExtractor`/`InnerTubeAttributeExtractor`, ex. `"700X28C"`, `"26X1.75"`, `"27.5X2.10"`), en n'en retenant que le **préfixe avant le `X`** (ex. `700`, `26`, `27.5`), filtré par une liste blanche de diamètres commerciaux vélo réels (voir `01-modele-donnees.md`) pour exclure le bruit fournisseur (`TR`, `TRAINER`, `URBAIN`, `/`). Les suffixes de lettre (`700` vs `700C`, `650` vs `650B`) restent des options distinctes, non fusionnées.

`etrto_size` continue d'exister en base tel que produit par la feature 25 (aucune modification d'extracteur) et redevient la source du filtre **Largeur (mm)** (premier nombre de `etrto_size`) — il n'est plus utilisé que pour la largeur, plus pour le diamètre.

### Largeur : deux filtres indépendants, mm et pouces (arbitrage 8)
Chaque article Pneu porte simultanément une largeur en mm (`etrto_size`) et une largeur en pouces/notation commerciale (suffixe après le `X` de `size_inches`) — deux mesures indépendantes du même pneu, pas une conversion calculable. Deux `<select>` distincts sont proposés : **Largeur (mm)** et **Largeur (pouces)**, chacun lisant sa propre source. Le filtre pouces ne retient que les valeurs au format simple (`28`, `28C`, `1.75`, `35B`) ; plages et fractions sont ignorées silencieusement.

### Décomposition de `tooth_range`
`tooth_range` (format `"min-max"`, ex. `"11-32"`) est décomposé en 2 filtres indépendants côté présentation : **Petit pignon** / **Grand pignon**.

Cette décomposition est une transformation de présentation dans l'endpoint de filtres (`filterOptions()`), pas un changement de modèle de données : `article_attributes` continue de stocker `etrto_size`/`tooth_range` tels que produits par les extracteurs de la feature 25, aucune migration de données ni modification des extracteurs.

### Normalisation cosmétique de `practice_type`
Les variantes fragmentées (`VTC/URBAIN`, `VTC`, `VTC/`) sont regroupées uniquement à l'affichage du filtre (ex. toutes les variantes contenant "VTC" → une seule option affichée "VTC/Urbain"). La valeur brute d'origine reste utilisée pour la requête de filtrage réelle envoyée au backend — pas de retouche de l'extracteur `TireAttributeExtractor` ni de ré-import du catalogue.

### Marque : filtre secondaire contextualisé, jamais liste globale
Le filtre Marque n'affiche que les marques ayant au moins un article dans la sous-catégorie sélectionnée — pas la liste de toutes les marques du catalogue comme aujourd'hui.

### Catégorie racine implicite, jamais affichée
"Pièces Cycles" n'apparaît nulle part dans l'UI — la liste de navigation part directement des sous-catégories.

## Hors périmètre (v1)

- Correction du bug de pertinence de `ArticleController::search()` / `ArticleAutocomplete.tsx` — documenté ci-dessus, non traité ici.
- Modification des extracteurs d'attributs (`app/Services/Catalogue/*AttributeExtractor.php`, feature 25) — aucune, la normalisation reste cosmétique côté présentation.
- Filtres pour les ~66 sous-catégories sans `article_attributes` : uniquement Marque + recherche texte, comme aujourd'hui — pas d'attribut inventé sans preuve de donnée fiable.
- Ré-import du catalogue CGN — ce chantier ne modifie ni les données ni le format de `article_attributes`.
