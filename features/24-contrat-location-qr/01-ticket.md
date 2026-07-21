# Ticket 24 : Contrat de location signé via QR code

**Type** : Nouvelle feature — full stack  
**Priorité** : Haute  
**Estimation** : 2 à 3 jours

---

## Flow complet

```
Fiche réservation
      ↓
Bouton "Préparer le contrat"
      ↓
Page opérateur : accessoires + caution + aperçu
      ↓
QR code affiché à l'écran
      ↓
Client scanne → page légère sur son smartphone
      ↓
Client lit le résumé du contrat → dessine sa signature → "Je signe"
      ↓
Confirmation → PDF généré → email au client → archivé dans la réservation
      ↓
Écran opérateur : "Contrat signé ✓"
```

---

## 1. Base de données

### Table `location_contracts`

| Colonne | Type | Description |
|---|---|---|
| `id` | bigint PK | |
| `reservation_id` | FK → reservations | |
| `token` | uuid unique | URL publique du contrat |
| `accessories` | json | Liste des accessoires cochés avec quantités |
| `caution_amount` | integer | Montant en centimes |
| `operator_name` | string | Nom de l'opérateur ayant remis les vélos (Nicolas / Jonathan) |
| `contract_data` | json | Snapshot des données au moment de la génération |
| `signed_at` | timestamp nullable | Date/heure de signature |
| `signature_image` | text nullable | Base64 PNG de la signature dessinée |
| `signer_name` | string nullable | Nom saisi par le locataire |
| `pdf_path` | string nullable | Chemin du PDF archivé |
| `expires_at` | timestamp | Expiration du lien (ex. 24h) |
| `created_at` / `updated_at` | timestamps | |

### Modification `reservations`
- Ajouter `contract_signed_at` (timestamp nullable) pour afficher le statut depuis la grille.

---

## 2. Backend

### Modèle `LocationContract`
- Relation `belongsTo(Reservation)`
- Cast `accessories` et `contract_data` en array
- Scope `pending()` : non signés et non expirés

### Routes

```
POST   /api/reservations/{id}/contract          → générer/regénérer un contrat
GET    /location/contrat/{token}                → page publique client (Inertia, sans auth)
POST   /api/location/contrat/{token}/sign       → soumettre la signature
GET    /api/reservations/{id}/contract          → statut du contrat (polling opérateur)
```

### `ContractService`
- `generate(Reservation $r, array $accessories, int $cautionAmount): LocationContract`
  - Crée le token UUID
  - Fige `contract_data` (snapshot client + vélos + dates + accessoires + caution)
  - Calcule `expires_at` (24h par défaut)
- `sign(LocationContract $contract, string $signerName, string $signatureBase64): void`
  - Vérifie non expiré et non déjà signé
  - Enregistre `signed_at`, `signer_name`, `signature_image`
  - Appelle `ContractPdfService::generate()`
  - Envoie email via `ContractSignedMail`
  - Met à jour `reservations.contract_signed_at`

### `ContractPdfService`
Génère le PDF via Dompdf à partir d'une vue Blade `pdf/location-contract.blade.php`.

Structure du PDF (fidèle au document de référence) :
1. En-tête : logo + "Contrat de Location de Vélos" + numéro de réservation
2. Article 1 : Objet
3. Article 2 : Tableau accessoires remis (VAE, VTC, casques, sacoches, rétroviseurs, supports téléphone, béquilles, lumières, sièges enfant, remorques, antivols) avec cases cochées
4. Article 3 : Durée (date début, date fin, heure limite de retour — champ texte libre)
5. Articles 4 à 9 : texte fixe (tarifs, responsabilités, dépôt de garantie, résiliation, clause de non-responsabilité)
6. Article 10 : Caution — nom du locataire + montant + signature image
7. Bloc loueur : nom de l'opérateur sélectionné + date — pas de signature dessinée
8. Bloc locataire : nom + date de signature + signature image

### `ContractSignedMail`
- Destinataire : email du client
- Sujet : "Votre contrat de location – Les vélos d'Armor"
- Corps : résumé (dates, vélos, caution) + lien de téléchargement ou PDF en pièce jointe
- Utilise `reservation->client->email`

---

## 3. Frontend opérateur

