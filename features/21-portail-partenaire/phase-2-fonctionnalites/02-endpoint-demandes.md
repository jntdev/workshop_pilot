# Ticket 21.2.2 : Endpoints demandes de réservation (Laravel)

**Type** : Backend / API
**Priorité** : Haute
**Estimation** : 1h

## Description

Créer les endpoints permettant au partenaire de soumettre une demande de réservation et de consulter l'historique de ses demandes. Une demande n'est pas une réservation — elle ne bloque pas de vélos et ne s'intègre pas dans le calendrier. Elle déclenche uniquement un email à l'admin.

## Tâches

- [ ] Créer `PartenaireDemandController` avec :

### `POST /api/partenaires/demandes`
- Validation :
  - `date_debut` : requis, date, pas dans le passé
  - `date_fin` : requis, date, après date_debut
  - `velos_demandes` : requis, array, au moins un type avec quantité > 0
  - `commentaire` : optionnel, string, max 500 caractères
- Créer une `DemandePartenaire` avec statut `en_attente`
- Déclencher l'email de notification admin (voir ticket 21.3.1)
- Retourner la demande créée

### `GET /api/partenaires/demandes`
- Retourne uniquement les demandes du partenaire authentifié
- Triées par date de création décroissante
- Inclut : id, date_debut, date_fin, velos_demandes, statut, commentaire, created_at

### `GET /api/partenaires/demandes/{demande}`
- Retourne le détail d'une demande
- Vérifie que la demande appartient bien au partenaire authentifié (403 sinon)

## Critères d'acceptation

- Un partenaire ne peut voir que ses propres demandes
- Une demande avec aucun vélo demandé est rejetée
- La demande est créée même si l'email admin échoue (l'email est découplé via job)
- Le statut initial est toujours `en_attente`
