# 02 - Statuts & transitions

## Valeurs (libellés français)
- `brouillon` : formulaire complet modifiable (marges, prix d’achat visibles).
- `prêt` : devis validé côté client (lecture seule, infos sensibles masquées).
- `modifiable` : réouverture limitée (ajouts devant le client) mais toujours sans marges/prix d’achat visibles.
- `facturé` : devis verrouillé définitivement (lecture seule stricte). Impossible de revenir vers un autre statut.

## Transitions autorisées
- `brouillon → prêt` (via select + bouton valider).
- `prêt ↔ modifiable` : possible dans les deux sens pour ajouter des éléments sans dévoiler les marges.
- `prêt → facturé` : une fois facturé, **aucune transition retour**.
- `modifiable → prêt` : pour finaliser après modifications.
- `modifiable → facturé` : si besoin de facturer directement depuis le mode modifiable.

## Constraints
- `facturé` est un état terminal (aucune transition possible).
- `prêt` : lecture seule stricte (aucune modification de contenu, seul le changement de statut est autorisé).
- `modifiable` : édition complète autorisée mais champs privés (marges, prix d'achat) masqués.
- `brouillon` : édition complète avec tous les champs visibles.
- Impossible de passer en `facturé` si des lignes ont `purchase_price_ht = null` (voir 06-clarifications-techniques.md).***
