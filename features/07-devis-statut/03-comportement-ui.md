# 03 - Comportement UI

1. **Select statut**
   - Placé près du bouton de validation (dans l’entête du formulaire devis).
   - Options : `Brouillon`, `Prêt`, `Modifiable`, `Facturé`.
   - Changement de valeur déclenche une action (Livewire ou requête POST) qui met à jour le statut.
2. **Affichage selon statut**
   - `brouillon` : formulaire complet (tous les champs visibles).
   - `prêt` : vue lecture seule, seules les infos client/prix final apparaissent. Sections marges/prix achat cachées.
   - `modifiable` : édition partielle (mêmes champs visibles que `prêt`, sans marges). On peut ajouter des lignes, modifier quantités/prix clients.
   - `facturé` : tout en lecture seule (aucune modification).
3. **Quote lines**
   - Même si les marges/prix d’achat sont masqués en UI, ils restent requis côté validation/mode brouillon pour conserver la cohérence.
4. **Feedback**
   - Chaque changement de statut déclenche un message via le Feedback Banner (feature 04) : « Devis marqué comme prêt », « Devis facturé », etc.
5. **Confirmation passage en facturé**
   - Sélectionner `Facturé` ouvre une modale :  
     « Cette action lie le devis à une facture et à son paiement. Il sera impossible de revenir en arrière. »  
     Boutons : `Annuler` / `Confirmer`. Le statut ne change qu’après confirmation.
6. **Statut final**
   - Une fois confirmé, le devis reste en `facturé` définitivement : le select devient désactivé (ou verrouillé) et aucune transition n’est proposée.
   - Toutes les routes étant déjà protégées par authentification, aucun contrôle de permission supplémentaire n’est prévu pour cette feature.***
