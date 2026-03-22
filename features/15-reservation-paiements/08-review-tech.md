# Review technique n°08 — Suivi des paiements

## 1. Badge de paiement (front)
- `ReservationForm.tsx:194-199` utilise désormais un simple `if (totalEncaisse === total) return 'success'; else return 'warning';`.  
- Le cas “trop perçu” n’est plus identifié visuellement : un encaissement > TTC reste en orange alors qu’il devrait être signalé en rouge pour cohérence avec le message “Trop perçu”.  
- Action : réintroduire un test `if (totalEncaisse > total) return 'error'` avant la clause `===`.

## 2. Convergence front/back
- `Reservation::totalPaid()` inclut maintenant les acomptes payés (`acompte_paye_le` + `acompte_montant`).  
- Les données exposées (API, tableau Location) correspondent enfin à ce que le formulaire calcule. ✅

## 3. Validation statut “Payé”
- `Store/UpdateReservationRequest` ajoutent un `withValidator` qui vérifie que `statut = paye` ⇒ somme paiements + acompte ≥ TTC.  
- L’incohérence serveur est réglée. ✅

## Conclusion
- Il reste uniquement le point 1 (badge rouge) à corriger pour aligner totalement l’UX avec l’état réel des paiements.
