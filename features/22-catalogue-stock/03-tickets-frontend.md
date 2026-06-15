# Tickets Frontend — Feature 22

---

## Ticket 22.8 : Page catalogue — gestion des catégories et articles

**Type** : Frontend
**Priorité** : Haute
**Estimation** : 3h

### Description

Créer la page `/stock` qui permet de gérer le catalogue d'articles : catégories, sous-catégories et articles.

### Structure de la page

```
/stock
├── Colonne gauche : arborescence catégories/sous-catégories
│   ├── [+ Ajouter catégorie]
│   ├── Pneus ▼
│   │   ├── Chambre à air     [modifier] [supprimer]
│   │   ├── Pneu route        [modifier] [supprimer]
│   │   └── [+ Ajouter sous-catégorie]
│   └── Transmission ▼
│       ├── Chaîne 7v         [modifier] [supprimer]
│       └── [+ Ajouter sous-catégorie]
└── Colonne droite : liste des articles de la sous-catégorie sélectionnée
    ├── [Recherche...] [+ Nouvel article]
    ├── Tableau : Référence | Désignation | Px achat | Px vente | TVA | Stock | Unité | Actions
    └── Pagination si nécessaire
```

### Tâches

- [ ] Route web `/stock` dans `routes/web.php` (Inertia, middleware auth)
- [ ] Page Inertia `resources/js/Pages/Stock/Index.tsx`
- [ ] Composant `ArticleCategoryTree.tsx` : arborescence avec état expand/collapse, boutons inline modifier/supprimer, ajout de catégorie et sous-catégorie
- [ ] Composant `ArticleList.tsx` : tableau des articles de la sous-catégorie sélectionnée, avec pagination et recherche texte
- [ ] Composant `ArticleForm.tsx` : formulaire création/édition d'un article (modale ou panneau latéral) avec champs référence, désignation, prix achat HT (en euros côté UI, converti en centimes pour l'API), prix vente HT, TVA, unité, fournisseur, notes
- [ ] Formulaires inline pour catégorie et sous-catégorie (comme les settings-panel existants)
- [ ] Lien de navigation dans le menu principal (sidebar) vers `/stock`
- [ ] SCSS `resources/scss/stock/_index-page.scss` (BEM, cohérent avec les autres pages)
- [ ] Import dans `resources/scss/app.scss`

### Règles UI

