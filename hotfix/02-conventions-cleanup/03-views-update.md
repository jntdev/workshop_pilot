# Étape 2 — Mise en conformité des vues Blade

1. `resources/views/livewire/counter.blade.php`
   - Remplacer les classes utilitaires (`flex`, `gap-4`, `text-4xl`, `bg-[#1b1b18]`, `dark:*`, etc.) par des classes sémantiques (`.counter`, `.counter__title`, `.counter__value`, `.counter__actions`, `.counter__button`).
   - Ajouter ces classes dans un partial SCSS dédié (cf. Étape 3).
   - Ne pas laisser de couleurs hex en dur dans les vues.
2. `resources/views/counter-demo.blade.php`
   - Utiliser `<x-layouts.main>` ou appliquer les classes `.layout-*` pour éviter les utilitaires Tailwind sur `<body>`.
   - Remplacer les classes du lien par `.counter-demo__cta` (définie en SCSS).
   - `@vite` doit cibler `resources/scss/app.scss`.

Résultat attendu : aucune classe `text-*`, `grid`, `flex`, `bg-*`, `dark:*`, etc., dans ces deux vues de démonstration.***
