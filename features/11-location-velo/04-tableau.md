# 4 - Tableau des disponibilités

## Structure générale
- Colonnes générées à partir du fichier de configuration ; l'ordre suit la flotte (VAE avant VTC, tailles croissantes).
- 365 lignes pour l'année en cours, créées via un utilitaire `generateYearDays(year)` et mémorisées pour limiter les recalculs.
- En-tête de ligne : date complète (jour, numéro, mois abrégé) + icône discrète pour signaler les jours fériés (prévu mais non bloquant).

## Cellules
- Organisation verticale avec un bandeau supérieur (1/3) qui s'allume en couleur accentuée quand `date === today`.
- Zone principale (2/3) prête à afficher l'état (`Disponible`, `Pré-réservé`, etc.) sous forme de tag coloré ; pour le MVP tout reste en état "Disponible" neutre.
- L'ensemble de la cellule est cliquable (cursor pointer) et déclenche un événement `onSelectDayBike({ date, bikeId })` qui sera capté par le panneau de droite.

## Virtualisation & ergonomie
- Utilisation combinée de `useReactTable` + `useVirtualizer` pour ne rendre que les lignes visibles.
- Sticky headers (colonnes + premières colonnes dates) pour conserver le contexte lorsque l'utilisateur scroll.
- Largeur minimale par colonne liée aux tailles d'écran ; si la flotte dépasse la largeur disponible, défilement horizontal activé mais limité grâce au ratio 75 %.

## Données futures
- Préparation d'un hook `useBikeAvailability(year)` qui retournera la matrice d'état (initialement générée localement), ce qui simplifiera le branchement ultérieur au backend de réservation.
