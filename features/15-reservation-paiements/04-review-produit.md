# Review produit

## Clarifications apportées

### 1. Pas de `created_by`
Le champ `created_by` est retiré du schéma. La table `reservation_payments` reste simple :
```php
$table->id();
$table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
$table->decimal('amount', 10, 2);
$table->enum('method', ['cb', 'liquide', 'cheque', 'virement', 'autre']);
$table->dateTime('paid_at');
$table->string('payer_name')->nullable();
$table->text('note')->nullable();
$table->timestamps();
```

### 2. Gestion des paiements : opérateur maître
- Les paiements peuvent être **librement édités ou supprimés** par l'opérateur
- Pas de soft delete ni d'audit trail — l'opérateur corrige ses erreurs de saisie comme il le souhaite
- La suppression d'une réservation entraîne la suppression de ses paiements (cascade)

### 3. Validation montant : pas de blocage
- Le front **affiche clairement** l'état (total encaissé vs TTC, reste dû, dépassement éventuel)
- **Aucun blocage technique** : l'opérateur reste maître de ses choix
- Les badges couleur (vert/orange/rouge) servent d'indicateurs visuels, pas de verrous

### 4. Tests
Les tests doivent couvrir le workflow réel :
1. Créer une réservation (sans paiements)
2. Modifier la réservation au retour des vélos pour inclure les paiements
3. Vérifier les calculs (totalPaid, remaining)
