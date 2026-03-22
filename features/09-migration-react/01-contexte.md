# 01 - Contexte & enjeux

L'application est en pleine transition d'une interface Livewire historique vers une expérience Inertia/React. Plusieurs écrans critiques (clients, atelier/devis, location, dashboard) coexistent aujourd'hui entre Blade + Livewire et React, ce qui provoque :

- des flux utilisateurs fragmentés (liste clients en React mais création/édition en Livewire),
- des incohérences techniques (validations différentes, assets Vite absents des vues Blade, API publiques),
- et une absence de couverture de tests pour la nouvelle couche React.

La feature `09-migration-react` a pour objectif de **terminer la bascule** en identifiant puis réalisant tout ce qu'il reste à faire pour offrir une expérience unifiée, sécurisée et testée autour de React/Inertia, tout en retirant proprement les reliquats Livewire.
