# Ticket 13.1 : Afficher les noms physiques des vélos dans les cartes planning

**Type** : Amélioration frontend / optimisation backend  
**Priorité** : Haute  
**Estimation** : 0,5 jour

## Problème actuel

Les cartes dans la vue "Voir aujourd'hui" affichent des **étiquettes génériques de type** (ex. « VAE Mixte B — M/L ») dérivées de `items.bikeType`. Ce libellé ne correspond à aucun vélo physique identifiable, ce qui oblige l'équipe à déduire quel vélo préparer parmi plusieurs exemplaires du même type.

## Objectif

Afficher les **noms propres des vélos physiques réservés** (ex. « Bosch 6 », « VAE_mb 3 »), tels qu'ils apparaissent déjà sur la grille annuelle, directement dans les cartes de la vue planning.

## Solution retenue

La colonne JSON `selection` sur la table `reservations` stocke déjà les `bike_id` nominatifs pour chaque créneau réservé. Format :

```json
[{"bike_id": 12, "dates": ["2026-07-01", "2026-07-02"], "is_hs": false}]
```

- 208 réservations sur 213 ont déjà ce champ renseigné (97 %).
- Le front-end reçoit déjà la liste complète des vélos avec leurs noms via `GET /api/location/full` (champ `bikes[]`), stockée dans le store agenda.
- Pas de migration ni de modification de schéma nécessaire.

## Changements techniques

### Backend — `LocationController::planning()`

**Fichier :** `app/Http/Controllers/Api/LocationController.php`

1. Retirer `items.bikeType` des eager loads des requêtes `$departures` et `$returns` (2 requêtes économisées).
2. Dans `formatReservation()` : remplacer le bloc `items` par `selection` (déjà présent sur le modèle).
3. Supprimer le champ `items` du tableau retourné — il n'est consommé par aucun autre composant.

```php
// Avant
$departures = Reservation::with(['client', 'items.bikeType'])…
// Après
$departures = Reservation::with(['client'])…
```

```php
// Avant dans formatReservation()
'items' => $r->items->map(fn ($item) => [
    'bike_type_id' => $item->bike_type_id,
    'quantite' => $item->quantite,
    'bike_type' => $item->bikeType ? [...] : null,
])->toArray(),

// Après
'selection' => $r->selection ?? [],
```

### Frontend — `PlanningPanel.tsx`

**Fichier :** `resources/js/Components/Location/PlanningPanel.tsx`

1. Supprimer l'interface locale `items: { bike_type_id, quantite, bike_type }[]`.
2. Remplacer le calcul de `bikesSummary` (qui groupait par `bike_type_id`) par une résolution via `selection` + le tableau `bikes` déjà chargé dans le store agenda.
3. Dégradation gracieuse : si `selection` est vide ou nul (5/213 réservations), afficher « Vélos non renseignés ».

Logique de résolution :

```tsx
// selection : Array<{ bike_id: number; dates: string[]; is_hs: boolean }>
// bikes : Bike[] déjà dans le store

const bikeNames = (reservation.selection ?? [])
  .map(s => bikes.find(b => b.id === s.bike_id)?.name)
  .filter(Boolean)
  .filter((name, i, arr) => arr.indexOf(name) === i); // déduplique
```

## Gains

| Avant | Après |
|---|---|
| 2 jointures `items` + `bikeTypes` par appel planning | 0 jointure supplémentaire |
| Libellés génériques de type (ex. « VAE Mixte B ») | Noms physiques (ex. « VAE_mb 3 ») |
| Champ `items` serialisé inutilement | Supprimé |

## Dégradation gracieuse

Pour les 5 réservations sans `selection`, la carte affiche « Vélos non renseignés » plutôt qu'un libellé vide ou erroné.

## Tests

- Vérifier que la route `/api/location/planning?date=<date>` ne retourne plus `items` dans le payload.
- Vérifier que `selection` est bien présent et contient des `bike_id` valides.
- Vérifier l'affichage des noms dans la vue planning pour une réservation avec `selection` et pour une sans.

## Critères d'acceptation

- [ ] `items.bikeType` n'est plus eager-loadé dans `planning()`.
- [ ] Le champ `items` n'est plus présent dans le payload JSON de la route planning.
- [ ] Le champ `selection` est présent dans le payload.
- [ ] Les cartes planning affichent les noms physiques des vélos (ex. « Bosch 6 »).
- [ ] Une réservation sans `selection` affiche « Vélos non renseignés » sans erreur JS.
- [ ] Aucune régression sur la grille annuelle ni sur le formulaire de réservation.
