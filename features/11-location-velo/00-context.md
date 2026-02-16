# 0 - Contexte

## Location vélo
- L'équipe doit répondre rapidement aux demandes téléphoniques de location en ayant une vue consolidée sur la disponibilité réelle des vélos (VAE/VTC, tailles S à XL).
- Les informations proviennent pour l'instant d'une configuration locale décrivant la flotte ; la logique de réservation arrivera plus tard.
- L'objectif immédiat est d'offrir un support visuel fiable pendant la prise de réservation, sans flux backend supplémentaire.

## Organisation visuelle
- L'écran se découpe en deux panneaux fixes : le tableau des disponibilités (75 % largeur, à gauche) et le panneau d'édition/formulaire (25 %, à droite).
- Les opérateurs doivent pouvoir retenir d'un coup d'œil quelles dates sont libres pour chaque vélo, grâce à des colonnes regroupant les vélos et des lignes représentant les jours de l'année.
- La première zone livrable se concentre exclusivement sur le tableau, mais tout le layout est spécifié pour éviter un rework lorsque le formulaire sera branché.
