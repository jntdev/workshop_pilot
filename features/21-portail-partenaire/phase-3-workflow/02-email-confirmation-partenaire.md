# Ticket 21.3.2 : Email de confirmation au partenaire (Laravel)

**Type** : Backend / Email
**Priorité** : Moyenne
**Estimation** : 45 min

## Description

Permettre à l'admin de changer le statut d'une demande partenaire (confirmer ou annuler), et déclencher un email automatique au partenaire lors de ce changement.

## Tâches

- [ ] Ajouter un endpoint admin :
  - `PATCH /admin/partenaires/demandes/{demande}/statut`
  - Accepte : `statut` (`confirmee` ou `annulee`), `message` optionnel
  - Protégé par le middleware admin
- [ ] Créer la Mailable `DemandePartenaireConfirmee`
- [ ] Créer la Mailable `DemandePartenaireAnnulee`
- [ ] Contenu email confirmation :
  - Objet : "Votre demande de réservation est confirmée"
  - Rappel des dates et vélos
  - Message personnalisé de l'admin (si renseigné)
  - Coordonnées pour la suite (téléphone, email atelier)
- [ ] Contenu email annulation :
  - Objet : "Votre demande de réservation n'a pas pu être honorée"
  - Rappel des dates
  - Message personnalisé de l'admin (si renseigné)
  - Invitation à soumettre une nouvelle demande sur d'autres dates
- [ ] Exposer le changement de statut dans la liste admin des demandes

## Critères d'acceptation

- L'admin peut confirmer ou annuler une demande depuis l'interface
- Le partenaire reçoit un email lors du changement de statut
- Le statut est mis à jour en base et visible dans le dashboard partenaire
