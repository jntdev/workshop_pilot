# Tickets — Feature 27 : Scan mobile pour créer le stock virtuel

Contexte, modèle de données et arbitrages produit : voir `../00-contexte.md`, `../01-modele-donnees.md`, `../04-arbitrages.md`.

## Ordre d'exécution

| # | Ticket | Dépend de |
|---|--------|-----------|
| 01 | [Route de lookup par code-barres](01-lookup-barcode.md) | — |
| 02 | [Upload photo article](02-upload-photo.md) | — |
| 03 | [Composant scanner caméra](03-composant-scanner.md) | — |
| 04 | [Composant de capture photo](04-composant-capture-photo.md) | — |
| 05 | [Écran Inventaire — scan + saisie quantité](05-ecran-inventaire.md) | 01, 03 |
| 06 | [Création rapide d'article inconnu](06-creation-rapide-article.md) | 02, 04, 05 |
| 07 | [Bouton d'accès depuis la page Stock](07-bouton-acces-stock.md) | 05 |
| 08 | [Tests backend](08-tests-backend.md) | 01, 02, 06 |

Les tickets 01, 02, 03 et 04 peuvent être menés en parallèle (aucune dépendance entre eux). Le ticket 03 (scan) et le ticket 04 (capture photo) sont volontairement des composants distincts, sans flux vidéo partagé — voir le ticket 03 (décision suite au feedback de préparation) et `../04-arbitrages.md`, arbitrage 13 (traduction des clés virtuelles, point connexe soulevé par le même feedback). Le ticket 05 assemble le scanner (03) et le lookup (01) en un écran fonctionnel pour les articles déjà connus ; le ticket 06 étend cet écran au cas code-barres inconnu, en s'appuyant sur la capture photo (04) et l'upload (02).
