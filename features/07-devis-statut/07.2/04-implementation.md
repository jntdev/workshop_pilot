# 04 - Etapes d'implementation

## Ordre de priorite

1. **Librairie PDF**
   - Utiliser `barryvdh/laravel-dompdf` (cible).
   - Valider la compatibilite avec le rendu HTML/CSS attendu.

2. **Templates PDF**
   - Creer un template devis PDF.
   - Creer un template facture PDF.
   - Reutiliser les styles essentiels (sans dependance JS).

3. **Generation**
   - Service unique pour construire les donnees et rendre le PDF.
   - Nom de fichier explicite (devis ou facture).
   - Pour un devis, garder un nom de fichier stable (pas de suffixe incremental).

4. **UI**
   - Ajouter les boutons "Telecharger PDF" sur devis et facture.
   - Prevoir un feedback utilisateur (succes ou erreur).

5. **Tests / validations**
   - Verifier que les PDFs se generent et contiennent les informations attendues.
