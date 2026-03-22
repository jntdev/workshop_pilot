# 05 - Checklist d'exécution

## Préparation
- [ ] Créer la branche `feature/09-migration-react` depuis `develop`.
- [ ] Relire `features/README.md` + dossiers `01`, `02`, `06` pour garder cohérence UI/UX.
- [ ] Lancer la suite de tests actuelle (`php artisan test`) pour disposer d'une base saine.

## Frontend
- [ ] Implémenter les pages React pour la création/édition des clients avec toutes les règles métier (origine_contact, avantages, suppression).
- [ ] Mettre à jour `Clients/Index` pour pointer vers les nouvelles routes React.
- [ ] Ajouter l'affichage des erreurs de validation et des messages succès/échec dans le formulaire de devis.
- [ ] Revoir le Feedback Banner : soit porté côté React, soit bundle `app.js` tant que Livewire vit.

## Backend & API
- [ ] Déplacer/dupliquer les routes API nécessaires sous middleware authentifié (`auth`, `verified`, ou Sanctum).
- [ ] Unifier les réponses JSON (wrappers `data`/`errors`) et ajuster les tests existants (`ClientApiTest`, etc.).
- [ ] Ajouter des tests Feature pour les nouveaux endpoints React (création client via API, sauvegarde devis, recherche clients).

## Livewire retirement
- [ ] Supprimer les vues Blade `resources/views/clients/*.blade.php` une fois les équivalents React livrés.
- [ ] Nettoyer les composants Livewire inutilisés (Clients, Atelier) et mettre à jour la doc.
- [ ] Vérifier Vite : retirer `resources/js/app.js` uniquement quand les composants associés disparaissent.

## Qualité & livraison
- [ ] Ajouter des tests front automatisés si nécessaire (RTL) pour vérifier routing/validation.
- [ ] Mettre à jour `features/README.md` + changelog avec les points clé de la migration.
- [ ] QA manuelle : création de devis avec nouveau client, erreur validation, affichage factures, navigation complète.
- [ ] Exécuter `php artisan test` + éventuels tests front avant toute livraison.

## Clôture QA & notation (obligatoire)
- [ ] Relire intégralement les fichiers du dossier `features/09-migration-react`.
- [ ] Cocher chaque étape ci-dessus, calculer la note = étapes validées / étapes totales (objectif 100 %).
- [ ] Ne merge que si tous les tests exigés sont verts et si la note est 100 %.
