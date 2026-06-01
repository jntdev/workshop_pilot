# Ticket 21.1.3 : Interface admin — gestion de la whitelist partenaires (Laravel)

**Type** : Backend + Frontend admin
**Priorité** : Haute
**Estimation** : 1h

## Description

Permettre à l'admin de gérer la whitelist des emails partenaires et de consulter les comptes créés.

L'admin whiteliste un email → le partenaire reçoit le lien d'inscription → il crée lui-même son compte avec son mot de passe. L'admin ne crée jamais de compte, ne gère jamais de mot de passe.

## Tâches

### Backend
- [ ] Créer `Admin\PartenaireController` avec :
  - `GET /admin/partenaires` — liste les emails whitelistés (`type = partenaire`) et les comptes créés
  - `POST /admin/partenaires/whitelist` — ajoute un email en whitelist avec `type = partenaire`
  - `DELETE /admin/partenaires/whitelist/{email}` — retire un email de la whitelist
  - `PATCH /admin/partenaires/{partenaire}/toggle` — active/désactive un compte partenaire existant
- [ ] Protéger ces routes avec le middleware auth existant (admin uniquement)

### Frontend admin
- [ ] Page `/admin/partenaires` avec deux sections :

**Section 1 — Whitelist**
- Liste des emails en attente d'inscription (whitelistés mais pas encore inscrits)
- Formulaire d'ajout : champ email + bouton "Autoriser"
- Bouton supprimer pour retirer un email non encore inscrit

**Section 2 — Comptes actifs**
- Liste des partenaires ayant créé leur compte : nom, email, statut actif/inactif, date d'inscription
- Bouton activer/désactiver un compte

## Règles

- On ne peut pas supprimer un email de la whitelist si le partenaire a déjà créé son compte (désactiver le compte à la place)
- Un email retiré de la whitelist n'empêche pas un compte déjà existant de fonctionner (la whitelist ne sert qu'à l'inscription)

## Critères d'acceptation

- L'admin peut ajouter un email en whitelist partenaire en quelques secondes
- L'admin voit clairement quels emails sont en attente d'inscription
- L'admin peut désactiver un partenaire (il ne peut plus se connecter)
- La liste des comptes actifs est visible et à jour
