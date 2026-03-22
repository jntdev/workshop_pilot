# Contexte — Suivi des paiements Location

- Aujourd'hui, le formulaire de réservation ne stocke qu'un champ `acompte` et une date `paiement_final_le`. Impossible de tracer plusieurs encaissements ni de savoir comment un groupe a réglé (CB, liquide, chèque).  
- Pour déclarer une réservation comme "Payée", l'opérateur doit bricoler : modifier le total, ajouter une note. Cela génère des incohérences et ne laisse aucune trace exploitable.  
- La Feature 15 introduit un vrai suivi des paiements : plusieurs encaissements, modes distincts, attribution "payé par" et total encaissement vs TTC.
