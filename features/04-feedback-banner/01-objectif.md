# 01 - Objectif

Créer un composant d’alerte transversal (card/toast) affiché sous le header pour toutes les pages.

- Deux états : **succès** (vert) lorsque la base confirme une action, **erreur** (rouge) lorsqu’une validation ou une exception survient.
- Le message doit rester visible même après redirection (ex. soumission d’un formulaire menant à une autre page) puis disparaître automatiquement au bout de 3 secondes.
- Pas d’animation complexe pour l’instant : apparition/disparition simples.
- Composant commun réutilisable dans tous les formulaires du projet (Livewire ou classiques).***
