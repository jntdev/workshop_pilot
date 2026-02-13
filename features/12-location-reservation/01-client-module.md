# 1 — Bloc client

## Recherche client existant
- Bandeau supérieur identique au moteur de recherche utilisé dans les devis (`ClientSearch`) avec auto-complétion sur `prenom`, `nom`, `email`, `telephone`.
- Résultat sélectionné affiche les métadonnées clés (nom complet, téléphone, mail) et verrouille le mini-formulaire pour éviter les divergences.
- Possibilité de désélectionner pour repartir à zéro si l'appel concerne un autre client.

## Création rapide d'un nouveau client (cas le plus fréquent)
- Formulaire minimal toujours visible avec les champs requis pour la base `clients` : `prenom`, `nom`, `telephone`, `email`.
- Validation immédiate (format mail/téléphone) et mise en évidence des champs obligatoires ; enregistrement déclenche la création dans la DB avant la sauvegarde de la réservation pour garantir un `client_id` valide.

## Détails facultatifs
- Accordéon "Plus de détails" replié par défaut donnant accès aux autres attributs du typage `Client` : `adresse`, `origine_contact`, `commentaires`, `avantage_type`, `avantage_valeur`, `avantage_expiration`.
- Les valeurs saisies sont stockées sur le client (pas uniquement sur la réservation) afin d'éviter les doublons d'informations.
- Un badge rappelle si un avantage est actif (type/valeur/expiration) pour guider la négociation commerciale.

## UX complémentaires
- Bouton "Créer un client" principal + spinner d'état pour signaler l'insertion.
- Messages flash intégrés au panneau pour confirmer la création ou signaler un doublon détecté.
