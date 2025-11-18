# 05 - Checklist d'exécution

## Préparation
- [ ] Créer branche `feature/06-atelier-quote` depuis `develop`.
- [ ] Lire `features/README.md` (section Atelier) + dossiers `02`/`03` pour garder cohérence client.
- [ ] Vérifier que migrations `users`/`clients` sont à jour, tests passent (`php artisan test`).

## Données & backend
- [ ] Écrire et exécuter migrations `create_quotes_table` & `create_quote_lines_table` (DECIMAL(10,2) pour montants, DECIMAL(7,4) pour taux).
- [ ] Implémenter modèles + relations + factories/seeds si utiles.
- [ ] Créer service `QuoteCalculator` avec toutes les méthodes de conversion + tests unitaires.

## Livewire & expérience utilisateur
- [ ] Composant `Clients\Search` (ou extension existante) publie un event avec l'ID client et ses infos.
- [ ] Composant `Atelier\Quotes\Form` : state client + lignes + totaux, méthodes `addLine`, `removeLine`, `updateLine`, `save`.
- [ ] Vue Livewire : onglets client, table prestations, totaux, boutons.
- [ ] Routes `/atelier/devis/...` + bouton "Nouveau devis" sur la page Atelier.
- [ ] Page lecture (show) affichant le devis créé.

## Calculs & validations
- [ ] Tous les inputs (PV HT/TTC, marge €, marge %) se recalculent mutuellement selon les règles du dossier.
- [ ] Totaux HT/TVA/TTC/marge totale et remise se recalculent à chaque modification.
- [ ] Respecter les règles d'arrondis (2 décimales pour montants, 4 pour taux) et n'arrondir qu'au moment de la persistance / affichage.
- [ ] Validation serveur : chaque ligne doit avoir un intitulé et des montants numériques cohérents (>= 0).
- [ ] Les modifications client effectuées après sélection d'un client existant ne sont persistées qu'à la validation du devis.

## SCSS
- [ ] Créer `resources/scss/atelier/_quotes.scss` + importer dans `app.scss`.
- [ ] Styles responsive pour blocs client, tableau prestations et totaux.
- [ ] États hover/suppression pour les lignes prestations.

## Tests & QA manuelle
- [ ] `php artisan test --filter=QuoteLines\\CreateQuoteLineTest`.
- [ ] `php artisan test --filter=Quotes\\CreateQuoteTest`.
- [ ] `php artisan test --filter=Livewire\\Atelier\\QuoteFormTest`.
- [ ] `php artisan test --filter=QuoteCalculatorTest` (ou dossier `Unit`).
- [ ] QA manuelle : client existant vs nouveau, ajout/suppression lignes, modification PV TTC -> recalcul, remise appliquée, feedback banner.

## Livraison
- [ ] Mettre à jour documentation (features/README + changelog le cas échéant).
- [ ] Capturer les commandes `artisan migrate` / `npm run build` s’ils ont été relancés.

## Clôture QA & notation (obligatoire)
- [ ] Relire intégralement tous les fichiers de `features/06-atelier-quote` avant de conclure la tâche.
- [ ] Vérifier chaque case ci-dessus (1 point validé par étape) et enregistrer la note = points validés / points totaux.
- [ ] Exécuter tous les tests listés et confirmer qu’ils sont verts.
- [ ] Autoriser commit/push uniquement si la note est 100 % et que les tests passent.
