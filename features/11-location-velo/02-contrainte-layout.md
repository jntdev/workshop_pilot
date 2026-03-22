# 2 - Contraintes de layout

- **Répartition fixe** : le container principal exploite CSS Grid ou Flex pour maintenir 75 % (tableau) / 25 % (formulaire) quelle que soit la taille d'écran ≥ 1280 px ; en dessous, la colonne formulaire passe sous le tableau mais conserve sa largeur minimale de 320 px.
- **Hauteur** : le tableau occupe toute la hauteur disponible sous le header applicatif et utilise `overflow-y: auto`; le panneau formulaire scroll indépendamment afin d'éviter un verrouillage de l'UI.
- **Bandeau "Aujourd'hui"** : chaque cellule réserve le tiers supérieur à un bandeau mince qui devient accentué uniquement pour la date du jour (repère visuel), en respectant le thème (pas de couleurs arbitraires).
- **Accessibilité** : contraste suffisant pour distinguer les états, navigation clavier possible via tabulation/arrow (alignée avec l'implémentation future des interactions).
- **Performances** : virtualisation obligatoire lorsque le viewport affiche plus de ~40 lignes, sinon l'UI devient poussive sur les postes d'accueil.
