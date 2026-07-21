# Contexte — Contrat de location signé via QR code

## Problème actuel

La remise des vélos nécessite d'imprimer un contrat papier, de le remplir à la main, et de le faire signer sur place. C'est une double saisie par rapport à la réservation déjà enregistrée dans l'agenda, et ça impose une imprimante fonctionnelle au comptoir.

Certaines informations ne sont connues qu'au moment de la remise : accessoires remis (casques, sacoches, rétroviseurs, etc.) et montant de la caution.

## Objectif

Générer un contrat pré-rempli depuis la réservation, permettre à l'opérateur d'y ajouter les accessoires et la caution, puis faire signer le locataire sur son propre smartphone via un QR code affiché à l'écran. Le contrat signé est envoyé par email au client et archivé dans la réservation. Zéro impression.

## Document de référence

Le contrat existant (joint au ticket 24) sert de base au PDF généré. Il contient :
- Identification des vélos et équipements remis
- Durée de location (dates début / fin / heure de retour)
- Conditions tarifaires et retard
- Responsabilités loueur / locataire
- Dépôt de garantie
- Clause de non-responsabilité
- Bloc caution signé (montant + nom + signature locataire)
- Bloc signature loueur (nom + date + signature)
