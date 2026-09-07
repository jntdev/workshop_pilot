# Ticket 04 : Refonte `CataloguePickerModal.tsx`

**Type** : Frontend
**Priorité** : Haute
**Estimation** : 3h30

**Dépend de** : Ticket 02, Ticket 03

## Tâches

- [ ] Type `ArticleFilterOptions` dans `resources/js/types/index.d.ts` :
  ```typescript
  export interface ArticleFilterOptions {
      attributes: Record<string, string[]>;
      brands: Brand[];
  }
  ```
- [ ] Dans `resources/js/Components/Stock/CataloguePickerModal.tsx`, remplacer l'état actuel :
  - Supprimer `categories`, `expandedIds`, `brands` (chargement global des marques) et `toggleExpand`
  - Ajouter :
    ```typescript
    const [subcategories, setSubcategories] = useState<ArticleSubcategory[]>([]);
    const [selectedSubcategoryId, setSelectedSubcategoryId] = useState<number | null>(null);
    const [filterOptions, setFilterOptions] = useState<ArticleFilterOptions | null>(null);
    const [selectedAttributes, setSelectedAttributes] = useState<Record<string, string>>({});
    const [selectedBrandId, setSelectedBrandId] = useState<number | null>(null);
    ```
- [ ] Effet de montage : `GET /api/article-categories` (endpoint existant, inchangé) → `setSubcategories(data.categories?.[0]?.subcategories ?? [])`. Ne plus appeler `GET /api/brands` au montage — la marque est désormais toujours contextualisée à une sous-catégorie (voir `../04-arbitrages.md`, arbitrage 2)
- [ ] Nouvel effet déclenché par `selectedSubcategoryId` : si non `null`, `GET /api/article-subcategories/{id}/filter-options` → `setFilterOptions(...)`. **Réinitialiser systématiquement `selectedAttributes` et `selectedBrandId`** à ce moment (avant ou en même temps que le nouveau `setFilterOptions`) — changer de sous-catégorie doit invalider les filtres précédents, sans quoi un filtre `practice_type=ROUTE` actif sur Pneus resterait actif en passant à Chambres, silencieusement sans résultat. Si `selectedSubcategoryId` devient `null`, réinitialiser aussi `filterOptions` à `null`
- [ ] `loadArticles` : ajouter la construction de `attribute[...]` dans les `URLSearchParams` à partir de `selectedAttributes` :
  ```typescript
  Object.entries(selectedAttributes).forEach(([key, value]) => {
      if (value) { params.set(`attribute[${key}]`, value); }
  });
  ```
  Le mode recherche texte (`q.length >= 3` → `/api/articles/search`) reste inchangé et prioritaire — ne pas y ajouter `attribute[...]` (cet endpoint ne le supporte pas, ticket 03 ne modifie que `index()`)
- [ ] Déclencher `loadArticles` sur changement de `selectedAttributes` en plus des dépendances actuelles (`selectedSubcategoryId`, `selectedBrandId`, `search`)
- [ ] Dictionnaire de libellés FR statique dans le composant, une entrée par clé de `FILTERABLE_KEYS` côté backend (ticket 02) + les 4 clés décomposées :
  ```typescript
  const ATTRIBUTE_LABELS: Record<string, string> = {
      practice_type: 'Pratique',
      valve_type: 'Type de valve',
      chainring_diameter_mm: 'Diamètre (mm)',
      tooth_count: 'Nombre de dents',
      speed_count: 'Nombre de vitesses',
      crank_length_mm: 'Longueur manivelle (mm)',
      side: 'Côté',
      speed_compat: 'Compatibilité vitesses',
      axle_type: 'Type d\'axe',
      position: 'Position',
      power_source: 'Alimentation',
      etrto_diameter_mm: 'Diamètre de roue (mm)',
      etrto_width_mm: 'Largeur (mm)',
      tooth_range_min: 'Petit pignon',
      tooth_range_max: 'Grand pignon',
  };
  ```
- [ ] JSX nav gauche : remplacer le bloc catégories/marques dépliables par une liste plate de boutons à partir de `subcategories`, sur le modèle des boutons `catalogue-picker__nav-sub` déjà existants — au clic, `setSelectedSubcategoryId(sub.id === selectedSubcategoryId ? null : sub.id)`
- [ ] JSX zone filtres, affichée seulement si `selectedSubcategoryId !== null` : un `<select>` par entrée de `filterOptions.attributes` (libellé via `ATTRIBUTE_LABELS[key] ?? key` en repli), + un `<select>` Marque à partir de `filterOptions.brands`. Si `filterOptions` est `null` (chargement en cours) ou `attributes` est vide, ne rendre que le select Marque (ou rien si `brands` est vide aussi)
- [ ] `etrto_diameter_mm`/`etrto_width_mm` affichés dans cet ordre précis (diamètre avant largeur, voir `../04-arbitrages.md`) — s'assurer que l'ordre de rendu des `<select>` respecte cet ordre même si l'itération sur `Object.entries(filterOptions.attributes)` ne le garantit pas nativement (trier explicitement les entrées avant rendu, ou coder l'ordre des clés en dur dans le JSX plutôt que dépendre de l'ordre des clés retourné par PHP)
- [ ] Ne pas restreindre dynamiquement `etrto_width_mm` selon le `etrto_diameter_mm` déjà sélectionné dans cette v1 — l'endpoint `filterOptions()` (ticket 02) retourne l'ensemble complet des largeurs, indépendamment d'un diamètre choisi ; cette restriction croisée est explicitement hors périmètre (voir `../00-contexte.md`, hors périmètre)
- [ ] Adapter `resources/scss/stock/_catalogue-picker.scss` si de nouvelles classes sont introduites pour la zone filtres (`catalogue-picker__filters`, `catalogue-picker__filter-select` ou équivalent) — vérifier les variables de couleur réelles dans `_colors.scss` avant toute nouvelle règle, ne pas inventer de variable

## Critères d'acceptation

- À l'ouverture de la modale, la nav gauche affiche directement les sous-catégories de "Pièces Cycles" — aucune mention de catégorie visible
- Cliquer sur "Pneus" affiche des `<select>` Diamètre de roue, Largeur, Pratique, Marque — jamais un `<select>` à 130+ options
- Choisir un diamètre puis une largeur puis valider filtre correctement la liste de résultats (vérifié en conditions réelles, pas seulement visuellement — comparer avec une requête `GET /api/articles` équivalente)
- Changer de sous-catégorie (ex. Pneus → Chambres) réinitialise visiblement tous les filtres précédemment sélectionnés
- Une sous-catégorie sans attribut structuré affiche uniquement un select Marque (ou rien si aucune marque), sans erreur console
- La recherche texte (≥ 3 caractères) continue de fonctionner comme avant, sans dépendre des filtres de sous-catégorie
- Aucune régression visuelle sur le reste de la modale (tableau de résultats, bouton Sélectionner, fermeture)
