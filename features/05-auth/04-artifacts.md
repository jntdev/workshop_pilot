# 04 - Artifacts attendus

## Backend / Config
- **Package** : `laravel/fortify` présent dans `composer.json` + autoload `FortifyServiceProvider`.
- **Config** : fichier `config/fortify.php` publié et ajusté (features, home, views).
- **Service Provider** : `app/Providers/FortifyServiceProvider.php` rempli (callbacks + vues).
- **Actions** : classes dans `app/Actions/Fortify` mises en place (CreateNewUser, UpdateUserPassword, ResetUserPassword, UpdateUserProfileInformation si nécessaire).

## Vues & Routage
- **Pages Blade** : `resources/views/auth/*.blade.php` (login, register, forgot, reset) alignées avec SCSS.
- **Layout** : partial `resources/views/layouts/auth.blade.php` (optionnel mais recommandé pour DRY).
- **Routes protégées** : `routes/web.php` mis à jour (`Route::middleware('auth')` autour de `/dashboard`, `/clients` et pages internes).
- **RouteServiceProvider** : constante `$HOME = '/dashboard'` mise à jour.

## Styles SCSS
- Nouveau partial `resources/scss/auth/_forms.scss` (inputs, boutons, cartes).
- Mise à jour `resources/scss/app.scss` pour importer le partial.
- Boutons/auth cards réutilisables (classes `.auth-card`, `.auth-form`, `.auth-alert`).

## Build & assets
- Compilation Vite/Mix OK (`npm run dev`/`npm run build`).
- Assets générés référencés dans les vues auth (par ex. `@vite(['resources/scss/app.scss', 'resources/js/app.js'])`).

## Validation
- Connexion / Inscription / Reset fonctionnent via Fortify (manuellement vérifié).
- Déconnexion invalide immédiatement la session.
- Routes internes redirigent bien vers `/login` pour visiteurs non authentifiés.
