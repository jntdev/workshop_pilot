# 04 - Etapes d'implementation

## Ordre de priorite

1. **Modele de donnees**
   - Remplacer la logique de statut par un indicateur simple (ex: `invoiced_at` nullable).
   - Migrer les devis existants : si ex-statut "facture", renseigner `invoiced_at`, sinon NULL.
   - Deprecier l'usage de `status` dans l'app.

2. **Regles metier**
   - Devis editable tant que `invoiced_at` est NULL.
   - Facture en lecture seule des que `invoiced_at` est defini.
   - Conversion uniquement via l'action "Transformer en facture".

3. **Formulaire devis**
   - Retirer select/badge de statut et messages associes.
   - Ajouter le bouton "Transformer en facture" + pop up de confirmation.
   - Apres confirmation, verrouiller l'edition et afficher un feedback.

4. **Liste des devis**
   - Afficher uniquement "Consulter" et "Supprimer".
   - Retirer la colonne de statut (ou afficher "Devis"/"Facture" si besoin).
   - ne pas afficher le bouton supprimer si il s'agit d'une facture

5. **Suppression**
   - Ajouter la confirmation cote UI.
   - la suppression n'est pas possible dans le cas de la facture

6. **Nettoyage et tests**
   - Supprimer l'ancien workflow (enum, validations, tests lies aux statuts).
   - Ajouter/adapter les tests de conversion et lecture seule.
