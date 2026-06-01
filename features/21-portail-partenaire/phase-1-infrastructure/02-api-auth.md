# Ticket 21.1.2 : API d'authentification partenaire (Laravel)

**Type** : Backend / API + Architecture
**Priorité** : Haute
**Estimation** : 2h

## Contexte architectural

Ce ticket est une **extension d'architecture**, pas juste quelques endpoints. L'état actuel du projet :

- Pas de Sanctum dans `composer.json` — l'auth API par token n'existe pas
- Un seul guard `web` (session) dans `config/auth.php`
- Les routes API existantes sont protégées par `['web', 'auth']` (session Laravel, pas token)

Les routes partenaire ne peuvent pas utiliser le guard session car le frontend partenaire est un domaine séparé (`partenaires.lesvelosdarmor.bzh`). Il faut une auth par token (Bearer). Sanctum est la solution naturelle dans l'écosystème Laravel.

**Important** : Sanctum doit être installé et configuré sans toucher aux routes API existantes qui continuent à fonctionner avec `['web', 'auth']`. Les deux mécanismes cohabitent.

## Flux d'inscription

1. L'admin ajoute l'email du partenaire dans `authorized_emails` avec `type = partenaire`
2. Le partenaire se rend sur `partenaires.lesvelosdarmor.bzh/inscription`
3. Il saisit son email + nom de l'établissement + mot de passe
4. Le backend vérifie que l'email est en whitelist avec `type = partenaire`
5. Si oui → compte `Partenaire` créé, token retourné, connecté
6. Si non → erreur "Cette adresse email n'est pas autorisée"

## Tâches

### Étape 1 — Installer et configurer Sanctum
- [ ] `composer require laravel/sanctum`
- [ ] `php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"`
- [ ] Exécuter la migration Sanctum (`personal_access_tokens`)
- [ ] Vérifier que les routes API existantes (`['web', 'auth']`) ne sont pas affectées

### Étape 2 — Modèle Partenaire compatible Sanctum
- [ ] S'assurer que `Partenaire` implémente `HasApiTokens` (trait Sanctum)
- [ ] Ajouter `Partenaire` dans `config/auth.php` comme provider :
  ```php
  'partenaires' => [
      'driver' => 'eloquent',
      'model' => App\Models\Partenaire::class,
  ],
  ```

> **Pourquoi pas de guard dédié ?** La doc Sanctum officielle ne documente que `auth:sanctum`. Créer un guard nommé `partenaire` casse la chaîne d'auth interne de Sanctum (cookie → token fallback) et perd les méthodes `tokenCan()`. On reste sur `auth:sanctum` et on délègue la logique métier au middleware.

### Étape 3 — Middleware métier
- [ ] Créer le middleware `EnsureIsPartenaire` :
  - S'applique **après** `auth:sanctum` (l'identité est déjà vérifiée)
  - Vérifie que `Auth::user()` est une instance de `Partenaire`
  - Retourne 403 si partenaire inactif (`actif = false`)
  - Retourne 403 si l'utilisateur authentifié n'est pas un `Partenaire`

### Étape 4 — Endpoints
- [ ] Créer `PartenaireAuthController` avec :
  - `POST /api/partenaires/inscription` — vérifie whitelist, crée le compte, retourne un token
  - `POST /api/partenaires/login` — vérifie email + password, retourne un token Sanctum
  - `POST /api/partenaires/logout` — révoque le token courant
  - `GET /api/partenaires/me` — retourne les infos du partenaire connecté
- [ ] Ajouter les routes dans `routes/api.php` dans un groupe séparé :
  - Inscription et login : sans middleware auth
  - Routes protégées : `['auth:sanctum', 'partenaire']` (sanctum vérifie le token, EnsureIsPartenaire vérifie le type et le statut)
- [ ] Configurer CORS dans `config/cors.php` pour accepter `partenaires.lesvelosdarmor.bzh`

### Étape 5 — Validation inscription
- [ ] `email` : requis, email valide, unique dans `partenaires`, présent dans `authorized_emails` avec `type = partenaire`
- [ ] `nom` : requis, string, max 100
- [ ] `password` : requis, min 8 caractères, confirmé

## Règles

- Les routes API existantes (`['web', 'auth']`) ne doivent pas être modifiées
- Un email peut s'inscrire une seule fois (unicité sur `partenaires.email`)
- Un partenaire inactif ne peut pas se connecter
- Le message si email non whitelisté doit être neutre : "Cette adresse email n'est pas autorisée"

## Critères d'acceptation

- Les routes API existantes fonctionnent toujours exactement comme avant
- `POST /api/partenaires/inscription` avec email whitelisté crée le compte et retourne un token
- `POST /api/partenaires/inscription` avec email non whitelisté retourne 422
- `POST /api/partenaires/inscription` avec email déjà inscrit retourne 422
- `POST /api/partenaires/login` avec bons identifiants retourne un token
- `POST /api/partenaires/login` avec mauvais identifiants retourne 401
- `POST /api/partenaires/login` avec partenaire inactif retourne 403
- Les routes protégées retournent 401 sans token valide
- Aucune régression sur les routes API existantes
