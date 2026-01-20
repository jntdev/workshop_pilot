# 03 - Stratégie de réalisation

Approche TDD : tests d'abord, implémentation ensuite.

## 1. Backend API (CRUD complet)

### a. Tests API
- `tests/Feature/Clients/ClientApiTest.php`
  - GET `/api/clients` : liste tous les clients
  - GET `/api/clients/{id}` : détail d'un client
  - PUT `/api/clients/{id}` : mise à jour
  - DELETE `/api/clients/{id}` : suppression

### b. Implémentation API
- Ajouter méthodes dans `Api\ClientController` :
  - `index()` : retourne liste paginée (optionnel) ou complète
  - `show($id)` : retourne un client
  - `update(UpdateClientRequest $request, $id)` : met à jour
  - `destroy($id)` : supprime
- Créer `UpdateClientRequest` (similaire à `StoreClientRequest`)
- Ajouter routes dans `routes/api.php`

### c. Exécution tests backend
```bash
php artisan test --filter=ClientApiTest
```

## 2. Composants Livewire Frontend

### a. Composant Liste
- `App\Livewire\Clients\Index`
  - Propriétés : `$clients`, `$search`
  - Méthode `updatedSearch()` : filtre instantané
  - Computed property `filteredClients()` : tri alphabétique + filtre
- Vue : `resources/views/livewire/clients/index.blade.php`
  - Barre recherche + bouton création
  - Grid de cartes clients
  - Hover effects en SCSS

### b. Composant Formulaire (réutilisation)
- Modifier `App\Livewire\Clients\Form` :
  - Ajouter propriété `$clientId` (null = création, id = édition)
  - Méthode `mount($clientId = null)` : charge données si édition
  - Méthode `save()` : détecte création vs update
  - Méthode `delete()` : suppression avec confirmation
- Vue : adapter `resources/views/livewire/clients/form.blade.php`
  - Titre dynamique (Nouveau vs Fiche client)
  - Boutons conditionnels (création vs édition)

### c. Tests Livewire
- `tests/Feature/Livewire/Clients/IndexTest.php`
  - Affichage liste
  - Recherche filtre correctement
  - Navigation vers création
  - Navigation vers détail
- `tests/Feature/Livewire/Clients/FormTest.php` (compléter)
  - Mode édition : chargement données
  - Mise à jour client
  - Suppression client

### d. Exécution tests frontend
```bash
php artisan test --filter=Livewire\\\\Clients
```

## 3. Routes et Pages

### a. Routes web
Dans `routes/web.php` :
```php
Route::get('/clients', function () {
    return view('clients.index');
})->name('clients.index');

Route::get('/clients/nouveau', function () {
    return view('clients.create');
})->name('clients.create');

Route::get('/clients/{id}', function ($id) {
    return view('clients.show', ['clientId' => $id]);
})->name('clients.show');
```

### b. Vues Blade
- `resources/views/clients/index.blade.php` : contient `<livewire:clients.index />`
- `resources/views/clients/create.blade.php` : contient `<livewire:clients.form />`
- `resources/views/clients/show.blade.php` : contient `<livewire:clients.form :client-id="$clientId" />`

## 4. SCSS et Styles

### a. Fichiers SCSS
- `resources/scss/clients/_index.scss` : styles liste et cartes
- `resources/scss/clients/_show.scss` : styles page détail (si spécifiques)
- Mise à jour `resources/scss/app.scss` : importer nouveaux partials

### b. Classes BEM
- `.clients-list` : container principal
- `.clients-list__search-bar` : barre recherche + bouton
- `.clients-list__grid` : grid responsive
- `.client-card` : carte client
- `.client-card__name`, `__phone`, `__email`
- `.client-card:hover` : effet élévation

## 5. Tests d'intégration

### a. Navigation complète
- Test E2E (optionnel) ou Feature test :
  - Liste → Création → Retour liste
  - Liste → Détail → Modification → Retour liste
  - Liste → Détail → Suppression → Retour liste

### b. Validation finale
```bash
php artisan test
npm run build
```

## 6. Ordre d'exécution

1. Tests API backend → Implémentation API → Tests ✓
2. Tests Livewire Index → Composant Index → Tests ✓
3. Tests Livewire Form (édition/suppression) → Mise à jour Form → Tests ✓
4. Routes + Vues → Intégration
5. SCSS → Build
6. Tests complets → Validation
