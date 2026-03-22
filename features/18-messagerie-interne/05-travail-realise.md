# Travail realise - Feature 18 Messagerie Interne

## Resume

Systeme de messagerie interne entre Nicolas (comptoir) et Jonathan (atelier) permettant la communication asynchrone, le suivi des messages lus/non lus, et la gestion des conversations avec reponses.

---

## Backend

### Migrations
- `create_messages_table` : id, author_mode, recipient_mode, category, contact_name, contact_phone, contact_email, content, status, read_at, resolved_at, timestamps
- `create_message_replies_table` : id, message_id, author_mode, recipient_mode, content, read_at, timestamps
- `add_category_to_messages_table` : ajout du champ category (enum: accueil, atelier, location, autre)

### Modeles
- `Message` : relations replies(), scopes forMode(), helpers unreadCountForMode(), unreadCountByCategoryForMode(), markAsRead(), markAsResolved(), reopen()
- `MessageReply` : relation message(), markAsRead()

### Enum
- `WorkMode` : comptoir, atelier avec labels francais

### Controllers
- `MessageController` : index, store, show, update (read/resolve/reopen), destroy
- `MessageReplyController` : store, update (read)

### Requests
- `StoreMessageRequest` : validation recipient_mode, category, contact_*, content
- `StoreReplyRequest` : validation recipient_mode, content

### Routes API
- `GET /api/messages?mode=xxx` - Liste des messages pour un mode
- `POST /api/messages` - Creer un message
- `GET /api/messages/{id}` - Detail d'un message
- `PUT /api/messages/{id}` - Modifier (read_at, status)
- `DELETE /api/messages/{id}` - Supprimer
- `POST /api/messages/{id}/replies` - Ajouter une reponse
- `PUT /api/replies/{id}` - Marquer reponse comme lue

### Factories & Seeders
- `MessageFactory` : states fromJonathan, fromNicolas, toJonathan, toNicolas, forSelf, read, resolved, categoryAccueil/Atelier/Location/Autre, withContact
- `MessageReplyFactory` : states fromJonathan, fromNicolas, toJonathan, toNicolas, read
- `MessageSeeder` : 30 messages avec 0-15 reponses chacun, categories variees

---

## Frontend

### Types (index.d.ts)
- `WorkMode` : 'comptoir' | 'atelier'
- `MessageCategory` : 'accueil' | 'atelier' | 'location' | 'autre'
- `Message` : id, author_mode, recipient_mode, author_label, recipient_label, category, contact_*, content, status, read_at, resolved_at, replies[], created_at
- `MessageReply` : id, author_mode, recipient_mode, author_label, recipient_label, content, read_at, created_at
- `UnreadByCategory` : Record<MessageCategory, number>

### Context
- `MessagingContext` : gestion du state (mode, messages, selectedId, unreadCount, unreadByCategory, loading)
- Actions : fetchMessages, createMessage, markMessageAsRead, markMessageAsResolved, reopenMessage, deleteMessage, createReply, markReplyAsRead, setMode, setSelectedId
- Gestion memoire des compteurs non lus par categorie

### Composants
- `MessagingButton` : lien navigation "Messages" avec badge non lus en coin
- `Messages` (page) : layout deux colonnes avec filtres categories et status
- `MessageListItem` : apercu message avec bordure coloree (bleu=non lu, jaune=en attente, vert=lu)
- `MessageDetail` : detail complet avec actions et reponses
- `NewMessageForm` : creation message avec selection destinataire, categorie, contact optionnel
- `ReplyForm` : formulaire reponse inline

### Page Messages
- Layout pleine page sans scroll global
- Deux colonnes avec scroll independant
- Filtres par categorie (accueil, atelier, location, autre) avec badges compteurs
- Filtres par status (tous, ouvert, resolu)
- Bouton nouveau message
- Bouton rafraichir

---

## Styles SCSS

### Fichiers
- `messaging/_page.scss` : layout page, toolbars, colonnes, filtres
- `messaging/_list-item.scss` : items liste avec bordures colorees
- `messaging/_detail.scss` : detail message, section reponses, footer sticky
- `messaging/_forms.scss` : formulaires nouveau message et reponse
- `components/layouts/_main.scss` : exception layout pour page messages, badge navigation

### Caracteristiques visuelles
- Bordure gauche coloree selon status lecture :
  - Bleu (`$color-primary-blue`) : message recu non lu
  - Jaune (`$color-toggle-mango`) : message envoye en attente de lecture
  - Vert (`$color-status-ok`) : message envoye et lu par destinataire
- Meme logique pour les reponses
- Notes perso sans status de lecture
- Footer reponse sticky en bas de la colonne droite
- Badge compteur dans navigation header (position absolute coin superieur droit)

---

## Fonctionnalites implementees

1. **Communication bidirectionnelle** : Nicolas <-> Jonathan
2. **Notes personnelles** : messages sans destinataire (recipient_mode = null)
3. **Categories** : accueil, atelier, location, autre avec filtres et compteurs
4. **Status** : ouvert, resolu avec possibilite de reouvrir
5. **Lecture** : marquage lu avec horodatage, indicateurs visuels
6. **Reponses** : fil de conversation avec status de lecture par reponse
7. **Contacts** : champs optionnels nom, telephone, email avec liens cliquables
8. **Interface** : layout deux colonnes, scroll independant, footer sticky

---

## Fichiers modifies/crees

### Backend
- `app/Enums/WorkMode.php`
- `app/Models/Message.php`
- `app/Models/MessageReply.php`
- `app/Http/Controllers/Api/MessageController.php`
- `app/Http/Controllers/Api/MessageReplyController.php`
- `app/Http/Requests/StoreMessageRequest.php`
- `app/Http/Requests/StoreReplyRequest.php`
- `database/migrations/*_create_messages_table.php`
- `database/migrations/*_create_message_replies_table.php`
- `database/migrations/*_add_category_to_messages_table.php`
- `database/factories/MessageFactory.php`
- `database/factories/MessageReplyFactory.php`
- `database/seeders/MessageSeeder.php`
- `routes/api.php`

### Frontend
- `resources/js/types/index.d.ts`
- `resources/js/Contexts/MessagingContext.tsx`
- `resources/js/Pages/Messages.tsx`
- `resources/js/Components/Messaging/index.ts`
- `resources/js/Components/Messaging/MessagingButton.tsx`
- `resources/js/Components/Messaging/MessageListItem.tsx`
- `resources/js/Components/Messaging/MessageDetail.tsx`
- `resources/js/Components/Messaging/NewMessageForm.tsx`
- `resources/js/Components/Messaging/ReplyForm.tsx`
- `resources/js/Layouts/MainLayout.tsx`

### Styles
- `resources/scss/messaging/_page.scss`
- `resources/scss/messaging/_list-item.scss`
- `resources/scss/messaging/_detail.scss`
- `resources/scss/messaging/_forms.scss`
- `resources/scss/messaging/_button.scss` (vide, styles deplaces)
- `resources/scss/components/layouts/_main.scss`
- `resources/scss/app.scss`

---

## Non implemente (scope futur)

- Real-time avec Laravel Reverb/Echo (notifications push)
- Tests automatises
- Pagination des messages
- Recherche dans les messages
- Pieces jointes
