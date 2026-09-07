# Ticket 03 : Extension du filtrage `ArticleController::index()`

**Type** : Backend / API
**Priorité** : Haute
**Estimation** : 2h

**Dépend de** : —

## Tâches

- [ ] Validation inline en tête de `index()` (cohérent avec le style actuel du contrôleur, pas de FormRequest pour ce `GET`) :
  ```php
  $request->validate([
      'attribute' => ['sometimes', 'array'],
      'attribute.*' => ['nullable', 'string', 'max:255'],
  ]);
  ```
- [ ] Pour chaque paire `(key, value)` de `$request->array('attribute')`, hors les 4 clés décomposées (voir plus bas), ajouter une condition **séparée** :
  ```php
  $query->whereHas('attributes', fn ($q) => $q->where('key', $key)->where('value', $value));
  ```
  Un `whereHas` par paire, jamais un seul groupé avec `whereIn` — nécessaire pour un AND logique correct entre attributs (chaque attribut est une ligne séparée dans `article_attributes`, un seul `whereHas` avec plusieurs conditions `orWhere` accepterait à tort des combinaisons portées par des lignes différentes du même article). Voir `../01-modele-donnees.md`.
- [ ] Cas des filtres décomposés `etrto_diameter_mm` / `etrto_width_mm` : si l'un ou l'autre est présent dans `attribute[...]`,
  - si les deux sont fournis : `whereHas('attributes', fn ($q) => $q->where('key', 'etrto_size')->where('value', "{$width}-{$diameter}"))`
  - si un seul est fourni : `whereHas('attributes', fn ($q) => $q->where('key', 'etrto_size')->where('value', 'like', "{$width}-%"))` (largeur seule) ou `->where('value', 'like', "%-{$diameter}")` (diamètre seul)
- [ ] Même logique pour `tooth_range_min` / `tooth_range_max` → clé réelle `tooth_range`, valeur composite `"{min}-{max}"`, avec `LIKE` équivalent si un seul des deux est fourni
- [ ] Retirer ces 4 clés virtuelles du traitement générique par paire (étape précédente) avant de les traiter spécifiquement — un `array_intersect_key` ou une simple liste d'exclusion en début de boucle suffit
- [ ] Ne pas modifier le comportement existant de `subcategory_id`, `brand_id`, `search` — ce ticket ajoute `attribute[...]` en complément, sans toucher aux filtres déjà en place

## Critères d'acceptation

- `GET /api/articles?subcategory_id=X&attribute[practice_type]=VTT` ne retourne que les articles ayant effectivement `practice_type=VTT` dans `article_attributes`
- `GET /api/articles?attribute[practice_type]=VTT&attribute[speed_count]=9` ne retourne que les articles matchant **les deux** conditions simultanément (pas l'union) — vérifier notamment qu'un article ayant `practice_type=VTT` sur une ligne et `speed_count=10` sur une autre (donc pas 9) est bien exclu
- `GET /api/articles?attribute[etrto_diameter_mm]=622&attribute[etrto_width_mm]=28` ne retourne que les articles avec `etrto_size=28-622` exact
- `GET /api/articles?attribute[etrto_diameter_mm]=622` seul (sans largeur) retourne tous les articles dont `etrto_size` se termine par `-622`
- `GET /api/articles?attribute[tooth_range_min]=11&attribute[tooth_range_max]=32` ne retourne que les articles avec `tooth_range=11-32` exact
- Un `attribute[...]` sur une clé sans correspondance retourne une liste vide, pas une erreur
- Les filtres existants (`subcategory_id`, `brand_id`, `search`) continuent de fonctionner seuls ou combinés à `attribute[...]`
