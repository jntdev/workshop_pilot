# 4 — Finances & statut

## Montants
- Champ `prix_total_ttc` (input monétaire) obligatoire. Peut être prérempli automatiquement en fonction des vélos/accessoires sélectionnés, mais reste éditable.
- Champ `acompte` (input monétaire) avec placeholder dynamique `30 % de prix_total` (calculé à la volée). On autorise la saisie libre pour couvrir les cas particuliers.

## Gestion d'acompte
- Checkbox `Acompte demandé`.
  - Si cochée, affichage de deux éléments :
    - Label rappelant le montant attendu (`Acompte attendu : 180 €`).
    - Date-picker `Acompte payé le` (optionnel tant que le paiement n'est pas reçu).
  - Lorsque la date est renseignée, un badge "Acompte reçu" apparaît automatiquement.

## Statut global de la réservation
- Select `statut` avec les valeurs : `reservé`, `en_attente_acompte`, `en_cours`, `payé`, `annulé`.
- Règles dynamiques :
  - Si `statut = en_attente_acompte`, forcer la checkbox `Acompte demandé` et alerter si aucun acompte n'est saisi.
  - Si `statut = payé`, rendre obligatoire un champ `Paiement final reçu le` (date). Ce champ est distinct de `Acompte payé le` pour éviter les confusions.
  - Si `statut = annulé`, afficher un textarea "Raison d'annulation" pour garder un historique.

## Récapitulatif visuel
- Un encart récapitule les montants (Total / Acompte / Reste dû) et se synchronise avec le statut pour éviter les incohérences.
- Lorsque tous les montants requis sont renseignés, un check vert "Prêt à confirmer" apparaît sous le bouton de sauvegarde.
