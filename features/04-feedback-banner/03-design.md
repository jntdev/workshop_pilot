# 03 - Design & SCSS

1. **Structure SCSS**
   - Créer `resources/scss/components/common/_feedback.scss`.
   - Importer ce partial via `resources/scss/base/_components.scss`.
2. **Classes**
   - `.feedback-banner` : conteneur principal (display flex, align center, padding, border-radius).
   - Modifiers : `.feedback-banner--success`, `.feedback-banner--error`.
   - Sous-éléments : `.feedback-banner__icon`, `.feedback-banner__message`, `.feedback-banner__close` (optionnel).
3. **Couleurs**
   - Succès : `$color-success-green` (à définir dans `base/_colors.scss`, ex. `#047857`).
   - Erreur : `$color-danger-red` existant.
4. **Positionnement**
   - Wrapper `.feedback-host` sous le header dans `layouts/main.blade.php`.
   - Largeur max alignée avec `.layout-main` (centré, `max-width: 1200px`).
5. **Temporalité visuelle**
   - Pas d’animation ; simple `opacity:1` → `display:none` géré par JS.
6. **Responsivité**
   - Texte sur deux lignes si besoin ; icône + message s’adaptent.

Le design doit rester cohérent avec la charte SCSS existante (typos, spacing).***
