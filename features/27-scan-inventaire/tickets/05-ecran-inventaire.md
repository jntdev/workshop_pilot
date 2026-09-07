# Ticket 05 : Écran Inventaire — scan + saisie quantité

**Type** : Frontend
**Priorité** : Haute
**Estimation** : 3h30

**Dépend de** : Ticket 01, Ticket 03

## Tâches

- [ ] Nouvelle page Inertia `resources/js/Pages/Inventory/Scan.tsx`, mobile-first (voir `../04-arbitrages.md`, arbitrage 3) — écran séparé, pas une modale superposée à la page Stock
- [ ] Route `GET /inventaire/scan` dans `routes/web.php` (groupe `auth`), rendant cette page
- [ ] États du composant :
  ```typescript
  const [scannedArticle, setScannedArticle] = useState<Article | null>(null);
  const [ambiguousArticles, setAmbiguousArticles] = useState<Article[] | null>(null);
  const [notFoundCode, setNotFoundCode] = useState<string | null>(null);
  const [quantity, setQuantity] = useState(''); // toujours vide au départ, voir arbitrage 8
  const [scannedCount, setScannedCount] = useState(0);
  ```
- [ ] Au `onDetected(code)` du `BarcodeScanner` (ticket 03) : appelle `GET /api/inventory/lookup-barcode?code={code}`, bascule `isPaused=true` sur le scanner pendant qu'une fiche est affichée
  - `result: "found"` → `setScannedArticle(article)`, affiche la fiche (photo, désignation, prix — réutilise le composant `ArticleCardImage` déjà existant) avec un champ quantité vide
  - `result: "ambiguous"` → `setAmbiguousArticles(articles)`, liste à choix (jamais de sélection arbitraire)
  - `result: "not_found"` → `setNotFoundCode(code)`, propose la création rapide (renvoie vers ticket 06)
- [ ] **Parcours après choix dans la liste `ambiguous` (précision suite au feedback de préparation, point 2)** : au clic sur un article de `ambiguousArticles`, exactement le même traitement qu'un `found` direct — `setScannedArticle(article)`, `setAmbiguousArticles(null)`, champ quantité vide, scanner reste en pause (`isPaused` ne change pas, il était déjà `true` depuis le scan initial). Aucun nouveau lookup réseau à cette étape — l'article choisi est déjà complet depuis la réponse `ambiguous`. La validation de quantité, la création du mouvement et le retour au scan suivent ensuite le chemin unique décrit ci-dessous, identique pour `found` et pour un choix issu de `ambiguous`.
- [ ] Validation de la quantité (article `found`, ou choisi depuis `ambiguous`) : `POST /api/articles/{id}/stock-movements` avec `type: manual_in`, `quantity` — respecter `quantityStep(article.unit)` (feature 26 bis) pour le pas de saisie et la validation entière
- [ ] Après validation réussie : incrémente `scannedCount`, réinitialise l'état (`setScannedArticle(null)`, `setQuantity('')`), relance le scan (`isPaused=false`) — retour immédiat à l'étape de scan sans renavigation (voir `../00-contexte.md`, parcours étape 6)
- [ ] Affichage permanent du compteur de progression ("X produits scannés") — état local à l'écran, pas persisté en base (voir `../04-arbitrages.md`, arbitrage 4)
- [ ] Bouton de sortie/retour vers `/stock`, visible en permanence

## Critères d'acceptation

- Scanner un code-barres connu affiche la fiche article avec un champ quantité vide, jamais pré-rempli
- Valider une quantité crée bien un mouvement d'entrée et relance immédiatement le scan
- Le compteur de progression s'incrémente à chaque validation réussie
- Un code-barres partagé par plusieurs articles affiche un choix explicite, jamais une sélection automatique
- Choisir un article dans la liste `ambiguous` affiche sa fiche avec un champ quantité vide, exactement comme un `found` direct — pas de nouvel appel réseau, pas d'état intermédiaire flou
- Quitter l'écran coupe la caméra (délégué au ticket 03, vérifié ici en conditions d'usage réelles)
- Un article déjà en stock (stock > 0) scanné à nouveau ajoute bien à son stock existant (mouvement d'entrée cumulatif), ne l'écrase pas
