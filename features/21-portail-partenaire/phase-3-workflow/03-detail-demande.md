# Ticket 21.3.3 : Page détail d'une demande (React)

**Type** : Frontend
**Priorité** : Basse
**Estimation** : 45 min

## Description

Page de détail d'une demande accessible depuis la liste dans le dashboard. Elle affiche toutes les informations de la demande et son statut actuel.

## Tâches

- [ ] Créer la page `DemandeDetailPage` accessible via `/demandes/:id`
- [ ] Appeler `GET /api/partenaires/demandes/:id`
- [ ] Afficher :
  - Statut en haut de page (badge coloré)
  - Dates de la demande
  - Vélos demandés (liste par type et quantité)
  - Commentaire soumis (si renseigné)
  - Date de soumission
- [ ] Bouton "Retour" vers le dashboard
- [ ] Gestion du 403 (demande ne lui appartient pas)
- [ ] Gestion du 404 (demande inexistante)

## Critères d'acceptation

- Le partenaire peut consulter le détail de n'importe laquelle de ses demandes
- Un accès à une demande d'un autre partenaire affiche une page d'erreur claire
- Le statut est affiché de manière lisible et colorée
