# 1 — Module client connecté

## Objectifs
- Identifier rapidement un client existant via `ClientSearch`.
- Créer ou mettre à jour un client sans quitter l’écran.
- Propager automatiquement l’`client_id` (ou les données du nouveau client) vers le payload de réservation.

## Flux cible
1. **Recherche**  
   - `ClientSearch` (déjà utilisé côté Atelier) reste le point d’entrée.  
   - Lorsqu’un client est sélectionné, un encart affiche ses coordonnées et verrouille les champs minimaux pour éviter les incohérences.  
   - Un bouton « Changer de client » réinitialise la sélection et remet les champs au mode “nouveau client”.
2. **Création / mise à jour**  
   - Les champs requis (`prenom`, `nom`, `telephone`) restent visibles en permanence.  
   - Les autres attributs (`email`, `adresse`, `origine_contact`, `commentaires`, `avantage_*`) sont regroupés dans un accordéon “Plus de détails”.  
   - Si aucun client n’est sélectionné mais que les champs requis sont remplis, le POST `/api/reservations` envoie `new_client`.  
   - Si un client est sélectionné et que les champs changent, l’appel inclut `update_client`.
3. **Retour utilisateur**  
   - Messages inline pour signaler la création ou les erreurs de validation.  
   - Tag vert “Client existant sélectionné” ou “Nouveau client – sera créé”.

## Points d’attention
- Les validations front doivent refléter `StoreReservationRequest` (formats téléphone/email, champs obligatoires).  
   Toute erreur provenant de l’API est mappée vers les champs `client_*`.
- Ne jamais bloquer la soumission si le client n’est pas encore créé : la création se fait au moment du POST principal.
- Prévoir un futur flag “client pro” mais hors-champ pour 12.0.
