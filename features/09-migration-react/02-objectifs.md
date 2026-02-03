# 02 - Objectifs métier & produit

1. **Expérience unifiée**  
   Toutes les pages côté client (liste, création, édition, consultation des devis) doivent fonctionner dans Inertia/React, sans retour forcé vers Livewire.

2. **Sécurité & cohérence des APIs**  
   Les endpoints réutilisés par React doivent être authentifiés, documentés et renvoyer un contrat stable (structures JSON homogènes, gestion des erreurs standardisée).

3. **Feedback utilisateur fiable**  
   Les formulaires React doivent afficher systématiquement les erreurs de validation et les messages de succès/échec via le Feedback Banner.

4. **Livraison industrialisée**  
   Les assets Vite nécessaires (React + scripts hérités) doivent être correctement buildés, et la suppression de Livewire doit être progressive et sécurisée.

5. **Qualité mesurable**  
   Des tests automatisés (PHP + éventuels tests front) doivent couvrir les nouveaux flux pour éviter les régressions lors de la désactivation de Livewire.
