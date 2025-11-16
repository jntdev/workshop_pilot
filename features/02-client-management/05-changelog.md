# Changelog - Feature 02: Client Management

## Session du 2025-01-16

### Ajouts majeurs

#### 1. Interface CRUD complète
- **Liste des clients** (`/clients`) avec recherche en temps réel et cartes cliquables
- **Création de client** (`/clients/nouveau`) avec formulaire Livewire
- **Édition/Suppression** (`/clients/{id}`) avec le même formulaire en mode édition

#### 2. API REST complète
- `GET /api/clients` - Liste tous les clients
- `GET /api/clients/{id}` - Affiche un client
- `POST /api/clients` - Crée un client
- `PUT /api/clients/{id}` - Met à jour un client
- `DELETE /api/clients/{id}` - Supprime un client

#### 3. Composants Livewire

**Index Component**
- Recherche instantanée sur nom, prénom, téléphone, email
- Tri alphabétique automatique (nom → prénom)
- Grid responsive avec cartes
- Computed property pour performance

**Form Component**
- Mode création/édition dans un seul composant
- Validation temps réel avec `wire:model.blur`
- Bouton retour vers la liste (mode édition uniquement)
- Redirections après save/delete
- Validation métier pour les avantages client

#### 4. Tests (62 tests, 201 assertions)

**Backend**
- `CreateClientTest` - 8 tests de création et validation
- `ClientApiTest` - 7 tests CRUD API

**Frontend**
- `FormTest` - 18 tests (création, édition, validation, suppression)
- `IndexTest` - 8 tests (liste, recherche, tri)
- `ClientNavigationTest` - 8 tests E2E de navigation

#### 5. Infrastructure

**Seeders**
- `ClientSeeder` - Génère 12 clients pour développement
- Intégré dans `DatabaseSeeder`

**Configuration**
- `phpunit.xml` configuré pour base de test MySQL `workshop_pilot_test`
- Séparation base dev/test pour éviter perte de données

### Styles SCSS (BEM)

**Liste des clients** (`_index-page.scss`)
```scss
.clients-list
  &__search-bar
  &__search-input
  &__grid
  &__empty

.client-card
  &__name
  &__phone
  &__email
```

**Formulaire** (`_client-form.scss`)
```scss
.client-form
  &__header
  &__back (nouveau)
  &__title
  &__form
  &__section
  &__grid
  &__field
  &__label
  &__input
  &__select
  &__textarea
  &__error
  &__actions
```

### Corrections et améliorations

1. **Validation email unique** - Exclut le client actuel en mode édition
2. **Redirections** - Retour automatique à la liste après save/delete
3. **Bouton retour** - Navigation intuitive depuis la page d'édition
4. **Seeder persistant** - Données de développement non supprimées par les tests
5. **SCSS** - Correction des variables de couleur (`$color-primary-blue`)

### Fichiers créés

```
app/Http/Controllers/Api/ClientController.php (étendu)
app/Http/Requests/UpdateClientRequest.php
app/Livewire/Clients/Index.php
database/seeders/ClientSeeder.php
resources/scss/clients/_index-page.scss
resources/views/clients/index.blade.php
resources/views/clients/create.blade.php
resources/views/clients/show.blade.php
resources/views/livewire/clients/index.blade.php
tests/Feature/Clients/ClientApiTest.php
tests/Feature/Clients/ClientNavigationTest.php
tests/Feature/Livewire/Clients/IndexTest.php
```

### Fichiers modifiés

```
app/Livewire/Clients/Form.php (ajout mode édition)
database/seeders/DatabaseSeeder.php (appel ClientSeeder)
phpunit.xml (config base test MySQL)
resources/scss/components/livewire/_client-form.scss (bouton retour)
resources/views/livewire/clients/form.blade.php (header + retour)
routes/api.php (routes CRUD)
routes/web.php (routes clients)
tests/Feature/Livewire/Clients/FormTest.php (tests édition)
```

### Problèmes connus

**Configuration tests MySQL**
- Les variables d'environnement de `phpunit.xml` ne sont pas toujours respectées
- Risque de vidage de la base de développement lors des tests
- **Solution temporaire** : Utiliser `php artisan db:seed --class=ClientSeeder` pour restaurer
- **TODO** : Investiguer pourquoi RefreshDatabase n'utilise pas `workshop_pilot_test`

### Prochaines étapes suggérées

1. Résoudre le problème de séparation base dev/test
2. Ajouter pagination à la liste des clients
3. Ajouter filtres avancés (par origine, par avantage)
4. Exporter la liste des clients (CSV, PDF)
5. Historique des modifications client
