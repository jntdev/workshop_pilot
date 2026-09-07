# Ticket 02 : Attributs structurés du catalogue (`article_attributes`)

**Type** : Backend / Data
**Priorité** : Haute
**Estimation** : 3h

**Dépend de** : Ticket 01

## Description

Table générique clé/valeur pour stocker des attributs filtrables extraits de la désignation fournisseur, plus les extracteurs dédiés aux familles de produits dont la grammaire a été vérifiée fiable (voir `../01-modele-donnees.md`). Chaque extracteur est isolé, testable indépendamment, et ne s'applique qu'à sa sous-catégorie.

## Tâches

- [ ] Migration `create_article_attributes_table` : `id`, `article_id` (FK cascade delete), `key` (string 50), `value` (string 255), timestamps ; index unique `(article_id, key)`
- [ ] Modèle `ArticleAttribute` (`$fillable`, `belongsTo(Article)`)
- [ ] Relation `Article::attributes(): HasMany` + méthode `Article::attributeValue(string $key): ?string` (jamais `getAttribute` — collision directe avec `Illuminate\Database\Eloquent\Model::getAttribute()`, qui casserait le comportement natif du modèle si redéfini avec une signature différente)
- [ ] Interface `app/Services/Catalogue/AttributeExtractor.php` : contrat commun `supports(string $rawSubcategory, string $designation): bool` + `extract(string $designation): array` (retourne un tableau `[key => value]`, uniquement les clés reconnues, jamais de valeur devinée). `supports()` prend aussi la désignation (pas seulement la sous-catégorie) car certaines sous-catégories CGN mélangent plusieurs familles distinguables uniquement par le préfixe de la désignation (voir `ROUE-LIBRES/CASSETTES` ci-dessous) — le registre doit tester les extracteurs dans l'ordre et prendre le premier qui `supports()`
- [ ] Extracteur `TireAttributeExtractor` (sous-catégorie `PNEUS VELO`) : `practice_type` (2e mot), `size_inches` (3e mot), `etrto_size` — regex `/(\d+)-(\d+)/` cherchée **sans ancrage de fin de chaîne** (`preg_match`, pas `preg_match` avec `$`), prend le premier match trouvé dans toute la désignation. Couverture vérifiée sur le CSV réel : 1242/1306 (95%) ; échecs résiduels = désignations tronquées par le fournisseur à ~80 caractères (parenthèse jamais fermée) ou désignations sans aucune notation ETRTO — cas légitimement non extractibles, pas un défaut de regex
- [ ] Extracteur `InnerTubeAttributeExtractor` (sous-catégorie `CHAMBRES VELO`) : `size_inches`, `etrto_size` (même regex non ancrée que les pneus ; la notation dominante ici est une plage `n/n-n` type `44/62-507` — la regex simple capture uniquement la borne utile `62-507`, décision assumée : on ne cherche pas à capturer le `44/` initial en v1), `valve_type` (VS/VP). Couverture vérifiée : 334/339 (98.5%)
- [ ] Extracteur `ChainringAttributeExtractor` (sous-catégorie `PLATEAUX`) : `practice_type`, `chainring_diameter_mm` (après `DIAM`), `tooth_count` (nombre avant `DTS`), `speed_count` (nombre avant `V.` ou `V` en fin de chaîne)
- [ ] Extracteur `CranksetAttributeExtractor` (sous-catégorie `PEDALIERS`) : `practice_type`, `tooth_range` (dents avant `D`, ex. `52-42-30` ou `44`), `crank_length_mm` (après `L`), `speed_count`
- [ ] Extracteur `CrankArmAttributeExtractor` (sous-catégorie `MANIVELLES`) : `side` (GAUCHE/DROITE/PAIRE), `practice_type`, `crank_length_mm`
- [ ] `ROUE-LIBRES/CASSETTES` mélange trois familles à grammaires distinctes (comme `JEUX DE PEDALIERS`, mais ici chacune est assez régulière pour être couverte) — un extracteur par préfixe de désignation, pas par sous-catégorie seule :
  - `CassetteAttributeExtractor` (préfixe `CASSETTE`, inclut `CASSETTE ET CHAINE`) : `speed_count` (nombre avant `V.`), `practice_type` (mot après le nombre de vitesses), `tooth_range` (après le tiret, ex. `11-28`)
  - `FreewheelAttributeExtractor` (préfixe `ROUE LIBRE`) : `speed_count` + `tooth_range` si forme multi-vitesses (`[n] V. ... [n]-[n]DTS`), sinon `tooth_count` seul si forme mono-vitesse (`[n] DTS MONOVITESSE`)
  - `CassetteBodyAttributeExtractor` (préfixe `CORPS CASSETTE` ou `CORPS DE CASSETTE`) : `speed_compat` (plage de vitesses compatibles, ex. `8/9/10`), `axle_type` (`QR` ou `TRAVERSANT`)
