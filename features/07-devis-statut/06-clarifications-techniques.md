# 06 - Clarifications techniques

## Décisions validées lors de l'échange

### Statut `modifiable` - Cas d'usage confirmé
**Besoin métier** : Permettre l'édition d'un devis devant le client sans dévoiler les marges.

**Exemple concret** : Un mécanicien présente un devis (`prêt`), découvre une pièce cassée supplémentaire, et doit l'ajouter immédiatement sans montrer ses prix d'achat ni ses marges.

**Responsabilité** : Les employés doivent connaître leurs tarifs d'achat pour travailler à la volée en mode `modifiable`.

### Transitions de statuts

**Confirmées** :
- `brouillon` → `prêt` (uniquement, pas de passage direct à `modifiable`)
- `prêt` ↔ `modifiable` (bidirectionnel)
- `prêt` → `facturé` (avec modale de confirmation)
- `modifiable` → `facturé` (possible si besoin)
- `facturé` → (aucune transition - état terminal)

**Rejetées** :
- ❌ `brouillon` → `modifiable` (toujours passer par `prêt` d'abord)

### Permissions d'édition par statut

- **`brouillon`** : Tout modifiable, tous les champs visibles (y compris marges et prix d'achat)
- **`prêt`** : Lecture seule stricte (sauf changement de statut)
- **`modifiable`** : Tout modifiable SAUF champs privés masqués (prix d'achat, marges)
- **`facturé`** : Lecture seule stricte (aucune modification, état terminal)

### Champs modifiables en mode `modifiable`

✅ **Autorisés** :
- Ajouter une ligne
- Supprimer une ligne
- Modifier titre/description d'une ligne
- Modifier quantité (si implémenté)
- Modifier prix de vente HT
- Modifier référence ligne
- Modifier infos client (nom, adresse, téléphone, email)
- Modifier remise globale
- Modifier date de validité

❌ **Masqués** (mais conservés en base) :
- Prix d'achat HT
- Marge montant HT
- Marge pourcentage

## Gestion du prix d'achat nullable

### Problématique identifiée
En mode `modifiable`, les champs privés (dont `purchase_price_ht`) sont masqués. Lors de l'ajout d'une nouvelle ligne, l'utilisateur ne peut pas renseigner le prix d'achat, ce qui pose un problème de cohérence des données.

### Solution adoptée : Distinction `null` vs `0`

**3 états possibles pour `purchase_price_ht`** :
- `null` = Non renseigné (ligne incomplète, ajoutée en mode `modifiable`)
- `0.00` = Main d'œuvre (pas de coût d'achat, marge = 100% du prix de vente)
- `> 0` = Pièce/fourniture (coût d'achat réel)

### Migration de base de données

**Fichier** : `database/migrations/2025_11_18_075915_make_purchase_price_ht_nullable_in_quote_lines_table.php`

```php
Schema::table('quote_lines', function (Blueprint $table) {
    $table->decimal('purchase_price_ht', 10, 2)->nullable()->default(null)->change();
});
```

### Comportement selon le statut

#### Mode `brouillon`
- ✅ Champ `purchase_price_ht` **visible et modifiable**
- ✅ Peut être laissé vide (`null`), mis à `0` (main d'œuvre), ou renseigné
- ✅ Calcul de marge automatique :
  - Si `null` → marge non calculée, indicateur visuel "À compléter"
  - Si `0` → marge = 100% du prix de vente (main d'œuvre)
  - Si `> 0` → marge calculée normalement

#### Mode `modifiable`
- ❌ Champ `purchase_price_ht` **masqué**
- ⚠️ Nouvelles lignes créées avec `purchase_price_ht = null`
- 💾 Lignes existantes conservent leur valeur
- 🏷️ Badge visuel "Prix d'achat à vérifier" sur les lignes avec `purchase_price_ht = null`

#### Mode `prêt`
- 👁️ Lecture seule, marges masquées

### Validation avant facturation

**Règle critique** : Impossible de passer en statut `facturé` si des lignes ont `purchase_price_ht = null`.

**Implémentation dans le Model Quote** :

```php
public function canBeInvoiced(): bool
{
    return !$this->lines()->whereNull('purchase_price_ht')->exists();
}

public function hasIncompleteLines(): bool
{
    return $this->lines()->whereNull('purchase_price_ht')->exists();
}

public function getIncompleteLinesCount(): int
{
    return $this->lines()->whereNull('purchase_price_ht')->count();
}
```

**Message d'erreur** : "Impossible de facturer : X ligne(s) sans prix d'achat. Passez en brouillon pour les compléter."

**Autorisation** : Les lignes avec `purchase_price_ht = 0` (main d'œuvre) sont autorisées pour la facturation.

### Interface utilisateur

**En mode `modifiable` - Ligne incomplète** :
```
┌─────────────────────────────────────┐
│ Titre: Réglage dérailleur          │
│ Prix de vente HT: 25.00 €          │
│ TVA: 20%                            │
│ Prix TTC: 30.00 €                   │
│                                     │
│ ⚠️  Prix d'achat à définir          │
│    (passer en brouillon)            │
└─────────────────────────────────────┘
```

**En mode `brouillon` - Complétion** :
```
┌─────────────────────────────────────┐
│ Titre: Réglage dérailleur          │
│ Prix d'achat HT: [____] €          │
│   ou ☐ Main d'œuvre (0€)           │
│ Prix de vente HT: 25.00 €          │
│ Marge: [Calculée automatiquement]  │
└─────────────────────────────────────┘
```

### Workflow complet avec prix d'achat nullable

1. **Création devis** (`brouillon`)
   - Saisie complète avec prix d'achat renseignés

2. **Client arrive** (`prêt`)
   - Présentation du devis, marges masquées

3. **Découverte problème supplémentaire** (`modifiable`)
   - Ajout ligne "Changement câble: 15€ HT"
   - `purchase_price_ht = null` (badge "À vérifier")
   - Client ne voit pas les champs privés

4. **Tentative de facturation**
   - ❌ Bloqué : "1 ligne sans prix d'achat"
   - Message : "Passez en brouillon pour compléter"

5. **Retour en brouillon**
   - Compléter : `purchase_price_ht = 8€`
   - Ou cocher "Main d'œuvre" : `purchase_price_ht = 0€`
   - Marge recalculée automatiquement

6. **Passage `prêt` → `facturé`**
   - ✅ Autorisé (toutes les lignes complètes)

## Avantages de cette architecture

✅ **Distinction sémantique claire** : `0` (main d'œuvre) ≠ `null` (non renseigné)
✅ **Flexibilité opérationnelle** : Travail devant client possible sans compromettre les données
✅ **Sécurité des données** : Impossible de facturer avec des données incomplètes
✅ **Traçabilité** : Identification claire des lignes ajoutées en mode `modifiable`
✅ **UX intuitive** : Indicateurs visuels et messages d'erreur explicites
✅ **Intégrité métier** : Force la vérification avant facturation

## Notes d'implémentation

### Ordre des priorités

1. Migration du champ `purchase_price_ht` nullable (déjà créée)
2. Enum `QuoteStatus` avec méthodes de transition
3. Méthodes de validation dans le Model `Quote`
4. Adaptation du composant Livewire `Form` pour gérer les statuts
5. Conditionnels de vue pour masquer/afficher selon le statut
6. Indicateurs visuels pour les lignes incomplètes
7. Validation côté frontend avant tentative de facturation
8. Tests unitaires et d'intégration

### Compatibilité avec les données existantes

**Impact sur les seeders** : Les seeders actuels créent des lignes avec `purchase_price_ht` renseigné, donc compatibles.

**Migration des données existantes** : Aucune donnée existante n'aura `purchase_price_ht = null` après la migration (ancien `default(0)` conservé pour les lignes existantes).
