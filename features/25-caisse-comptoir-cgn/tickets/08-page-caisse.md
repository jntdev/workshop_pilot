# Ticket 08 : Page Caisse

**Type** : Frontend / Inertia
**Priorité** : Haute
**Estimation** : 4h

**Dépend de** : Ticket 06

## Description

Nouvelle page permettant de constituer un panier en tapant/scannant des codes-barres, puis d'encaisser.

## Cycle de vie du brouillon (arbitrage nécessaire, absent de la première version du ticket)

Charger la page ne doit pas systématiquement créer une nouvelle `Sale` en base — sans quoi chaque rechargement/navigation accumule des brouillons vides. Stratégie retenue : **la vente est créée au premier article ajouté**, pas à l'affichage de la page. Tant qu'aucun article n'a été scanné, l'état du panier reste purement local (aucun appel `POST /api/sales`). Au premier `addLine` réussi, si aucune `Sale` locale n'existe encore, créer la vente puis la ligne dans la foulée. Ceci évite d'avoir à gérer une purge de brouillons vides côté serveur.

## Tâches

- [ ] Route web `GET /vente/caisse` → `Inertia::render('Vente/Caisse')`, nom `vente.caisse` (dans `routes/web.php`, groupe authentifié)
- [ ] Lien de navigation "Caisse" dans `MainLayout.tsx` (jamais "Comptoir" — déjà pris par le toggle de confidentialité)
- [ ] `resources/js/Pages/Vente/Caisse.tsx` : page conteneur
- [ ] `resources/js/Components/Vente/CaisseForm.tsx`, structuré comme `ReservationForm.tsx` (état local `useState`, pas de state global) :
  - Champ code-barres `autoFocus`, `ref` pour refocus programmatique
  - `onKeyDown` : sur `Enter`, appelle `GET /api/sales/lookup-article?q=...` (via `apiGet`, `resources/js/utils/api.ts`), vide le champ, refocus — reproduit le comportement d'un scanner USB « keyboard wedge » (tape les caractères puis Enter) aussi bien qu'une saisie manuelle
  - Si un seul résultat exact (EAN ou référence) : ajoute directement au panier (crée la `Sale` au besoin, voir cycle de vie ci-dessus), incrémente la quantité si l'article y est déjà
  - Si plusieurs résultats (recherche texte floue) : affiche une liste de choix, l'utilisateur sélectionne l'article avant ajout — **jamais d'ajout automatique d'un résultat approximatif**
  - Si aucun résultat : message d'erreur bref, sans bloquer la saisie suivante
  - Tableau panier : désignation / référence / prix unitaire TTC (éditable) / quantité (éditable) / total ligne / suppression
  - Total TTC calculé côté client (`useMemo`) pour affichage instantané ; la vérité finale reste le recalcul serveur à la finalisation
  - Bouton "Finaliser" → sélection `payment_method` (type `PaymentMethod` TypeScript existant : `cb`, `liquide`, `cheque`, `virement`, `autre` — voir note ci-dessous) → `POST /api/sales/{id}/complete`, désactivé si le panier est vide
  - Après finalisation réussie : reset de l'état local (nouvelle vente créée seulement au prochain ajout, cf. cycle de vie du brouillon), refocus le champ code-barres (permettre d'enchaîner les ventes sans recharger la page)
- [ ] **Note `PaymentMethod`** : le type TypeScript existant (`resources/js/types/index.d.ts:302`) reste la source utilisée côté front, aucun changement requis ici. Le ticket 05 introduit un enum PHP équivalent côté serveur (nouveau, pas une réutilisation) — les deux définitions doivent rester synchronisées manuellement si les valeurs évoluent
- [ ] Types TypeScript dans `resources/js/types/index.d.ts` : `SaleStatus`, `Sale`, `SaleLine`
- [ ] SCSS : nouveau fichier `resources/scss/vente/_caisse.scss` suivant les conventions BEM du projet (voir `../../CONVENTIONS.md`), importé dans `app.scss`

## Critères d'acceptation

- À l'arrivée sur `/vente/caisse`, avant tout scan, aucun appel `POST /api/sales` n'est effectué (panier local vide, pas de `Sale` créée) — vérifiable en observant l'absence de requête réseau au chargement de la page
- Taper une référence CGN existante dans le champ code-barres puis Entrée déclenche, dans cet ordre : `POST /api/sales` (création de la `Sale`, une seule fois) puis `POST /api/sales/{id}/lines` (ajout de la ligne) — la ligne apparaît dans le panier une fois les deux appels résolus
- Scanner un deuxième article différent sur la même session n'appelle plus jamais `POST /api/sales` (la `Sale` créée au premier ajout est réutilisée pour tous les ajouts suivants jusqu'à la finalisation)
- Scanner deux fois le même article incrémente sa quantité au lieu de dupliquer la ligne
- Une recherche texte donnant plusieurs résultats affiche un choix, n'ajoute rien automatiquement
- Un code-barres inconnu affiche une erreur claire sans bloquer la suite de la saisie
- Ajuster la quantité ou le prix d'une ligne met à jour le total affiché immédiatement
- Le bouton Finaliser est désactivé tant que le panier est vide
- Finaliser sans mode de paiement sélectionné est bloqué avec un message clair
- Après finalisation, le panier se vide et le champ code-barres reprend le focus automatiquement

## Tests

- [ ] Test de route Inertia pour `/vente/caisse` (la page se charge, statut 200)
- [ ] Vérification du build TypeScript/Vite après ajout des types `Sale`, `SaleLine`, `SaleStatus` et du nouvel écran (`npm run build` sans erreur)
- [ ] QA manuelle clavier : saisie code-barres, `Enter`, scans successifs rapprochés, code-barres inconnu, refocus après ajout et après finalisation
- [ ] QA manuelle : modification quantité/prix d'une ligne, blocage de la finalisation sans mode de paiement, blocage si panier vide
