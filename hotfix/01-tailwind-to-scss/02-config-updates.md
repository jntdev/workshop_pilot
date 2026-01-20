# Étape 1 — Nettoyage configuration build

1. **Supprimer Tailwind du bundle :**
   - Dans `package.json`, retirer `tailwindcss` et `@tailwindcss/vite` des `devDependencies`.
   - `npm uninstall tailwindcss @tailwindcss/vite`.
2. **Vite :**
   - Fichier `vite.config.js` :
     - Retirer l’import `@tailwindcss/vite`.
     - Dans `laravel({ input: [...] })`, supprimer `resources/css/app.css`, ne garder que `resources/scss/app.scss` et `resources/js/app.js`.
     - Ne plus enregistrer le plugin `tailwindcss()`.
3. **Assets :**
   - Supprimer `resources/css/app.css` (plus utilisé).
   - Vérifier que `resources/scss/app.scss` importe bien les bases (`base/_colors.scss`, `_typos.scss`, `_rules.scss`, `_components.scss`, + chapitres).
4. **Layout principal :**
   - Dans `resources/views/components/layouts/main.blade.php`, modifier `@vite` pour charger uniquement `resources/scss/app.scss` et `resources/js/app.js`.
   - Idem pour `resources/views/welcome.blade.php` dans le bloc `@vite`.
5. **Fonts / resets :**
   - Si un reset Tailwind était attendu, ajouter un reset SCSS dédié dans `resources/scss/base/_rules.scss` (ex. `* { box-sizing: border-box; }`).

Ces actions garantissent que le build front ne dépend plus de Tailwind et que seule la pipeline SCSS est utilisée.***
