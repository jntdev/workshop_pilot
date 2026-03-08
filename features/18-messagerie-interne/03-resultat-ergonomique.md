# Résultat ergonomique

## Header global

Le badge messagerie et le sélecteur de mode sont dans le header, visibles sur toutes les pages.

```
┌────────────────────────────────────────────────────────────────┐
│  Workshop Pilot                      [📩 3]   [Nicolas ▼]     │
│                                               ├─ Nicolas (3)   │
│                                               └─ Jonathan (1)  │
└────────────────────────────────────────────────────────────────┘
```

- **Badge [📩 3]** : Nombre de messages non résolus pour l'utilisateur courant
- **Sélecteur d'utilisateur** : Affiche le prénom (Nicolas ou Jonathan), dropdown pour changer
- Le badge se met à jour en temps réel (WebSocket)

**Utilisateurs fixes** :
- **Nicolas** = Comptoir
- **Jonathan** = Atelier

## Panneau messagerie

Clic sur le badge → Ouvre un panneau latéral droit (comme les paramètres ou la fiche réservation).

### Liste des messages

```
┌─────────────────────────────────────────────────────────────┐
│  Messagerie — Nicolas                              [×]      │
├─────────────────────────────────────────────────────────────┤
│  [+ Nouveau message]                                        │
├─────────────────────────────────────────────────────────────┤
│  ● Jean Dupont — Rappeler pour devis       il y a 2h       │
│    De : Jonathan → Nicolas                                  │
│  ──────────────────────────────────────────────────────────│
│  ● Commande pièces reçue                   il y a 4h       │
│    De : Nicolas (pour moi-même)                             │
│  ──────────────────────────────────────────────────────────│
│  ○ Client satisfait, affaire classée       hier            │
│    De : Jonathan → Nicolas · Résolu                         │
└─────────────────────────────────────────────────────────────┘
```

- **●** = Non résolu (couleur vive)
- **○** = Résolu (grisé)
- Messages triés par date décroissante
- Affiche : contact (ou début du contenu), auteur → destinataire, temps relatif
- Les prénoms **Nicolas** et **Jonathan** apparaissent toujours explicitement

### Détail d'un message (clic sur un message)

```
┌─────────────────────────────────────────────────────────────┐
│  ← Retour                                          [×]      │
├─────────────────────────────────────────────────────────────┤
│  ● Non lu                               il y a 2 heures    │
├─────────────────────────────────────────────────────────────┤
│  CONTACT                                                    │
│  Nom : Jean Dupont                                          │
│  Tél : 06 12 34 56 78                                       │
│  Email : jean.dupont@mail.com                               │
├─────────────────────────────────────────────────────────────┤
│  MESSAGE                                                    │
│  De : Jonathan → Nicolas                                    │
│  07/03/2026 à 14:30                                         │
│  ────────────────────────────────────────────────────────── │
│  "Le client souhaite un devis pour conversion VAE.          │
│   Il rappellera demain matin."                              │
│                                                             │
│  [Info lue]                                                 │
├─────────────────────────────────────────────────────────────┤
│  RÉPONSES                                                   │
│  ────────────────────────────────────────────────────────── │
│  Nicolas → Jonathan                                         │
│  07/03 16:45                                                │
│  "J'ai rappelé, on a RDV vendredi 10h."                     │
│                                         Lu ✓ (07/03 17:01)  │
│  ────────────────────────────────────────────────────────── │
│  Jonathan                                                   │
│  07/03 17:15                                                │
│  "Parfait, je prépare le devis."                            │
│                                         Non lu              │
├─────────────────────────────────────────────────────────────┤
│  Répondre...                                                │
│  ┌───────────────────────────────────────────────────────┐  │
│  │                                                       │  │
│  └───────────────────────────────────────────────────────┘  │
│  Destinataire : [Nicolas ▼]                   [Envoyer]     │
│                  ├─ Nicolas                                 │
│                  └─ Jonathan                                │
├─────────────────────────────────────────────────────────────┤
│  [Marquer résolu]                         [Supprimer]       │
└─────────────────────────────────────────────────────────────┘
```

### Bouton "Info lue"

- Visible uniquement si :
  - Je suis le destinataire du message (ou d'une réponse)
  - ET `read_at` est null
- Au clic :
  - Envoie `PATCH /api/messages/{id}/read`
  - Le bouton disparaît, remplacé par "Lu ✓ (heure)"
  - L'auteur voit le statut "Lu ✓" apparaître en temps réel

### Formulaire nouveau message

Clic sur "+ Nouveau message" :

```
┌─────────────────────────────────────────────────────────────┐
│  Nouveau message                                   [×]      │
├─────────────────────────────────────────────────────────────┤
│  CONTACT (optionnel)                                        │
│  Nom      [_______________________]                         │
│  Tél      [_______________________]                         │
│  Email    [_______________________]                         │
├─────────────────────────────────────────────────────────────┤
│  MESSAGE                                                    │
│  ┌───────────────────────────────────────────────────────┐  │
│  │                                                       │  │
│  │                                                       │  │
│  └───────────────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────────────┤
│  Destinataire : [Pour moi-même ▼]                           │
│                  ├─ Pour moi-même                           │
│                  ├─ Nicolas                                 │
│                  └─ Jonathan                                │
├─────────────────────────────────────────────────────────────┤
│  [Annuler]                                [Créer]           │
└─────────────────────────────────────────────────────────────┘
```

- "Pour moi-même" = `recipient_mode: null`
- L'utilisateur courant (Nicolas ou Jonathan) détermine `author_mode`

## Notifications temps réel

### Réception d'un nouveau message

1. WebSocket reçoit `MessageCreated`
2. Badge incrémente
3. Si panneau ouvert → le message apparaît en haut de la liste
4. Optionnel : son de notification discret

### Confirmation de lecture

1. Destinataire clique "Info lue"
2. WebSocket broadcast `MessageRead`
3. L'auteur voit "Lu ✓ (heure)" apparaître sans recharger

### Nouvelle réponse

1. WebSocket reçoit `ReplyCreated`
2. Si le message est ouvert → la réponse apparaît
3. Si destinataire de la réponse → badge incrémente

## États visuels

| État | Indicateur | Couleur |
|------|------------|---------|
| Non lu (destinataire) | ● Non lu | Rouge/Orange |
| Lu, non résolu | ● Ouvert | Bleu |
| Résolu | ○ Résolu | Gris |
| Message lu par destinataire | Lu ✓ (heure) | Vert discret |

## Affichage des prénoms

**Règle** : Toujours afficher les prénoms **Nicolas** et **Jonathan** explicitement, jamais "Comptoir" ou "Atelier" seuls.

| Contexte | Affichage |
|----------|-----------|
| Sélecteur header | `Nicolas` / `Jonathan` |
| Titre panneau | `Messagerie — Nicolas` |
| Auteur message | `De : Jonathan → Nicolas` |
| Auteur réponse | `Nicolas → Jonathan` |
| Message pour soi | `De : Nicolas (pour moi-même)` |
| Dropdown destinataire | `Nicolas` / `Jonathan` |
