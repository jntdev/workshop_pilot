# 03 - Stratégie de réalisation

Approche incrémentale : installer Fortify, câbler la logique backend, brancher les vues SCSS, puis protéger les routes internes.

## 1. Installer et enregistrer Fortify
- `composer require laravel/fortify`
- `php artisan vendor:publish --provider="Laravel\\Fortify\\FortifyServiceProvider"`
- Ajouter `App\Providers\FortifyServiceProvider::class` dans `config/app.php` ou `app/Providers` avec enregistrement dans `AppServiceProvider`.
- Vérifier que la migration `users` est à jour (`php artisan migrate`).

## 2. Configurer `config/fortify.php`
- Guard : `web` ; middleware : `['web']`.
- `home` => `/dashboard` (page d'accueil connectée).
- Activer uniquement : `Features::registration()`, `Features::resetPasswords()`, `Features::updatePasswords()`.
- Désactiver 2FA, email verification, profil updates pour cette phase.

## 3. Lier les actions Fortify
Dans `FortifyServiceProvider` :
- `Fortify::createUsersUsing(CreateNewUser::class);`
- `Fortify::authenticateUsing()` (optionnel si on veut personnaliser, sinon laisser par défaut).
- `Fortify::resetUserPasswordsUsing(ResetUserPassword::class);`
- `Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);`
- Déclarer les vues : `loginView`, `registerView`, `requestPasswordResetLinkView`, `resetPasswordView`, `verifyEmailView` (même si non utilisé, renvoyer une vue simple).

## 4. Produire les vues Blade + SCSS
- Création des fichiers :
  - `resources/views/auth/login.blade.php`
  - `resources/views/auth/register.blade.php`
  - `resources/views/auth/forgot-password.blade.php`
  - `resources/views/auth/reset-password.blade.php`
  - Partials communs (header / bouton / alertes) si besoin.
- Définir les blocs HTML alignés sur la grille SCSS existante :
  - Layout `auth-layout` (centrer la carte, background neutre).
  - Composant `.auth-card` (titre, copy, formulaire, CTA secondaire).
- Ajouter un partial SCSS `resources/scss/auth/_forms.scss` importé dans `app.scss` (variables, boutons, alertes, inputs).

## 5. Sécuriser les routes applicatives
- Envelopper toutes les routes internes dans le middleware `auth` (`routes/web.php`).
- Ajouter la redirection par défaut dans `app/Providers/RouteServiceProvider.php::$HOME = '/dashboard';`.
- Vérifier que Livewire utilise bien `->middleware(['auth'])` si composant autonome.
- Pour l'instant on laisse l'inscription accessible publiquement ; prévoir un flag de config pour la fermer rapidement (`config/fortify.enable_registration`).

## 6. Tests et QA rapide
- Tests artisan (à écrire plus tard) : pour l'instant, vérifier manuellement que création/connexion/logout fonctionnent.
- Checklist manuelle (cf. fichier 05) + capture d'écran si besoin.
- Script build front : `npm run build` pour vérifier que les SCSS auth ne cassent pas les autres pages.

## 7. Documentation
- Compléter la doc interne (ce dossier) + CLAUDE.md si la procédure d'auth impacte ses scripts.
- Ajouter commandes `composer`/`artisan` dans README si on veut onboarder rapidement.
