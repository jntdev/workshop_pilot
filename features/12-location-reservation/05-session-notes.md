# Session Notes — Feature 12

## Décisions prises
1. **Store partagé calendrier ↔ formulaire**  
   - Implémenter un hook `useReservationDraft` exposant l’état de sélection et les actions (sélection cellule, suppression vélo, reset).  
   - `LocationIndex` et `ReservationForm` se branchent dessus au lieu de gérer chacun leur `useState`.

2. **Vélos HS sélectionnables**  
   - Pas de blocage : seules des alertes visuelles sont affichées dans la grille et le formulaire.

3. **Accessoires génériques**  
   - En attendant un inventaire précis, un simple tableau de configuration suffit. Les quantités sont stockées dans `formData.accessories`.

4. **Payload enrichi**  
   - Ajout d’une clé `selection` (détaillée dans `02-dates-logistique.md`).  
   - Backend doit loguer les anomalies mais n’empêche pas la création.

## TODO techniques
- [x] Créer le hook `useReservationDraft` (`resources/js/hooks/useReservationDraft.ts`).
- [x] Ajouter les classes CSS `location-table__cell--selected` et `location-table__cell--selectable`.
- [x] Étendre le contrôleur API pour accepter `selection` (migration + model + validation).
- [ ] Écrire des tests Feature pour vérifier la création avec nouveau client + sélection.

## Questions ouvertes
- Doit-on gérer la sélection multi-périodes pour un même vélo (ex. 3–5 avr et 10–12 avr) ? Pour l’instant on impose une plage unique par vélo.  
- Comment afficher les conflits s’il existe déjà une réservation sur la même plage ? (non prévu en 12.0).
