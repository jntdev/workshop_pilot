# 05 - Checklist d'exécution

## Préparation
- [ ] Vérifier que `composer install` et `npm install` ont déjà été exécutés.
- [ ] S'assurer que `.env` contient les variables Mail (Mailtrap ou log driver) pour les emails de reset.
- [ ] Lancer `php artisan migrate` pour garantir que la table `users` est prête.

## Mise en place Fortify
- [ ] Installer `laravel/fortify` + publier la config.
- [ ] Enregistrer `FortifyServiceProvider` et déclarer toutes les vues via `Fortify::...View`.
- [ ] Ajuster `config/fortify.php` (features min, home, middleware).
- [ ] Créer/ajuster les classes `app/Actions/Fortify` (validation simple, pas de règles domaine pour le moment).

## Vues & SCSS
- [ ] Créer les pages Blade (login/register/forgot/reset) basées sur le layout `auth`.
- [ ] Factoriser les composants communs (input, bouton, alerte) si utile.
- [ ] Écrire `resources/scss/auth/_forms.scss` + importer dans `app.scss`.
- [ ] Vérifier le rendu desktop + mobile (centrage vertical, responsive minimal : 320px min).

## Routes + Middleware
- [ ] Ajouter `Route::middleware('auth')` autour des routes internes (`/dashboard`, `/clients`, Livewire, etc.).
- [ ] Ajouter un lien de déconnexion (form POST `route('logout')`).
- [ ] Tester navigation : utilisateur non connecté → `/login`.

## QA manuelle
- [ ] Inscription puis redirection vers `/dashboard`.
- [ ] Déconnexion puis tentative d'accès à `/dashboard` → redirection login.
- [ ] Connexion avec mauvais mot de passe → message d'erreur.
- [ ] Process reset password complet (demande + reset + login avec nouveau mot de passe).
- [ ] `npm run build` passe sans erreur.

## Livraison
- [ ] Mettre à jour la doc (README + CLAUDE si nécessaire).
- [ ] Capturer les commandes clés dans le changelog.

## Clôture QA & notation (obligatoire)
- [ ] Relire intégralement tous les fichiers de `features/05-auth` avant de conclure la tâche.
- [ ] Vérifier chaque case ci-dessus en contrôlant concrètement que l’implémentation correspond (1 point par étape).
- [ ] Calculer la note = points validés / points totaux et poursuivre uniquement si elle est de 100 %.
- [ ] Exécuter et faire passer tous les tests indiqués, puis seulement ensuite autoriser commit/push.
