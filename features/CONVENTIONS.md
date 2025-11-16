# Conventions de nommage & bonnes pratiques

## PHP / Laravel / Livewire
- **Classes & modèles** : `StudlyCase` (`Client`, `ClientFormComponent`, `CreateClientTest`).
- **Méthodes / fonctions** : `camelCase` (`saveClient()`, `runBackendTests()`).
- **Variables** : `camelCase` (`$clientData`, `$avantageValeur`).
- **Routes nommées** : `snake_case` (`clients.index`, `atelier.index`).
- **Fichiers tests** : `PascalCase` (`ClientsFormTest.php`), namespaces explicites (`Tests\Feature\Livewire`).
- **Tables DB** : `snake_case` pluriel (`clients`), colonnes en `snake_case`.

## Blade / Composants
- **Composants** : nom en `kebab-case` (`<x-layouts.main>`, `<x-layouts.chapter>`).
- **Slots** : `snake_case` (`<x-slot:title>`).
- **Classes CSS dans les vues** : uniquement des classes sémantiques définies dans SCSS (pas de classes utilitaires).

## SCSS
- **Fichiers** : `kebab-case` (`_dashboard.scss`, `_layout-header.scss`).
- **Organisation** : base (`_colors.scss`, `_typos.scss`, `_rules.scss`, `_components.scss`), components, chapitres/pages.
- **Classes** : BEM (`.dashboard-card`, `.dashboard-card__title`, `.dashboard-card--highlight`).
- **Variables** : `$color-primary-blue`, `$spacing-md`.
- **Mixins / fonctions** : `kebab-case` ou `camelCase` mais cohérent partout (`@mixin contrast-color($bg)`).

## Branches & commits
- Branches : `feature/<numero>-<slug>` (ex. `feature/01-client-form`), créées depuis `develop`.
- Commits : impératif et atomique (`Add Livewire client form`), idéalement un par étape décrite dans la feature.

## Tests
- Toujours écrire/mettre à jour les tests correspondants à chaque feature (backend + frontend/Livewire).
- Nom des méthodes de test : `test_...` descriptif (`test_client_creation_stores_data()`).

Ces conventions complètent le `features/00-workflow.md` et doivent être respectées par tous les agents (Claude ou développeurs humains) pour garantir un code homogène. 
