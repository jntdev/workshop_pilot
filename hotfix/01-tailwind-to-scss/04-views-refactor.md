# Étape 3 — Refactor des vues (suppression Tailwind)

Pour chaque fichier ci-dessous : remplacer les classes utilitaires par les classes SCSS définies à l’étape 2. Aucune règle inline ne doit rester.

## 1. `resources/views/components/layouts/main.blade.php`
- `<body>` : ajouter `class="layout"`.
- `<header class="border-b ...">` → `class="layout-header"`.
- `<div class="container mx-auto px-4 py-4">` → `class="layout-header__inner"`.
- `<div class="flex items-center justify-between">` → `class="layout-header__bar"`.
- `<h1 class="text-2xl font-semibold">` → `class="layout-header__title"`.
- `<nav class="flex gap-6">` → `class="layout-nav"`.
- Lien actif/hover : remplacer `class="hover:text-primary transition"` par `class="layout-nav__link"`.
- Bloc breadcrumb `class="bg-neutral-50 ...">` → `class="layout-breadcrumb"`, conteneur `class="layout-breadcrumb__inner"`.
- `<main class="container mx-auto px-4 py-8">` → `class="layout-main"`.

## 2. `resources/views/components/layouts/chapter.blade.php`
- `<div class="chapter-layout">` reste.
- Header : remplacer `class="flex items-center justify-between mb-6"` par `class="chapter-layout__head"`.
- Boutons : `class="chapter-actions"` devient `class="chapter-layout__actions"`.
- Contenu : `class="chapter-content"` → `class="chapter-layout__content"`.
- Ajouter un wrapper `class="chapter-layout__title"` autour du `<h2>` si besoin pour cibler la typo.

## 3. `resources/views/home/dashboard.blade.php`
- `<div class="dashboard">` reste.
- Titre `class="text-4xl font-semibold mb-8"` → `class="dashboard__title"`.
- `div` des cartes : remplacer `class="dashboard-cards grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"` par `class="dashboard__cards"`.
- Liens `.dashboard-card` restent mais supprimer toute classe utilitaire, s’assurer que le markup contient :
  ```html
  <a ... class="dashboard-card">
      <div class="dashboard-card__content">
          <h2 class="dashboard-card__title">...</h2>
          <p class="dashboard-card__description">...</p>
      </div>
  </a>
  ```
- Supprimer `text-2xl`, `font-semibold`, `text-neutral-600` etc. Toute la mise en forme passe en SCSS.

## 4. Pages chapitres
- `resources/views/clients/index.blade.php` :
  - `<div class="clients-index">` reste.
  - `<p class="text-neutral-600">` → `class="clients-index__placeholder"`.
- `resources/views/atelier/index.blade.php` :
  - Idem avec classes `.atelier-index`, `.atelier-index__placeholder`.
- `resources/views/location/index.blade.php` :
  - Idem `.location-index`, `.location-index__placeholder`.

## 5. `resources/views/welcome.blade.php`
- Remplacer le bloc `<style>` inline Tailwind par l’inclusion standard `@vite(['resources/scss/app.scss', 'resources/js/app.js'])`.
- Adapter le HTML pour utiliser les mêmes classes que `layouts.main` (ou inclure `x-layouts.main` si possible) afin d’éviter toute dépendance à Tailwind.

## 6. Composants Livewire
- Vérifier que le futur formulaire client (ou tout autre composant) n’utilise pas de classes utilitaires. Prévoir des classes comme `.client-form`, `.client-form__field`, `.client-form__label`, etc., définies dans `resources/scss/clients/_form.scss`.

Après ces changements, plus aucune classe Tailwind (`text-*`, `grid`, `flex`, etc.) ne doit subsister dans les Blade.***
