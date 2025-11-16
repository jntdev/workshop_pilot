# 03 - Stratégie de réalisation

Séparer backend et frontend, en écrivant les tests avant les features.

1. **Migration & modèle**
   - Créer la migration `create_clients_table`.
   - Définir le modèle `Client` + règles de validation (FormRequest/DTO).
2. **Test backend préalable**
   - `tests/Feature/Clients/CreateClientTest.php`.
   - Cas heureux : envoi JSON fictif ⇒ vérifie écriture + restitution.
   - Cas validation : rejeter données invalides (bonus).
3. **Feature backend**
   - Route API `POST /api/clients`.
   - Contrôleur : validation selon le modèle, retour JSON.
4. **Exécution tests backend**
   - `php artisan test --filter=CreateClientTest`.
5. **Test frontend Livewire**
   - `tests/Feature/Livewire/Clients/FormTest.php`.
   - Cas heureux : composant `Clients\Form` sauvegarde et réinitialise les champs.
   - Cas erreur : champ requis manquant ⇒ message validation visible.
6. **Formulaire frontend Livewire**
   - Composant PHP `App\Livewire\Clients\Form` (propriétés bindées aux champs).
   - Vue `resources/views/livewire/clients/form.blade.php` (wire:model + bouton `save`).
7. **Gestion des erreurs UI**
   - Utiliser `@error`/messages Livewire pour afficher un label temporaire.
   - Extraire un composant d’affichage si réutilisation.
8. **Exécution tests frontend**
   - `php artisan test --filter=Livewire\\Clients\\FormTest`. 
