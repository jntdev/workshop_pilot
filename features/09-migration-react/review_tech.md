# Review technique – migration React

## 1. Formulaire de devis (React)
- `resources/js/Pages/Atelier/Quotes/Form.tsx` conserve des erreurs serveur (`errors`) mais ne les affiche jamais. En cas de validation 422, l’utilisateur ne voit pas quels champs corriger.
- Seul le message générique `quote-form__alert` est rendu ; il faut propager les erreurs près des inputs (lignes, résumé, client).

## 2. Appels API côté Clients
- La recherche (`resources/js/Pages/Clients/Index.tsx`) appelle `/api/clients` sans `credentials: 'same-origin'` ni en-têtes CSRF. Or les routes API sont désormais derrière `['web','auth']` (routes/api.php).
- Sans les cookies envoyés explicitement, le fetch reçoit des redirections ou 401. Factoriser un helper pour envoyer systématiquement cookies + XSRF.

### 2.1 Parité des données côté Inertia
- Les routes Inertia (`/clients`, `/atelier/devis/*`) ne renvoient pas les champs métier (origine, commentaires, avantages), alors que les pages React (Clients/Form, QuoteForm) attendent désormais ces données.
- Tant que l’index React ne reçoit que `prenom/nom/email/téléphone/adresse`, toute sélection effectuée depuis cette liste peut réécrire les colonnes manquantes avec des valeurs vides.
- Actions : aligner les payloads Inertia avec la structure complète de `Client` (voir `resources/js/types/index.d.ts`) et s’assurer que les composants ne serialisent pas de données partielles.

## 3. Données client perdues lors d’un devis
- Le formulaire React de devis n’envoie que `prenom/nom/email/téléphone/adresse` (`QuoteForm.handleSave`). Les champs métier introduits par Livewire (`origine_contact`, `commentaires`, avantage…) ne sont plus persistés lorsqu’on crée un client depuis un devis.
- `QuoteController::resolveClient()` crée donc des clients partiels, contrairement à l’ancien flux. Il faut aligner le payload et mettre à jour la validation côté API.

### 3.1 Sélection / édition d’un client existant
- L’API `/api/clients` et la payload Inertia (`routes/web.php`) ne renvoient pas les champs métier. Lorsqu’un client est sélectionné via `ClientSearch` ou qu’on édite un devis, `QuoteForm` initialise ces champs à vide.
- À la sauvegarde, `QuoteController::resolveClient()` détecte ces “changements” et écrase les valeurs en base.
- Actions : enrichir les réponses Inertia (`clients.index/show`, `atelier.devis.*`) et l’API `/api/clients` pour inclure `origine_contact`, `commentaires`, `avantage_*`, ou empêcher la mise à jour quand les champs ne sont pas fournis.

## Suivi
- Ajouter rendering des erreurs + tests Feature pour couvrir les validations devis.
- Mettre à jour les fetchs clients (Index + ClientSearch + calculate line/totals) pour respecter l’auth web.
- Étendre les DTO/payloads client et ajuster `QuoteController` + tests API pour préserver la parité fonctionnelle avec Livewire.
