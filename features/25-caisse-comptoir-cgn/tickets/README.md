# Tickets — Feature 25 : Caisse comptoir + import catalogue fournisseur CGN

Contexte, modèle de données et arbitrages produit : voir `../00-contexte.md`, `../01-modele-donnees.md`, `../04-arbitrages.md`.

## Ordre d'exécution

| # | Ticket | Dépend de |
|---|--------|-----------|
| 01 | [Colonnes catalogue additionnelles sur Article](01-barcode-article.md) | — |
| 02 | [Attributs structurés du catalogue (`article_attributes`)](02-article-attributes.md) | 01 |
| 03 | [Quantité décimale sur `stock_movements`](03-stock-movements-decimal.md) | — |
| 04 | [Commande d'import catalogue CGN](04-import-cgn.md) | 01, 02 |
| 05 | [Modèles et migrations Sale / SaleLine](05-modeles-sale.md) | 03 |
| 06 | [API Sale — caisse](06-api-sale.md) | 05 |
| 07 | [Tests backend](07-tests-backend.md) | 01 à 06 |
| 08 | [Page Caisse](08-page-caisse.md) | 06 |
| 09 | [Ventes récentes et annulation](09-ventes-recentes-annulation.md) | 06, 08 |

Les tickets 01, 02 et 03 peuvent être menés en parallèle (aucune dépendance entre eux). Le ticket 03 touche du code de la feature 22 déjà en production — à traiter avec la même rigueur qu'une migration de production.
