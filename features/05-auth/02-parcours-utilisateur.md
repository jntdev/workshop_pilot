# 02 - Parcours utilisateur

## Connexion (`/login`)
1. L'utilisateur arrive sur la page de login Fortify personnalisée (vue Blade + SCSS maison).
2. Formulaire email + mot de passe + bouton "Se connecter".
3. Validation côté serveur (Fortify) : email requis, format valide, mot de passe requis.
4. En cas d'échec : message d'erreur sous les champs + flash global.
5. En cas de succès : redirection vers le dashboard interne (`/dashboard`).

## Inscription (`/register`)
1. Page dédiée avec formulaire prénom, nom, email, mot de passe, confirmation.
2. Fortify utilise `CreateNewUser` pour valider et créer l'entrée dans `users`.
3. Après création, l'utilisateur est authentifié et redirigé sur `/dashboard`.
4. Afficher un encart pour rappeler que l'outil est réservé aux collaborateurs (texte statique côté vue).

## Mot de passe oublié (`/forgot-password`)
1. Formulaire unique : champ email + CTA "Envoyer l'e-mail".
2. Fortify envoie un lien de réinitialisation (mailtrap/local mail driver pendant le dev).
3. Afficher un message de confirmation même si l'email n'existe pas (comportement Fortify par défaut).

## Réinitialisation (`/reset-password/{token}`)
1. Formulaire email + nouveau mot de passe + confirmation.
2. À la soumission, Fortify vérifie le token puis met à jour le mot de passe.
3. Redirection immédiate vers `/login` avec message de succès.

## Déconnexion
- Bouton "Se déconnecter" disponible dans l'en-tête global (formulaire POST `logout`).
- Redirection vers `/login`.

## Gestion des erreurs UX
- Tous les formulaires affichent les erreurs validation sous chaque champ (`@error`).
- Utiliser un composant SCSS commun pour les alertes (succès/erreur) et les boutons principaux.
- Prévoir messages pour : identifiants invalides, mot de passe trop court, email déjà utilisé.
