# Contraintes techniques

1. **Layout**
   - Grille responsive en trois colonnes (stack vertical < 1024px).  
   - Chaque colonne contient un header (titre + période), puis trois lignes KPI avec valeurs + sous-label (ex. “Mois en cours”).

2. **Sources de données**
   - Vente / Atelier : déjà alimentés via `monthly_kpis` (`metier = vente`/`atelier`).  
   - Location : ajouter des enregistrements `monthly_kpis` avec `metier = location` (cf. Feature 15). Jusqu’à ce que la marge soit disponible, on envoie `margin_ht = null`.

3. **API**
   - `DashboardController` lit `monthly_kpis` pour le mois courant (et éventuellement N-1 si on veut afficher une variation).  
   - Prévoir la possibilité de changer la période plus tard (sélecteur mois/année) → utiliser un service `DashboardKpiService`.

4. **Calculs**
   - CA = `revenue_ht` ou TTC selon convention (à clarifier ; si Vente/Atelier sont HT, aligner Location).  
   - Panier moyen = `CA / invoice_count` (afficher “—” si 0).  
   - Marge brute = `margin_ht` (pour Location, placeholder « en cours » tant que les coûts ne sont pas suivis).

5. **Performances**
   - Pas de requêtes lourdes : tout est agrégé dans `monthly_kpis`.  
   - Si KPI manquant (pas de ligne Location yet), afficher un skeleton / message “KPIs en construction”.

6. **Évolutivité**
   - Prévoir un composant générique `<KpiCard>` qui accepte `title`, `value`, `subValue`, `trend?` pour réutilisation.  
   - La colonne Location doit pouvoir intégrer de nouveaux indicateurs (ex. taux d’occupation) sans casser le layout.
