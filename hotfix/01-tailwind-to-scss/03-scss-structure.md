# Étape 2 — Consolidation SCSS

1. **Importer les partials communs dans `resources/scss/app.scss`** (ordre recommandé) :
   ```scss
   @use 'base/colors';
   @use 'base/typos';
   @use 'base/rules';
   @use 'base/components';

   @use 'home/dashboard';
   @use 'clients/index-page';
   @use 'atelier/index-page';
   @use 'location/index-page';
   ```
   Adapter selon la convention `@use`/`@forward`.
2. **Définir les composants transverses :**
   - `resources/scss/components/common/_buttons.scss` : styles boutons (danger/validate) via variables.
   - `resources/scss/components/common/_layout.scss` : header, nav, breadcrumb, container.
   - `resources/scss/components/common/_cards.scss` : cards dashboard.
   - Importer ces fichiers depuis `base/_components.scss`.
3. **Créer des classes sémantiques correspondant aux vues :**
   - `home/_dashboard.scss` : `.dashboard`, `.dashboard__title`, `.dashboard__cards`, `.dashboard-card`, `.dashboard-card__content`.
   - `clients/_index-page.scss` : `.clients-index`.
   - `atelier/_index-page.scss` : `.atelier-index`.
   - `location/_index-page.scss` : `.location-index`.
   - `components/layouts/_main.scss` (à référencer) : `.layout`, `.layout-header`, `.layout-nav`, `.layout-main`, `.layout-breadcrumb`.
   - `components/layouts/_chapter.scss` : `.chapter-layout`, `.chapter-layout__head`, `.chapter-layout__actions`, `.chapter-layout__content`.
4. **Variables couleur (déjà prévues) :**
   - Vérifier que `$color-danger-red`, `$color-primary-blue`, `$color-toggle-blue`, `$color-toggle-mango`, `$color-neutral-dark`, `$color-neutral-light` sont définies dans `base/_colors.scss`.
5. **Mixins :**
   - Ajouter `@mixin contrast-color($bg)` dans `base/_rules.scss` (utilisé par boutons/toggles).

Cette étape s’assure que toutes les classes nécessaires aux vues existent côté SCSS avant de supprimer les utilitaires Tailwind.***
