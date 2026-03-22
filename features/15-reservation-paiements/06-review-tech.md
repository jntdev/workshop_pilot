# Review technique — Feature 15

## 1. Badge de paiement incohérent (front)
- Fichier : `resources/js/Components/Location/ReservationForm.tsx:194-199`  
- Le calcul du badge fait `if (totalEncaisse >= total) return 'success'; if (totalEncaisse > total) return 'error';`.  
- Comme `>=` est évalué avant `>`, le cas “trop perçu” n’est jamais coloré en rouge et reste en vert.  
- Correction : tester `> total` d’abord, puis `=== total`, sinon l’utilisateur ne voit pas l’erreur malgré le message “Trop perçu”.

## 2. Total encaissé divergent front/back
- Front : `totalEncaisse = somme(payments.amount) + acomptePaye` (DTO dans `ReservationForm.tsx:170-189`).  
- Backend : `Reservation::totalPaid()` ne somme que la table `reservation_payments` (champ `acompte_montant` ignoré).  
- Une réservation avec acompte payé mais sans ligne de paiement héritée aura `total_paid = 0` côté API alors que le formulaire affiche un encaissement.  
- Action : convertir l’acompte en ligne de paiement lors de la persistance (ou inclure l’acompte dans `totalPaid()`), pour garder la cohérence des données exposées (API, Tableau Location, exports).

## 3. Statut “Payé” non vérifié serveur
- `StoreReservationRequest` / `UpdateReservationRequest` valident les paiements mais n’imposent pas que `statut === 'paye'` ⇔ `total paiements >= prix_total_ttc`.  
- Le front bloque visuellement, mais un appel direct à l’API peut enregistrer une réservation “payée” sans encaissement.  
- Recommandation : ajouter un `withValidator` qui calcule `array_sum(payments.amount)` et rejette l’enregistrement si `statut = paye` mais total insuffisant (et l’accepter si total ≥ TTC).
