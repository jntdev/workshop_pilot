# Resultat ergonomique attendu

## Flow utilisateur - PC

1. Dans la messagerie, clic sur bouton "Ajouter photo depuis mobile"
2. Modal s'ouvre avec :
   - QR code bien visible
   - Instructions : "Scannez avec votre telephone"
   - Compteur : "0 photo(s) recue(s)"
   - Bouton "Terminer" (grise tant que 0 photos)
3. Quand des photos arrivent :
   - Thumbnails apparaissent en temps reel
   - Compteur se met a jour
   - Possibilite de supprimer une photo
4. Clic "Terminer" → photos attachees au message

## Flow utilisateur - Mobile

1. Scan du QR code (camera native ou app QR)
2. Page web s'ouvre automatiquement
3. Interface simple :
   - Logo/titre "Workshop Pilot"
   - Bouton principal "Prendre une photo" (ouvre camera)
   - Bouton secondaire "Choisir dans la galerie"
   - Zone de preview des photos selectionnees
   - Barre de progression pendant compression/upload
   - Message de succes "Photo envoyee !"
4. Possibilite d'ajouter d'autres photos
5. Message final "Vous pouvez fermer cette page"

## Etats visuels

### QR Code modal (PC)
- Fond sombre semi-transparent
- Modal blanc centre
- QR code grande taille (~250px)
- Animation subtile "en attente"

### Page upload (Mobile)
- Design epure, gros boutons (touch-friendly)
- Feedback visuel clair (upload en cours, succes, erreur)
- Fonctionne en portrait et paysage

### Thumbnails recus (PC)
- Grille de miniatures
- Hover : bouton supprimer
- Clic : agrandir en lightbox

## Messages d'erreur

- Token expire : "Ce lien n'est plus valide. Generez un nouveau QR code."
- Limite atteinte : "Nombre maximum de photos atteint (10)."
- Erreur upload : "Erreur d'envoi. Reessayez."
- Fichier trop gros (apres compression) : "Image trop volumineuse."
