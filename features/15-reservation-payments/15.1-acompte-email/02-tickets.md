# Tickets - Feature 15.1 : Email de demande d'acompte

---

## Ticket 15.1.1 : Configuration mailer Location

**Type** : Backend / Config
**Priorité** : Haute
**Estimation** : 15 min

### Description
Configurer un mailer SMTP dédié pour les emails du métier Location.

### Tâches
- [ ] Ajouter les variables dans `.env.example` :
  ```
  MAIL_LOCATION_HOST=
  MAIL_LOCATION_PORT=587
  MAIL_LOCATION_USERNAME=
  MAIL_LOCATION_PASSWORD=
  MAIL_LOCATION_ENCRYPTION=tls
  MAIL_LOCATION_FROM_ADDRESS=
  MAIL_LOCATION_FROM_NAME="Location Vélos"
  ```
- [ ] Ajouter les variables RIB :
  ```
  LOCATION_RIB_IBAN=
  LOCATION_RIB_BIC=
  LOCATION_RIB_TITULAIRE=
  LOCATION_RIB_BANQUE=
  ```
- [ ] Ajouter le mailer `location` dans `config/mail.php`
- [ ] Ajouter config `location.rib` dans `config/services.php` ou nouveau fichier `config/location.php`

### Critères d'acceptation
- Le mailer `location` est disponible
- Les credentials sont externalisés dans .env
- Les coordonnées bancaires sont accessibles via config()

---

## Ticket 15.1.2 : Créer le Mailable AcompteRequestMail

**Type** : Backend
**Priorité** : Haute
**Estimation** : 30 min

### Description
Créer la classe Mailable pour l'email de demande d'acompte.

### Tâches
- [ ] Créer `app/Mail/AcompteRequestMail.php` via artisan
- [ ] Passer les données nécessaires :
  - Reservation (avec client, items)
  - Montant acompte
  - Coordonnées bancaires (depuis config)
- [ ] Configurer le mailer `location`
- [ ] Définir le sujet dynamique

### Code attendu
```php
class AcompteRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Reservation $reservation,
        public float $montantAcompte
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Demande d'acompte - Réservation du {$this->reservation->date_reservation} au {$this->reservation->date_retour}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.acompte-request',
            with: [
                'rib' => config('location.rib'),
            ],
        );
    }
}
```

### Critères d'acceptation
- Le Mailable utilise le mailer `location`
- Toutes les données sont accessibles dans le template

---

## Ticket 15.1.3 : Créer le template email

**Type** : Backend / View
**Priorité** : Haute
**Estimation** : 30 min

### Description
Créer le template Blade pour l'email de demande d'acompte.

### Tâches
- [ ] Créer `resources/views/emails/acompte-request.blade.php`
- [ ] Design simple, texte brut (pas de HTML complexe)
- [ ] Inclure :
  - Introduction automatique
  - Montant acompte
  - Coordonnées bancaires formatées
  - **Instruction libellé virement** (nom du client)
  - Délai 7 jours
  - Signature

### Contenu exact
```
Bonjour,

Ce mail est envoyé automatiquement car votre réservation a été renseignée dans notre agenda.
Si vous avez des questions, vous pouvez directement répondre à ce mail.

Pour valider votre réservation, un acompte est nécessaire.
Il faudra donc virer {{ $montantAcompte }} € sur le compte dont vous trouverez les informations ci-dessous.

COORDONNÉES BANCAIRES
---------------------
IBAN : {{ $rib['iban'] }}
BIC : {{ $rib['bic'] }}
Titulaire : {{ $rib['titulaire'] }}
Banque : {{ $rib['banque'] }}

⚠️ N'oubliez pas de mentionner le nom de votre réservation "{{ $clientNom }}"
dans le libellé du virement pour que nous puissions faire le rapprochement bancaire.

Merci de procéder au virement dans un délai de 7 jours.

Cordialement,
L'équipe Location
```

### Critères d'acceptation
- Email lisible sur mobile
- Instruction libellé bien visible
- Pas de HTML complexe (compatibilité email clients)

---

## Ticket 15.1.4 : Route et Controller

**Type** : Backend
**Priorité** : Haute
**Estimation** : 30 min

### Description
Créer l'endpoint API pour envoyer l'email de demande d'acompte.

### Tâches
- [ ] Ajouter route dans `routes/api.php` :
  ```php
  Route::post('/reservations/{reservation}/send-acompte-email', [ReservationController::class, 'sendAcompteEmail']);
  ```
- [ ] Implémenter `sendAcompteEmail()` dans ReservationController :
  - Valider que la réservation existe
  - Valider que le client a un email
  - Valider que acompte_montant > 0
  - Envoyer le mail via mailer `location`
  - Retourner réponse JSON

### Validations
- Réservation existe
- Client avec email
- Montant acompte renseigné

### Critères d'acceptation
- Endpoint accessible et sécurisé
- Messages d'erreur clairs
- Email envoyé via le bon mailer

---

## Ticket 15.1.5 : Bouton frontend

**Type** : Frontend
**Priorité** : Haute
**Estimation** : 45 min

### Description
Ajouter le bouton d'envoi d'email dans la section Acompte du formulaire de réservation.

### Tâches
- [ ] Ajouter bouton "Envoyer demande d'acompte" dans ReservationForm.tsx
- [ ] Conditions d'affichage :
  - `acompte_demande === true`
  - `acompte_montant > 0`
  - `selectedClient?.email` existe
  - Réservation déjà créée (editingReservation)
- [ ] État `isSendingEmail` pour le loading
- [ ] Appel API `POST /api/reservations/{id}/send-acompte-email`
- [ ] Afficher feedback (toast ou message inline)
- [ ] Style cohérent avec le reste du formulaire

### UI suggérée
```
[Section Acompte]
☑️ Acompte demandé
Montant : [___150___] € (suggéré: 150.00 €)
Date paiement : [__/__/____]

[📧 Envoyer demande d'acompte]  ← nouveau bouton
```

### Critères d'acceptation
- Bouton visible uniquement si conditions remplies
- État loading pendant l'envoi
- Message de succès/erreur affiché
- Pas d'envoi possible si réservation non sauvegardée

---

## Ticket 15.1.6 : Tests

**Type** : Test
**Priorité** : Moyenne
**Estimation** : 30 min

### Description
Écrire les tests pour la fonctionnalité d'envoi d'email d'acompte.

### Tâches
- [ ] Test unitaire AcompteRequestMail (contenu, mailer)
- [ ] Test feature endpoint :
  - Succès envoi
  - Erreur client sans email
  - Erreur montant manquant
  - Erreur réservation inexistante
- [ ] Vérifier que le bon mailer est utilisé (Mail::fake)

### Critères d'acceptation
- Tous les tests passent
- Couverture des cas d'erreur

---

## Récapitulatif

| Ticket | Description | Estimation |
|--------|-------------|------------|
| 15.1.1 | Configuration mailer Location | 15 min |
| 15.1.2 | Mailable AcompteRequestMail | 30 min |
| 15.1.3 | Template email | 30 min |
| 15.1.4 | Route et Controller | 30 min |
| 15.1.5 | Bouton frontend | 45 min |
| 15.1.6 | Tests | 30 min |
| **Total** | | **~3h** |

## Dépendances

- Credentials SMTP boîte Location (à fournir)
- Coordonnées bancaires RIB (à fournir)
