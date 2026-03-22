# 1 - Stack technique

- **React + TanStack Table v8** pour construire la grille, profiter des colonnes dynamiques et des hooks de virtualisation.
- **@tanstack/react-virtual** pour n'afficher qu'un sous-ensemble des 365 lignes et maintenir un défilement fluide.
- **TypeScript** afin de garantir que la définition des vélos (type, taille, libellé) reste alignée avec les colonnes générées.
- **Design System interne / SCSS existant** : réutiliser les tokens d'espacement, couleurs d'état et règles BEM décrites dans `features/CONVENTIONS.md` pour le styling.
- **Gestion d'état locale (React Context ou Zustand léger)** pour synchroniser le clic sur une cellule avec le panneau d'édition, sans surcharger le store global tant que la persistance n'est pas connectée.
