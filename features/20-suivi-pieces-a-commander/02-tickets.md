# Tickets - Feature 20 : Suivi des pieces a commander

---

## Ticket 20.1 : Etendre le modele `quote_lines` pour le suivi d'approvisionnement

**Type** : Backend / Data
**Priorite** : Haute
**Estimation** : 45 min

### Description
Ajouter sur chaque ligne de devis les informations minimales pour suivre une piece a commander sans creer un stock atelier complet.

### Taches
- [ ] Ajouter les colonnes suivantes sur `quote_lines` :
  - `needs_order` boolean default false
  - `ordered_at` nullable timestamp
  - `received_at` nullable timestamp
- [ ] Ajouter les champs au modele `QuoteLine`
- [ ] Ajouter les casts Eloquent dates/bool correspondants
- [ ] Definir les regles de coherence minimales :
  - une ligne recue implique une ligne commandee
  - une ligne non `needs_order` ne doit pas conserver des dates residuelles

### Criteres d'acceptation
- Une ligne de devis peut etre marquee ou non "a commander"
- Le backend sait persister l'etat "commandee" et "recue"
- Le schema reste centre sur le devis, sans table de stock

---

## Ticket 20.2 : Exposer les nouveaux champs dans l'API devis

**Type** : Backend / API
**Priorite** : Haute
**Estimation** : 45 min

### Description
Faire circuler les donnees de suivi d'approvisionnement dans les endpoints existants des devis.

Point critique :
le fonctionnement actuel `delete() + recreate()` de `syncLines()` n'est pas compatible avec un suivi porte par `quote_line_id`.

Pour cette feature, l'identite d'une ligne doit rester stable dans le temps afin de permettre :
- le suivi dans la vue panier
- les transitions de statut sur la bonne ligne
- des liens fiables entre edition du devis et suivi d'approvisionnement

La strategie retenue est donc un upsert applicatif :
- si une ligne du payload contient un `id` existant appartenant au devis, elle est mise a jour
- si une ligne du payload n'a pas de `id`, elle est creee
- toute ligne existante du devis absente du payload est supprimee

### Taches
- [ ] Etendre la validation de `QuoteController` pour accepter, sur chaque ligne :
  - `id`
  - `needs_order`
  - `ordered_at`
  - `received_at`
- [ ] Remplacer la logique `delete() + recreate()` de `syncLines()` par une logique d'upsert
- [ ] Mettre a jour les lignes existantes par `id` quand il est present dans le payload
- [ ] Creer les nouvelles lignes quand aucun `id` n'est present
- [ ] Supprimer les lignes existantes absentes du payload
- [ ] Persister les champs de suivi d'approvisionnement sans casser l'identite des lignes deja creees
- [ ] Ajouter au modele `QuoteLine` une propriete calculee `supply_status` via accessor Eloquent ou attribut equivalent
- [ ] Mettre a jour `formatQuote()` pour les retourner au frontend
- [ ] Mettre a jour les types TypeScript lies aux devis

### Regles de validation
- Le `id` d'une ligne, s'il est fourni, doit appartenir au devis en cours d'edition
- Si `needs_order = true`, `reference` devient obligatoire
- `received_at` ne peut pas etre renseigne si la ligne n'est pas commandee
- Si `received_at` est renseigne sans `ordered_at`, le backend positionne `ordered_at` a la meme date ou refuse explicitement ; choisir une seule regle et la documenter

### Criteres d'acceptation
- Les `quote_line_id` existants sont preserves apres modification d'un devis
- Les nouveaux champs round-trip correctement entre frontend et backend
- `supply_status` est expose de maniere centralisee depuis le modele `QuoteLine`
- Une ligne "a commander" sans reference est rejetee avec un message clair
- Les incoherences de statut sont bloquees cote backend
- Une ligne supprimee du formulaire est effectivement supprimee en base

---

## Ticket 20.3 : Ajouter la case "A commander" dans le tableau des lignes de devis

**Type** : Frontend
**Priorite** : Haute
**Estimation** : 45 min

