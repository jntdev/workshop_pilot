# Ticket 09 : Ventes récentes et annulation

**Type** : Frontend / Inertia + Backend
**Priorité** : Haute — **fait partie de la v1**, pas une évolution différée
**Estimation** : 2h

**Dépend de** : Tickets 06, 08

## Description

L'API expose `Sale::cancel()` (ticket 06), mais sans écran pour retrouver une vente terminée, cette action est inutilisable en pratique au comptoir. Sans ce ticket, une erreur de caisse (mauvais article scanné, double finalisation) resterait irréversible pour le vendeur, qui n'a pas d'accès Tinker/admin — l'API d'annulation conçue avec soin (mouvements `sale_return`, idempotence, ticket 05) serait inutilisable dès le lancement. Vue minimale listant les ventes du jour avec possibilité d'annuler.

## Tâches

- [ ] `SaleController::recent()` (Api) : `GET /api/sales/recent` — liste les ventes `completed`/`cancelled` du jour courant (par défaut), triées par `completed_at` décroissant, avec référence/total/heure/statut/nombre de lignes
- [ ] Section "Ventes récentes" dans `resources/js/Pages/Vente/Caisse.tsx` ou sous-onglet dédié : liste compacte (référence, heure, total, statut), bouton "Annuler" sur les ventes `completed` uniquement
- [ ] Confirmation avant annulation (la caisse n'a pas de mécanisme d'annulation immédiate ailleurs dans l'app à répliquer — une simple confirmation JS/modale suffit, pas de pattern spécifique à respecter)
- [ ] Rafraîchir la liste après annulation réussie

## Critères d'acceptation

- Les ventes complétées dans la journée apparaissent dans la liste, les plus récentes en premier
- Annuler une vente met à jour son statut affiché sans recharger la page
- Une vente déjà annulée n'offre plus le bouton "Annuler"
