# 05 - Tests

## Unitaires
- `MonthlyKpiUpdaterTest` :
  - Convertir un devis en facture increment les bons champs.
  - Verification `invoice_count`, `revenue_ht`, `margin_ht`.
  - Verifier que le metier de la quote est utilise.

## Commande de backfill
- `BackfillMonthlyKpisCommandTest` :
  - Deux factures sur deux mois => deux lignes creees avec sommes correctes.
  - Backfill par metier fonctionne correctement.
  - Option `--all` reconstruit tous les metiers.

## Livewire / Feature
- `DashboardStatsTest` :
  - Le dashboard lit les valeurs de `monthly_kpis`.
  - Comparaison N vs N-1 retourne les bons deltas.
  - Mois sans donnees retourne des zeros.

---

## Checklist d'implementation

- [x] Enum `Metier` cree
- [x] Migration `metier` sur `quotes` executee
- [x] Modele `Quote` mis a jour avec cast `metier`
- [x] Migration `monthly_kpis` executee
- [x] Modele `MonthlyKpi` cree
- [x] Service `MonthlyKpiUpdater` cree
- [x] Hook dans `convertToInvoice()` ajoute
- [x] Commande `kpis:rebuild-monthly` creee
- [x] Dashboard modifie pour lire `monthly_kpis`
- [x] Tests unitaires passent
- [x] Tests feature passent
- [ ] Backfill execute sur les donnees existantes (a faire apres deploiement)
