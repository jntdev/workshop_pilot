# 01 - Périmètre métier

Feature : **06 - Atelier / Création de devis**

## Objectifs
- Permettre aux équipes atelier de créer rapidement un devis pour une réparation ou un entretien vélo.
- Gérer les cas client existant ou nouveau via un seul formulaire.
- Calculer automatiquement prix HT/TTC et marges brutes par prestation et pour l'ensemble du devis.
- Préparer les données nécessaires pour suivre les devis (totaux, remises, statut, date) et les relier aux futures ordres de réparation.

## Processus métier cible
1. Accès depuis la page `Atelier` via le bouton « Nouveau devis ».
2. Saisie/selection du client : soit recherche d'un client connu, soit création d'un nouveau client. Les champs restent éditables après sélection.
3. Ajout des prestations (plusieurs lignes dynamiques) avec références produits, prix achat, prix vente HT/TTC, marges automatiques.
4. Vue synthèse des totaux HT/TVA/TTC + somme des marges + remise éventuelle.
5. Sauvegarde du devis (statut brouillon) et future validation/envoi.

## Hors périmètre immédiat
- Génération PDF / envoi email.
- Transformation du devis en ordre de réparation ou facture (documenter hooks mais non implémenté).
- Gestion des stocks fournisseurs.

## Contraintes
- L'UI reste en Blade + SCSS (pas de framework CSS externe).
- Calculs en temps réel gérés exclusivement par Livewire (pas d'Alpine.js).
- Les montants doivent rester exacts (pas d'arrondis approximatifs), utiliser `bcmath`/helpers Laravel pour les conversions avec règles d'arrondis : montants financiers sur 2 décimales, taux/marges sur 4 décimales.
