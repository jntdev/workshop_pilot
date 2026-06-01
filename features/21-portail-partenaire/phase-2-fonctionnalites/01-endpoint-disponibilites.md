# Ticket 21.2.1 : Endpoint disponibilités (Laravel)

**Type** : Backend / API
**Priorité** : Haute
**Estimation** : 1h30

## Description

Créer l'endpoint qui retourne le nombre de vélos disponibles par type sur une plage de dates donnée. Le partenaire ne voit jamais les noms des vélos ni les détails des réservations existantes — uniquement un compte disponible.

## Logique de calcul

### Pourquoi ne pas utiliser `bike_types.stock`

`bike_types.stock` est calculé via `COUNT(*)` sans filtre sur `status` — il inclut les vélos HS. Il est utilisé dans l'interface admin pour afficher le stock total par type dans le formulaire de réservation. Le modifier casserait l'existant.

**Pour cet endpoint, le stock opérationnel est calculé à la volée** via un `COUNT` sur `bikes` filtré sur `status = OK`. `bike_types.stock` n'est pas utilisé ni modifié.

### Pourquoi ne pas utiliser `bikes.bike_type_id`

`bike_type_id` est un **accessor PHP calculé** dans `Bike.php` — il n'existe pas comme colonne en base. Il n'est donc pas utilisable dans une requête SQL. La jointure entre `bikes` et `bike_types` se fait via trois colonnes réelles : `bike_category_id`, `bike_size_id`, `frame_type` — exactement comme dans `syncBikeType()`.

### Formule

Pour chaque `bike_type` :

1. **Stock opérationnel** = `COUNT(*)` sur `bikes` où :
   - `bike_category_id` = celui du `bike_type`
   - `bike_size_id` = celui du `bike_type`
   - `frame_type` = celui du `bike_type`
   - `status = 'OK'`
2. **Réservés** = somme de `reservation_items.quantite` pour ce `bike_type_id`, sur les réservations actives qui chevauchent la plage
   - Statuts actifs : `reserve`, `en_attente_acompte`, `en_cours`, `paye`
   - Chevauchement : `date_reservation < date_fin` ET `date_retour > date_debut`
3. **Disponible** = max(0, stock_opérationnel − réservés)

### Approche d'implémentation

Itérer sur `BikeType::all()`, et pour chaque type :
- Compter les `Bike` OK via `bike_category_id + bike_size_id + frame_type` (même pattern que `syncBikeType()`)
- Sommer les `ReservationItem::where('bike_type_id', $type->id)` sur les réservations qui chevauchent

## Tâches

- [ ] Créer `PartenaireDisponibiliteController`
- [ ] Endpoint `GET /api/partenaires/disponibilites` (authentifié partenaire) :
  - Paramètres requis : `date_debut` (Y-m-d), `date_fin` (Y-m-d)
  - Validation : date_debut < date_fin, pas dans le passé, max 30 jours d'écart
  - Retourne pour chaque type : `{ type_id, label, stock_operationnel, reserve, disponible }`
- [ ] Calculer le stock opérationnel par jointure `bike_category_id + bike_size_id + frame_type` avec filtre `status = OK`
- [ ] Calculer les réservés via `ReservationItem` avec filtre sur `bike_type_id` et chevauchement de dates sur `Reservation`
- [ ] Exclure les réservations annulées du calcul
- [ ] Ne retourner que les types ayant au moins 1 vélo opérationnel

## Réponse attendue

```json
{
  "date_debut": "2026-06-01",
  "date_fin": "2026-06-07",
  "disponibilites": [
    { "type_id": "VAE_s", "label": "VAE Taille S", "stock_operationnel": 3, "reserve": 1, "disponible": 2 },
    { "type_id": "VAE_m", "label": "VAE Taille M", "stock_operationnel": 5, "reserve": 5, "disponible": 0 },
    ...
  ]
}
```

## Critères d'acceptation

- Le stock opérationnel est calculé à la volée, pas depuis `bike_types.stock`
- Les vélos HS ne sont pas comptés dans le stock opérationnel
- Les réservations annulées ne réduisent pas le disponible
- Le disponible ne peut jamais être négatif
- `bike_types.stock` et le comportement de l'interface admin existante sont inchangés
- Une plage invalide retourne une erreur 422 claire
- L'endpoint est protégé (401 sans token partenaire valide)
