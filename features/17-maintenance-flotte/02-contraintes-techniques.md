# Contraintes techniques

## Modèle de données

### Table `bike_maintenance_sheets` (fiches atelier vélo)
| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | PK |
| bike_id | bigint | FK → bikes.id (cascade) |
| description | varchar(255) | Titre/nature de l'intervention |
| performed_at | date | Date de réalisation |
| total_ht | decimal(10,2) | Total coût HT (calculé) |
| total_time_minutes | int | Temps total passé |
| notes | text nullable | Remarques libres |
| timestamps | | created_at, updated_at |

### Table `bike_maintenance_lines` (lignes de fiche)
| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | PK |
| maintenance_sheet_id | bigint | FK → bike_maintenance_sheets.id (cascade) |
| title | varchar(255) | Désignation (pièce ou main d'œuvre) |
| quantity | int | Quantité |
| unit_cost_ht | decimal(10,2) | Coût unitaire HT |
| line_total_ht | decimal(10,2) | quantity × unit_cost_ht |
| time_minutes | int nullable | Temps passé (si main d'œuvre) |
| position | int | Ordre d'affichage |
| timestamps | | created_at, updated_at |

## Relations Eloquent

```php
// Bike.php
public function maintenanceSheets(): HasMany

// BikeMaintenanceSheet.php
public function bike(): BelongsTo
public function lines(): HasMany

// BikeMaintenanceLine.php
public function sheet(): BelongsTo
```

## Calculs

- `total_ht` de la fiche = Σ `line_total_ht` des lignes
- `total_time_minutes` de la fiche = Σ `time_minutes` des lignes
- Coût maintenance mensuel = Σ `total_ht` des fiches du mois (basé sur `performed_at`)
- Marge Location = CA Location − Coût maintenance

## Impact KPIs

- La table `monthly_kpis` pour `metier = 'location'` utilisera :
  - `revenue_ht` = paiements + acomptes (inchangé)
  - `margin_ht` = revenue_ht − Σ maintenance du mois
- Le rebuild des KPIs Location devra intégrer ce calcul
