# Hotfix HF-01 : Tests en dérive — RoutingTest, LocationPlanningTest, RebuildMonthlyKpisCommandTest

**Type** : QA / Correction de tests
**Priorité** : Moyenne
**Origine** : Détectés lors de la feature 20, antérieurs à celle-ci
**Date de détection** : 2026-04-22

---

## Contexte

Lors de la suite de tests complète exécutée après la feature 20, 14 tests échouent dans trois fichiers distincts. Ces échecs ne sont pas liés à la feature 20 — ils préexistaient et reflètent une dérive entre l'état des tests et l'état actuel de l'application.

---

## Problème 1 — `RoutingTest::home dashboard route is accessible`

**Fichier** : `tests/Feature/Frontend/RoutingTest.php:21`

**Symptôme** :
```
Unexpected Inertia page component.
Expected : 'Dashboard'
Got      : 'Location/Index'
```

**Cause** : La route `/` a été redirigée vers `Location/Index` lors de l'implémentation de la feature location (commit "page location"). Le test attend toujours l'ancien composant `Dashboard`.

**Correction attendue** :
- Mettre à jour l'assertion pour refléter le composant réellement servi par la route `/`
- Ou rétablir une route `/` vers `Dashboard` si c'est l'intention produit

---

## Problème 2 — `LocationPlanningTest` (11 tests)

**Fichier** : `tests/Feature/LocationPlanningTest.php`

**Symptôme** :
```
Failed asserting that 404 is identical to 200.
```
Tous les tests du fichier obtiennent un 404 sur `GET /location/planning`.

**Cause** : La route `/location/planning` n'existe pas ou n'est plus enregistrée dans l'état actuel de `routes/web.php`. La feature planning (commit "page location", "accessoirs") a vraisemblablement changé la structure des routes location sans mettre à jour les tests.

**Correction attendue** :
- Vérifier si la route `/location/planning` existe encore dans `routes/web.php`
- Si elle a été supprimée ou renommée : mettre à jour les tests pour pointer vers la bonne route
- Si elle doit être rétablie : re-créer la route et le contrôleur correspondant

---

## Problème 3 — `RebuildMonthlyKpisCommandTest::rebuild all`

**Fichier** : `tests/Feature/RebuildMonthlyKpisCommandTest.php:134`

**Symptôme** :
```
Failed asserting that a row in the table [monthly_kpis] matches the attributes {"metier": "location"}.
Found: [{"metier": "atelier"}, {"metier": "vente"}]
```

**Cause** : La commande `rebuild:monthly-kpis` ne génère plus de KPI pour le métier `location`. Soit le métier `Location` a été retiré de la liste traitée par la commande, soit le seeder/factory utilisé dans le test ne crée pas de données `location`.

**Correction attendue** :
- Vérifier que `Metier::Location` est bien inclus dans la commande de rebuild
- Si le métier location n'est plus actif dans l'application, supprimer l'assertion du test
- Si c'est un oubli dans la commande, réintégrer le métier location dans la liste traitée

---

## Critères d'acceptation

- [ ] `RoutingTest::home dashboard route is accessible` passe au vert
- [ ] Les 11 tests `LocationPlanningTest` passent au vert ou sont supprimés si la feature planning est abandonnée
- [ ] `RebuildMonthlyKpisCommandTest::rebuild all` passe au vert
- [ ] Aucune régression sur les tests existants passants

---

## Arbitrages à trancher avant de corriger

### Question 1 — Route `/`
La route d'accueil doit-elle pointer vers `Dashboard` ou vers `Location/Index` ? Cela détermine si on corrige le test ou si on corrige la route.

### Question 2 — Feature planning location
La feature planning location (`/location/planning`) est-elle active, en cours, ou abandonnée ? Si abandonnée, les tests peuvent être supprimés avec approbation. Si active, la route doit être rétablie.

### Question 3 — KPI location
Le métier location est-il inclus dans le périmètre des KPI mensuels ? Si oui, corriger la commande. Si non, supprimer l'assertion.
