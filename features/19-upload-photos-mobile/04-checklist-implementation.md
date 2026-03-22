# Checklist d'implementation

## Phase 1 : Backend - Infrastructure

### Spatie Media Library
- [ ] Installer le package : `composer require spatie/laravel-medialibrary`
- [ ] Publier les migrations : `php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"`
- [ ] Executer la migration
- [ ] Configurer le disk dans `config/filesystems.php` si besoin
- [ ] Creer le lien symbolique : `php artisan storage:link`

### Modeles avec Media
- [ ] Ajouter trait `InteractsWithMedia` sur `Message`
- [ ] Ajouter trait `InteractsWithMedia` sur `MessageReply`
- [ ] Definir les collections media (`photos`)
- [ ] Definir les conversions (`thumb` : 300x300)
- [ ] Implementer l'interface `HasMedia`

### Tokens d'upload
- [ ] Migration `create_upload_tokens_table`
- [ ] Modele `UploadToken` avec scopes (valid, expired)
- [ ] Methode `UploadToken::generate($contextType, $contextId, $maxUses = 10, $expiresInMinutes = 15)`
- [ ] Methode `UploadToken::consume($token)` → retourne le contexte ou null

## Phase 2 : Backend - API

### Routes
- [ ] `POST /api/upload-tokens` → genere un token, retourne {token, url, expires_at}
- [ ] `GET /upload/{token}` → page publique d'upload (Blade simple, pas Inertia)
- [ ] `POST /api/upload/{token}` → recoit les fichiers, retourne les URLs
- [ ] `GET /api/upload-tokens/{token}/photos` → liste les photos deja uploadees
- [ ] `DELETE /api/photos/{id}` → supprime une photo

### Controllers
- [ ] `UploadTokenController@store` : cree le token
- [ ] `MobileUploadController@show` : affiche la page d'upload
- [ ] `MobileUploadController@upload` : traite les fichiers
- [ ] `PhotoController@index` : liste les photos d'un contexte
- [ ] `PhotoController@destroy` : supprime une photo

### Validation
- [ ] Token valide et non expire
- [ ] Limite d'uploads non atteinte
- [ ] Types de fichiers acceptes (jpeg, png, webp, heic)
- [ ] Taille max par fichier (5MB apres compression client)

## Phase 3 : Frontend - Page upload mobile

### Dependencies
- [ ] Installer `browser-image-compression` : `npm install browser-image-compression`

### Page Blade (pas React - plus leger pour mobile)
- [ ] Template `resources/views/upload/mobile.blade.php`
- [ ] Styles inline ou fichier CSS dedie (minimal)
- [ ] JavaScript vanilla pour :
  - Selection fichiers (input file)
  - Preview avant upload
  - Compression avec browser-image-compression
  - Upload via fetch
  - Feedback visuel (progress, succes, erreur)

### UI Mobile
- [ ] Bouton "Prendre une photo" (`capture="environment"`)
- [ ] Bouton "Choisir dans la galerie" (`multiple`)
- [ ] Zone de preview avec thumbnails
- [ ] Barre de progression
- [ ] Messages de succes/erreur
- [ ] Design responsive (portrait/paysage)

## Phase 4 : Frontend - Modal QR Code (React)

### Dependencies
- [ ] Installer `qrcode` : `npm install qrcode`

### Composants
- [ ] `PhotoUploadModal.tsx` : modal avec QR code
- [ ] `PhotoThumbnail.tsx` : miniature avec suppression
- [ ] `PhotoGallery.tsx` : grille de photos recues

### Integration messagerie
- [ ] Bouton "Ajouter photo" dans `NewMessageForm.tsx`
- [ ] Bouton "Ajouter photo" dans `ReplyForm.tsx`
- [ ] Affichage des photos dans `MessageDetail.tsx`
- [ ] Affichage des photos dans les replies

### Logique
- [ ] Appel API pour generer le token
- [ ] Generation du QR code cote client
- [ ] Polling pour verifier les nouvelles photos (toutes les 2s)
- [ ] Mise a jour de l'affichage en temps reel
- [ ] Gestion de la suppression

## Phase 5 : Styles

### SCSS
- [ ] `_photo-upload-modal.scss` : modal QR code
- [ ] `_photo-gallery.scss` : grille de photos
- [ ] `_photo-thumbnail.scss` : miniature individuelle

### Page mobile
- [ ] Styles inline ou CSS minimal
- [ ] Gros boutons touch-friendly
- [ ] Feedback visuel clair

## Phase 6 : Tests

### Backend
- [ ] Test generation token
- [ ] Test expiration token
- [ ] Test limite d'uploads
- [ ] Test upload fichier valide
- [ ] Test rejet fichier invalide
- [ ] Test suppression photo

### Frontend (manuel)
- [ ] Flow complet PC → mobile → PC
- [ ] Test sur iOS Safari
- [ ] Test sur Android Chrome
- [ ] Test compression gros fichier
- [ ] Test selection multiple

## Fichiers a creer

| Fichier | Description |
|---------|-------------|
| `database/migrations/*_create_upload_tokens_table.php` | Migration tokens |
| `app/Models/UploadToken.php` | Modele token |
| `app/Http/Controllers/Api/UploadTokenController.php` | API tokens |
| `app/Http/Controllers/MobileUploadController.php` | Page + upload mobile |
| `app/Http/Controllers/Api/PhotoController.php` | CRUD photos |
| `resources/views/upload/mobile.blade.php` | Page upload mobile |
| `resources/js/Components/Photos/PhotoUploadModal.tsx` | Modal QR |
| `resources/js/Components/Photos/PhotoGallery.tsx` | Grille photos |
| `resources/js/Components/Photos/PhotoThumbnail.tsx` | Miniature |
| `resources/scss/photos/_modal.scss` | Styles modal |
| `resources/scss/photos/_gallery.scss` | Styles grille |

## Fichiers a modifier

| Fichier | Modifications |
|---------|---------------|
| `app/Models/Message.php` | Ajouter HasMedia, InteractsWithMedia |
| `app/Models/MessageReply.php` | Ajouter HasMedia, InteractsWithMedia |
| `routes/api.php` | Routes upload tokens, photos |
| `routes/web.php` | Route page upload mobile |
| `resources/js/Components/Messaging/NewMessageForm.tsx` | Bouton + integration photos |
| `resources/js/Components/Messaging/ReplyForm.tsx` | Bouton + integration photos |
| `resources/js/Components/Messaging/MessageDetail.tsx` | Affichage photos |
| `resources/js/types/index.d.ts` | Types Photo, UploadToken |
| `resources/scss/app.scss` | Import nouveaux styles |
