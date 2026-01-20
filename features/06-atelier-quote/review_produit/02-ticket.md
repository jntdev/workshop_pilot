# Ticket complémentaire – Verrouillage statuts devis

## Problèmes ouverts
1. **Transitions statutaires incorrectes**
   - `QuoteStatus::Editable` autorise un retour direct vers `brouillon` alors que le workflow 07 impose `prêt` comme étape obligatoire.
   - Les tests backend valident cette transition erronée.
2. **Absence de confirmation/modale en passage `facturé`**
   - Le select change immédiatement le statut. Il faut insérer une modale de confirmation bloquante avant `changeStatus('facturé')`.
3. **Blocage `save()` incomplet**
   - Le composant Livewire empêche uniquement la sauvegarde en statut `prêt`. `facturé` reste modifiable via POST. Doit refuser tout `save()` dès que `canEdit()` est faux.
4. **Badge “À compléter” invisible en mode modifiable**
   - Les lignes ajoutées sans prix d’achat n’affichent aucune alerte lorsque la colonne PA est masquée (mode modifiable). Il faut afficher un badge ou une mention dans la ligne, quel que soit le statut.
5. **Tests manquants**
   - Ajouter un test Feature pour les transitions (`prêt ↔ modifiable`, blocage `modifiable → brouillon`, modale facturation).
   - Ajouter un test Livewire vérifiant que les sections passent en lecture seule selon le statut et que l’alerte ligne incomplète est visible même sans champs PA.

## Acceptance
- Workflow 07 respecté (pas de transition directe `modifiable → brouillon`).
- Modale de confirmation apparait avant changement `facturé` et le select devient inactif après validation.
- `save()` impossible dès que le statut courant n’autorise pas l’édition.
- Badge “Prix d’achat à compléter” visible en mode modifiable et prêt (lecture seule) pour les lignes concernées.
- Nouveaux tests couvrent les scénarios ci-dessus.
