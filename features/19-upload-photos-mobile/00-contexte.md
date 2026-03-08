# Feature 19 : Upload photos depuis mobile via QR Code

## Contexte

L'atelier et le comptoir ont besoin de partager des photos rapidement :
- Photo d'un velo endommage pour montrer a l'autre poste
- Photo d'une piece a commander
- Photo d'un probleme technique a documenter
- Futur : pieces jointes pour les devis envoyes par email

## Probleme actuel

Les photos sont sur le smartphone, l'application tourne sur le PC de l'atelier/comptoir. Pas de moyen simple de transferer les images sans passer par email, cloud, ou cable USB.

## Solution proposee

Systeme de transfert via QR Code :
1. PC : clic sur "Ajouter photo depuis mobile" → affiche un QR code
2. Mobile : scan du QR → ouvre une page d'upload dediee
3. Mobile : selection/prise de photos (multiple)
4. Mobile : compression automatique cote navigateur
5. Mobile : upload vers le serveur
6. PC : reception en temps reel des images

## Avantages

- Aucune app a installer sur le telephone
- Fonctionne avec n'importe quel smartphone
- Compression automatique (photos de 5MB → ~300KB)
- Selection multiple possible
- Reutilisable pour les devis (feature future)
