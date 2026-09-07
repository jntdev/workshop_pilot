# Ticket 07 : Bouton d'accès depuis la page Stock

**Type** : Frontend
**Priorité** : Moyenne
**Estimation** : 30min

**Dépend de** : Ticket 05

## Tâches

- [ ] Dans `resources/js/Pages/Stock/Index.tsx`, ajouter un bouton d'accès à l'écran Inventaire, à côté du bouton existant "+ Nouvel article" (`stock-page__header`)
- [ ] Lien simple (`<a href="/inventaire/scan">` ou navigation Inertia `router.visit`), pas de logique complexe
- [ ] Bien visible sur mobile (voir `../04-arbitrages.md`, arbitrage 3) : vérifier le rendu à largeur mobile (375px), pas seulement desktop — le bouton ne doit pas être coupé ou masqué par le header existant sur petit écran
- [ ] Libellé explicite, ex. "📷 Scanner" ou "Inventaire"

## Critères d'acceptation

- Le bouton est visible et cliquable sur `/stock` en résolution mobile (375px de large) sans scroll horizontal ni superposition avec les onglets Stock atelier/Catalogue complet
- Cliquer dessus navigue vers `/inventaire/scan`
