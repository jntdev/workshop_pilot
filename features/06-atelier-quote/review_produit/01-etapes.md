# Revue produit – actions de clôture

1. **Verrouiller l'UI selon le statut**
   - En `prêt` : formulaires en lecture seule; seules actions statut autorisées.
   - En `modifiable` : champs sensibles (PA/marges) masqués, mais possibilité d'ajouter/éditer lignes visibles.
   - En `facturé` : formulaire totalement verrouillé (inputs disabled + `save()` refuse).
2. **Mise à jour client seulement après validation**
   - Conserver la logique actuelle mais s'assurer que les edits client ne s'appliquent qu'après `save()` et refléter ce comportement dans la vue (bannière rappel).
3. **Gestion des lignes incomplètes**
   - Autoriser `purchase_price_ht = null` quand statut ≠ brouillon.
   - Ajouter badges "À compléter" visibles même quand PA masqué et empêcher facturation tant que lignes incomplètes.
4. **Confirmation passage en `facturé`**
   - Ajouter modale (feature 07) + double confirmation; select doit se désactiver après facturation.
5. **Corriger calculs de remise**
   - `QuoteCalculator::applyDiscount()` doit recalculer TVA et TTC après remise (actuellement TVA reste pleine).
6. **Tests à compléter**
   - Feature tests transitions (brouillon→prêt→modifiable→facturé), blocage si lignes incomplètes, interdiction de retour.
   - Tests Livewire UI : rendu des sections selon statut, confirmation facturation, badge lignes incomplètes.
   - Tests unitaires sur calculs remise + arrondis.
7. **Checklist QA finale**
   - Vérifier flux complet : création brouillon → passage prêt → ajout ligne modifiable → retour brouillon pour compléter → facturation.
   - Capturer screenshot du select statut + modale pour la revue produit.