- [ ] Extracteur `LightAttributeExtractor` (sous-catégorie `ECLAIRAGE`, **`supports()` doit vérifier le préfixe exact `ECLAIRAGE VELO` ou `ECLAIRAGE FRONTAL` de la désignation, pas seulement la sous-catégorie** — sur 238 lignes classées `ECLAIRAGE`, 83 sont en fait des accessoires connexes mal classés : supports, autocollants, chargeurs, piles, câbles... qui ne commencent pas par `ECLAIRAGE`. Un `supports()` basé sur la seule sous-catégorie produirait 35% de faux positifs) : `position` (AV/AR/AV+AR/AV+PROJECTEUR), `power_source` (DYNAMO/PILE/RECHARG. → normalisé en `dynamo`/`pile`/`rechargeable`). Couverture vérifiée sur les 155 lignes réellement préfixées `ECLAIRAGE` : 154/155 (99%)
- [ ] Chaque extracteur retourne un tableau vide (aucune clé) si la désignation ne correspond pas au patron attendu — jamais d'exception, jamais de valeur approximative
- [ ] Registre `AttributeExtractorRegistry` (ou simple tableau de mapping sous-catégorie → extracteur) utilisé par la commande d'import (ticket 04)
- [ ] **Stratégie de ré-extraction (arbitrage)** : à chaque import, pour un article donné dont un extracteur `supports()`, remplacer l'intégralité des attributs produits par cet extracteur (`delete` des clés qu'il gère puis recréation), pas un simple upsert additif — sinon une clé devenue obsolète après évolution d'un extracteur (ex. un extracteur corrigé qui ne retourne plus `chainring_count` par erreur) resterait en base indéfiniment. Concrètement : chaque extracteur expose la liste fixe des clés qu'il est susceptible de produire (`possibleKeys(): array`), l'import supprime ces clés précises pour l'article avant d'insérer le résultat frais de `extract()`

## Tests

`tests/Unit/Services/Catalogue/` — un fichier de test par extracteur, sur des désignations réelles extraites du CSV (pas des exemples inventés) :
- [ ] `TireAttributeExtractorTest` : au moins un cas nominal (`PNEU ROUTE 700X28C TR MICHELIN DYNAMIC CLASSIC TT NOIR/BEIGE (28-622)` → `practice_type=ROUTE`, `size_inches=700X28C`, `etrto_size=28-622`), un cas de désignation tronquée sans parenthèse fermée (doit retourner `etrto_size` absent, pas d'exception)
- [ ] `ChainringAttributeExtractorTest`, `CranksetAttributeExtractorTest`, `CrankArmAttributeExtractorTest` : au moins un cas nominal chacun, plus un cas de désignation hors patron (ex. `INTRAVIS...` classée par erreur dans `PLATEAUX`) qui doit retourner un tableau vide sans erreur
- [ ] `CassetteAttributeExtractorTest` : cas nominal (`CASSETTE 10V. VTT SHIMANO DEORE CS-M4100 - 11-42DTS`), cas sans plage de dents (`CASSETTE 9V. ROUTE MICHE PRIMATO ADAPT. CAMPA` → `tooth_range` absent, `speed_count`/`practice_type` présents), cas `CASSETTE ET CHAINE`
- [ ] `FreewheelAttributeExtractorTest` : cas multi-vitesses (`ROUE LIBRE 7 V. SHIMANO TZ500 14-28DTS`), cas mono-vitesse (`ROUE LIBRE 18 DTS MONOVITESSE BRONZE` → `tooth_count=18`, pas de `tooth_range`), vérifier qu'il ne capture pas les lignes `CASSETTE`/`CORPS CASSETTE` de la même sous-catégorie
- [ ] `CassetteBodyAttributeExtractorTest` : cas nominal (`CORPS CASSETTE SHIMANO XT M770/775/776 9/10V POUR AXE QR` → `speed_compat=9/10`, `axle_type=QR`), variante `CORPS DE CASSETTE`, cas `AXE TRAVERSANT`
- [ ] `LightAttributeExtractorTest`, `InnerTubeAttributeExtractorTest` : cas nominal + cas limite

## Critères d'acceptation

- Chaque extracteur ne s'applique qu'à son périmètre déclaré (`supports()` — sous-catégorie, et préfixe exact de désignation quand une sous-catégorie mélange plusieurs familles ou contient des accessoires connexes mal classés, comme `ROUE-LIBRES/CASSETTES` et `ECLAIRAGE`)
- `extract()` retourne uniquement les clés reconnues sous forme de tableau associatif (`['practice_type' => 'ROUTE', ...]`) — jamais de clé avec une valeur `null`. Une désignation hors patron ne fait jamais échouer l'extraction : tableau vide (`[]`), jamais d'exception
- La désignation brute de l'article n'est jamais modifiée par l'extraction (uniquement lue)
- Les extracteurs sont rejouables indépendamment de l'import (utile pour ré-extraire sur des articles déjà en base sans re-parser le CSV)
- Rejouer l'extraction sur un article dont un extracteur produit désormais moins de clés qu'auparavant supprime bien les clés devenues obsolètes (pas d'accumulation silencieuse d'attributs périmés)
- Couverture vérifiée sur le CSV réel (`StockNouveautesCgn022252.csv`) au moment de l'implémentation : pneus 1242/1306, chambres 334/339, éclairage 154/155 (sur les lignes réellement préfixées `ECLAIRAGE`). Ces chiffres évoluent à chaque nouvel export fournisseur ; le rapport de la commande d'import (ticket 04) doit recalculer et afficher la couverture réelle à chaque exécution plutôt que de se fier à ces chiffres figés