### Description
Permettre a l'utilisateur de designer directement depuis un devis les lignes qui doivent remonter dans le panier.

### Taches
- [ ] Ajouter une colonne "A commander" dans `QuoteLinesTable.tsx`
- [ ] Ajouter une case a cocher par ligne
- [ ] Connecter la case au state React du formulaire devis
- [ ] Afficher un etat visuel explicite si la ligne est dans le panier
- [ ] Afficher une erreur inline si l'utilisateur coche "A commander" sans reference de piece

### Criteres d'acceptation
- Une ligne peut etre ajoutee ou retiree du panier depuis le devis
- Le changement est sauvegarde avec le devis
- L'utilisateur comprend visuellement quelles lignes partiront en commande

---

## Ticket 20.4 : Creer l'endpoint liste "panier de pieces"

**Type** : Backend / API
**Priorite** : Haute
**Estimation** : 1 h

### Description
Creer un endpoint dedie qui retourne les lignes de devis a commander sous une forme directement exploitable par la future vue panier.

Point d'architecture :
`supply_status` ne doit pas etre calcule uniquement dans la Resource ou dans le controller.
Il doit etre porte par le modele `QuoteLine` via un accessor Eloquent ou une propriete calculee equivalente, afin d'etre reutilisable :
- dans la liste panier
- dans les mutations du ticket 20.5
- dans tout affichage futur du statut d'approvisionnement

### Reponse attendue
Chaque element doit contenir au minimum :
- `quote_line_id`
- `quote_id`
- `client_id`
- `client_nom_complet`
- `bike_description`
- `line_title`
- `line_reference`
- `quantity`
- `needs_order`
- `ordered_at`
- `received_at`
- `supply_status` calcule (`to_order`, `ordered`, `received`)

### Taches
- [ ] Ajouter un endpoint du type `GET /api/quotes/order-lines`
- [ ] Charger les relations utiles (`quote`, `client`) sans N+1
- [ ] Retourner par defaut uniquement les lignes `needs_order = true` et `received_at is null`
- [ ] Ajouter un filtre optionnel pour afficher aussi les lignes recues
- [ ] Trier par anciennete de creation ou de commande, de facon deterministe
- [ ] Reutiliser la propriete de modele `supply_status` au moment de serialiser la reponse

### Criteres d'acceptation
- Le frontend peut construire la vue panier sans recharger tous les devis
- Le contexte client + velo + reference de piece est present sur chaque ligne
- Le frontend dispose de l'identifiant necessaire pour proposer une action `Consulter` vers le devis source
- `supply_status` provient du modele et non d'un calcul duplique dans plusieurs couches
- Les lignes recues ne polluent pas la vue ouverte par defaut

---

## Ticket 20.5 : Creer l'action de mise a jour de statut depuis le panier

**Type** : Backend / API
**Priorite** : Haute
**Estimation** : 1 h

### Description
Permettre de faire evoluer l'etat d'une ligne de piece directement depuis la vue panier, sans repasser par l'edition complete du devis.

### Taches
- [ ] Ajouter un endpoint de mutation sur une ligne, par exemple :
  - `PATCH /api/quote-lines/{quoteLine}/order-status`
- [ ] Accepter un payload minimal :
  - `needs_order`
  - `mark_as_ordered`
  - `mark_as_received`
- [ ] Definir la logique serveur :
  - cocher "commandee" renseigne `ordered_at` si absent
  - cocher "recue" renseigne `received_at` et force `ordered_at` si besoin
  - decocher "commandee" ou "recue" doit etre explicitement autorise ou interdit ; choisir la regle et la documenter
- [ ] Retourner l'objet mis a jour avec son `supply_status` calcule depuis le modele `QuoteLine`

### Decision recommandee
Autoriser le retour arriere uniquement tant que la piece n'est pas marquee recue.

### Criteres d'acceptation
- Une ligne peut passer de `to_order` a `ordered`
- Une ligne peut passer de `ordered` a `received`
- Les transitions incoherentes sont bloquees par le backend

