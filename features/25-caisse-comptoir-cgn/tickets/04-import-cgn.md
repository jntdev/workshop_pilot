# Ticket 04 : Commande d'import catalogue CGN

**Type** : Backend / Console
**Priorité** : Haute
**Estimation** : 3h

**Dépend de** : Tickets 01, 02

## Description

Commande Artisan `catalogue:import-cgn {path=StockNouveautesCgn022252.csv}` qui lit le CSV fournisseur (chemin relatif résolu depuis `base_path()`) et crée/met à jour les `Article` correspondants.

## Tâches

- [ ] Créer `app/Console/Commands/ImportCgnCatalogue.php`
- [ ] Lecture : `File::get($path)` puis `mb_convert_encoding($content, 'UTF-8', 'Windows-1252')` (le fichier CGN est en Windows-1252), split par ligne, parsing de chaque ligne avec `str_getcsv($line, ';')`
- [ ] **Helper unique de parsing décimal**, utilisé pour toutes les colonnes numériques (prix d'achat, prix conseillé, poids) plutôt que de dupliquer une normalisation différente par colonne :
  ```php
  private function parseDecimal(?string $value): ?float
  {
      $value = trim((string) $value);
      if ($value === '') {
          return null;
      }
      return (float) str_replace(',', '.', $value);
  }
  ```
  Vérifié sur le fichier réel : la colonne prix d'achat (index 2) n'utilise que le point décimal aujourd'hui (`9.28`), la colonne poids (index 11) utilise systématiquement la virgule (`0,32000`) — sans ce helper commun, le poids serait mal parsé (`(float) "0,32000"` vaut `0`, pas `0.32`). Utiliser `parseDecimal()` partout élimine le risque, y compris si le fournisseur change un jour le format d'une colonne aujourd'hui au point
- [ ] Mapping colonnes (0-indexé, **re-vérifié ligne par ligne sur le CSV réel**) : `0` référence CGN, `1` désignation, `2` prix d'achat HT (`parseDecimal()` puis `round(*100)`), `3` catégorie brute, `4` sous-catégorie brute, `7` EAN, `9` marque, `10` prix public conseillé (`parseDecimal()` puis `round(*100)`), `11` poids brut (`weight_kg`, `parseDecimal()`, colonne systématiquement en virgule ex. `0,33000`), `12` URL photo (`image_url`, prise telle quelle si non vide, ex. `https://www.cgnfrance-pro.com/PartageWeb/Produits/435447_0.jpg`)
- [ ] Nettoyage désignation : retirer le préfixe `µ ` et le suffixe ` - EPUISE -` (insensible à la casse) avant stockage — **la mention EPUISE n'est jamais perdue**, voir le point dédié ci-dessous
- [ ] Dédoublonnage EAN : passe préalable sur tout le fichier construisant une map EAN → ligne à conserver. En cas de collision, garder la fiche dont la désignation ne contient pas "EPUISE" ; sinon garder la première rencontrée. Les lignes sans EAN ne sont pas concernées.
- [ ] **Fiches EPUISE sans doublon (arbitrage)** : la ligne est importée normalement (pas ignorée), mais avant nettoyage de la désignation, détecter la mention EPUISE. **À chaque import, pour chaque article traité** (pas seulement les nouveaux) : si la désignation brute contient EPUISE, écrire/maintenir l'attribut `article_attributes` (`key = supplier_status`, `value = discontinued`) ; **sinon, supprimer explicitement l'attribut `supplier_status` s'il existait** — sans quoi un article qui redevient actif dans un export ultérieur (mention EPUISE retirée par le fournisseur) resterait marqué `discontinued` indéfiniment. Un article n'ayant jamais eu la mention n'a jamais d'attribut `supplier_status` (l'état actif est représenté par l'absence d'attribut, pas par une valeur `active` explicite)
- [ ] **Prix à zéro (arbitrage)** : aucun traitement spécial — `purchase_price_ht`/`sale_price_ttc` sont importés tels quels, y compris à 0. Le rapport final (voir plus bas) compte et affiche ces lignes pour permettre une correction humaine ultérieure, sans jamais bloquer ni ignorer la ligne

## Performance — caches en mémoire, pas de `firstOrCreate` par ligne

Appeler `Brand::firstOrCreate()`/`ArticleCategory::firstOrCreate()`/`ArticleSubcategory::firstOrCreate()` à chaque ligne produirait potentiellement jusqu'à 3 requêtes SELECT (+ INSERT occasionnels) par ligne, soit un ordre de grandeur de 45 000 requêtes inutiles sur ~15 000 lignes — la grande majorité des lignes référencent une marque/catégorie déjà résolue par une ligne précédente dans le même import. Précharger et mettre en cache en mémoire avant la boucle principale :

- [ ] Avant la boucle d'import : charger `Brand::pluck('id', 'name')`, `ArticleCategory::pluck('id', 'name')`, `ArticleSubcategory::query()->get(['id', 'article_category_id', 'name'])` (indexé par `"{$categoryId}:{$name}"`) dans des tableaux PHP en mémoire
- [ ] `resolveBrand(string $raw): ?int` : normalise (`trim`), cherche dans le cache local ; absent → `Brand::create()` puis ajoute au cache local (pas de nouvelle lecture DB) ; vide → `null` sans toucher au cache
- [ ] `resolveCategory(string $raw): int` / `resolveSubcategory(int $categoryId, string $raw): int` : même principe, cache local mis à jour à chaque création
- [ ] Ces caches vivent le temps d'une exécution de la commande (propriétés de la classe, pas de cache Laravel partagé — la commande tourne une fois puis se termine, pas besoin de persistance entre exécutions)

## Comptage précis créés / mis à jour

`Article::updateOrCreate()` seul ne permet pas de distinguer fiablement une création d'une mise à jour dans les compteurs du rapport — utiliser la propriété `wasRecentlyCreated` du modèle immédiatement après l'appel (`$article->wasRecentlyCreated ? $created++ : $updated++`) plutôt que de déduire l'un des deux par soustraction ou supposition.

## Tâches restantes

- [ ] Upsert par référence préfixée : `Article::updateOrCreate(['reference' => 'CGN-'.$referenceCgn], [...])`, par chunks de ~500 dans une transaction (`DB::transaction()` par chunk)
- [ ] Champs toujours écrasés à l'upsert : `purchase_price_ht`, `sale_price_ttc`, `barcode`, `image_url`, `weight_kg`, `designation` (nettoyée), `brand_id` (uniquement si la marque brute n'est pas vide — voir point dédié), `article_subcategory_id`
- [ ] Champs renseignés uniquement à la création : `tva_rate` (défaut 20.00), `unit` (défaut 'pièce'). `supplier_id` jamais renseigné par l'import.
- [ ] **Marque vide (21 lignes du CSV réel)** : ne jamais appeler `Brand::firstOrCreate()`/`resolveBrand()` avec une chaîne vide/blanche après `trim()` — dans ce cas, laisser `brand_id = null` sur l'article plutôt que de créer une marque fantôme
- [ ] Après upsert de l'article, appeler l'`AttributeExtractorRegistry` (ticket 02) avec la sous-catégorie brute et la désignation nettoyée : si un extracteur correspond, **supprimer d'abord** les `article_attributes` existants dont la `key` fait partie de `$extractor->possibleKeys()` pour cet article, **puis insérer** les attributs retournés par `extract()` — jamais un simple upsert additif (voir ticket 02, stratégie de ré-extraction), sinon une clé devenue obsolète resterait en base indéfiniment. Ne rien faire si aucun extracteur ne couvre cette sous-catégorie
- [ ] Barre de progression (`progressStart`/`progressAdvance`/`progressFinish`) vu le volume (~15000 lignes)
- [ ] Rapport final : lignes lues, doublons EAN ignorés, articles créés (via `wasRecentlyCreated`), articles mis à jour, marques créées à la volée, sous-catégories créées à la volée, lignes sans EAN, lignes sans marque (comptées, sans créer de `Brand`), lignes avec `purchase_price_ht = 0`, lignes avec `sale_price_ttc = 0`, fiches `supplier_status = discontinued` détectées, attributs structurés extraits (compte par `key`), taux de couverture ETRTO réel pour pneus/chambres (voir `../01-modele-donnees.md`, ne pas se fier aux pourcentages figés dans la doc)

## Idempotence — synchronisation, pas suppression

L'upsert par référence `CGN-*` empêche les doublons lors d'un import répété du même fichier. Il ne supprime en revanche jamais un `Article` dont la référence CGN aurait disparu d'un export ultérieur (article retiré du catalogue fournisseur sans passer par le marqueur EPUISE, ou fichier partiel). Comportement assumé explicitement : **l'import synchronise les fiches présentes dans le fichier fourni, il ne supprime ni ne désactive jamais les fiches absentes**. Une désactivation éventuelle resterait une action manuelle (ou une évolution future basée sur une comparaison explicite des références vues/non vues).

## Critères d'acceptation

- La commande tourne sans erreur sur le fichier réel `StockNouveautesCgn022252.csv`
- Un article importé deux fois de suite (fichier identique) ne crée pas de doublon (upsert par référence) et n'incrémente pas le compteur "créés" au second passage
- L'EAN dupliqué connu (fiche normale + fiche "épuisée" du même EAN) ne produit qu'un seul `Article`
- Les accents des désignations sont correctement affichés en UTF-8 après import (pas de caractères `�`)
- Un pneu importé (sous-catégorie `PNEUS VELO`) obtient ses `article_attributes` (`practice_type`, `size_inches`, `etrto_size`) ; un article d'une sous-catégorie non couverte (ex. `OUTILLAGE`) n'obtient aucun attribut, sans erreur
- Le nombre de requêtes SQL exécutées pour la résolution marque/catégorie/sous-catégorie reste borné (proche du nombre de marques/catégories distinctes du fichier, pas du nombre de lignes) — vérifiable via `DB::enableQueryLog()` dans un test dédié
