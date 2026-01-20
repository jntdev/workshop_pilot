# 02 - Spécifications de l'interface

## Page liste `/clients`

### Layout
```
┌─────────────────────────────────────────────────┐
│ [← Retour]  Clients                             │
├─────────────────────────────────────────────────┤
│                                                  │
│  [🔍 Rechercher...]     [+ Créer nouveau client]│
│                                                  │
│  ┌──────────────┐ ┌──────────────┐ ┌─────────┐ │
│  │ Jean Dupont  │ │ Marie Martin │ │ ...     │ │
│  │ 0612345678   │ │ 0698765432   │ │         │ │
│  │ jean@ex.com  │ │ marie@ex.com │ │         │ │
│  └──────────────┘ └──────────────┘ └─────────┘ │
│  (hover = élévation)                            │
└─────────────────────────────────────────────────┘
```

### Comportements
- **Recherche** : Filtrage instantané sur prenom, nom, telephone, email
- **Ordre** : Alphabétique par nom puis prénom
- **Hover card** : `transform: translateY(-4px)` + shadow
- **Click card** : Navigation vers `/clients/{id}`
- **Bouton création** : Navigation vers `/clients/nouveau`

## Page création `/clients/nouveau`

### Layout
```
┌─────────────────────────────────────────────────┐
│ [← Retour à la liste]                           │
│                                                  │
│  Nouveau client                                 │
│                                                  │
│  [Formulaire Livewire réutilisé]                │
│                                                  │
│  [Enregistrer le client] (bleu)                 │
└─────────────────────────────────────────────────┘
```

### Comportements
- **Formulaire** : Composant `Clients\Form` en mode création
- **Succès** : Redirection vers `/clients/{id}` du client créé
- **Retour** : Navigation vers `/clients`

## Page détail/modification `/clients/{id}`

### Layout
```
┌─────────────────────────────────────────────────┐
│ [← Retour à la liste]                           │
│                                                  │
│  Fiche client : Jean Dupont                     │
│                                                  │
│  [Formulaire Livewire pré-rempli]               │
│                                                  │
│  [Supprimer] (rouge)    [Modifier] (bleu)       │
└─────────────────────────────────────────────────┘
```

### Comportements
- **Formulaire** : Composant `Clients\Form` en mode édition (pré-rempli)
- **Supprimer** : Confirmation puis suppression et redirection `/clients`
- **Modifier** : Sauvegarde et feedback de succès
- **Retour** : Navigation vers `/clients`

## Composants réutilisés
- `Clients\Form` : Mode création ET édition (détecté via propriété `$clientId`)
- Layout principal avec navigation
