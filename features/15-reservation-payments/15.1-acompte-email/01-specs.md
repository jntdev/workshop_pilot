# Feature 15.1 : Email de demande d'acompte

## Contexte

Actuellement, quand une réservation nécessite un acompte, l'utilisateur doit contacter le client manuellement (téléphone, email perso) pour lui communiquer le montant et les coordonnées bancaires. Cette feature automatise l'envoi d'un email professionnel de demande d'acompte.

## Objectif

Permettre l'envoi d'un email automatisé au client pour lui demander de verser un acompte, avec :
- Le montant exact à verser
- Les coordonnées bancaires (RIB)
- Les instructions pour le libellé du virement (nom de la réservation pour rapprochement bancaire)

## Spécifications fonctionnelles

### Déclenchement

Un bouton "Envoyer demande d'acompte" apparaît dans la section Acompte du formulaire de réservation quand :
- La checkbox "Acompte demandé" est cochée
- Le montant de l'acompte est renseigné (> 0)
- Le client sélectionné possède une adresse email

### Contenu de l'email

**Objet** : Demande d'acompte - Réservation du {date_reservation} au {date_retour}

**Corps** :
```
Bonjour,

Ce mail est envoyé automatiquement car votre réservation a été renseignée dans notre agenda.
Si vous avez des questions, vous pouvez directement répondre à ce mail.

Pour valider votre réservation, un acompte est nécessaire.
Il faudra donc virer {montant_acompte} € sur le compte dont vous trouverez les informations ci-dessous.

COORDONNÉES BANCAIRES
---------------------
IBAN : {iban}
BIC : {bic}
Titulaire : {titulaire}
Banque : {banque}

⚠️ N'oubliez pas de mentionner le nom de votre réservation "{NOM Prénom}"
dans le libellé du virement pour que nous puissions faire le rapprochement bancaire.

Merci de procéder au virement dans un délai de 7 jours.

Cordialement,
L'équipe Location
```

### Boîte mail dédiée

L'email est envoyé depuis une boîte mail spécifique au métier "Location" (différente de la boîte mail par défaut de l'application). Cela permet :
- Une séparation claire des communications
- Un suivi plus facile des échanges location
- Une adresse d'expéditeur professionnelle dédiée

### Feedback utilisateur

- Bouton avec état de chargement pendant l'envoi
- Message de succès : "Email envoyé à {email}"
- Message d'erreur si échec (client sans email, erreur SMTP, etc.)

## Spécifications techniques

### Configuration (.env)

```env
# Boîte mail Location
MAIL_LOCATION_MAILER=smtp
MAIL_LOCATION_HOST=
MAIL_LOCATION_PORT=587
MAIL_LOCATION_USERNAME=
MAIL_LOCATION_PASSWORD=
MAIL_LOCATION_ENCRYPTION=tls
MAIL_LOCATION_FROM_ADDRESS=
MAIL_LOCATION_FROM_NAME="Location Vélos - Workshop Pilot"

# Coordonnées bancaires Location
LOCATION_RIB_IBAN=
LOCATION_RIB_BIC=
LOCATION_RIB_TITULAIRE=
LOCATION_RIB_BANQUE=
```

### Nouveau mailer dans config/mail.php

```php
'mailers' => [
    // ... existing mailers ...

    'location' => [
        'transport' => 'smtp',
        'host' => env('MAIL_LOCATION_HOST'),
        'port' => env('MAIL_LOCATION_PORT', 587),
        'encryption' => env('MAIL_LOCATION_ENCRYPTION', 'tls'),
        'username' => env('MAIL_LOCATION_USERNAME'),
        'password' => env('MAIL_LOCATION_PASSWORD'),
        'timeout' => null,
    ],
],
```

### Route API

```
POST /api/reservations/{id}/send-acompte-email
```

**Response 200** :
```json
{
    "success": true,
    "message": "Email envoyé à client@example.com"
}
```

**Response 422** (validation) :
```json
{
    "success": false,
    "error": "Le client n'a pas d'adresse email"
}
```

### Classes PHP

- `App\Mail\AcompteRequestMail` - Mailable
- Méthode `sendAcompteEmail()` dans `ReservationController`

### Template email

- `resources/views/emails/acompte-request.blade.php`
- Design simple et professionnel
- Compatible mobile

## Checklist d'implémentation

- [ ] Ajouter variables .env pour mailer Location
- [ ] Ajouter variables .env pour RIB
- [ ] Configurer mailer "location" dans config/mail.php
- [ ] Créer AcompteRequestMail.php
- [ ] Créer template email acompte-request.blade.php
- [ ] Ajouter route POST /api/reservations/{id}/send-acompte-email
- [ ] Implémenter sendAcompteEmail() dans ReservationController
- [ ] Ajouter bouton dans ReservationForm.tsx
- [ ] Gérer états loading/success/error
- [ ] Tester envoi email
- [ ] Mettre à jour .env.example

## Questions en attente

1. Credentials SMTP de la boîte mail Location
2. Coordonnées bancaires (IBAN, BIC, titulaire, banque)
