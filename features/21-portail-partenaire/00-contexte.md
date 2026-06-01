# Feature 21 : Portail partenaire

## Contexte

L'atelier travaille avec des hôtels partenaires qui souhaitent proposer la location de vélos à leurs clients. Aujourd'hui, ces demandes arrivent par téléphone ou email, sans structure, et doivent être saisies manuellement dans l'agenda location.

L'objectif est de donner à chaque hôtel partenaire un espace dédié pour soumettre ses demandes de réservation de manière autonome, structurée et traçable.

## Décisions d'architecture

### Sous-domaine séparé
Le portail partenaire est une application React standalone hébergée sur `partenaires.lesvelosdarmor.bzh`. Elle est distincte de l'application admin (`admin.lesvelosdarmor.bzh`) pour trois raisons :
- Aucun risque de régression sur l'auth existante (Google OAuth)
- Interface épurée, sans les menus et fonctionnalités de l'admin
- Déploiement indépendant

### Monorepo
Le projet React partenaire est hébergé dans le sous-dossier `partenaires/` du même dépôt Git. Cela permet de travailler dans un seul contexte, de partager des types TypeScript si nécessaire, et de pousser en un seul commit.

### Auth partenaire indépendante
Les partenaires n'ont pas nécessairement de compte Google. Ils s'authentifient avec email + mot de passe. L'admin whiteliste l'email du partenaire, et le partenaire crée lui-même son compte via la page d'inscription. L'admin ne gère jamais de mot de passe. L'API d'auth est portée par Laravel Sanctum avec un guard dédié.

### Pas d'assignation automatique de vélos
Le portail partenaire ne montre pas l'agenda ni les vélos nominatifs. Il expose uniquement un nombre de vélos disponibles par type (VAE S/M/L/XL, VTC S/M/L/XL) sur une plage de dates. L'assignation réelle des vélos reste manuelle côté admin.

### Workflow de traitement
1. Le partenaire soumet une demande (dates + types + quantités + commentaire)
2. L'admin reçoit un email avec toutes les informations nécessaires
3. L'admin crée manuellement la réservation dans le calendrier location
4. L'admin notifie le partenaire par email de la confirmation ou d'un ajustement

## Hors périmètre (v1)

- Assignation automatique de vélos depuis le portail
- Paiement en ligne
- Intégration directe dans le calendrier admin
- Multi-utilisateurs par partenaire
- Réinitialisation de mot de passe autonome (à prévoir en v2)
