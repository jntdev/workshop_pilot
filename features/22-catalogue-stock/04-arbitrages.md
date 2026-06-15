# Arbitrages produit — Feature 22

## Arbitrage 1 : Stock = mouvements, pas un champ

Le stock d'un article n'est jamais un champ `quantity` mis à jour. C'est toujours `SUM(stock_movements.quantity)`. Raison : l'historique est la source de vérité, et l'API fournisseur (future) pourra créer des mouvements sans refonte du modèle.

## Arbitrage 2 : article_id nullable sur QuoteLine et BikeMaintenanceLog

La liaison catalogue est optionnelle. La saisie libre reste possible. On n'oblige pas l'employé à trouver un article dans le catalogue pour créer un devis. Cela évite de bloquer le flux de travail si le catalogue n'est pas encore complet.

## Arbitrage 3 : Les prix restent modifiables après sélection catalogue

Le catalogue pré-remplit, il ne verrouille pas. Un employé peut ajuster un prix pour un cas particulier (remise commerciale, prix négocié ponctuel). Le prix catalogue est le point de départ normalisé, pas une règle absolue.

## Arbitrage 4 : Pas de fournisseur structuré en v1

Un champ texte libre `supplier` sur l'article suffit pour l'instant. La création d'un modèle `Supplier` est réservée au moment où l'API fournisseur sera implémentée.

## Arbitrage 5 : Suppression d'article non bloquante

La suppression d'un article passe `article_id` à null sur les QuoteLines et BikeMaintenanceLogs liés (via `onDelete('set null')`). Elle n'est pas bloquée. Les lignes de devis existantes conservent leurs données texte (référence, désignation, prix) même après suppression de l'article source.

## Arbitrage 6 : Mouvements automatiques — périmètre v1

En v1, les mouvements `quote_consumption` et `maintenance_consumption` ne sont **pas** créés automatiquement. Le stock est alimenté uniquement par des entrées manuelles (`manual_in`). La création automatique de mouvements à la finalisation d'un devis ou d'un log de maintenance est une v2, une fois le flux de travail stabilisé.

## Arbitrage 7 : Pas de stock négatif bloqué

Le système ne bloque pas si le stock passe en négatif. Un avertissement visuel (couleur rouge) est suffisant pour l'instant. Bloquer les devis sur rupture de stock serait prématuré avant que le catalogue soit complet et fiable.
