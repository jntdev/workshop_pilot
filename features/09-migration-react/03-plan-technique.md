# 03 - Plan technique

## A. Clients
- Reproduire le formulaire Livewire (`App\Livewire\Clients\Form`) en React : champs origine_contact, commentaires, avantages, suppression client, validations dynamiques.
- Créer les pages React `Clients/Create` et `Clients/Show` (ou `Edit`) + routing Inertia associé (`routes/web.php`).
- Mutualiser les règles de validation côté backend via FormRequest dédiées aux endpoints React (ou réutilisation existante).
- Supprimer progressivement les vues Blade `resources/views/clients/*.blade.php` après vérification.

## B. Atelier / devis
- Afficher les erreurs de validation dans `resources/js/Pages/Atelier/Quotes/Form.tsx` (liées aux champs et en-tête).
- Finaliser les composants partagés (ClientSearch, QuoteLinesTable) pour gérer les états d'erreur/requis.
- Harmoniser les appels API (gestion des CSRF, serialization) et documenter les payloads attendus.
- **Note** : Les champs `bike_description` et `reception_comment` (identification du vélo et motif de réception) ont été ajoutés au modèle Quote. Ils sont déjà intégrés dans `Form.tsx`, `Show.tsx`, `QuotesTabs.tsx` et le PDF.

## C. APIs & sécurité
- Déplacer les routes `/api/clients`, `/api/quotes`, `/api/atelier/*` sous middleware authentifié (web + Sanctum ou sanctuaire dédié).
- Mettre en place des ressources JSON cohérentes (`data`, `meta`, `errors`) pour toutes les réponses.
- Ajouter des tests Feature ciblant ces endpoints avec authentification.

## D. Build & assets
- Tant que Livewire subsiste : réintroduire `resources/js/app.js` dans l'input Vite ou extraire Feedback Banner côté React.
- Préparer un plan de retrait de Livewire (`composer remove livewire/livewire`, nettoyage composants) une fois les pages migrées.

## E. Tests & QA
- Étendre PHPUnit : tests API pour clients/devis, tests d'intégration Inertia (response view) et tests unitaires pour les calculateurs existants.
- Mettre en place une base minima de tests front (React Testing Library) pour le routing et les formulaires critiques (clients, devis).
- Documenter la QA manuelle (scénarios de création client/devis, erreurs serveur, affichage Feedback Banner).
