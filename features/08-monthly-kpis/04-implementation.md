# 04 - Etapes d'implementation

## 1. Enum `Metier`
- Creer l'enum `App\Enums\Metier` avec les valeurs : `Atelier`, `Vente`, `Location`.
- Fichier : `app/Enums/Metier.php`.

## 2. Migration : ajout du champ `metier` sur `quotes`
- Ajouter la colonne `metier` (string, nullable pour l'instant).
- Valeur par defaut : `atelier` (pour les quotes existantes).
- Mettre a jour le modele `Quote` : ajouter `metier` dans `$fillable` et le cast vers l'enum.

## 3. Migration : table `monthly_kpis`
- Creer la table `monthly_kpis`.
- Colonnes :
  - `id` (pk)
  - `metier` (string, ex: `atelier`)
  - `year` (unsigned smallint)
  - `month` (unsigned tinyint)
  - `invoice_count` (unsigned int, default 0)
  - `revenue_ht` (decimal(12,2), default 0)
  - `margin_ht` (decimal(12,2), default 0)
  - timestamps
- Index unique : `(metier, year, month)`.
- Index additionnel : `(metier, year)` pour lister les annees rapidement.

## 4. Modele `MonthlyKpi`
- Fichier : `app/Models/MonthlyKpi.php`.
- Fillable : `metier`, `year`, `month`, `invoice_count`, `revenue_ht`, `margin_ht`.
- Cast `metier` vers l'enum `Metier`.

## 5. Service d'aggregation
- Fichier : `app/Services/Kpis/MonthlyKpiUpdater.php`.
- Methode `applyInvoice(Quote $quote)` :
  - Precondition : `$quote->invoiced_at` non null.
  - Deriver `year` et `month` depuis `invoiced_at`.
  - `updateOrCreate` la ligne pour `(metier, year, month)`.
  - Incrementer `invoice_count`, `revenue_ht`, `margin_ht`.
  - Utiliser une transaction pour garantir l'atomicite.

## 6. Hook sur conversion en facture
- Dans `Quote::convertToInvoice()` :
  - Apres la mise a jour du devis, appeler `MonthlyKpiUpdater::applyInvoice($this)`.
  - La methode `convertToInvoice()` leve une exception si deja facture => pas de double comptage.

## 7. Backfill initial
- Commande artisan : `kpis:rebuild-monthly --metier=atelier` (ou `--all`).
- Comportement :
  - Supprimer les lignes existantes du metier concerne.
  - Agreger en SQL : `metier`, `YEAR(invoiced_at)`, `MONTH(invoiced_at)`, `COUNT(*)`, `SUM(total_ht)`, `SUM(margin_total_ht)`.
  - Inserer une ligne par combinaison (metier, year, month).

## 8. Dashboard atelier
- Fichier : `app/Livewire/Atelier/Dashboard.php`.
- `getStatsForMonth()` lit dans `monthly_kpis` (metier = `atelier`).
- Si aucune ligne, retourner des zeros.
- `getAvailableYears()` se base sur `monthly_kpis`.
- `margin_rate` et panier moyen restent calcules a l'affichage.

## Champs existants sur Quote (confirmation)
- `total_ht` : existe (decimal 10,2)
- `margin_total_ht` : existe (decimal 10,2)
- `invoiced_at` : existe (datetime, nullable)
