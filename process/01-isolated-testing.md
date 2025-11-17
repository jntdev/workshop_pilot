# Processus 01 – Base de données isolée pour les tests

## Problème
`php artisan test` (ou les tests automatiques lancés par Claude) utilisent `RefreshDatabase`. Si les tests pointent vers la même base que l’environnement de travail (`.env`), toutes les tables sont migrées/tronquées et les données locales sont perdues.

## Objectif
Garantir que :
1. Les tests exécutent leurs migrations sur une base **séparée**.
2. Cette base peut être détruite/recréée sans impacter la base de développement.

## Stratégie
- **Production / Développement** : configuration classique dans `.env` (MySQL ou autre), jamais réutilisée pendant les tests automatisés.
- **Tests** : `.env.testing` dédié avec une base SQLite isolée (`database/database-testing.sqlite`). Chaque exécution de `php artisan test` s’appuie sur ce fichier, que Laravel recrée automatiquement lors de `RefreshDatabase`.
- **Script unique (vanilla)** : aucun Docker/Alpine – uniquement la configuration Laravel/tools existants.

## Étapes à mettre en place
1. **Créer `.env.testing`**  
   - Copier `.env` puis modifier les variables suivantes :
     ```
     APP_ENV=testing
     APP_DEBUG=true
     DB_CONNECTION=sqlite
     DB_DATABASE=database/database-testing.sqlite
     ```
   - (Optionnel) mettre des valeurs neutres pour les services externes (mail, queue…).
2. **Créer le fichier SQLite**  
   - `touch database/database-testing.sqlite` (ignorable dans Git si besoin).
3. **Adapter `phpunit.xml`**  
   - Supprimer les entrées `DB_*` ou les remplacer par les valeurs SQLite, pour laisser Laravel charger celles de `.env.testing`.
4. **Workflow tests**  
   - Toujours exécuter `php artisan test` (ou `phpunit`) **sans** modifier `APP_ENV`; Laravel utilisera automatiquement `.env.testing`.
   - Aucun `php artisan migrate:fresh` sans `--env=testing` n’est autorisé dans les scripts automatisés.
5. **Communication**  
   - Documenter ce process dans `features/00-workflow.md` (rappel : “Les tests tournent sur `.env.testing` uniquement”) et dans les instructions données à Claude.

## Vérification
- Lancer `php artisan test` doit générer/migrer `database-testing.sqlite`.  
- Vérifier que votre base `.env` (ex. `workshop_pilot`) ne contient plus de tables tronquées juste après les tests.  
- Ajouter un test simple (ex. création client) pour confirmer que les données n’apparaissent que dans SQLite.

Avec ce process, les données locales de travail ne sont jamais touchées par les tests automatisés.***
