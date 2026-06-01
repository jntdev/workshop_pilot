# Résultat ergonomique attendu

## Flow 1 — Connexion partenaire

1. L'admin ajoute l'email du partenaire dans la whitelist.
2. Le partenaire se rend sur `partenaires.lesvelosdarmor.bzh/inscription`.
3. Il saisit son email, le nom de son établissement et choisit son mot de passe.
4. Son compte est créé et il est redirigé vers son dashboard.
4. Il accède à son dashboard.

## Flow 2 — Soumettre une demande

1. Depuis le dashboard, le partenaire accède au formulaire de demande.
2. Il renseigne :
   - Les dates souhaitées (date de début et date de fin)
   - Le nombre de vélos par type souhaités
   - Un commentaire optionnel (ex : livraison souhaitée, contexte particulier)
3. Le formulaire lui indique combien de vélos de chaque type sont disponibles sur ces dates.
4. Il soumet la demande.
5. Il reçoit un message de confirmation : "Votre demande a bien été transmise. Nous reviendrons vers vous rapidement."

## Flow 3 — Suivre ses demandes

1. Depuis le dashboard, le partenaire consulte la liste de toutes ses demandes.
2. Chaque demande affiche :
   - Les dates
   - Les vélos demandés
   - Le statut : En attente / Confirmée / Annulée
3. Il peut consulter le détail d'une demande.

## Règles UX

- L'interface doit être simple et utilisable sans formation.
- Le vocabulaire doit être orienté client final (pas de jargon interne).
- Le partenaire ne voit jamais les réservations des autres partenaires ni les détails de l'agenda.
- Les disponibilités sont exprimées en nombre de vélos, jamais en noms de vélos.

## À préciser avant le développement (2.3)

Le formulaire de demande (étape 2 du flow 2) nécessite une session de cadrage produit avant d'être développé :
- Enjeux métier : que peut/ne peut pas demander le partenaire ?
- UX : tunnel de saisie, retours visuels, gestion des indisponibilités partielles
- UI : maquette ou description visuelle précise
