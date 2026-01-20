# 04 - Étapes d'implémentation

## Ordre de priorité

1. **Migration base de données**
   - ✅ Rendre `purchase_price_ht` nullable dans `quote_lines` (déjà fait : migration 2025_11_18_075915)
   - Ajouter une colonne `status` (enum natif MySQL) à la table `quotes` avec valeur par défaut `brouillon`
   - Valeurs : `'brouillon', 'prêt', 'modifiable', 'facturé'`

2. **Enum PHP `QuoteStatus`**
   - Créer `app/Enums/QuoteStatus.php` (backed enum avec string)
   - Implémenter méthodes :
     - `canTransitionTo(QuoteStatus $newStatus): bool`
     - `showMargins(): bool`
     - `isEditable(): bool`
     - `canShowPurchasePrice(): bool`

3. **Model Quote - Méthodes de validation**
   - `canBeInvoiced(): bool` - Vérifie que toutes les lignes ont `purchase_price_ht` renseigné
   - `hasIncompleteLines(): bool` - Détecte les lignes avec `purchase_price_ht = null`
   - `getIncompleteLinesCount(): int` - Compte les lignes incomplètes
   - Ajouter cast : `'status' => QuoteStatus::class`

4. **Model Quote - Méthodes de transition**
   - `markAsReady(): void` - Transition vers `prêt`
   - `markAsModifiable(): void` - Transition vers `modifiable`
   - `markAsInvoiced(): void` - Transition vers `facturé` (avec validation)
   - Chaque méthode doit :
     - Vérifier que la transition est autorisée via `QuoteStatus::canTransitionTo()`
     - Lancer une exception si transition interdite
     - Mettre à jour le statut

5. **Composant Livewire Form**
   - Ajouter propriété publique `$status`
   - Méthode `changeStatus(string $newStatus)` pour les transitions
   - Conditionnels pour masquer/afficher champs selon `$status`
   - Validation : empêcher sauvegarde en `facturé` si lignes incomplètes
   - En mode `modifiable` : nouvelles lignes créées avec `purchase_price_ht = null`

6. **Templates Blade**
   - Select de statut dans l'entête du formulaire
   - Conditionnels `@if($status === 'brouillon')` pour champs privés
   - Badges visuels pour lignes incomplètes (`purchase_price_ht = null`)
   - Modale de confirmation pour passage en `facturé`
   - Chip de statut actuel (Brouillon, Prêt, Modifiable, Facturé)

7. **Feedback Banner**
   - Message à chaque changement de statut
   - Message d'erreur si tentative de facturation avec lignes incomplètes
   - Alerte si passage en `modifiable` (rappel que prix d'achat seront masqués)

8. **QuoteCalculator - Gestion du null**
   - Gérer le cas `purchase_price_ht = null` dans les calculs
   - Retourner marge = `null` si prix d'achat non renseigné
   - Accepter `purchase_price_ht = 0` pour main d'œuvre (marge = 100%)

## Détails techniques

### Enum QuoteStatus (exemple)

```php
enum QuoteStatus: string
{
    case Draft = 'brouillon';
    case Ready = 'prêt';
    case Editable = 'modifiable';
    case Invoiced = 'facturé';

    public function canTransitionTo(self $newStatus): bool
    {
        return match ($this) {
            self::Draft => $newStatus === self::Ready,
            self::Ready => in_array($newStatus, [self::Editable, self::Invoiced]),
            self::Editable => in_array($newStatus, [self::Ready, self::Invoiced]),
            self::Invoiced => false,
        };
    }

    public function showMargins(): bool
    {
        return $this === self::Draft;
    }

    public function isEditable(): bool
    {
        return in_array($this, [self::Draft, self::Editable]);
    }

    public function canShowPurchasePrice(): bool
    {
        return $this === self::Draft;
    }
}
```

### Migration quotes (à créer)

```php
Schema::table('quotes', function (Blueprint $table) {
    $table->enum('status', ['brouillon', 'prêt', 'modifiable', 'facturé'])
          ->default('brouillon')
          ->after('reference');
});
```

**Note** : Mapper les valeurs existantes `draft` → `brouillon`, `validated` → `prêt`

## Points d'attention

- **Compatibilité seeders** : Mettre à jour QuoteSeeder pour utiliser les nouvelles valeurs
- **Tests existants** : Adapter les tests qui utilisent `draft`/`validated`
- **Calculs de marge** : Gérer `purchase_price_ht = null` sans crasher
- **Validation front + back** : Double validation pour empêcher facturation avec lignes incomplètes

Voir [06-clarifications-techniques.md](06-clarifications-techniques.md) pour le détail complet de la gestion du `purchase_price_ht` nullable.
