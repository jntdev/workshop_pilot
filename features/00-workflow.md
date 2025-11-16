# 00 - Workflow git pour les features

Pour chaque feature décrite dans ce dossier :

1. **Créer une branche dédiée avant toute implémentation.**
   - Nom conseillé : `feature/<numero>-<slug>` (ex. `feature/01-client-form`).
   - Commande : `git checkout -b feature/<numero>-<slug>`.
2. **Travailler uniquement dans cette branche** jusqu’au merge : migrations, tests, Livewire, SCSS…
3. **Commits clairs et atomiques** en suivant les étapes décrites dans la feature.
4. **Avant tout commit**, relire le dossier de la feature :
   - Vérifier chaque étape attendue (chaque étape = 1 point).
   - Attribuer une note sur le nombre total d’étapes et n’autoriser commit/push que si toutes les étapes sont conformes (note maximale).
   - Si un point manque, corriger avant de committer.
5. **Vérifier les tests** (backend et/ou frontend) avant de proposer la branche.
6. **Soumettre la branche** (merge request / pull request) pour revue afin de visualiser l’ensemble des modifications.

Ainsi chaque feature dispose de son historique propre et peut être inspectée facilement dans Git. 
