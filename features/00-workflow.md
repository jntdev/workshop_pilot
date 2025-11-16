# 00 - Workflow git pour les features

Pour chaque feature décrite dans ce dossier :

1. **Créer une branche dédiée avant toute implémentation.**
   - Nom exact : `feature/<numero>-<slug>` (ex. `feature/01-client-form`).
   - Toujours partir de `develop` (sauf mention contraire explicite dans la feature).
   - Commande : `git checkout develop && git pull && git checkout -b feature/<numero>-<slug>`.
2. **Travailler uniquement dans cette branche** jusqu’au merge : migrations, tests, Livewire, SCSS, etc.
3. **Produire des commits atomiques**, dans l’ordre des étapes documentées (un commit par étape lorsque possible) en respectant strictement `features/CONVENTIONS.md`.
4. **Processus obligatoire avant chaque commit/push** :
   - Relire intégralement le dossier de la feature (tous les fichiers `0X-*.md`).
   - Pour chaque étape listée : vérifier concrètement que le code réalisé correspond (1 point par étape).
   - Exécuter tous les tests indiqués (backend, frontend, Livewire…) et s’assurer qu’ils passent. Si un test échoue, corriger avant de poursuivre.
   - Attribuer une note = nombre de points obtenus / nombre d’étapes. Autoriser commit/push uniquement si la note est maximale (toutes les étapes validées + tests verts).
   - Documenter dans le message de commit quels points ont été couverts si nécessaire.
5. **Pousser la branche** vers le remote (sans merge). Claude n’ouvre jamais de pull/merge request et ne merge jamais lui-même ; il se contente de `git push` pour que la revue soit faite par l’équipe.

Ainsi chaque feature dispose de son historique propre et peut être inspectée facilement dans Git. 
