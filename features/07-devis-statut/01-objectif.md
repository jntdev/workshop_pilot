# 01 - Objectif

Définir le cycle de vie d’un devis et l’interface associée :
- Création modifiable (statut `brouillon`), puis bascule en lecture contrôlée (`prêt`, `modifiable`, `facturé`).
- Masquer les informations internes (marges, prix d’achat) dès qu’un devis n’est plus en brouillon.
- Empêcher tout retour en arrière depuis `facturé`.
