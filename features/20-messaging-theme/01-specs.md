# Feature 20 : Theme Starcraft pour la Messagerie

## Objectif

Créer un thème visuel **optionnel et activable** inspiré de Starcraft/Command & Conquer : cadres métalliques, bordures rouges/vertes luminescentes, fond sombre texturé, scrollbars custom, boutons biseautés.

**Le thème ne remplace pas le style actuel** - il s'active via un toggle et peut être désactivé à tout moment.

---

## Système d'activation du thème

### Méthode : Classe CSS sur le conteneur parent

```tsx
// Contexte React pour le thème
const [theme, setTheme] = useState<'default' | 'starcraft'>('default');

// Le conteneur messaging reçoit la classe du thème actif
<div className={`messaging-page ${theme === 'starcraft' ? 'theme-starcraft' : ''}`}>
  ...
</div>
```

### Structure SCSS

```scss
// Les styles Starcraft ne s'appliquent QUE si le parent a .theme-starcraft
.theme-starcraft {
  // Variables CSS overridées
  --msg-bg-primary: #{$sc-bg-dark};
  --msg-border-color: #{$sc-border-red};
  // etc.

  // Composants stylés
  .messaging-panel { ... }
  .message-card { ... }
  .btn { ... }
}
```

### Toggle UI

Un bouton/switch dans le header de la messagerie pour activer/désactiver le thème :

```
[🎮 Mode Starcraft: ON/OFF]
```

### Persistence

Le choix du thème est sauvegardé en `localStorage` pour persister entre les sessions :

```typescript
localStorage.getItem('messaging-theme') // 'default' | 'starcraft'
```

---

## Palette de couleurs

```scss
// Fond principal
$sc-bg-dark: #1a1a2e;           // Fond très sombre bleuté
$sc-bg-panel: #0d0d1a;          // Fond des panneaux
$sc-bg-texture: url('...') ;    // Texture métal/space optionnelle

// Bordures lumineuses
$sc-border-red: #8b0000;        // Rouge sombre
$sc-border-red-glow: #ff3333;   // Rouge lumineux (hover/active)
$sc-border-green: #2d5a27;      // Vert sombre
$sc-border-green-glow: #33ff33; // Vert lumineux
$sc-border-metal: #4a4a5a;      // Gris métallique

// Texte
$sc-text-primary: #00ff00;      // Vert terminal
$sc-text-secondary: #88aa88;    // Vert atténué
$sc-text-highlight: #ffffff;    // Blanc pour titres
$sc-text-warning: #ffaa00;      // Orange/mango

// Accents
$sc-accent-blue: #0066cc;       // Bleu sélection
$sc-accent-cyan: #00ffff;       // Cyan highlights
```

---

## Composants UI

### 1. Panel Container (`.sc-panel`)

Cadre principal avec bordure métallique et coins biseautés.

```
┌─────────────────────────────────────┐
│▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓│  <- Barre titre métallique
├─────────────────────────────────────┤
│                                     │
│   Contenu du panel                  │
│                                     │
│                                     │
└─────────────────────────────────────┘
     ↑ Bordure rouge 2-3px avec glow
```

**Styles :**
- `border: 2px solid $sc-border-red`
- `box-shadow: 0 0 10px rgba(255, 51, 51, 0.3), inset 0 0 20px rgba(0,0,0,0.5)`
- `background: $sc-bg-panel`
- Coins légèrement coupés (clip-path ou pseudo-éléments)

---

### 2. Header Bar (`.sc-panel__header`)

Barre de titre avec dégradé métallique.

**Styles :**
- `background: linear-gradient(180deg, #4a4a5a 0%, #2a2a3a 50%, #1a1a2a 100%)`
- `border-bottom: 1px solid $sc-border-red`
- `text-transform: uppercase`
- `letter-spacing: 2px`
- `font-family: 'Share Tech Mono', monospace` (ou police pixel)

---

### 3. Boutons (`.sc-btn`)

Boutons biseautés style interface de jeu.

#### Bouton OK / Primaire (`.sc-btn--ok`)
```
    ╱─────────────╲
   │      Ok       │
    ╲─────────────╱
```
- Fond dégradé vert sombre vers vert clair
- Bordure verte avec glow au hover
- Texte vert lumineux

#### Bouton Cancel / Danger (`.sc-btn--cancel`)
- Fond dégradé rouge sombre
- Bordure rouge avec glow au hover

#### Bouton Neutre (`.sc-btn--neutral`)
- Fond gris métallique
- Bordure grise

**États :**
- `:hover` - Augmenter le glow, légère translation Y
- `:active` - Inverser le dégradé (pressed)
- `:disabled` - Désaturer, réduire opacité

---

### 4. Select / Dropdown (`.sc-select`)

Menu déroulant avec flèche triangulaire.

```
┌─────────────────────────▼┐
│  Random                  │
└──────────────────────────┘
```

**Styles :**
- Fond sombre avec bordure fine verte/rouge
- Flèche triangulaire (▼) à droite
- Options avec highlight au hover
- `font-family: monospace`

---

### 5. Scrollbar (`.sc-scrollbar`)

Scrollbar custom style terminal/jeu.

```
    ▲      <- Bouton haut (triangle)
   ┌┐
   ││      <- Track sombre
   ├┤      <- Thumb métallique
   ││
   └┘
    ▼      <- Bouton bas
```