- Les prix sont affichés en euros (ex. `2,80 €`) mais envoyés en centimes à l'API (× 100)
- Le stock est affiché avec une couleur : vert si > 0, orange si = 0, gris si non applicable
- La sélection d'une sous-catégorie dans l'arborescence filtre la liste d'articles à droite
- Si aucune sous-catégorie sélectionnée, afficher tous les articles (ou message d'invitation à sélectionner)

### Critères d'acceptation

- On peut créer, modifier, supprimer une catégorie et une sous-catégorie
- On peut créer, modifier, supprimer un article
- La liste d'articles se met à jour lors de la sélection d'une sous-catégorie
- Les prix sont correctement convertis euros ↔ centimes entre UI et API

---

## Ticket 22.9 : Page stock — mouvements et entrées manuelles

**Type** : Frontend
**Priorité** : Haute
**Estimation** : 2h

### Description

Sur la fiche d'un article, afficher l'historique des mouvements de stock et permettre d'ajouter des entrées/sorties manuelles.

### Structure

```
Fiche article (panneau ou modale depuis la liste)
├── Infos article (référence, désignation, catégorie, fournisseur)
├── Stock actuel : [8 pièces]
├── [+ Entrée stock] [- Sortie stock]
└── Historique des mouvements
    ├── Date | Type | Qté | Prix unit. | Source | Note
    ├── 15/06/2026  Entrée manuelle  +5  2,80€  —  Réception commande
    ├── 10/06/2026  Devis #42  -1  2,80€  Dupont Jean  —
    └── ...
```

### Tâches

- [ ] Panneau ou modale `ArticleStockPanel.tsx` accessible depuis la ligne d'article
- [ ] Affichage du stock courant en gros
- [ ] Formulaire entrée manuelle : type (Entrée / Sortie), quantité, prix unitaire (optionnel), note
- [ ] Liste des mouvements avec source affichée en texte lisible (ex. "Devis #42 — Dupont Jean", "Entrée manuelle", "Maintenance Vélo #3")
- [ ] Bouton supprimer sur les mouvements manuels uniquement
- [ ] SCSS dans `resources/scss/stock/_stock-panel.scss`

### Critères d'acceptation

- Le stock affiché correspond à la somme des mouvements
- Un mouvement `quote_consumption` n'a pas de bouton supprimer
- Après ajout d'un mouvement, le stock et la liste se rechargent

---

## Ticket 22.10 : Autocomplete catalogue dans les lignes de devis

**Type** : Frontend
**Priorité** : Haute
**Estimation** : 2h

### Description

Dans le formulaire de devis (`QuoteLinesTable.tsx`), ajouter un champ de recherche catalogue sur chaque ligne. Quand l'employé tape 3 caractères, une liste déroulante propose les articles correspondants. La sélection pré-remplit la ligne.

### Comportement

1. L'employé commence à taper dans le champ référence ou désignation d'une ligne
2. Après 3 caractères : appel `GET /api/articles/search?q=...`
3. Une liste déroulante apparaît avec les résultats (référence + désignation + stock disponible)
4. L'employé sélectionne un article : la ligne est pré-remplie avec référence, désignation, prix d'achat HT, prix de vente HT, TVA
5. Les champs restent modifiables après sélection
6. Un badge visuel (ex. icône catalogue) indique que la ligne est liée à un article du catalogue
7. Un bouton "Détacher" retire le `article_id` et repasse en saisie libre

### Tâches

- [ ] Composant `ArticleAutocomplete.tsx` réutilisable (input + dropdown + debounce 300ms)
- [ ] Intégrer dans chaque ligne de `QuoteLinesTable.tsx` à la place ou en complément du champ référence actuel
- [ ] Gérer l'état `article_id` sur chaque ligne dans le state du formulaire devis
- [ ] Envoyer `article_id` dans le payload de sauvegarde du devis
- [ ] Badge visuel "catalogue" si `article_id` renseigné
- [ ] Bouton "Détacher" qui null-ifie `article_id` sans effacer les champs remplis

### Critères d'acceptation

- L'autocomplete ne se déclenche pas avant 3 caractères
- La sélection d'un article remplit bien tous les champs de la ligne
- Une ligne pré-remplie depuis le catalogue reste modifiable
- Le `article_id` est conservé à la sauvegarde du devis
- La saisie libre fonctionne toujours si on n'utilise pas l'autocomplete

---

## Ticket 22.11 : Modale catalogue dans les lignes de devis

**Type** : Frontend
**Priorité** : Haute
**Estimation** : 2h30

### Description

Ajouter un bouton "Catalogue" sur chaque ligne de devis qui ouvre une modale pleine largeur permettant de naviguer dans l'arborescence et sélectionner un article. Complément de l'autocomplete pour les employés qui ne connaissent pas la référence.

### Comportement

- Clic sur le bouton catalogue d'une ligne → ouvre `CataloguePickerModal`
- Modale pleine largeur avec :
  - Colonne gauche (30%) : arborescence catégories/sous-catégories navigable
  - Colonne droite (70%) : liste des articles de la sélection, avec barre de recherche en tête
  - Chaque article affiché : référence, désignation, prix vente, stock disponible
  - Clic sur un article → ferme la modale et pré-remplit la ligne (même comportement que l'autocomplete)
- La modale a un fond sombre et une croix de fermeture
- La recherche dans la modale filtre en temps réel dans la colonne droite

### Tâches

- [ ] Composant `CataloguePickerModal.tsx`
- [ ] Chargement des catégories via `GET /api/article-categories` à l'ouverture
- [ ] Chargement des articles via `GET /api/articles?subcategory_id=X` au clic sur une sous-catégorie
- [ ] Recherche temps réel via `GET /api/articles/search?q=...` (débounce 300ms)
- [ ] Callback `onSelect(article)` transmis au parent qui pré-remplit la ligne
- [ ] Bouton "Catalogue" discret sur chaque ligne de `QuoteLinesTable.tsx` (icône ou texte court)
- [ ] SCSS `resources/scss/stock/_catalogue-picker.scss`

### Critères d'acceptation

- La modale s'ouvre sur le bon contexte (la ligne cliquée)
- La sélection d'un article ferme la modale et pré-remplit correctement la ligne
- La recherche dans la modale est indépendante de la navigation par catégorie
- La modale est fermable sans sélection (la ligne reste inchangée)

---

## Ticket 22.12 : Autocomplete et modale catalogue dans les logs de maintenance

**Type** : Frontend
**Priorité** : Moyenne
**Estimation** : 1h

### Description

Même intégration que les tickets 22.10 et 22.11, appliquée au formulaire de log de maintenance (`BikeMaintenanceLog` dans la fiche vélo).

### Tâches

- [ ] Réutiliser `ArticleAutocomplete.tsx` dans le formulaire de maintenance
- [ ] Réutiliser `CataloguePickerModal.tsx`
- [ ] Envoyer `article_id` dans le payload de création/mise à jour du log
- [ ] Badge catalogue sur les lignes de maintenance liées à un article

### Critères d'acceptation

- Même UX que dans les devis
- `article_id` persiste à la sauvegarde du log de maintenance
