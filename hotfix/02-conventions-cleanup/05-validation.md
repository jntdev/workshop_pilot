# Étape 4 — Vérifications finales

1. **Recherche interdictions**
   - `rg -n "text-" resources/views` et variantes (`grid`, `flex`, `bg-`, `dark:`) pour confirmer la disparition des classes Tailwind.
   - `rg -n "class=\\".*#" resources/views` pour éviter les couleurs inline en classes (ex. `bg-[#...]`).
2. **Compilation**
   - `npm install` (si dépendances modifiées), puis `npm run build` / `npm run dev`.
3. **Tests Laravel**
   - `php artisan test` (incluant `tests/Feature/Frontend/RoutingTest.php`).
4. **Tests d’affichage**
   - Étendre `tests/Feature/Frontend/RoutingTest.php` ou ajouter un test Livewire vérifiant la présence des nouvelles classes (`assertSee('dashboard-card')`, etc.).
5. **Revue visuelle**
   - `php artisan serve` + `npm run dev` → vérifier dashboard + pages + Counter Demo.

Une fois toutes les étapes validées, appliquer le workflow git (`feature/01-frontend-routing` ou branche courante) et pousser la correction.***
