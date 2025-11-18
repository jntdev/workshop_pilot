# Processus 02 – Règles STRICTES pour les tests

## ⚠️ RÈGLES ABSOLUES - NE JAMAIS ENFREINDRE

### 1. AVANT de lancer TOUT test avec `RefreshDatabase`

**OBLIGATOIRE** : Vérifier que les tests utilisent SQLite :

```bash
# Vérifier la configuration de test
grep "DB_CONNECTION" phpunit.xml
# DOIT afficher : <env name="DB_CONNECTION" value="sqlite"/>

# Vérifier que le fichier .env.testing existe
ls -la .env.testing
# DOIT exister

# Vérifier la base de dev (NE DOIT PAS être touchée)
mysql -u root -ppassword123 -e "SELECT 'OK - Base dev protégée'" workshop_pilot 2>/dev/null
```

### 2. Commandes de test AUTORISÉES

✅ **TOUJOURS utiliser :**
```bash
php artisan test                    # Utilise automatiquement .env.testing
php artisan test --filter=NomTest   # Idem, pour un test spécifique
vendor/bin/phpunit                  # Idem, utilise phpunit.xml
```

❌ **NE JAMAIS utiliser :**
```bash
php artisan migrate:fresh                    # Vide la base DEV !
php artisan migrate:fresh --seed             # Vide la base DEV !
php artisan db:wipe                          # Vide la base DEV !
php artisan migrate:reset                    # Vide la base DEV !
```

### 3. Pour restaurer les données de seed

✅ **UNIQUEMENT cette commande :**
```bash
php artisan db:seed
```

Si la base a été vidée accidentellement :
```bash
php artisan migrate          # Recréer les tables si nécessaire
php artisan db:seed         # Repeupler avec les seeders
```

### 4. Vérification post-test OBLIGATOIRE

Après CHAQUE exécution de tests, **TOUJOURS vérifier** :

```bash
# Compter les enregistrements dans la base DEV
mysql -u root -ppassword123 -e "
  SELECT 'Clients' as table_name, COUNT(*) as count FROM clients
  UNION SELECT 'Devis', COUNT(*) FROM quotes;
" workshop_pilot 2>/dev/null

# ATTENDU : au moins 12 clients et 5 devis
# SI 0 : la base dev a été vidée → ERREUR CRITIQUE
```

### 5. En cas de doute : NE PAS LANCER DE TESTS

Si je ne suis pas sûr à 100% que les tests utiliseront SQLite, je DOIS :

1. **D'abord** vérifier la configuration (règle 1)
2. **Ensuite** lancer UN SEUL test simple
3. **Immédiatement** vérifier que la base dev n'a pas été touchée (règle 4)
4. **Seulement alors** lancer la suite complète

## 🚨 Protocole d'urgence si base vidée

Si malgré tout la base dev est vidée :

```bash
# 1. Avertir l'utilisateur IMMÉDIATEMENT
echo "ERREUR : Base de développement vidée !"

# 2. Restaurer les données
php artisan db:seed

# 3. Vérifier la restauration
mysql -u root -ppassword123 -e "SELECT COUNT(*) FROM clients" workshop_pilot
```

## 📋 Checklist avant tests (OBLIGATOIRE)

- [ ] `phpunit.xml` contient `DB_CONNECTION=sqlite` ?
- [ ] `.env.testing` existe et contient `DB_CONNECTION=sqlite` ?
- [ ] La commande ne contient PAS `migrate:fresh` ?
- [ ] La commande ne contient PAS `db:wipe` ?
- [ ] Après test : base dev toujours peuplée ?

## Responsabilité

**Je (Claude) suis responsable de :**
- Vérifier la configuration AVANT chaque test
- Ne JAMAIS lancer de commande destructive sur la base dev
- Restaurer immédiatement si erreur

**L'utilisateur ne devrait JAMAIS avoir à :**
- Perdre ses données à cause de mes tests
- Me rappeler ces règles
- Restaurer manuellement sa base
