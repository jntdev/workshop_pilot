# Feature 27 : Scan mobile pour créer le stock virtuel (Inventaire)

## Contexte

Le catalogue importé depuis le CSV fournisseur CGN (feature 25) contient ~15 000 articles, mais la quasi-totalité n'a jamais eu de mouvement de stock — c'est un catalogue fournisseur pur, pas un reflet de ce qui est physiquement présent en magasin (voir feature 25/26, toggle "Stock atelier" vs "Catalogue complet" sur la page Stock). Le stock atelier réel est aujourd'hui vide : aucune donnée de stock virtuel n'a encore été créée.

## Objectif produit

Permettre de créer ce stock virtuel rapidement en parcourant physiquement le magasin avec un smartphone : scanner le code-barres de chaque produit réellement présent en rayon, renseigner la quantité comptée, passer au produit suivant. Ce n'est **pas** un inventaire de correction d'écart (comparer théorique vs compté) — c'est une création initiale de données de stock, un scan crée un mouvement d'entrée qui fait automatiquement basculer l'article de "Catalogue complet" vers "Stock atelier" (mécanisme déjà existant, feature 26).

Explicitement voulu : pas de dépendance à un appareil dédié (douchette). N'importe quel smartphone avec navigateur fait l'affaire, au comptoir comme en tournée d'inventaire dans les rayons — flexibilité plutôt qu'investissement matériel.

## Parcours utilisateur

1. Accès via un bouton dédié, bien visible sur mobile, présent sur la page Stock (`/stock`).
2. Écran séparé, mobile-first, plein écran caméra.
3. Le smartphone scanne un code-barres via la caméra (décodage côté navigateur, aucun matériel externe).
4. **Cas article connu** (code-barres retrouvé en base) : affiche la fiche article (photo, désignation, prix), avec un champ quantité **vide** (pas de valeur par défaut, saisie manuelle systématique — évite d'enregistrer une quantité fausse par inattention). Validation crée un mouvement d'entrée (`manual_in`) de la quantité saisie.
5. **Cas code-barres inconnu** (aucun article ne le porte) : propose de créer l'article à la volée — capture photo caméra + formulaire minimal (voir `01-modele-donnees.md`), pour que le produit soit immédiatement vendable et filtrable comme le reste du catalogue. **La quantité comptée est aussi demandée dans ce formulaire** : la création de l'article est immédiatement suivie de la création du mouvement d'entrée correspondant, exactement comme pour un article déjà connu — sans quoi le stock virtuel ne serait pas réellement peuplé pour ce cas (voir `04-arbitrages.md`, arbitrage 10).
6. Retour immédiat à l'étape de scan après validation (article connu ou nouvellement créé), sans renavigation — la vitesse de la boucle scan→valider→scan est le critère de réussite de cet écran.
7. Un compteur simple ("X produits scannés dans cette session") pour se rassurer sur la progression. Pas de notion de clôture/session obligatoire à finaliser — l'utilisateur peut s'arrêter et reprendre à tout moment, chaque scan étant indépendant.

## Décision explicite : base à enrichir, pas figée

Cette feature est volontairement scopée à son strict nécessaire (scan + création rapide), avec l'intention assumée de l'enrichir plus tard (ex. futur mode de correction d'écart, statistiques de session, export). L'architecture doit rester simple à étendre, sans sur-ingénierie prématurée pour des besoins non encore exprimés.

## Hors périmètre (v1)

- Mode "inventaire de correction d'écart" (comparer stock théorique vs compté, ajuster dans les deux sens) — explicitement écarté par l'utilisateur, ce n'est pas le besoin réel actuel.
- Notion de session d'inventaire persistée/clôturée en base — le compteur de progression est un état local à l'écran, pas une entité stockée.
- Gestion des sorties de stock — reste hors périmètre comme déjà noté sur l'édition inline de la page Stock (feature 26).
- `spatie/media-library` sur `Article` — la photo capturée est stockée comme simple fichier, `image_url` reste une colonne texte comme aujourd'hui (URLs CGN externes).
