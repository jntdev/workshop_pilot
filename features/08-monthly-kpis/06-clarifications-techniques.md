# 06 - Clarifications techniques

## Source of truth
- Toutes les factures = quotes avec `invoiced_at` non null.
- Les KPI stockent uniquement des sommes et un compteur. Les ratios sont derives a l'affichage.

## Champ `metier` sur Quote
- Enum `Metier` avec trois valeurs : `Atelier`, `Vente`, `Location`.
- Chaque quote doit avoir un metier assigne.
- Pour les quotes existantes, valeur par defaut = `atelier`.

## Mois en cours vs mois passes
- Mois passes : consideres immuables, KPI figes.
- Mois en cours : KPI mis a jour en temps reel a chaque conversion en facture.
- Le dashboard lit toujours `monthly_kpis`, meme pour le mois courant.

## Protection contre le double comptage
- La methode `convertToInvoice()` leve une `DomainException` si la quote est deja une facture.
- L'appel a `MonthlyKpiUpdater::applyInvoice()` se fait uniquement apres la conversion reussie.
- Pas de flag supplementaire necessaire.

## Correction exceptionnelle
- En cas de correction sur des factures passees, relancer la commande de backfill.
- Le backfill supprime les lignes existantes avant de recalculer.

## Timezone
- Utiliser la timezone applicative (`config('app.timezone')`) pour extraire year/month depuis `invoiced_at`.

## Concurrence
- Utiliser une transaction avec `lockForUpdate` pour garantir l'atomicite des increments.
- Alternative : utiliser `increment()` d'Eloquent qui est atomique au niveau SQL.

## Extension future
- Le champ `metier` permet d'avoir des dashboards separes pour `vente` et `location`.
- Meme structure, meme table `monthly_kpis`, filtre different.
