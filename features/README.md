# Mémoire métiers & chapitres

## Vision globale
Notre application couvre trois pôles principaux :

1. **Clients (pôle transverse)**  
   - Gestion centralisée des clients utilisés par l’atelier, la vente/conversion et la location.  
   - Formulaire Livewire (feature `02-client-management`) + validations backend/Livewire + tests.  
   - Le formulaire doit être intégré sur la page `/clients` et réutilisé partout où un client est créé.

2. **Atelier**  
   - Englobe la réparation, la vente et la conversion (traitées comme sous-sections de l’atelier).  
   - Routage et pages préparés via la feature `01-frontend-routing`.  
   - Futur contenu : gestion des dossiers atelier, suivi réparation/vente, etc.

3. **Location (courte & longue durée)**  
   - Chapitre dédié aux offres de location.  
   - Routes/pages skeleton prêtes via `01-frontend-routing`, styles spécifiques prévus dans `resources/scss/location`.

## Dashboard
- La page d’accueil (`/`) sert de **dashboard**. Elle présente les accès rapides vers Clients, Atelier, Location et accueillera plus tard des métriques/statistiques.
- Les styles dashboard sont centralisés dans `resources/scss/home/_dashboard.scss`.

## Design & feedback
- Styles SCSS : dossiers par chapitre (`home`, `clients`, `atelier`, `location`) et composants communs (`resources/scss/components`).  
- **Feedback Banner** (feature `04`) : composant global affichant les messages succès/erreur sous le header, utilisé par tous les formulaires (Livewire ou classiques).

## Workflow
- Chaque feature documentée (dossier `features/NN-*`) doit être réalisée via une branche `feature/<numero>-<slug>` créée depuis `develop`, en respectant `features/00-workflow.md` et `features/CONVENTIONS.md`.
- Les tests (backend + frontend) sont obligatoires et doivent réussir avant tout commit/push.

Ce fichier fait office de mémoire partagée : maintenir à jour les informations métiers et l’articulation des chapitres à mesure que l’application évolue.***
