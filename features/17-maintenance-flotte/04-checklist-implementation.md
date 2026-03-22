# Checklist d'implémentation

## Backend

- [ ] Migration `create_bike_maintenance_sheets_table`
- [ ] Migration `create_bike_maintenance_lines_table`
- [ ] Modèle `BikeMaintenanceSheet` avec relations et calculs
- [ ] Modèle `BikeMaintenanceLine` avec relation
- [ ] Relation `maintenanceSheets()` sur `Bike`
- [ ] Factory `BikeMaintenanceSheetFactory`
- [ ] Factory `BikeMaintenanceLineFactory`
- [ ] `BikeMaintenanceSheetController` (CRUD API)
- [ ] `StoreMaintenanceSheetRequest` avec validation
- [ ] `UpdateMaintenanceSheetRequest` avec validation
- [ ] Route API `/api/bikes/{bike}/maintenance` (index, store)
- [ ] Route API `/api/maintenance-sheets/{sheet}` (show, update, destroy)
- [ ] Modifier `rebuildLocationKpis()` pour calculer la marge
- [ ] Modifier `syncReservationPayments()` pour recalculer la marge
- [ ] Test `BikeMaintenanceSheetTest` (CRUD, calculs)

## Frontend

- [ ] Type `BikeMaintenanceSheet` dans `types/index.d.ts`
- [ ] Type `BikeMaintenanceLine` dans `types/index.d.ts`
- [ ] Composant `BikePanel.tsx` (panneau droit fiche vélo)
- [ ] État `selectedBike` dans `Location/Index.tsx`
- [ ] Clic header colonne → ouvre `BikePanel`
- [ ] Affichage infos vélo éditables dans `BikePanel`
- [ ] Liste historique maintenance dans `BikePanel`
- [ ] Totaux par année et total cumulé
- [ ] Composant `MaintenanceSheetForm.tsx` (formulaire fiche)
- [ ] Gestion des lignes (ajout, suppression, calcul)
- [ ] Bouton "+ Nouvelle fiche atelier" dans `BikePanel`
- [ ] Ouverture fiche existante en clic sur historique
- [ ] Suppression fiche avec confirmation
- [ ] Styles SCSS `_bike-panel.scss`
- [ ] Styles SCSS `_maintenance-form.scss`

## KPIs

- [ ] Dashboard affiche marge Location (plus "À venir")
- [ ] Rebuild KPIs intègre le coût maintenance
- [ ] Test du calcul marge = CA − maintenance

## Seeds (optionnel)

- [ ] Seeder fiches maintenance pour tests visuels
