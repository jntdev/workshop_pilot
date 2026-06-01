# Ticket 21.1.1 : Modèle Partenaire (Laravel)

**Type** : Backend / Data
**Priorité** : Haute
**Estimation** : 30 min

## Description

Créer la table et le modèle `Partenaire` dans l'application Laravel. Un partenaire est un hôtel ou établissement tiers qui soumet des demandes de réservation via le portail dédié.

L'inscription d'un partenaire est conditionnée à la présence de son email dans la whitelist `authorized_emails` avec le type `partenaire`. L'admin whiteliste l'email, le partenaire crée lui-même son compte.

## Tâches

- [ ] Ajouter une colonne `type` sur `authorized_emails` :
  - Enum : `admin` | `partenaire`, default `admin`
  - Mettre à jour les 3 emails existants en `admin` dans la migration
- [ ] Mettre à jour le modèle `AuthorizedEmail` :
  - Ajouter le cast `type`
  - Ajouter un scope `partenaire()` pour filtrer par type
- [ ] Créer la migration `create_partenaires_table` avec les colonnes :
  - `id`
  - `nom` (string) — nom de l'établissement
  - `email` (string, unique)
  - `password` (string, haché)
  - `actif` (boolean, default true)
  - `timestamps`
- [ ] Créer le modèle `Partenaire` avec :
  - `$fillable` : nom, email, password, actif
  - Cast `password` en hashed
  - Cast `actif` en boolean
  - Scope `actif()` pour filtrer les partenaires actifs
- [ ] Créer la migration `create_demandes_partenaire_table` avec les colonnes :
  - `id`
  - `partenaire_id` (foreign key)
  - `date_debut` (date)
  - `date_fin` (date)
  - `velos_demandes` (json) — structure `{ "VAE_s": 2, "VAE_m": 1, ... }`
  - `commentaire` (text, nullable)
  - `statut` (enum : `en_attente`, `confirmee`, `annulee`, default `en_attente`)
  - `timestamps`
- [ ] Créer le modèle `DemandePartenaire` avec :
  - Relation `partenaire()` vers `Partenaire`
  - Cast `velos_demandes` en array
  - Cast `date_debut` / `date_fin` en date
- [ ] Créer les factories `PartenaireFactory` et `DemandePartenaireFactory`

## Critères d'acceptation

- La colonne `type` sur `authorized_emails` distingue admins et partenaires
- Les emails admin existants restent en `type = admin` après migration
- Un partenaire peut être créé en base avec un password haché
- Une demande est liée à un partenaire et stocke les vélos demandés en JSON
