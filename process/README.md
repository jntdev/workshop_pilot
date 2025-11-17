# Process - Procédures techniques

Ce dossier contient les procédures et configurations techniques pour le projet.

## Liste des process

### [01 - Base de données isolée pour les tests](01-isolated-testing.md)

**Statut** : ✅ Implémenté

Configuration d'une base de données SQLite isolée pour les tests afin de protéger les données de développement.

**Fichiers créés :**
- `.env.testing` - Configuration environnement de test
- `.env.testing.example` - Template pour nouveaux développeurs
- `database/database-testing.sqlite` - Base SQLite (ignorée par Git)

**Fichiers modifiés :**
- `phpunit.xml` - Configuration PHPUnit pour utiliser SQLite
- `.gitignore` - Ignore les fichiers de test
- `features/00-workflow.md` - Référence le processus

**Vérification :**
```bash
# Lancer les tests
php artisan test

# Vérifier que la base SQLite existe
ls -lh database/database-testing.sqlite

# Vérifier que la base MySQL dev n'a pas été touchée
mysql -u root -p workshop_pilot -e "SHOW TABLES"
```

**Commandes importantes :**
- ✅ `php artisan test` - Utilise automatiquement `.env.testing` et SQLite
- ❌ `php artisan migrate:fresh` - N'utiliser qu'avec `--env=testing`
- ❌ Modifier `APP_ENV` manuellement dans les scripts

### [02 - Règles STRICTES pour les tests](02-strict-testing-rules.md)

**Statut** : ✅ Implémenté - **CRITIQUE**

⚠️ **Document OBLIGATOIRE à consulter avant CHAQUE exécution de tests.**

Définit les règles absolues pour éviter de vider la base de développement lors des tests :

**Règles clés :**
- ✅ Vérifier `phpunit.xml` et `.env.testing` AVANT les tests
- ✅ Uniquement `php artisan test` (jamais `migrate:fresh`)
- ✅ Vérifier la base dev APRÈS chaque test
- ⚠️ En cas de doute : NE PAS lancer de tests

**Protocole d'urgence si base vidée :**
```bash
php artisan db:seed  # Restauration immédiate
```

## Workflow général

⚠️ **TOUS les process de ce dossier doivent être respectés**, particulièrement le **process 02** qui est CRITIQUE pour la protection des données.
