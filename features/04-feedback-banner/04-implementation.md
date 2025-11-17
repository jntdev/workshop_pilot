# 04 - Étapes d’implémentation

> **Flux global unique**  
> - Les messages qui survivent à une redirection utilisent `session()->flash('feedback', ...)`.  
> - Les interactions Livewire sans reload déclenchent l’événement navigateur `feedback-banner` via `$this->dispatch('feedback-banner', ...)`.  
> - Dans les deux cas, **le composant JS n’écoute qu’un seul canal** : `feedback-banner`. Lors d’un chargement de page, il initialise son état avec la valeur flashée, puis toute mise à jour passe par l’événement `feedback-banner`.

1. **Backend utilitaire**
   - Créer un helper `app/Support/Feedback.php` (ou `App\Services\FeedbackService`) avec méthodes statiques :
     - `success(string $message): void` → `session()->flash('feedback', ['type' => 'success', 'message' => $message]);`
     - `error(string $message): void` → idem avec `error`.
   - L’utiliser dans les contrôleurs/formulaires existants (ex. après création client).
2. **Livewire events**
   - Dans les composants Livewire, après succès : `$this->dispatch('feedback-banner', type: 'success', message: 'Client enregistré');`.
   - Sur erreur manuelle : même event avec `type => 'error'`.
3. **Composant Blade global**
   - Créer `resources/views/components/feedback/banner.blade.php` :
     - Lit `session('feedback')`.
     - Écoute un event JS `window.addEventListener('feedback-banner', ...)` pour mettre à jour dynamiquement (via un **petit script vanilla** `resources/js/feedback-banner.js`).
     - Gère l’autohide 3 s (`setTimeout`).
4. **Layout**
   - Dans `resources/views/components/layouts/main.blade.php`, juste sous le header mais avant le breadcrumb, inclure `<x-feedback.banner />`.
5. **JS (vanilla)**
   - Nouveau fichier `resources/js/feedback-banner.js` :
     - Lit `window.feedbackBannerData` injecté par le composant pour afficher un message au chargement.
     - Écoute événements `CustomEvent('feedback-banner', { detail: { type, message } })`.
     - Applique `data-visible` pour déclencher l’affichage et lance un `setTimeout` 3 s pour cacher.
   - Importer ce script dans `resources/js/app.js`.
6. **Livewire intégration**
   - Chaque composant Livewire doit **dispatcher directement** l’événement navigateur `feedback-banner` après l’action (ex. `$this->dispatch('feedback-banner', type: 'success', message: 'Client enregistré');`).
   - Pas d’événement métier (ex. `client-saved`) à écouter : le composant d’alerte ne consomme que `feedback-banner`, quel que soit le contexte (`facture`, `commande`, etc.).
   - Pour les actions avec redirection, utiliser `Feedback::success(...)` avant `return redirect()->route(...)` afin que le message survive au changement de page.
7. **SCSS**
   - Implémenter les styles décrits (classes, couleurs) et s’assurer que `resources/scss/app.scss` compile le partial.

Ordre conseillé : helper backend → composant Blade + JS → SCSS → intégration layout → adaptation Livewire.***
