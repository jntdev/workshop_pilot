# Arbitrages produit — Feature 28

## Arbitrage 1 : Déclenchement manuel uniquement, pas d'automatisation

Voir `00-contexte.md`, section "Décision d'architecture" pour les trois pistes d'automatisation envisagées et écartées. Raison centrale : l'accès à un vrai crontab système sur le serveur mutualisé n'est pas confirmé, et le besoin exprimé ("faisons un simple bouton import") ne demande pas d'automatisation. Une automatisation ultérieure resterait un ajout, pas une réécriture, puisqu'elle appellerait le même `CgnFtpSyncService`.

## Arbitrage 2 : Écrasement du fichier local, pas d'archivage

Le fichier téléchargé écrase `StockNouveautesCgn022252.csv` à chaque synchronisation, sans conserver de copie des versions précédentes. Raison : aucun besoin exprimé de comparer les imports dans le temps ; le fichier committé dans le repo aujourd'hui était de toute façon un instantané ponctuel, pas une source de vérité versionnée. Le vrai historique de ce qui a changé reste dans les données elles-mêmes (`Article`, `ArticleAttribute`, timestamps `updated_at`), pas dans des copies de CSV.

## Arbitrage 3 : Bouton bloquant, pas de Job en file d'attente

Le temps d'exécution attendu (connexion FTP + téléchargement d'un fichier de quelques Mo + réimport d'environ 15 000 lignes) reste de l'ordre de quelques secondes à quelques dizaines de secondes — acceptable pour une requête HTTP synchrone avec spinner, sans complexifier l'architecture avec une queue Laravel dont la disponibilité en continu sur le mutualisé n'est pas garantie. Si l'expérience réelle montre un temps d'attente inconfortable, ce choix pourra être révisé sans changer le service sous-jacent (seul le contrôleur/frontend serait impacté).

## Arbitrage 4 : Aucune modification de `ImportCgnCatalogue`

La commande existante (feature 25) encapsule déjà toute la logique métier d'import (dédoublonnage EAN, extraction d'attributs, gestion du Supplier CGN, détection de collision de référence). La feature 28 se contente de lui fournir un fichier local à jour et de l'invoquer programmatiquement (`Artisan::call`) — aucune règle métier d'import n'est dupliquée ni modifiée. Le chemin par défaut de la commande (`StockNouveautesCgn022252.csv`) reste inchangé et continue de fonctionner pour un import manuel local, indépendamment de cette feature.

## Arbitrage 5 : Message d'erreur générique, pas de détail technique exposé

En cas d'échec (FTP injoignable, identifiants invalides, fichier distant absent), le message affiché à l'utilisateur reste descriptif au niveau produit ("Connexion au FTP impossible", "Fichier introuvable sur le serveur distant") sans exposer de détails d'infrastructure (adresse IP, chemin serveur, trace technique) dans l'interface — ces informations, si nécessaires au diagnostic, restent dans les logs applicatifs (`storage/logs/laravel.log`), jamais renvoyées telles quelles au navigateur.
