# Hotfix 02 — Vérification des conventions

## Objectif
Garantir le respect strict de `features/CONVENTIONS.md` :
- Pas de classes utilitaires (Tailwind) ni de styles inline dans les vues.
- Utilisation exclusive des classes SCSS BEM prévues.
- Assets front compilés uniquement via `resources/scss`.

## Constats
1. **Référence à l’ancien build CSS**
   - `resources/views/counter-demo.blade.php` continue d’appeler `@vite(['resources/css/app.css', ...])` alors que l’entrée a été supprimée (le fichier n’existe plus). Cela casserait `npm run dev`.
2. **Vues Livewire de démonstration**
   - `resources/views/livewire/counter.blade.php` : nombreuses classes Tailwind (`flex`, `text-4xl`, `bg-[#1b1b18]`, etc.) et couleurs inline `dark:*`, contraire aux conventions.
   - `resources/views/counter-demo.blade.php` : même problème (body + lien), et l’appel `@vite` référence encore `resources/css/app.css`.
3. **Duplication SCSS**
   - `.dashboard-card` est défini à la fois dans `resources/scss/home/_dashboard.scss` et `resources/scss/components/common/_cards.scss`, ce qui crée des styles contradictoires selon l’ordre d’import.

Les autres layouts/pages respectent déjà les conventions (BEM + SCSS). Les corrections restantes se concentrent sur ces points.***

Ces points contreviennent aux conventions et doivent être corrigés via les étapes suivantes.***
