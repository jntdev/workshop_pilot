# Contraintes techniques

1. **Modèle de données**  
   - Créer la table `reservation_payments` :  
     ```php
     $table->id();
     $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
     $table->decimal('amount', 10, 2);
     $table->enum('method', ['cb', 'liquide', 'cheque', 'virement', 'autre']);
     $table->dateTime('paid_at');
     $table->string('payer_name')->nullable();
     $table->text('note')->nullable();
     $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
     $table->timestamps();
     ```
   - Relation `Reservation::payments()` + helpers `totalPaid()` / `remaining()`.

2. **API**  
   - `StoreReservationRequest` / `UpdateReservationRequest` : accepter `payments: [{ amount, method, paid_at, payer_name?, note? }]`.  
   - Contrôleur : transaction → créer/MAJ réservation, puis synchroniser les paiements (delete+create ou upsert).  
   - Validation : montants > 0, `paid_at` date, somme vs total si statut `paye`.

3. **Front**  
   - Étendre `ReservationFormData` avec `payments: PaymentLine[]`.  
   - Section UI avec tableau et bouton “Ajouter un paiement”.  
   - Calcul `totalEncaisse = payments.reduce(...)`.  
   - Si statut `paye` mais `totalEncaisse < prix_total_ttc`, bloquer la sauvegarde.  
   - Auto-remplir `paiement_final_le` = date du dernier paiement (modifiable).

4. **Compatibilité**  
   - Backfill : créer une migration pour transformer `acompte_paye_le` / `paiement_final_le` existants en lignes de paiement si possible.  
   - Conserver les champs historiques pour ne pas casser les exports actuels (ils pourront être dépréciés plus tard).

5. **Tests**  
   - Tests Feature API : création avec paiements multiples, statut paye sans total suffisant → erreur.  
   - Tests front : ajout/suppression de ligne, calcul reste dû, blocage statut paye.