### Bouton dans la fiche réservation
- Bouton "Préparer le contrat" visible dès que la réservation est en statut `en_cours` ou `reserve`
- Si un contrat existe déjà et est signé → affiche "Contrat signé le XX/XX/XXXX · Télécharger"
- Si un contrat existe non signé → affiche le QR code directement + option "Regénérer"

### Page / panneau de préparation
Formulaire avant génération du QR :

**Accessoires remis** (cases à cocher + quantité) :
- VAE (nb), VTC (nb)
- Casque, rétroviseur, sacoche/sac, support téléphone
- Béquille, lumière, antivol
- Siège enfant, remorque enfant

**Heure limite de retour** : champ texte libre (ex. "avant 18h", "avant fermeture", "avant 17h30") — pas de date picker

**Caution** : champ montant libre (€), saisie manuelle par l'opérateur

**Email client** : si la réservation n'a pas d'email client, un champ apparaît pour en saisir un. La validation du formulaire met à jour `clients.email` avant de générer le QR.

**Opérateur** : sélecteur (Nicolas / Jonathan) — apparaît dans le bloc loueur du PDF avec la date de génération

**Bouton** : "Générer le QR code"

### Écran QR code
- QR code centré, taille lisible (250×250 px minimum)
- URL publique affichée en clair en dessous
- Texte d'invite : "Montrez cet écran à votre client pour qu'il signe le contrat sur son téléphone"
- Indicateur de statut qui poll `/api/reservations/{id}/contract` toutes les 3 secondes
- Dès que `signed_at` est renseigné : animation de confirmation "✓ Contrat signé" + bouton "Télécharger le PDF"

---

## 4. Page publique client (mobile-first)

Route : `/location/contrat/{token}` — accessible sans authentification

### États

**En attente de signature :**
- Résumé lisible : nom du client, vélos, dates, accessoires remis, montant de la caution
- Texte court rappelant les points clés du contrat (responsabilité, retard, caution)
- Champ "Votre nom complet" (pré-rempli avec le nom du client)
- Canvas de signature (`signature_pad`) avec bouton "Effacer"
- Bouton "Je signe et j'accepte le contrat"

**Expiré :**
- Message : "Ce lien a expiré. Demandez à votre loueur de générer un nouveau QR code."

**Déjà signé :**
- Message : "Vous avez déjà signé ce contrat. Un email vous a été envoyé."

**Confirmation post-signature :**
- "Merci ! Votre contrat a été signé. Vous allez recevoir un email de confirmation."

---

## 5. Librairie signature

- **`signature_pad`** (npm) — léger, sans dépendance, canvas HTML5
- Intégration dans la page publique uniquement
- La signature est exportée en `image/png` base64 avant envoi

---

## 6. QR code

- Génération côté serveur via `bacon/bacon-qr-code` (déjà courant dans l'écosystème Laravel) ou côté client via `qrcode` (npm)
- Préférer la génération **côté client** (plus simple, pas de dépendance PHP supplémentaire) : la lib `qrcode` génère un canvas ou SVG depuis l'URL du contrat

---

## 7. Tests

- `ContractServiceTest` : génération du token, expiration, double signature refusée
- `ContractSignTest` : soumission valide, token expiré → 410, déjà signé → 409
- `ContractPdfTest` : vérifier que le PDF contient les données du snapshot
- Test feature : flow complet génération → signature → email → statut réservation mis à jour

---

## Critères d'acceptation

- [ ] Le bouton "Préparer le contrat" est accessible depuis la fiche réservation
- [ ] L'opérateur peut cocher les accessoires et saisir la caution avant de générer le QR
- [ ] Le QR code pointe vers une page publique accessible sans connexion
- [ ] La page client est lisible sur mobile (responsive)
- [ ] Le client peut dessiner sa signature et soumettre
- [ ] Un token expiré ou déjà utilisé affiche un message clair
- [ ] Le PDF généré est fidèle au document de référence (articles 1 à 10, signatures)
- [ ] Si le client n'a pas d'email, le formulaire de préparation en demande un et met à jour `clients.email` avant de générer le QR
- [ ] Le PDF est envoyé par email au client après signature
- [ ] L'écran opérateur se met à jour automatiquement dès que le client a signé
- [ ] La réservation porte la date de signature dans `contract_signed_at`
- [ ] Aucun contrat non signé ne peut être signé après expiration

## Définition de terminé

- Migration testée en montée et rollback
- Flow complet testé manuellement sur mobile (QR → signature → email reçu)
- PDF conforme au document de référence
- Tests backend verts
