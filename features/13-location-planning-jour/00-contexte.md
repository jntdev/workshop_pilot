# Contexte — Planning quotidien Location

- L’équipe Location doit préparer chaque matin les vélos qui partent (livraison ou remise sur place) et anticiper les retours.  
- Aujourd’hui, ils lisent la grille annuelle pour savoir si un vélo est libre, mais cette vue n’aide ni à préparer la journée (liste des départs/livraisons) ni à la clôturer (retours à vérifier).  
- Nous voulons une vue dédiée, centrée sur une journée donnée, accessible directement depuis l’écran principal Location.

## Périmètre de la feature 13
1. Un bouton « Voir aujourd’hui » dans le header de la page Location qui ouvre `/location/planning?date=<jour>`.  
2. Une page Planning affichant les départs et retours du jour sélectionné, groupés par mode logistique (livraison vs sur place).  
3. Possibilité de naviguer à un autre jour (hier/demain ou sélection directe) pour préparer la logistique à l’avance.
