# Étape 1 — Nettoyage des références asset

1. **Mettre à jour `counter-demo`**
   - Dans `resources/views/counter-demo.blade.php`, remplacer `@vite(['resources/css/app.css', 'resources/js/app.js'])` par `@vite(['resources/scss/app.scss', 'resources/js/app.js'])`.
2. **Vérifier les autres vues**
   - `rg -n "resources/css/app.css" -g "*.blade.php" resources/views` doit retourner 0 résultat.

Objectif : garantir que seules les entrées SCSS (`resources/scss/app.scss`) sont chargées partout.***
