# Hotfix 01 — Remplacement Tailwind → SCSS

## Problème
- Tailwind est toujours compilé (`resources/css/app.css`, `@tailwindcss/vite`, classes utilitaires dans les vues).
- Les vues issues de la feature 01 (layouts + pages Clients/Atelier/Location/Dashboard) contiennent encore des classes Tailwind (`text-4xl`, `grid grid-cols-1`, etc.).
- Objectif : ne conserver que le design system SCSS (`resources/scss/**`) et bannir tout style dans les vues en dehors de classes sémantiques prévues.

## Périmètre audit
- Config & build : `package.json`, `vite.config.js`, `resources/css/app.css`.
- Layouts & pages : 
  - `resources/views/components/layouts/main.blade.php`
  - `resources/views/components/layouts/chapter.blade.php`
  - `resources/views/home/dashboard.blade.php`
  - `resources/views/clients/index.blade.php`
  - `resources/views/atelier/index.blade.php`
- `resources/views/location/index.blade.php`
- Vue `welcome.blade.php` encore générée avec Tailwind (fallback) → à aligner sur SCSS pour éviter la régression lors du build.

**Branche de travail :** toutes les opérations de ce hotfix se font sur `feature/01-frontend-routing` pour éviter d’introduire Tailwind sur `develop` avant la correction.

Les fichiers listés doivent être transformés conformément aux étapes suivantes.***
