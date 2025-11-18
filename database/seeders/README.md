# Database Seeders

Ce dossier contient les seeders pour peupler la base de données avec des données de test.

## Seeders disponibles

### DatabaseSeeder (principal)
Seeder principal qui appelle tous les autres seeders dans l'ordre approprié.

### ClientSeeder
Crée 12 clients dont :
- **5 clients nommés** avec des données réalistes (Paris, Lyon, Bordeaux, Nantes, Toulouse)
- **7 clients aléatoires** générés via la factory

**Clients nommés :**
- Marie Dubois (Paris) - Avantage 10%
- Jean Martin (Lyon) - Avantage 25€
- Sophie Bernard (Bordeaux) - Sans avantage
- Thomas Petit (Nantes) - Avantage 15%
- Claire Robert (Toulouse) - Sans avantage

### QuoteSeeder
Crée 5 devis réalistes avec leurs lignes :

1. **DEV-202511-0001** - Marie Dubois (draft, remise 10%)
   - Réparation frein avant (70€ HT)
   - Changement chaîne (45€ HT)
   - Réglage dérailleur (17€ HT)
   - **Total TTC : 194.40€**

2. **DEV-202511-0002** - Jean Martin (validated, sans remise)
   - Révision complète (40€ HT)
   - Graissage câbles (15€ HT)
   - Contrôle freins (30€ HT)
   - **Total TTC : 102.00€**

3. **DEV-202511-0003** - Sophie Bernard (draft, remise 15€)
   - Pneu avant 700x28 (50€ HT)
   - Pneu arrière 700x28 (50€ HT)
   - Pose pneus (20€ HT)
   - **Total TTC : 126.00€**

4. **DEV-202511-0004** - Thomas Petit (draft, sans remise)
   - Centrage roue arrière (30€ HT)
   - Remplacement rayons (35€ HT)
   - **Total TTC : 78.00€**

5. **DEV-202511-0005** - Claire Robert (validated, remise 5%)
   - Éclairage LED avant (45€ HT)
   - Éclairage LED arrière (38€ HT)
   - Garde-boue avant/arrière (35€ HT)
   - Pose accessoires (22€ HT)
   - **Total TTC : 159.60€**

## Utilisation

### Peupler toute la base de données
```bash
php artisan db:seed
```

### Peupler uniquement les clients
```bash
php artisan db:seed --class=ClientSeeder
```

### Peupler uniquement les devis
```bash
php artisan db:seed --class=QuoteSeeder
```

### Réinitialiser et peupler (⚠️ ATTENTION: supprime toutes les données)
```bash
php artisan migrate:fresh --seed
```

## Notes importantes

⚠️ **Base de test isolée** : Les seeders s'exécutent sur la base de données définie dans `.env`. Pour les tests automatisés, une base SQLite isolée est utilisée (voir `process/01-isolated-testing.md`).

⚠️ **QuoteSeeder dépendance** : Le QuoteSeeder nécessite que des clients existent déjà. Toujours exécuter ClientSeeder avant QuoteSeeder (ou utiliser `db:seed` qui respecte cet ordre).

## Vérification des données

```bash
# Compter les enregistrements
mysql -u root -ppassword123 -e "
  SELECT 'Clients' as table_name, COUNT(*) as count FROM clients
  UNION SELECT 'Devis', COUNT(*) FROM quotes
  UNION SELECT 'Lignes devis', COUNT(*) FROM quote_lines;
" workshop_pilot

# Voir les devis créés
mysql -u root -ppassword123 -e "
  SELECT q.reference, CONCAT(c.prenom, ' ', c.nom) as client,
         q.status, q.total_ttc
  FROM quotes q
  JOIN clients c ON q.client_id = c.id
  ORDER BY q.reference;
" workshop_pilot
```
