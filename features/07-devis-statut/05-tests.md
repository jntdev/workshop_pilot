# 05 - Tests & validations

1. **Tests backend**
   - Vérifier les transitions autorisées/ interdites (`facturé` terminal).
   - S’assurer que les tentatives d’édition de champs sensibles en `prêt` ou `facturé` sont rejetées.
2. **Tests Livewire/UI**
   - Rendu du formulaire selon chaque statut (sections visibles ou non).
   - Changement via le select déclenche la bonne mise à jour + feedback.
3. **Tests d’intégration**
   - Scenario complet : création (`brouillon`), passage en `prêt`, retour en `modifiable`, retour en `prêt`, passage en `facturé`, vérification qu’on ne peut plus modifier.
4. **Non régression quote lines**
   - Vérifier que les validations côté lignes de devis restent satisfaites même quand les inputs sont masqués (garder les valeurs en mémoire).***
