# Tickets — Feature 26 : Catalogue par sous-catégorie avec filtres dynamiques

Contexte, modèle de données et arbitrages produit : voir `../00-contexte.md`, `../01-modele-donnees.md`, `../04-arbitrages.md`.

## Ordre d'exécution

| # | Ticket | Dépend de |
|---|--------|-----------|
| 01 | [Migration d'index sur `article_attributes.key`](01-index-article-attributes.md) | — |
| 02 | [Endpoint `filterOptions()` par sous-catégorie](02-endpoint-filter-options.md) | 01 |
| 03 | [Extension du filtrage `ArticleController::index()`](03-extension-index-filtrage.md) | — |
| 04 | [Refonte `CataloguePickerModal.tsx`](04-refonte-catalogue-picker-modal.md) | 02, 03 |
| 05 | [Tests backend](05-tests-backend.md) | 01 à 03 |

Les tickets 01 et 03 peuvent être menés en parallèle (aucune dépendance entre eux). Le ticket 04 ne peut démarrer qu'une fois les deux endpoints backend disponibles.
