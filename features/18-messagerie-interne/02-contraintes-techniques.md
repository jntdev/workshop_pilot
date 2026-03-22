# Contraintes techniques

## Mode session

Le mode est stocké en **localStorage** côté client :

```typescript
type WorkMode = 'comptoir' | 'atelier';

// Mapping fixe — ce sont les vrais utilisateurs
const MODE_USER = {
  comptoir: { id: 1, name: 'Nicolas' },
  atelier: { id: 2, name: 'Jonathan' },
} as const;

// Helpers
function getModeLabel(mode: WorkMode): string {
  return mode === 'comptoir' ? 'Nicolas' : 'Jonathan';
}
```

**Important** : Ces prénoms sont fixes et doivent apparaître clairement dans l'interface pour éviter toute ambiguïté sur l'auteur et le destinataire des messages.

## Modèle de données

### Table `messages`

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | PK |
| author_mode | enum('comptoir','atelier') | Qui a créé le message |
| recipient_mode | enum('comptoir','atelier') nullable | À qui c'est destiné (null = pour soi-même) |
| contact_name | varchar(255) nullable | Nom du contact externe |
| contact_phone | varchar(50) nullable | Téléphone |
| contact_email | varchar(255) nullable | Email |
| content | text | Contenu du message |
| status | enum('ouvert','resolu') | Statut du message |
| read_at | datetime nullable | Date/heure de lecture par le destinataire |
| resolved_at | datetime nullable | Date/heure de résolution |
| created_at | datetime | |
| updated_at | datetime | |

### Table `message_replies`

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | PK |
| message_id | bigint | FK → messages.id (cascade) |
| author_mode | enum('comptoir','atelier') | Qui a écrit la réponse |
| recipient_mode | enum('comptoir','atelier') nullable | À qui la réponse est destinée |
| content | text | Contenu de la réponse |
| read_at | datetime nullable | Date/heure de lecture |
| created_at | datetime | |
| updated_at | datetime | |

## Relations Eloquent

```php
// Message.php
public function replies(): HasMany

// MessageReply.php
public function message(): BelongsTo
```

## Calcul du badge (messages non résolus pour un mode)

```php
public static function unreadCountForMode(string $mode): int
{
    return self::where('status', 'ouvert')
        ->where(function ($q) use ($mode) {
            // Messages qui me sont destinés
            $q->where('recipient_mode', $mode)
            // OU messages que j'ai créés pour moi-même
              ->orWhere(fn ($q) => $q
                  ->where('author_mode', $mode)
                  ->whereNull('recipient_mode')
              );
        })
        ->count();
}
```

## Real-time avec Laravel Reverb

### Channels

- `mode.comptoir` — Événements pour le comptoir
- `mode.atelier` — Événements pour l'atelier
- `message.{id}` — Événements sur un message spécifique (lecture, réponses)

### Événements broadcast

| Événement | Channel | Payload | Déclenché quand |
|-----------|---------|---------|-----------------|
| `MessageCreated` | `mode.{recipient_mode}` | message complet | Nouveau message avec destinataire |
| `MessageRead` | `message.{id}` | { message_id, read_at } | Clic "Info lue" |
| `MessageResolved` | `message.{id}` | { message_id, resolved_at } | Marqué résolu |
| `ReplyCreated` | `message.{id}` + `mode.{recipient_mode}` | reply complet | Nouvelle réponse |
| `ReplyRead` | `message.{id}` | { reply_id, read_at } | Réponse lue |

## API Endpoints

```
GET    /api/messages?mode=comptoir          Liste des messages pour ce mode
GET    /api/messages/unread-count?mode=xxx  Nombre pour le badge
POST   /api/messages                        Créer un message
GET    /api/messages/{id}                   Détail d'un message
PATCH  /api/messages/{id}/read              Marquer comme lu
PATCH  /api/messages/{id}/resolve           Marquer comme résolu
DELETE /api/messages/{id}                   Supprimer

POST   /api/messages/{id}/replies           Ajouter une réponse
PATCH  /api/replies/{id}/read               Marquer réponse comme lue
```

## State frontend (Context React)

```typescript
interface MessagingState {
  mode: WorkMode;
  messages: Message[];
  loaded: boolean;
  unreadCount: number;
  panelOpen: boolean;
  selectedMessageId: number | null;

  setMode(mode: WorkMode): void;
  fetchMessages(): Promise<void>;
  createMessage(data: CreateMessageData): Promise<void>;
  markRead(messageId: number): Promise<void>;
  markResolved(messageId: number): Promise<void>;
  addReply(messageId: number, content: string, recipientMode?: WorkMode): Promise<void>;

  // Actions appelées par WebSocket
  onMessageCreated(message: Message): void;
  onMessageRead(messageId: number, readAt: string): void;
  onReplyCreated(reply: Reply): void;
}
```

## Cache & mémoire

- Les messages sont chargés une fois par mode et gardés en mémoire
- Le WebSocket met à jour le state en temps réel (pas de refetch)
- Changement de mode → charge depuis le cache si déjà loadé, sinon fetch
- `localStorage` stocke uniquement le `mode` actuel (pas les messages)