---

## Ticket 20.6 : Ajouter la vue atelier "Pieces a commander"

**Type** : Frontend
**Priorite** : Haute
**Estimation** : 1 h 30

### Description
Creer une vue centralisee type panier pour traiter les pieces a commander en serie.

### Taches
- [ ] Ajouter un acces visible depuis la section atelier
- [ ] Creer une page React dediee, par exemple `resources/js/Pages/Atelier/OrderLines/Index.tsx`
- [ ] Charger la liste via le nouvel endpoint API
- [ ] Afficher un tableau avec les colonnes :
  - Client
  - Velo
  - Piece / intitule
  - Reference
  - Qte
  - Statut
  - Actions
- [ ] Ajouter les actions inline :
  - cocher "Commandee"
  - cocher "Recue"
- [ ] Ajouter dans `Actions` un bouton `Consulter` vers le devis source
- [ ] Ajouter un filtre "Afficher les recues"

### Criteres d'acceptation
- L'utilisateur peut passer sa commande sans ouvrir chaque devis
- Le tableau donne assez de contexte pour reconnaitre la bonne piece
- Le bouton `Consulter` permet d'ouvrir rapidement le devis source en cas de doute
- Le traitement de plusieurs lignes a la suite est fluide

---

## Ticket 20.7 : Messages UI et regles de comprehension

**Type** : Frontend / Produit
**Priorite** : Moyenne
**Estimation** : 30 min

### Description
Clarifier le sens des cases a cocher et eviter les ambiguites metier.

### Taches
- [ ] Employer les libelles exacts :
  - `A commander`
  - `Commandee`
  - `Recue`
- [ ] Ajouter un badge ou libelle de statut lisible dans la vue panier
- [ ] Prevoir un message vide utile :
  - "Aucune piece a commander"
- [ ] Prevoir les messages de validation principaux :
  - reference obligatoire
  - transition impossible

### Criteres d'acceptation
- Le vocabulaire est stable entre devis et panier
- Un utilisateur atelier comprend les 3 etats sans explication externe

---

## Ticket 20.8 : Tests

**Type** : QA / Tests
**Priorite** : Haute
**Estimation** : 1 h 30

### Backend
- [ ] Test creation d'un devis avec ligne `needs_order = true`
- [ ] Test edition d'un devis conserve les `quote_line_id` existants
- [ ] Test ajout d'une nouvelle ligne cree un nouveau `quote_line_id`
- [ ] Test suppression d'une ligne absente du payload la supprime en base
- [ ] Test accessor / propriete `supply_status` sur les 3 cas `to_order`, `ordered`, `received`
- [ ] Test rejet si `needs_order = true` sans `reference`
- [ ] Test endpoint liste panier
- [ ] Test filtre cache les lignes recues par defaut
- [ ] Test transition `to_order -> ordered`
- [ ] Test transition `ordered -> received`
- [ ] Test blocage d'une transition incoherente

### Frontend / Manuel
- [ ] Cocher "A commander" sur une ligne de devis et sauvegarder
- [ ] Verifier l'apparition dans la vue panier
- [ ] Marquer plusieurs lignes "Commandee"
- [ ] Marquer une ligne "Recue" et verifier sa disparition de la vue ouverte par defaut
- [ ] Verifier que le contexte client + velo + reference est suffisant pour reconnaitre chaque piece

---

## Arbitrages produit a conserver

### Decision 1
Pas de stock atelier dans cette feature.

### Decision 2
Le suivi est porte par la `quote_line`, pas par une table de commandes fournisseur.

### Decision 3
La vue panier est une vue de travail centralisee sur les lignes a commander, pas un panier e-commerce avec validation finale globale.

### Decision 4
La reference de piece est obligatoire pour toute ligne marquee "A commander".

### Decision 5
Le backend doit conserver des `quote_line_id` stables :
- update par `id` si la ligne existe deja
- creation si la ligne est nouvelle
- suppression si la ligne a disparu du payload