**Styles :**
```scss
::-webkit-scrollbar {
  width: 16px;
  background: $sc-bg-dark;
}

::-webkit-scrollbar-track {
  background: linear-gradient(90deg, #1a1a2a, #0d0d1a, #1a1a2a);
  border: 1px solid $sc-border-metal;
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(180deg, #5a5a6a, #3a3a4a, #2a2a3a);
  border: 1px solid $sc-border-green;

  &:hover {
    border-color: $sc-border-green-glow;
    box-shadow: 0 0 5px $sc-border-green-glow;
  }
}
```

---

### 6. Input Text (`.sc-input`)

Champ de saisie style terminal.

```
┌──────────────────────────────┐
│ > _                          │
└──────────────────────────────┘
```

**Styles :**
- Fond très sombre
- Bordure fine verte
- Texte vert monospace
- Curseur vert clignotant
- Placeholder en vert atténué

---

### 7. Message Card (`.sc-message-card`)

Carte de message dans la liste.

```
┌─────────────────────────────────────┐
│ ● Accueil → Atelier    il y a 2h   │
│─────────────────────────────────────│
│ Contenu du message tronqué...      │
│                                     │
│ [Non lu]              3 réponses   │
└─────────────────────────────────────┘
```

**Variantes :**
- `.sc-message-card--unread` : Bordure gauche verte lumineuse + badge
- `.sc-message-card--resolved` : Bordure gauche grise, texte atténué
- `.sc-message-card--selected` : Fond légèrement plus clair, bordure cyan

---

### 8. Badge / Tag (`.sc-badge`)

Indicateurs et tags.

```
[ Non lu ]   [ 3 ]   [ Résolu ]
```

**Variantes :**
- `.sc-badge--unread` : Fond vert, texte noir
- `.sc-badge--count` : Fond rouge, texte blanc
- `.sc-badge--resolved` : Fond gris, texte blanc

---

### 9. Textarea (`.sc-textarea`)

Zone de texte multi-ligne.

**Styles :**
- Mêmes styles que `.sc-input`
- Scrollbar custom intégrée
- Ligne de compteur de caractères en bas

---

### 10. Divider (`.sc-divider`)

Séparateur horizontal style circuit.

```
──────●──────────●──────────●──────
```

**Styles :**
- Ligne de 1px avec points/cercles décoratifs
- Dégradé du centre vers les bords

---

## Typographie

### Police principale
- **Primaire** : `'Share Tech Mono'`, `'Courier New'`, monospace
- **Titres** : `'Orbitron'`, `'Share Tech'`, sans-serif (optionnel)
- **Alternative pixel** : `'Press Start 2P'` pour certains éléments

### Tailles
- Titres panels : 14px, uppercase, letter-spacing: 2px
- Texte normal : 13px
- Labels : 11px, uppercase
- Badges : 10px

---

## Fichiers à créer

| Fichier | Description |
|---------|-------------|
| `resources/scss/messaging/themes/_starcraft.scss` | **Tout le thème Starcraft** (un seul fichier) |
| `resources/js/Contexts/ThemeContext.tsx` | Contexte React pour le thème |
| `resources/js/Components/Messaging/ThemeToggle.tsx` | Bouton toggle du thème |

### Structure du fichier `_starcraft.scss`

```scss
// Tout est scopé sous .theme-starcraft
.theme-starcraft {
  // 1. Variables CSS
  // 2. Panel overrides
  // 3. Buttons overrides
  // 4. Forms overrides
  // 5. Cards overrides
  // 6. Scrollbar
  // 7. Typography
}
```

---

## Fichiers à modifier

| Fichier | Modifications |
|---------|---------------|
| `resources/scss/messaging/_page.scss` | Import du thème `themes/starcraft` |
| `resources/js/Pages/Messaging.tsx` | Wrapper avec ThemeContext + classe conditionnelle |
| `resources/js/Components/Messaging/MessagingHeader.tsx` | Ajouter ThemeToggle |

---

## Assets requis

1. **Polices Google Fonts** :
   - Share Tech Mono
   - Orbitron (optionnel)

2. **Fond d'écran** (fourni par l'utilisateur) :
   - Texture spatiale/métallique
   - Format : PNG ou JPG optimisé
   - Emplacement : `public/images/sc-background.jpg`

---

## Implémentation progressive

### Phase 1 : Infrastructure thème
- Créer `ThemeContext.tsx` avec state + localStorage
- Créer `ThemeToggle.tsx` composant switch
- Modifier `Messaging.tsx` pour wrapper avec le contexte

### Phase 2 : Fichier SCSS thème
- Créer `resources/scss/messaging/themes/_starcraft.scss`
- Variables CSS scopées sous `.theme-starcraft`
- Override des panels et layout

### Phase 3 : Composants visuels
- Boutons (`.btn` → style SC)
- Inputs/Selects (`.form-control` → style SC)
- Cards (`.message-card` → style SC)

### Phase 4 : Finitions
- Scrollbar custom
- Fond d'écran (si fourni)
- Animations/transitions
- Tests et ajustements

---

## Aperçu visuel attendu

L'interface finale doit évoquer :
- Un terminal de communication spatiale
- Les menus de Starcraft/C&C des années 90-2000
- Ambiance sombre, éléments luminescents
- Sensation de "haute technologie rétro"
