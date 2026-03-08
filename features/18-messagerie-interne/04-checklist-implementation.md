# Checklist d'implémentation

## Backend — Base

- [ ] Migration `create_messages_table`
- [ ] Migration `create_message_replies_table`
- [ ] Enum `WorkMode` (comptoir, atelier)
- [ ] Modèle `Message` avec relations et scopes
- [ ] Modèle `MessageReply` avec relations
- [ ] Factory `MessageFactory`
- [ ] Factory `MessageReplyFactory`
- [ ] `MessageController` (index, store, show, destroy)
- [ ] `MessageReplyController` (store)
- [ ] `StoreMessageRequest` avec validation
- [ ] `StoreReplyRequest` avec validation
- [ ] Méthode `Message::unreadCountForMode(string $mode)`
- [ ] Route `GET /api/messages?mode=xxx`
- [ ] Route `GET /api/messages/unread-count?mode=xxx`
- [ ] Route `POST /api/messages`
- [ ] Route `GET /api/messages/{id}`
- [ ] Route `PATCH /api/messages/{id}/read`
- [ ] Route `PATCH /api/messages/{id}/resolve`
- [ ] Route `DELETE /api/messages/{id}`
- [ ] Route `POST /api/messages/{id}/replies`
- [ ] Route `PATCH /api/replies/{id}/read`
- [ ] Test `MessageTest` (CRUD, scopes, counts)

## Backend — Real-time (Laravel Reverb)

- [ ] Installer Laravel Reverb (`php artisan install:broadcasting`)
- [ ] Event `MessageCreated` (broadcastOn: mode.{recipient_mode})
- [ ] Event `MessageRead` (broadcastOn: message.{id})
- [ ] Event `MessageResolved` (broadcastOn: message.{id})
- [ ] Event `ReplyCreated` (broadcastOn: message.{id} + mode.{recipient_mode})
- [ ] Event `ReplyRead` (broadcastOn: message.{id})
- [ ] Dispatch events dans les controllers
- [ ] Configurer channels dans `routes/channels.php`

## Frontend — State & Context

- [ ] Type `WorkMode` dans `types/index.d.ts`
- [ ] Type `Message` dans `types/index.d.ts`
- [ ] Type `MessageReply` dans `types/index.d.ts`
- [ ] Context `MessagingContext` avec state et actions
- [ ] Provider `MessagingProvider` dans `app.tsx`
- [ ] Hook `useMessaging()` pour accéder au context
- [ ] Stockage mode dans localStorage
- [ ] Chargement initial du badge au mount

## Frontend — Header

- [ ] Composant `MessagingBadge.tsx` (icône + compteur)
- [ ] Composant `ModeSelector.tsx` (dropdown comptoir/atelier)
- [ ] Intégration dans `MainLayout.tsx`
- [ ] Clic badge → ouvre panneau messagerie
- [ ] Changement mode → recharge messages + badge

## Frontend — Panneau messagerie

- [ ] Composant `MessagingPanel.tsx` (panneau latéral)
- [ ] Composant `MessageList.tsx` (liste des messages)
- [ ] Composant `MessageItem.tsx` (ligne dans la liste)
- [ ] Composant `MessageDetail.tsx` (vue détaillée)
- [ ] Composant `MessageForm.tsx` (nouveau message)
- [ ] Composant `ReplyForm.tsx` (répondre)
- [ ] Composant `ReplyItem.tsx` (affichage réponse)
- [ ] Bouton "Info lue" avec logique
- [ ] Bouton "Marquer résolu"
- [ ] Bouton "Supprimer" avec confirmation
- [ ] Styles SCSS `_messaging.scss`

## Frontend — Real-time (Laravel Echo)

- [ ] Installer laravel-echo + pusher-js
- [ ] Configurer Echo dans `bootstrap.ts`
- [ ] Écoute channel `mode.{currentMode}` pour nouveaux messages
- [ ] Écoute channel `message.{id}` quand message ouvert
- [ ] Handler `onMessageCreated` → ajoute au state + badge++
- [ ] Handler `onMessageRead` → met à jour read_at
- [ ] Handler `onReplyCreated` → ajoute réponse au message
- [ ] Désinscription channels au changement de mode/fermeture

## Seeds (optionnel)

- [ ] Seeder messages de test entre comptoir et atelier
