# Contraintes techniques

1. **Toggle global**  
   - Bouton slider dans le header principal (à droite du titre) libellé `Atelier/Admin` (mode actif) ↔ `Comptoir`.  
   - Stocker l'état côté front (LocalStorage) pour persistance entre pages.

2. **Feature flag UI**  
   - Les composants sensibles doivent consommer ce flag (`usePrivacyMode()`).  
   - Mode Comptoir : masquer marges, prix achat HT, montants internes, KPI dashboards.  
   - Mode Atelier/Admin : tout afficher.

3. **Devis / ateliers**  
   - Dans les pages de devis et fiches atelier, masquer colonnes `achat HT`, `marge`, `coût` en mode Comptoir.  
   - Les valeurs doivent rester dans le DOM uniquement si nécessaire (éviter de les rendre invisibles via CSS pour limiter le survol). Idéalement, ne pas les injecter dans les props ou utiliser placeholders `•••`.

4. **Exports / impressions**  
   - Mode Comptoir s'applique aussi aux PDF ou aperçus si l'action est déclenchée quand le mode est actif (sinon risque de fuite).  
   - À documenter (hors périmètre initial si trop complexe).

5. **Performance**  
   - Le mode est purement visuel : pas de changement d'autorisation backend.  
   - S'assurer que la bascule ne déclenche pas un reload complet (toggle doit être instantané).
