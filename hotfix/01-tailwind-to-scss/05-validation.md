# Étape 4 — Vérifications

1. **Analyse statique :**
   - `rg -n "text-" resources/views` pour s’assurer qu’aucune classe Tailwind ne reste.
   - Idem pour `class="flex"`, `class="grid"`, `class="border-"`, etc.
2. **Compilation :**
   - `npm install` (après uninstall Tailwind).
   - `npm run build` ou `npm run dev` pour vérifier que Vite compile uniquement SCSS.
3. **Tests :**
   - `php artisan test` pour s’assurer que les features existantes fonctionnent toujours.
4. **Inspection visuelle :**
   - Lancer `php artisan serve` + `npm run dev`, vérifier que dashboard + pages Clients/Atelier/Location utilisent bien les nouveaux styles SCSS.
5. **Tests d’affichage automatisés :**
   - Ajouter/mettre à jour un test frontend (ex. `tests/Feature/Frontend/RoutingTest.php` ou tests Dusk/Pest) qui rend les pages clés et vérifie :
     - Que les classes SCSS attendues sont présentes (ex. `.dashboard-card`, `.clients-index__placeholder`).
     - Que chaque route retourne un statut HTTP 200.
   - Exécuter ces tests après la refactorisation pour confirmer le rendu.

Une fois ces points validés, la hotfix est prête à être committée/poussée en suivant le workflow `features/00-workflow.md`.***
