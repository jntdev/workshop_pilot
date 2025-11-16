# 04 - Règles design & variables

- Palette (dans `_colors.scss`) :
  - `$color-danger-red` : boutons suppression (fond rouge).
  - `$color-primary-blue` : boutons validation (fond bleu foncé).
  - `$color-toggle-blue` : état ON des toggles (bleu clair).
  - `$color-toggle-mango` : état OFF des toggles (jaune mangue).
  - `$color-neutral-dark` / `$color-neutral-light` : gris texte/fond communs.
- Boutons :
  - Suppression = fond `$color-danger-red`, texte blanc `#fff`.
  - Validation = fond `$color-primary-blue`, texte blanc `#fff`.
  - Styles globaux dans `components/common/_buttons.scss`.
- Toggles :
  - Fond/slider ON = `$color-toggle-blue`, OFF = `$color-toggle-mango`.
  - Texte ON/OFF déterminé via un mixin `contrast-color($bg)` (défini dans `_rules.scss`) pour choisir noir ou blanc selon la luminosité.
- Prévoir les variables pour l’ensemble des chapitres afin d’éviter les duplications. 
