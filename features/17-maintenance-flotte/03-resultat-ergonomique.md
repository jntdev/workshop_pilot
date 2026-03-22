# Résultat ergonomique

## Accès à la fiche vélo

**Depuis l'agenda Location** : Clic sur le header d'une colonne vélo → Ouvre le panneau droit avec la fiche vélo (même pattern que ReservationForm ou SettingsPanel).

## Panneau fiche vélo

```
┌─────────────────────────────────────────┐
│  ← Fermer                    [Modifier] │
├─────────────────────────────────────────┤
│  ESb1                                   │
│  VAE • Taille S • Cadre bas             │
├─────────────────────────────────────────┤
│  Modèle      [____________________]     │
│  Batterie    [____________________]     │
│  Statut      [Disponible ▼]             │
│  Notes       [____________________]     │
│              [____________________]     │
├─────────────────────────────────────────┤
│  [ + Nouvelle fiche atelier ]           │
├─────────────────────────────────────────┤
│  HISTORIQUE MAINTENANCE                 │
│  ───────────────────────────────────    │
│  12/02/2026 │ Changement freins  45,00€ │
│  03/01/2026 │ Pneu arrière       28,50€ │
│  15/11/2025 │ Révision complète  85,00€ │
│  ───────────────────────────────────    │
│  Total 2026 :                   73,50 € │
│  Total 2025 :                   85,00 € │
│  ───────────────────────────────────    │
│  TOTAL CUMULÉ :                158,50 € │
└─────────────────────────────────────────┘
```

## Formulaire fiche atelier (modale ou panneau)

Clic sur "+ Nouvelle fiche atelier" → Ouvre un formulaire :

```
┌─────────────────────────────────────────┐
│  Nouvelle fiche atelier — ESb1          │
├─────────────────────────────────────────┤
│  Description  [Changement freins_____]  │
│  Date         [2026-03-07]              │
├─────────────────────────────────────────┤
│  LIGNES                                 │
│  ───────────────────────────────────    │
│  Désignation    Qté  Coût HT   Total    │
│  [Plaquettes__] [2]  [12,00]   24,00 €  │
│  [Main œuvre_]  [1]  [20,00]   20,00 €  │
│                        Temps: [30] min  │
│  [+ Ajouter une ligne]                  │
│  ───────────────────────────────────    │
│  TOTAL HT :                    44,00 €  │
│  Temps total :                 30 min   │
├─────────────────────────────────────────┤
│  Notes  [_________________________]     │
├─────────────────────────────────────────┤
│  [Annuler]              [Enregistrer]   │
└─────────────────────────────────────────┘
```

## Liste historique

- Triée par date décroissante (plus récent en haut)
- Clic sur une ligne → Ouvre la fiche en lecture/édition
- Suppression possible (avec confirmation)

## Dashboard KPIs

La ligne Location affiche désormais une vraie marge :

```
Métier     │ CA          │ Marge       │ Panier
───────────┼─────────────┼─────────────┼────────
Location   │ 5 400,00 €  │ 5 241,50 €  │ 180,00 €
```

(Marge = 5400 − 158,50 de maintenance)
