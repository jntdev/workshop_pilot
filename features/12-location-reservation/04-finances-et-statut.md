# 4 — Finances & statut

## Champs principaux
- `prix_total_ttc` (obligatoire)  
  - Input numérique avec suffixe `€`.  
  - Message d’aide si le montant est incohérent (ex. inférieur à 0).  
  - Peut être prérempli via les gabarits tarifaires mais reste éditable.
- `acompte_demande` (checkbox) + `acompte_montant` + `acompte_paye_le`  
  - Placeholder dynamique `30 % = {suggestedAcompte} €`.  
  - Si `acompte_demande === true` et `acompte_montant === ''`, afficher un warning non bloquant.
- `statut` (select) : `reserve`, `en_attente_acompte`, `en_cours`, `paye`, `annule`.
- `paiement_final_le` : obligatoire uniquement si `statut === 'paye'`.
- `raison_annulation` : obligatoire si `statut === 'annule'`.

## Règles dynamiques
| Statut | Effets |
|--------|--------|
| `en_attente_acompte` | force `acompte_demande = true` |
| `paye` | affiche `paiement_final_le` + badge “Paiement reçu” si rempli |
| `annule` | affiche textarea `raison_annulation` |

## Récap et CTA
- Le bloc récap (`reservation-form__summary`) affiche Total / Acompte / Reste dû.  
- Le tag “Prêt à confirmer” apparaît si : client valide, dates présentes, au moins un vélo, `prix_total_ttc > 0`.  
- Bouton principal : `Enregistrer la réservation` (désactivé si `isSaving` ou si les conditions minimales ne sont pas remplies).

## Payload attendu côté API
```json
{
  "prix_total_ttc": 450,
  "acompte_demande": true,
  "acompte_montant": 135,
  "acompte_paye_le": "2026-04-12",
  "paiement_final_le": "2026-04-20",
  "statut": "en_attente_acompte",
  "raison_annulation": null,
  "selection": [...],
  "items": [...],
  "accessories": [...]
}
```
L’API conserve les règles actuelles (`StoreReservationRequest`) mais accepte désormais `selection` et `accessories`.
