# Étape 3 — Ajustements SCSS

1. **Déduplication `.dashboard-card`**
   - Garder une seule définition dans `resources/scss/components/common/_cards.scss`.
   - Supprimer le bloc dupliqué de `resources/scss/home/_dashboard.scss` (ne conserver là-bas que `.dashboard` + variations).
2. **Nouveaux partiels Livewire**
   - Créer `resources/scss/components/livewire/_counter.scss` définissant les classes BEM utilisées dans `livewire/counter.blade.php`.
   - L’importer dans `resources/scss/base/_components.scss` (ex. `@use '../components/livewire/counter';`).
3. **Appel SCSS global**
   - Vérifier que `resources/scss/app.scss` charge tous les partiels nécessaires après ces ajouts.
4. **Variables / mixins**
   - Si des couleurs utilisées dans les vues `counter`/`welcome` sont spécifiques, les déclarer dans `base/_colors.scss` plutôt qu’en dur dans le SCSS.
5. **Nettoyage**
   - Supprimer tout code/commentaire faisant référence à Tailwind.

Ces ajustements garantissent l’unicité des composants SCSS et facilitent leur réutilisation.***
