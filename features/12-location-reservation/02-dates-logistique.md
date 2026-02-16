# 2 — Sélection calendrier & panneau logistique

Cette sous-feature (12.1) synchronise le calendrier de disponibilité avec le formulaire de réservation. Le/la chargé·e location sélectionne d’abord des cellules (couple `vélo + date`) puis complète les informations client/logistique/finances au sein du même écran.

## Objectifs
- Transformer la grille en outil de sélection : chaque clic ajoute/retire la cellule dans une « sélection courante » sans bloquer les vélos HS.
- Propager automatiquement la sélection vers le formulaire (dates, vélos, quantités) pour éviter la double saisie.
- Conserver la responsabilité humaine : on peut réserver un vélo HS ou créer des chevauchements si nécessaire, mais l’UI affiche les avertissements adéquats.

## Parcours utilisateur
1. L’opérateur clique « Nouvelle réservation » (ou équivalent) pour activer le **mode sélection**. Une sélection vide est créée et l’utilisateur est invité à cliquer dans le calendrier.
2. Il clique/glisse sur les cellules correspondant au/des vélos voulus et aux dates souhaitées. Chaque interaction met à jour la sélection courante.
3. Dès qu’au moins une cellule est sélectionnée :
   - Les champs `date_reservation` / `date_retour` se remplissent avec la date min et max de l’ensemble des cellules.
   - Le bloc « Vélos » du formulaire affiche une carte par vélo sélectionné, avec la plage de dates et le nombre de jours.
   - Le tag récapitulatif (déjà présent dans le formulaire) liste `quantité × libellé` en temps réel.
4. L’utilisateur complète ensuite les sections Client / Dates & logistique / Finances & statut.
5. Lorsqu’il valide, le payload inclut la structure de sélection (voir plus bas). Aucune vérification automatique n’empêche l’enregistrement d’un vélo HS ou d’un chevauchement : c’est au chargé·e de corriger au besoin.

## Sélection technique
- Stocker un état `reservationSelection` au niveau de `LocationIndex` :
  ```ts
  type SelectedCell = { bikeId: string; date: string; dayIndex: number; isHS: boolean };
  type SelectedBike = {
      bikeId: string;
      dates: Set<string>;
      minDate: string;
      maxDate: string;
      isHS: boolean;
  };
  interface ReservationSelection {
      id: string; // uuid
      bikes: Record<string, SelectedBike>;
      dayIndexes: Set<number>;
  }
  ```
- `handleCellClick(date, bikeId)` devient `toggleCell({ bikeId, date, dayIndex, isHS })` dès que `reservationMode` est actif. Pas d’interdiction sur `isHS`.
- Un hook partagé (`useReservationDraft`) expose :
  - `selectCell`, `clearSelection`, `removeBike`.
  - Selectors dérivés : `globalMinDate`, `globalMaxDate`, `selectedBikes[]`.
- Toute mise à jour déclenche un effet qui synchronise `ReservationForm` :
  - Prefill `date_reservation = globalMinDate`, `date_retour = globalMaxDate`.
  - Prefill `formData.items` avec `{ bike_type_id, quantite: 1 }` (ou plus tard, fusion par type).
  - Ajouter un tableau `selectionWarnings` listant les vélos HS (`isHS = true`) pour affichage.

## Formulaire & logistique
- Conserver la structure actuelle (sections Client, Dates & logistique, Vélos, Finances, Commentaires) mais :
  - Rendre `date_reservation` / `date_retour` éditables tout en avertissant si l’utilisateur sort de la plage sélectionnée (bannière jaune, mais pas d’empêchement).
  - Lorsque l’utilisateur supprime un vélo dans la section Vélos, appeler `removeBike(bikeId)` pour vider les cellules correspondantes.
  - Le récap (`reservation-form__recap`) devient la source unique de vérité pour « X vélos sur Y jours ».
- Logistique (Livraison / Récupération) reste inchangée : cases à cocher, bloc de champs obligatoires, préremplissage de la récupération avec l’adresse de livraison lorsque l’option est activée.

## Structure envoyée à l’API
À la soumission, ajouter au payload :
```json
{
  "selection": [
    {
      "bike_id": "vae-m-02",
      "start_date": "2026-04-03",
      "end_date": "2026-04-06",
      "dates": ["2026-04-03","2026-04-04","2026-04-05","2026-04-06"],
      "is_hs": false
    }
  ],
  "metadata": {
    "total_bikes": 1,
    "total_days": 4
  }
}
```
Le backend ne rejette pas les vélos HS ; il se contente d’enregistrer ce qui est envoyé.

## Acceptance criteria
1. **Sélection visuelle** : activer le mode réservation permet de cliquer n’importe quelle cellule (HS comprise). Les cellules sélectionnées affichent un état dédié, et l’en-tête du vélo montre qu’il est inclus.
2. **Synchronisation formulaire** : dates, liste de vélos et recap se mettent à jour automatiquement après sélection ; ces valeurs peuvent ensuite être ajustées manuellement.
3. **Bidirectionnalité** : retirer un vélo dans le formulaire retire immédiatement les cellules associées dans le calendrier (et inversement, `clearSelection` réinitialise le formulaire).
4. **Payload complet** : l’appel POST `/api/reservations` contient la clé `selection` détaillée ci-dessus en plus des champs client/logistique/finances existants.
5. **Vélos HS** : un vélo HS peut être sélectionné et envoyé. L’UI affiche toutefois un avertissement textuel dans le formulaire pour inciter à choisir un autre vélo.
