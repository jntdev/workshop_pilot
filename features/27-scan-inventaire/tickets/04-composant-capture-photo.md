# Ticket 04 : Composant de capture photo

**Type** : Frontend
**Priorité** : Haute
**Estimation** : 2h

**Dépend de** : —

## Tâches

- [ ] **Décision explicite (suite au feedback de préparation sur le ticket 03)** : ce composant est **distinct** de `BarcodeScanner.tsx` — pas de partage de flux vidéo ni d'API de capture pilotée depuis le parent du scanner. Un accès caméra indépendant, activé uniquement quand la capture photo est nécessaire (formulaire de création rapide, ticket 06)
- [ ] Créer `resources/js/Components/Inventory/PhotoCapture.tsx` :
  ```typescript
  interface Props {
      onCaptured: (file: File) => void;
      onCancel: () => void;
  }
  ```
- [ ] Accès caméra (`getUserMedia`, `facingMode: 'environment'`), affichage du flux vidéo en direct avec un bouton de déclenchement
- [ ] Au déclenchement : capture une image depuis le flux vidéo (`canvas.drawImage(videoElement, ...)`, puis `canvas.toBlob()` → conversion en `File`), affiche un aperçu de la photo capturée avant confirmation (permet de reprendre la photo si mauvaise)
- [ ] Nettoyage systématique du flux caméra au démontage (même exigence que `BarcodeScanner.tsx`, ticket 03) — y compris après confirmation ou annulation, jamais de caméra qui reste allumée
- [ ] Gestion d'erreur explicite si la permission caméra est refusée (même pattern que ticket 03)

## Critères d'acceptation

- Le composant affiche le flux caméra, permet de capturer une image, puis de la confirmer ou de reprendre
- `onCaptured(file)` reçoit un fichier image valide, exploitable directement pour l'upload (ticket 02)
- Le flux caméra est coupé dans tous les cas de sortie (confirmation, annulation, démontage) — vérifiable via l'indicateur caméra du système d'exploitation
- Aucune erreur si la permission caméra est refusée — message clair, pas d'écran blanc
