# 04 - Artifacts attendus

## Backend
- **API Controller** : `Api\ClientController` complété avec `index()`, `show()`, `update()`, `destroy()`
- **Form Request** : `UpdateClientRequest` pour validation des mises à jour
- **Routes API** : GET/PUT/DELETE dans `routes/api.php`
- **Tests** : `tests/Feature/Clients/ClientApiTest.php` (4+ tests)

## Frontend Livewire
- **Composant Index** : `App\Livewire\Clients\Index` avec recherche et liste
- **Composant Form** : `App\Livewire\Clients\Form` mis à jour (création + édition + suppression)
- **Vues** :
  - `resources/views/livewire/clients/index.blade.php` (liste + recherche)
  - `resources/views/livewire/clients/form.blade.php` (mise à jour pour édition)
- **Tests** :
  - `tests/Feature/Livewire/Clients/IndexTest.php` (5+ tests)
  - `tests/Feature/Livewire/Clients/FormTest.php` (complété avec 5+ tests supplémentaires)

## Pages Blade
- `resources/views/clients/index.blade.php` : page liste
- `resources/views/clients/create.blade.php` : page création
- `resources/views/clients/show.blade.php` : page détail/édition

## Routes
- `routes/web.php` : routes GET pour `/clients`, `/clients/nouveau`, `/clients/{id}`

## SCSS
- `resources/scss/clients/_index.scss` : styles liste et cartes
- `resources/scss/clients/_show.scss` : styles page détail (optionnel)
- `resources/scss/app.scss` : mis à jour avec imports

## Validation
- Tous les tests passent (backend + frontend)
- Build SCSS réussi (`npm run build`)
- Navigation fonctionnelle entre toutes les pages
