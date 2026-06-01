# Ticket 21.4.1 : Déploiement du portail partenaire

**Type** : DevOps / Infrastructure
**Priorité** : Haute
**Estimation** : 2h

## Description

Configurer le déploiement de l'application React standalone sur le sous-domaine `partenaires.lesvelosdarmor.bzh`, indépendamment de l'application Laravel admin.

## Tâches

### Build
- [ ] Créer `partenaires/.env.production` :
  ```
  VITE_API_URL=https://admin.lesvelosdarmor.bzh
  ```
- [ ] Vérifier que `npm run build` dans `partenaires/` génère un `dist/` fonctionnel

### Serveur
- [ ] Configurer un vhost nginx pour `partenaires.lesvelosdarmor.bzh` :
  - Pointe vers `partenaires/dist/`
  - Redirige toutes les routes vers `index.html` (SPA routing)
  - HTTPS via Let's Encrypt
- [ ] Configurer CORS côté Laravel pour accepter `https://partenaires.lesvelosdarmor.bzh` en production

### Variables d'environnement Laravel
- [ ] Ajouter dans `.env` production :
  ```
  ADMIN_NOTIFICATION_EMAIL=...
  ```

### DNS
- [ ] Ajouter l'entrée DNS `partenaires.lesvelosdarmor.bzh` pointant vers le serveur

## Critères d'acceptation

- `https://partenaires.lesvelosdarmor.bzh/login` est accessible
- Les requêtes API depuis le portail arrivent bien sur Laravel sans erreur CORS
- Le routing SPA fonctionne (rafraîchissement de page sur `/dashboard` ne donne pas de 404)
- HTTPS actif avec certificat valide
