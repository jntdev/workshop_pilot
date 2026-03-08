# Plan KPI Location

## Objectifs
- Alimenter `monthly_kpis (metier = 'location')` pour fournir CA, marge brute (placeholder) et panier moyen.  
- Éviter de recalculer en boucle : les KPIs sont mis à jour à l’écriture (création/suppression de paiements, changement de statut).

## Pipeline
1. **Événement** : enregistrement ou suppression d’un `reservation_payment` (voir Feature 15).  
2. **Service** `LocationKpiUpdater` :  
   - Détermine la période (`$year = $paid_at->year`, `$month = $paid_at->month`).  
   - `MonthlyKpi::firstOrCreate(['metier' => 'location', 'year' => $year, 'month' => $month])`.  
   - Incrémente `revenue_ht` (ou TTC) du montant du paiement.  
   - Si c’est le premier paiement de la réservation pour ce mois, incrémenter `invoice_count`.  
   - Recalcule `margin_ht` (pour l’instant = 0) et `average_basket = revenue / invoice_count` (champ calculé côté front ou stocké si besoin).
3. **Job de recalcul** (optionnel) : commande artisan “location:kpi-rebuild” qui regenère les mois historiques si on importe des données.

## Consommation Dashboard
- `DashboardController` lit `monthly_kpis` pour `metier in ('vente','atelier','location')`.  
- Pour la Location :  
  - CA = `revenue_ht`.  
  - Marge = `margin_ht` (afficher “en cours” tant qu’à 0 + note).  
  - Panier = `invoice_count > 0 ? revenue_ht / invoice_count : '—'`.  
- En cas d’absence de données : card grisée + message “Pas de réservations ce mois-ci / KPIs à venir”.
