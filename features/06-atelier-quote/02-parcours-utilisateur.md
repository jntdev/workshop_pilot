# 02 - Parcours utilisateur

## 1. Accès Atelier
- Depuis `/atelier`, bouton `Nouveau devis` menant à `/atelier/devis/nouveau`.
- Breadcrumb affiché : Atelier > Devis > Nouveau.

## 2. Bloc Client (onglet double)
1. **Onglet Recherche (par défaut)**
   - Champ recherche live (`nom`, `prénom`, `email`, `téléphone`).
   - Résultats listés sous le champ, clic = remplissage des inputs client (mais edits toujours possibles).
2. **Onglet Nouveau client**
   - Formulaire complet (prenom, nom, email, téléphone, adresse, code postal, ville, notes internes).
   - Validation live. Après sauvegarde, le client est persisté et devient le client associé au devis.
   - Si l'utilisateur modifie les champs client après avoir sélectionné un client existant, c'est la validation finale du devis qui déclenche l'update du client (pas à la volée).
3. Le bloc affiche en résumé : `Client sélectionné : <Nom> (modifier)`.

## 3. Bloc Prestations
- Table dynamique : colonnes `Intitulé`, `Référence`, `Prix achat HT`, `Prix vente HT`, `Marge (€)`, `Marge (%)`, `Prix vente TTC`, `Actions`.
- Chaque champ est éditable inline.
- Bouton « Ajouter une ligne » sous la dernière ligne (ajoute une ligne vide avec valeurs par défaut : TVA 20%, montants à 0).
- Icône suppression par ligne avec confirmation légère (tooltip "Supprimer la prestation ?").

### Règles de recalcul
- `PV TTC = PV HT × (1 + taux TVA)`.
- `Marge € = PV HT − PA HT`.
- `Marge % = (Marge € / PV HT) × 100` (masquer si PV HT = 0 pour éviter division).
- Modifier PV TTC recalculera PV HT et marges.
- Modifier PV HT recalculera PV TTC et marges.
- Modifier Marge € (ou %) recalculera PV HT puis PV TTC.
- Modifier Prix achat HT mettra à jour la marge €/%.

## 4. Résumé devis
- Bloc final affichant :
  - `Total HT` = somme PV HT lignes.
  - `TVA` = somme TVA par ligne.
  - `Total TTC`.
  - `Marge totale (€)` et `Marge moyenne (%)`.
  - Champ `Remise` (toggle montant/%) appliqué sur le total HT avant TVA.
  - Champ `Date de validité` (par défaut +15 jours).
- Les valeurs se mettent à jour automatiquement dès qu'une ligne est modifiée.

## 5. Actions
- Bouton principal « Enregistrer le devis » (statut brouillon) + bouton secondaire « Enregistrer et rester » (pour continuer les modifications).
- Bouton « Annuler » retour `/atelier`.
- Sur succès, feedback banner verte + redirection conditionnelle :
  - Si "Enregistrer", aller sur `/atelier/devis/{id}` (vue lecture).
  - Sinon rester sur le formulaire avec message "Brouillon mis à jour".

## 6. Page détail devis (lecture)
- Résumé client, liste prestations en lecture seule, totaux et marges.
- CTA « Modifier » + « Convertir en ordre de réparation » (placeholder futur).
