# 03 - Strategie de realisation

Approche : enum metier -> migrations -> service d'aggregation -> backfill -> usage dashboard -> tests.

1. Creer l'enum `Metier` (atelier, vente, location).
2. Migration : ajouter le champ `metier` sur `quotes`.
3. Migration + model `MonthlyKpi`.
4. Service d'update (delta) appele lors de la conversion en facture.
5. Commande artisan de backfill (rebuild) pour remplir les mois existants.
6. Dashboard atelier lit `monthly_kpis` au lieu de charger les factures.
7. Tests unitaires + Livewire/feature.
