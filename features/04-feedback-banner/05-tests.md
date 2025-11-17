# 05 - Tests & validations

1. **Tests backend**
   - `tests/Feature/Clients/CreateClientTest.php` (ou nouveau test dédié) doit vérifier qu’après création réussie on flash bien `feedback` (ex. `Session::get('feedback')['type'] === 'success'`).
   - Ajouter un test pour l’échec (ex. validation) qui flash `error`.
2. **Tests Livewire**
   - Dans `tests/Feature/Livewire/Clients/FormTest.php`, simuler l’événement `client-saved` et vérifier qu’un événement navigateur `feedback-banner` est dispatché (`assertDispatched('feedback-banner')`).
3. **Tests Blade / Feature**
   - Nouveau test `tests/Feature/Feedback/FeedbackBannerTest.php` :
     - Injecter un message dans la session et vérifier que la vue `home.dashboard` contient la card avec la classe `.feedback-banner--success`.
     - Faire de même pour une erreur.
4. **Tests JS (optionnel)**
   - Si possible, ajouter un test front (Vitest) pour vérifier que le module `feedback-banner.js` masque la card après 3 s (mock timers).
5. **Validation manuelle**
   - Lancer `php artisan serve` + `npm run dev` :
     - Soumission du formulaire clients → vérifier l’apparition de la card verte puis sa disparition.
     - Forcer une erreur (email dupliqué) → card rouge visible 3 s.

Tous ces tests doivent être exécutés (référence dans workflow) avant push.***
