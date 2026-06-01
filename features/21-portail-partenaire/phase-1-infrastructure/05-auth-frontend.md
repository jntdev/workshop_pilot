# Ticket 21.1.5 : Authentification frontend partenaire

**Type** : Frontend
**Priorité** : Haute
**Estimation** : 1h30

## Description

Créer les pages d'inscription, de connexion et la gestion de session côté frontend partenaire. Le partenaire s'inscrit une fois avec son email whitelisté, puis se connecte avec email + mot de passe.

## Tâches

- [ ] Créer le hook `useAuth` :
  - `register(email, nom, password, passwordConfirmation)` — appelle `POST /api/partenaires/inscription`, stocke le token
  - `login(email, password)` — appelle `POST /api/partenaires/login`, stocke le token et les infos partenaire
  - `logout()` — appelle `POST /api/partenaires/logout`, vide le localStorage, redirige vers `/login`
  - `isAuthenticated` — boolean
  - `partenaire` — infos du partenaire connecté (nom, email)

- [ ] Créer le composant `ProtectedRoute` :
  - Redirige vers `/login` si non authentifié
  - Affiche un loader pendant la vérification initiale

- [ ] Créer la page `InscriptionPage` (`/inscription`) :
  - Champ email
  - Champ nom de l'établissement
  - Champ mot de passe
  - Champ confirmation mot de passe
  - Message d'erreur si email non autorisé : "Cette adresse email n'est pas autorisée à créer un compte"
  - Message d'erreur si email déjà inscrit : "Un compte existe déjà avec cette adresse"
  - Lien vers `/login` pour les partenaires déjà inscrits
  - Redirection vers `/dashboard` après inscription réussie

- [ ] Créer la page `LoginPage` (`/login`) :
  - Champ email + mot de passe
  - Message d'erreur si identifiants incorrects
  - Message d'erreur si compte inactif : "Votre compte a été désactivé, contactez-nous"
  - Lien vers `/inscription` pour les nouveaux partenaires
  - Redirection vers `/dashboard` après connexion réussie

- [ ] Créer le layout `PartenaireLayout` :
  - Header simple : nom de l'établissement + bouton déconnexion
  - Pas de menu complexe

## Règles UX

- Les pages inscription et login doivent être centrées, simples, sans distraction
- Le message d'erreur "email non autorisé" ne doit pas indiquer si l'email existe ou non en whitelist (sécurité)
- Après déconnexion, retour immédiat sur `/login`

## Critères d'acceptation

- Un partenaire whitelisté peut créer son compte et est immédiatement connecté
- Un email non whitelisté reçoit un message neutre et ne peut pas s'inscrire
- Un partenaire déjà inscrit peut se connecter avec email + mot de passe
- Un partenaire inactif voit un message clair
- Le token est persisté entre deux rechargements de page
- Une URL protégée sans session redirige vers `/login`
