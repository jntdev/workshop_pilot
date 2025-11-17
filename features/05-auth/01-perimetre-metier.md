# 01 - Périmètre métier

Feature : **05 - Authentification interne via Laravel Fortify**.

## Objectifs
- Autoriser uniquement les collaborateurs de l'entreprise à accéder à l'outil interne.
- Offrir les flux basiques (inscription, connexion, réinitialisation de mot de passe, déconnexion) sans logique sécurité avancée pour l'instant.
- Préparer une base propre pour pouvoir pousser plus loin (vérif e-mail, double facteur, règles de domaine) lorsque le besoin arrivera.

## Contraintes
- Pas de Tailwind ni de starter d'UI pré-packagé : toutes les vues auth sont stylées en SCSS (même stack que le reste du projet).
- On reste sur le guard `web` classique, sessions Laravel, pas de SPA.
- Le delivery doit être compatible avec Fortify "vanilla" (pas de package design supplémentaire).

## Hypothèses
- Les comptes sont créés librement via le formulaire (workflow provisoire). Un admin pourra superviser plus tard.
- Les utilisateurs sont déjà dans la base `users` fournie par Laravel, migrée via `php artisan migrate`.
- Nous voulons garder la structure de routes Livewire/Blade actuelle et juste protéger les écrans internes avec `auth`.

## Hors scope pour cette itération
- Restrictions par domaine e-mail.
- Validation d'adresse e-mail, 2FA, captcha, SSO, provisioning automatique.
- Rate-limiting custom autre que celui fourni par Fortify (ThrottleRequests par défaut).
