# 03 - Structure SCSS

- Base `resources/scss/base/`
  - `_colors.scss` : palette + tokens (cf. règles design).
  - `_typos.scss` : familles, tailles, interlignage.
  - `_rules.scss` : helpers (spacing, grid, mixins).
  - `_components.scss` : import commun des composants globaux.
- Composants
  - `resources/scss/components/common/` : boutons, cards dashboard, toggles réutilisables.
  - Sous-dossiers par chapitre (`components/clients`, `components/atelier`, `components/location`) si variations locales.
- Chapitres / pages
  - `resources/scss/home/_dashboard.scss`
  - `resources/scss/clients/_index-page.scss`
  - `resources/scss/atelier/_index-page.scss`
  - `resources/scss/location/_index-page.scss`
- Feuille principale `resources/scss/app.scss` important base + components + chapitres.
- Une feuille spécifique par page, plus la feuille générale pour les règles communes. 
