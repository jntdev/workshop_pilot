# Feature 12 — Réservation location synchronisée

## Pourquoi faire évoluer l’écran Location ?
La V1 du panneau droit permettait de saisir une réservation en parallèle du calendrier mais sans aucun lien fonctionnel. Les opérateurs devaient jongler entre les colonnes pour retrouver quels vélos étaient libres, puis ressaisir manuellement les plages de dates dans le formulaire. La feature 12 introduit un workflow unifié : la sélection effectuée dans le calendrier nourrit automatiquement le formulaire et inversement.

## Périmètre clé
1. **Sélection interactive dans la grille**  
   - Cliquer (ou cliquer-glisser) sur une cellule « date × vélo » l’ajoute à une sélection courante.  
   - Les cellules restent sélectionnables même si le vélo est marqué HS ; l’interface affiche seulement un avertissement.
2. **Formulaire connecté**  
   - Les champs `date_reservation`, `date_retour`, `items` et les recap s’ajustent en temps réel quand la sélection change.  
   - Toute modification dans le formulaire (suppression d’un vélo, changement de dates) se reflète dans la sélection.
3. **Payload enrichi**  
   - L’appel POST `/api/reservations` inclut la liste détaillée des cellules sélectionnées pour historiser quels vélos ont été réservés et sur quelles dates.

## Hors périmètre immédiat
- Pas de validation automatique des conflits entre réservations (vérification manuelle par l’équipe).
- Pas de blocage sur les vélos HS (simple mention visuelle).
- Pas encore de génération de documents (devis/facture) ; la structure collectée doit néanmoins être prête pour ces évolutions.

## Dépendances
- Le calendrier React (`resources/js/Pages/Location/Index.tsx`) et le formulaire (`resources/js/Components/Location/ReservationForm.tsx`) partagent un store (contexte ou hook).
- Les données bikes proviennent de `config/bikes.php` pour l'affichage et de la table `bike_types` pour les types louables.

## Chargement des réservations (Feature 6)
Pour optimiser les performances, les réservations sont chargées selon une fenêtre glissante :
- **Chargement initial** : J−15 à J+30 (45 jours autour d'aujourd'hui)
- **Règles** :
  1. Réservations dont `date_reservation` est dans la fenêtre [J−15, J+30]
  2. OU réservations démarrées avant J−15 mais dont `date_retour` ≥ aujourd'hui (toujours actives)
  3. Exclusion des réservations avec statut `annule`
- **Lazy loading** : Endpoint `GET /api/reservations/window?start=YYYY-MM-DD&end=YYYY-MM-DD` pour charger des fenêtres supplémentaires quand l'utilisateur scroll au-delà de la période initiale.

## Livrables attendus
| # | Fichier/Module | Résultat attendu |
|---|----------------|------------------|
| 1 | `LocationIndex` | Mode sélection, état partagé, classes CSS pour les cellules sélectionnées |
| 2 | `ReservationForm` | Préremplissage auto, recap dynamique, warnings vélos HS |
| 3 | API `/api/reservations` | Champ `selection` accepté/validé en plus des données existantes |
| 4 | Documentation (présent dossier) | Instructions claires pour développeurs & QA |

Cette feature sert de base aux futures itérations (12.x) : ajout d’accessoires, contrôle de disponibilité, génération de devis. Le présent dossier décrit la cible fonctionnelle et UX pour livrer 12.0.
