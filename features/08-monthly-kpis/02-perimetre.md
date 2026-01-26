# 02 - Perimetre

## Inclus
- Ajout du champ `metier` (enum) sur la table `quotes`.
- Nouvelle table `monthly_kpis` avec index unique `(metier, year, month)`.
- Alimentation automatique lors de la conversion d'un devis en facture (dans `convertToInvoice()`).
- Backfill des KPI a partir des factures existantes.
- Dashboard atelier base sur `monthly_kpis` (stats + annees dispo).

## Hors perimetre
- Analyses avancees (par client, par prestation, par canal).
- Graphiques ou nouvelles vues UI.
- Gestion des annulations/avoirs (si ajoutee plus tard, traiter en delta).
