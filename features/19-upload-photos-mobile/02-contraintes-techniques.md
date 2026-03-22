# Contraintes techniques

## Stockage des fichiers

### Option retenue : Spatie Media Library
- Package Laravel mature et bien maintenu
- Relation polymorphique : attachable a Message, MessageReply, Quote...
- Conversions automatiques (thumbnails)
- Gestion des collections (photos, documents...)

### Configuration stockage
- Disk : `public` (ou S3 si besoin de scalabilite)
- Dossier : `storage/app/public/media/`
- Lien symbolique : `php artisan storage:link`

## Compression cote client

### Librairie : browser-image-compression
- ~30KB gzippe
- Web Worker (ne bloque pas l'UI)
- Gere l'orientation EXIF automatiquement
- Configuration recommandee :
  - `maxSizeMB: 1` (max 1MB par image)
  - `maxWidthOrHeight: 1920` (redimensionne si plus grand)
  - `useWebWorker: true`

## Token d'upload temporaire

### Securite
- Token unique genere cote serveur (UUID)
- Expiration : 15 minutes
- Usage unique ou limite (ex: max 10 photos)
- Lie a un contexte (message_id ou session_id)

### Table `upload_tokens`
- `id`, `token`, `context_type`, `context_id`, `expires_at`, `used_count`, `max_uses`, `created_at`

## Communication temps reel

### Option simple : Polling
- Page PC poll toutes les 2-3 secondes pendant que le QR est affiche
- Simple a implementer, suffisant pour ce cas d'usage

### Option avancee : WebSocket (Laravel Reverb)
- Notification instantanee quand une photo arrive
- Plus reactif mais plus complexe
- A considerer si deja utilise ailleurs

## QR Code

### Librairie : qrcode (npm)
- Generation cote client (pas de requete serveur)
- SVG ou Canvas
- Taille adaptative

### Contenu du QR
- URL : `https://domain.com/upload/{token}`
- Courte, scannable facilement
