# Ticket 21.1.4 : Initialisation du projet React standalone

**Type** : Frontend / Infrastructure
**Priorité** : Haute
**Estimation** : 1h

## Description

Créer le projet React standalone dans le sous-dossier `partenaires/` du monorepo. Ce projet est indépendant de l'application Laravel/Inertia existante. Il communique avec Laravel uniquement via API REST.

## Tâches

- [ ] Initialiser le projet dans `partenaires/` :
  ```bash
  cd partenaires && npm create vite@latest . -- --template react-ts
  ```
- [ ] Installer les dépendances nécessaires :
  - `react-router-dom` — routing
  - `axios` — client HTTP
- [ ] Configurer `vite.config.ts` :
  - Variable d'environnement `VITE_API_URL`
  - Proxy dev vers `http://localhost:8000` en développement
- [ ] Créer le fichier `.env.example` :
  ```
  VITE_API_URL=https://admin.lesvelosdarmor.bzh
  ```
- [ ] Mettre à jour `.gitignore` à la racine du monorepo :
  - Ajouter `partenaires/node_modules/`
  - Ajouter `partenaires/dist/`
- [ ] Créer la structure de dossiers :
  ```
  partenaires/src/
  ├── api/          # clients axios par domaine
  ├── components/   # composants réutilisables
  ├── pages/        # pages (Login, Dashboard, Demandes)
  ├── types/        # types TypeScript
  └── hooks/        # hooks personnalisés
  ```
- [ ] Créer le client API de base (`src/api/client.ts`) :
  - Instance axios avec `baseURL = VITE_API_URL`
  - Intercepteur pour injecter le token Bearer depuis localStorage
  - Intercepteur pour rediriger vers `/login` si 401
- [ ] Configurer le router React (`src/App.tsx`) avec les routes :
  - `/login`
  - `/dashboard`
  - Route protégée (redirect si non connecté)

## Critères d'acceptation

- `npm run dev` dans `partenaires/` démarre un serveur de développement
- `npm run build` génère un dossier `partenaires/dist/`
- Le client API injecte automatiquement le token sur chaque requête
- Une route sans token redirige vers `/login`
