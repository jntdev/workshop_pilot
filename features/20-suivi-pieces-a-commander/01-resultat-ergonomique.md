# Resultat ergonomique attendu

## Flow 1 - Depuis un devis atelier

1. L'utilisateur ouvre un devis atelier en cours.
2. Dans le tableau des lignes, chaque ligne dispose d'une case "A commander".
3. Si la case est cochee :
   - la ligne remonte dans une vue centralisee de suivi
   - la ligne reste modifiable dans le devis
4. Si la ligne est decochee avant commande, elle disparait du panier.

## Flow 2 - Vue panier / pieces a commander

1. L'utilisateur ouvre une nouvelle vue dediee depuis l'atelier, par exemple "Pieces a commander".
2. Il voit la liste de toutes les lignes cochees "A commander" non recues.
3. Chaque ligne affiche au minimum :
   - nom du client
   - description du velo
   - intitule de la ligne
   - reference de la piece
   - quantite
   - statut d'approvisionnement
   - action `Consulter` vers le devis source
4. Depuis cette vue, il peut cocher :
   - "Commandee"
   - "Recue"
5. Une piece recue reste visible comme recue tant que l'utilisateur est sur la vue courante si besoin de feedback immediat, puis sort de la liste ouverte au rechargement suivant ou via filtre.

## Regles UX

- la reference de piece doit etre tres visible dans la vue panier
- le contexte client + velo doit permettre de reconnaitre immediatement "de quoi on parle"
- la vue doit permettre un traitement en serie, sans ouvrir chaque devis
- les statuts doivent etre comprenables sans formation

## Statuts affiches

### Statut 1 - A commander
- ligne cochee "A commander"
- pas encore marquee "Commandee"
- pas encore marquee "Recue"

### Statut 2 - Commandee
- ligne marquee "Commandee"
- pas encore marquee "Recue"

### Statut 3 - Recue
- ligne marquee "Recue"
- la reception implique qu'elle a ete commandee

## Filtres minimums recommandes

- ouvertes uniquement : par defaut, n'afficher que les pieces non recues
- afficher les recues : filtre secondaire optionnel
