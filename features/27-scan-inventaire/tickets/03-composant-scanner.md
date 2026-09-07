# Ticket 03 : Composant scanner caméra

**Type** : Frontend
**Priorité** : Haute
**Estimation** : 3h

**Dépend de** : —

## Tâches

- [ ] Ajouter la dépendance `@zxing/library` (`npm install @zxing/library`) — compatible iOS/Android/desktop, contrairement à `BarcodeDetector` natif qui exclut Safari/iOS (voir `../04-arbitrages.md`, arbitrage 1). **Nouvelle dépendance, nécessite validation utilisateur avant ajout au `package.json`** (règle CLAUDE.md : ne pas changer les dépendances sans approbation)
- [ ] Créer `resources/js/Components/Inventory/BarcodeScanner.tsx`, composant réutilisable :
  ```typescript
  interface Props {
      onDetected: (code: string) => void;
      isPaused?: boolean; // true pendant qu'une fiche article est affichée, évite de re-scanner en boucle
  }
  ```
- [ ] Utiliser `BrowserMultiFormatReader` de `@zxing/library` : accès `getUserMedia` (caméra arrière par défaut, `facingMode: 'environment'`), décodage continu sur le flux vidéo
- [ ] Nettoyage systématique du flux caméra au démontage du composant (`reset()`/`stopContinuousDecode()` dans le cleanup du `useEffect`) — une caméra qui reste allumée après avoir quitté l'écran est un bug critique sur mobile (batterie, vie privée)
- [ ] Gestion d'erreur explicite si la permission caméra est refusée ou l'API indisponible (contexte non-HTTPS par exemple, voir `../04-arbitrages.md`) : message clair à l'utilisateur, pas un écran blanc silencieux
- [ ] Pas de logique de lookup/navigation dans ce composant — il se contente de détecter un code et de le remonter via `onDetected`, toute la logique métier reste dans l'écran parent (ticket 04)
- [ ] **Décision explicite (suite au feedback de préparation)** : ce composant n'expose **aucune** API de capture photo (pas de `captureFrame()`, pas d'accès direct au flux vidéo depuis le parent) — la capture photo pour la création rapide d'article (ticket 05) est portée par un composant distinct (`PhotoCapture.tsx`), avec son propre accès caméra indépendant. Un seul flux vidéo actif à la fois dans l'écran (le scanner est mis en pause/démonté avant que la capture photo ne s'active), pas de partage de flux entre les deux usages — plus simple et plus robuste qu'une API de capture pilotée par le parent.

## Critères d'acceptation

- Le composant demande l'accès caméra au montage, affiche le flux vidéo en plein écran
- Un code-barres présenté devant la caméra déclenche `onDetected(code)` avec la valeur décodée exacte
- `isPaused=true` interrompt la détection continue (évite un déclenchement multiple pendant que l'utilisateur consulte la fiche article résultante)
- Le flux caméra est bien coupé quand le composant est démonté (vérifiable via les DevTools navigateur — indicateur caméra du système d'exploitation s'éteint)
- Un refus de permission caméra affiche un message d'erreur explicite, pas une page blanche
