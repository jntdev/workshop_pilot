# Review technique – suivi du temps atelier

## 1. Calcul du total estimé côté frontend
- `resources/js/Pages/Atelier/Quotes/Form.tsx` calcule `totalEstimatedTimeMinutes` via `lines.reduce(...) || null`. Dès que la somme vaut `0` (toutes les lignes saisies à 0 h, ou combinaison d’heures positives + 0 sur la dernière ligne), l’opérateur `||` renvoie `null`.
- Conséquences :
  - Le total affiché passe à `-` même si l’utilisateur a renseigné “0h”.
  - Le payload envoyé à l’API transmet `null`, alors que le backend conserverait `0` (cf. `calculateTotalEstimatedTime`).
- À corriger : reproduire la logique backend (somme des minutes, puis `return $hasAnyTime ? $total : null`). En React, conserver un flag `hasAnyEstimated` plutôt que d’utiliser `|| null`.

## 2. Édition du temps réel sur facture
- L’API expose `PATCH /api/quotes/{quote}/actual-time` (routes/api.php) pour modifier `actual_time_minutes` sur un document déjà facturé.
- L’UI facture reste verrouillée (`isReadOnly === true` supprime les boutons de sauvegarde) et le champ “Temps réel (h)” dans `QuoteTotals` ne déclenche qu’un `setActualTimeMinutes` local. Aucun appel HTTP n’est fait vers la route PATCH.
- Résultat : impossible de saisir le temps réel après facturation, qui était précisément l’objectif.
- À prévoir :
  - soit un bouton dédié “Enregistrer le temps réel” sur la page show/édit facture (appels PATCH),
  - soit un formulaire simplifié autorisé en lecture seule lorsque seul `actual_time_minutes` change.

## 3. Tests
- `tests/Feature/QuoteTimeTrackingTest.php` couvre bien les calculs backend, mais il manque un test pour l’endpoint PATCH et pour vérifier qu’on peut modifier `actual_time_minutes` sur une facture. Ajouter une méthode `it_updates_actual_time_on_invoice()` aiderait à sécuriser ce flux.
