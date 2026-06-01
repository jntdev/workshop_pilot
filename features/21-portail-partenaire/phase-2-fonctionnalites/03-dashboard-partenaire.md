# Ticket 21.2.3 : Dashboard partenaire (React)

**Type** : Frontend
**Priorité** : Haute
**Estimation** : 2 jours

## Description

Page principale du partenaire après connexion. Elle contient deux zones :
1. Le formulaire de demande de disponibilité / réservation
2. La liste des demandes passées

---

## Cadrage produit (2026-05-12)

### Ce que le partenaire fait
1. Choisit ses dates + créneau (journée entière / matin / après-midi)
2. Consulte le stock disponible par taille de cadre (S/M/L…) sur ces dates — **indicatif uniquement**, jamais bloquant
3. Ajoute ses clients un par un avec leur taille en cm
4. S'appuie sur le guide de correspondance taille cm → taille cadre affiché dans le formulaire
5. Ajoute un commentaire optionnel
6. Soumet la demande

### Ce que l'admin fait
- Reçoit la demande avec les tailles clients en cm
- Traduit en tailles de cadre et optimise le stock à sa guise (potence, substitution) sans impliquer le partenaire

### Règles métier
- Le stock affiché est **indicatif** : le partenaire peut soumettre même si le stock semble insuffisant
- La correspondance taille cm → taille cadre est un **guide**, pas une contrainte technique
- Les substitutions (ex: S avec potence rehaussée) sont décidées **par l'admin uniquement**

---

## Formulaire de demande

### Champs
- **Date début** + **Date fin** (input date)
- **Créneau** : journée entière / matin / après-midi (radio ou select)
- **Stock indicatif** : affiché après saisie des dates, par taille de cadre (S/M/L…), stock calculé sur les dates choisies
- **Guide de correspondance** taille cm → taille cadre (affiché en permanence dans le formulaire)
- **Liste des clients** : ajout dynamique, chaque client = une taille en cm
- **Commentaire** : champ texte optionnel
- **Bouton Envoyer la demande**

### Guide de correspondance (à confirmer avec admin)
| Taille client | Taille cadre |
|---|---|
| < 155 cm | XS |
| 155–165 cm | S |
| 165–175 cm | M |
| 175–185 cm | L |
| > 185 cm | XL |

---

## Liste des demandes passées

- Tableau : dates, créneau, nb clients, statut, date de soumission
- Statuts :
  - `en_attente` → "En attente de confirmation"
  - `confirmee` → "Confirmée"
  - `annulee` → "Annulée"

---

## Dépendances

- Ticket 21.2.1 : endpoint GET `/api/partenaires/disponibilites`
- Ticket 21.2.2 : endpoint POST `/api/partenaires/demandes` + GET liste

---

## Ce qui reste à préciser

- Valeurs exactes du guide de correspondance taille cm → cadre (à valider avec l'admin)
- Libellés exacts des tailles de cadre dans la base (S/M/L ou autre)
