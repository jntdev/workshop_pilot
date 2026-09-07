# Feedback — Feature 27

## Statut

La réécriture a bien traité les points bloquants du feedback précédent :

- le flux code-barres inconnu demande maintenant une quantité ;
- la création rapide crée l'article et le mouvement `manual_in` dans le même appel serveur ;
- un endpoint dédié `POST /api/inventory/articles` isole la validation stricte du flux inventaire ;
- la capture photo est séparée du scanner ;
- la dette des uploads photo orphelins est assumée explicitement ;
- les clés virtuelles d'attributs sont traduites avant persistance.

La feature est donc globalement cohérente. Les points ci-dessous sont les derniers risques à lever avant ou pendant l'implémentation.

## Points à corriger ou clarifier

### 1. Le backend ne garantit pas explicitement `reference === barcode`

Le cadrage dit que le code-barres scanné devient à la fois `barcode` et `reference`.

Le payload frontend prévu envoie bien :

```typescript
reference: scannedCode,
barcode: scannedCode,
```

Mais les règles de `StoreInventoryArticleRequest` valident actuellement `reference` et `barcode` séparément :

```php
'reference' => ['required', 'string', 'max:100', 'unique:articles,reference'],
'barcode' => ['required', 'string', 'max:64'],
```

Risque : un client peut envoyer `reference !== barcode`. L'article serait alors créé dans un état contraire aux critères d'acceptation, sauf si le contrôleur ignore la référence reçue.

Attendu : choisir une règle non ambiguë :

- soit retirer `reference` du payload attendu et la dériver côté serveur depuis `barcode` ;
- soit conserver les deux champs mais ajouter une validation explicite de type `same:barcode` sur `reference`.

Le ticket de tests doit aussi vérifier ce contrat. Aujourd'hui, la liste des champs obligatoires testés oublie `reference`, alors que le request le déclare obligatoire.

### 2. Le parcours `ambiguous` n'indique pas quoi faire après le choix utilisateur

Le ticket 05 indique qu'un code-barres partagé affiche une liste à choix, sans sélection automatique.

Il manque l'étape suivante : quand l'utilisateur choisit un article dans cette liste, le comportement doit être défini exactement comme pour un `found`.

Attendu : préciser dans le ticket 05 :

- sélection d'un article ambigu ;
- `setScannedArticle(article)` ;
- `setAmbiguousArticles(null)` ;
- champ quantité vide ;
- scanner toujours en pause ;
- validation via `POST /api/articles/{id}/stock-movements` avec `type: manual_in` ;
- retour au scan après succès.

Sans cette précision, l'implémentation peut afficher la liste mais laisser le flux sans action claire.

### 3. Les attributs acceptés par `StoreInventoryArticleRequest` sont trop permissifs

Le formulaire charge les attributs via `GET /api/article-subcategories/{id}/filter-options`, donc l'interface ne devrait proposer que des attributs pertinents pour la sous-catégorie choisie.

Côté serveur, la validation prévue est seulement :

```php
'attributes' => ['sometimes', 'array'],
'attributes.*' => ['nullable', 'string', 'max:255'],
```

Risque : un client peut envoyer des clés arbitraires ou des attributs qui ne correspondent pas à la sous-catégorie choisie. Ces valeurs peuvent polluer `article_attributes` et rendre les filtres incohérents.

Attendu : définir une règle serveur explicite :

- accepter uniquement les clés retournées par `filterOptions()` pour la sous-catégorie reçue ;
- accepter les clés virtuelles connues uniquement pour les traduire (`wheel_diameter`, `wheel_width_mm`, `wheel_width_inches`, `tooth_range_min`, `tooth_range_max`) ;
- ignorer ou rejeter toute clé inconnue ;
- ne pas persister les valeurs nulles ou vides.

### 4. `image_url` ne prouve pas que la photo vient de l'upload prévu

Le flux produit impose une photo pour la création rapide, mais la requête finale valide seulement une chaîne `image_url`.

Risque : le backend peut accepter n'importe quelle URL ou chemin arbitraire comme preuve de photo. Ce n'est pas forcément critique, mais ce n'est pas équivalent à "photo capturée et uploadée".

Attendu : trancher explicitement :

- soit validation stricte : `image_url` doit correspondre au chemin retourné par `POST /api/articles/upload-photo`, par exemple un chemin local sous `/storage/articles/` ;
- soit dette acceptée : la garantie "photo obligatoire" est surtout frontend en v1, et le backend exige seulement une URL non vide.

## Priorité recommandée

Avant développement ou avant validation finale, je traiterais dans cet ordre :

1. verrouiller `reference === barcode` côté serveur et dans les tests ;
2. compléter le parcours `ambiguous` ;
3. filtrer les clés d'attributs acceptées par le backend ;
4. décider le niveau de validation attendu pour `image_url`.
