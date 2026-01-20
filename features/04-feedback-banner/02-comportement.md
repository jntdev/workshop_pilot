# 02 - Comportement fonctionnel

1. **Affichage global**
   - La card apparaît sous le header principal (`<x-layouts.main>`). Elle doit être incluse dans le layout pour toutes les pages.
   - Format : titre optionnel + message texte.
2. **Types**
   - `success` ⇒ fond vert, texte blanc, icône check.
   - `error` ⇒ fond rouge, texte blanc, icône warning.
3. **Temporalité**
   - Visible dès qu’un message est disponible.
   - Se cache automatiquement après 3 secondes (timeout côté JS/Livewire) même si l’utilisateur change de page (message persiste via session jusqu’à expiration ou appelé).
4. **Sources des messages**
   - Backend : utiliser `session()->flash('feedback', ['type' => 'success', 'message' => ...])`.
   - Livewire : soit via `session()->flash` (lors d’un redirect), soit via event browser (`$this->dispatch('feedback', type: 'success', message: '...')`) capté par le composant.
5. **Prise en charge des erreurs validation**
   - Sur validation échouée (422), si l’action redirige, stocker un message flash (ex. “Veuillez corriger le formulaire”).
   - Pour validations inline (Livewire), déclencher l’événement `feedback` pour afficher la card sans rechargement.
6. **Accessibilité**
   - Utiliser `role="alert"` et `aria-live="assertive"` pour que les lecteurs d’écran annoncent le message.
7. **Un seul message affiché**
   - Le composant ne gère qu’un message à la fois ; les messages s’écrasent (dernière action prime).
8. **Fermer manuellement (option future)**
   - Prévoir un bouton close `x` optionnel pour future extension (pas nécessairement fonctionnel dans cette itération).***
