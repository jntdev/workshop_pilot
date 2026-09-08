# Feature 28 : Synchronisation manuelle du catalogue CGN depuis le FTP fournisseur

## Contexte

Le catalogue CGN est aujourd'hui importé depuis un fichier CSV (`StockNouveautesCgn022252.csv`) commis dans le repo et récupéré manuellement — une opération ponctuelle, pas un flux répétable. Le fichier réel doit être récupéré chaque jour sur un FTP privé fournisseur (accès déjà détenus par l'utilisateur), pour refléter les mises à jour de stock/prix/nouveautés côté CGN.

## Décision d'architecture (suite à discussion)

Trois pistes ont été envisagées et écartées avant de converger sur la solution retenue :
1. **Scheduler Laravel + crontab système** — écarté : le projet tourne sur un serveur mutualisé dont l'accès à un vrai crontab (fichier crontab ou panneau hébergeur exécutant une commande shell) n'est pas confirmé.
2. **Route HTTP déclenchée par un service de cron externe** (type cron-job.org) avec jeton secret — solution de repli technique valable si besoin futur d'automatisation, mais non retenue pour cette itération.
3. **Job en file d'attente + polling de statut** — écarté pour l'instant : ajoute de la complexité (nécessite une queue Laravel active en continu sur le mutualisé, à vérifier) pour un gain non demandé.

**Solution retenue : déclenchement manuel, bouton unique dans la page Stock.** L'utilisateur clique, la requête reste ouverte le temps de la connexion FTP + téléchargement + réimport (bouton bloquant avec spinner), puis affiche un résultat de succès (résumé chiffré) ou d'échec (message clair). Pas de scheduler, pas de crontab, pas de Job asynchrone, pas de jeton — la manipulation la plus simple qui couvre le besoin réel exprimé ("faisons un simple bouton import"). Une automatisation périodique reste possible plus tard sans réécrire cette base (le Job d'automatisation appellerait le même service).

## Parcours utilisateur

1. Bouton "Importer le catalogue CGN" dans la page Stock (`/stock`), à côté des boutons existants ("+ Nouvel article", "📷 Scanner").
2. Clic → bouton désactivé + spinner, requête HTTP synchrone vers le backend.
3. Backend : connexion FTP avec les identifiants configurés, téléchargement du fichier distant, écrasement du fichier local, exécution de `catalogue:import-cgn` sur ce fichier fraîchement téléchargé.
4. Résultat affiché sans rechargement de page :
   - **Succès** : résumé chiffré (nombre d'articles créés/mis à jour, tel que déjà produit par `ImportCgnCatalogue`).
   - **Échec** : message clair selon la cause (connexion FTP impossible, fichier distant introuvable, erreur d'import) — jamais une erreur 500 brute ni un écran figé.

## Hors périmètre (v1)

- Toute automatisation périodique (crontab, scheduler, service de cron externe) — décision explicite de rester au déclenchement manuel pour cette itération, voir "Décision d'architecture".
- Historique des imports (dates, résultats passés) persisté en base — seul le résultat du dernier import déclenché est affiché, à la volée, non stocké.
- Notification (email, etc.) en cas d'échec — l'utilisateur est présent au clavier puisque le déclenchement est manuel, un message d'erreur à l'écran suffit.
- Modification de `ImportCgnCatalogue` elle-même — la commande existante est réutilisée telle quelle, appelée programmatiquement (`Artisan::call`), aucune de ses règles d'import (dédoublonnage EAN, extracteurs d'attributs, gestion Supplier CGN) n'est retouchée.
