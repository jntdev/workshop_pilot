# Ticket 21.1.0 : Durcissement de l'auth Google admin (Laravel)

**Type** : Sécurité / Backend
**Priorité** : Critique — à faire avant tout autre ticket de la phase 1
**Estimation** : 15 min

## Problème

Aujourd'hui, `GoogleAuthController` vérifie uniquement la présence de l'email dans `authorized_emails`, sans distinction de type. Dès qu'on ajoute la colonne `type` (ticket 21.1.1) et qu'on whiteliste des emails partenaires, un partenaire possédant un compte Google avec cet email pourrait se connecter à l'interface admin.

## Correction

Dans `GoogleAuthController`, remplacer la vérification actuelle :

```php
// Avant — accepte tout email présent dans authorized_emails
AuthorizedEmail::where('email', $email)->exists()
```

par une vérification filtrée sur le type :

```php
// Après — accepte uniquement les emails de type admin
AuthorizedEmail::where('email', $email)->where('type', 'admin')->exists()
```

## Tâches

- [ ] Lire `app/Http/Controllers/Auth/GoogleAuthController.php`
- [ ] Mettre à jour la vérification whitelist pour filtrer sur `type = admin`
- [ ] Vérifier que les 3 emails admin existants ont bien `type = admin` dans la migration (ticket 21.1.1)
- [ ] Ajouter un test : un email en whitelist avec `type = partenaire` est rejeté par l'auth Google

## Ordre d'exécution

Ce ticket doit être appliqué **en même temps** que la migration qui ajoute la colonne `type` sur `authorized_emails` (ticket 21.1.1). Les deux sont atomiques — l'un sans l'autre crée une faille.

## Critères d'acceptation

- Un email `type = admin` peut toujours se connecter via Google
- Un email `type = partenaire` est rejeté par l'auth Google avec le message existant "Adresse e-mail non autorisée"
- Aucune régression sur les 3 comptes admin existants
