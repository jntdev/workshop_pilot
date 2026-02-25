# Résultat ergonomique

## Header Toggle
- Commutateur horizontal (type slider) libellé `Atelier/Admin` à gauche, `Comptoir` à droite.  
- État par défaut : `Atelier/Admin`.  
- Tooltip : “Masque les infos sensibles pour afficher l'écran au client”.

## Mode Atelier/Admin
- Vue actuelle inchangée : colonnes marges, achats HT, KPI financiers visibles.  
- Boutons d'action complets (éditer coûts, appliquer remises, voir statistiques).

## Mode Comptoir
- Sections masquées :  
  - Dans les devis : colonnes `Prix achat HT`, `Marge`, `Taux`.  
  - Dashboards : cartes CA, marge, graphiques financiers.  
  - Réservation Location : champ `prix d'achat` (s'il existe), notes internes sensibles, champs financiers non pertinents.  
- Remplacement par placeholders : “—” ou icône cadenas, plus un message “Visible en mode Atelier”.  
- Boutons restreints : pas de modifications de prix d'achat, pas de duplication export interne.

## Feedback utilisateur
- Bannière subtile en haut : “Mode Comptoir actif – données sensibles masquées”.  
- En mode Atelier/Admin, bannière disparait.  
- Toggle reste accessible à tout moment.
