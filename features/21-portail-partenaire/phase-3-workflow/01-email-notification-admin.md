# Ticket 21.3.1 : Email de notification à l'admin (Laravel)

**Type** : Backend / Email
**Priorité** : Haute
**Estimation** : 1h

## Description

Quand un partenaire soumet une demande, l'admin reçoit un email contenant toutes les informations nécessaires pour créer manuellement la réservation dans le calendrier location. L'email doit être actionnable sans avoir à ouvrir l'app.

## Tâches

- [ ] Créer la Mailable `NouvelleDemandePartenaire`
- [ ] Créer le job `SendDemandeNotificationToAdmin` (ShouldQueue) :
  - Dispatché après création de la demande
  - Découplé de la requête HTTP (l'échec d'email n'empêche pas la création de la demande)
- [ ] Destinataire : adresse admin configurée dans `.env` (`ADMIN_NOTIFICATION_EMAIL`)
- [ ] Contenu de l'email :
  - Objet : "Nouvelle demande de [Nom de l'hôtel] — [date_debut] au [date_fin]"
  - Nom de l'établissement partenaire
  - Email du partenaire (pour répondre directement)
  - Dates de la demande
  - Vélos demandés par type et quantité (ex: "2 × VAE Taille M, 1 × VTC Taille S")
  - Commentaire du partenaire (si renseigné)
  - Lien direct vers l'interface admin pour créer la réservation
- [ ] Ajouter `ADMIN_NOTIFICATION_EMAIL` dans `.env.example`

## Critères d'acceptation

- L'admin reçoit l'email après chaque nouvelle demande
- L'email contient suffisamment d'informations pour agir sans ouvrir l'app
- L'échec de l'envoi email ne bloque pas la création de la demande
- L'email est mis en queue (pas d'attente synchrone côté partenaire)
