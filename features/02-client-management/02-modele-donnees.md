# 02 - Modèle de données

Table `clients` (montants en EUR).

| Champ | Type | Contraintes / Description |
| --- | --- | --- |
| `id` | UUID ou BIGINT | PK |
| `prenom` | string | obligatoire |
| `nom` | string | obligatoire |
| `telephone` | string | obligatoire, format libre (peut inclure plusieurs numéros) |
| `email` | string | optionnel, unique s’il est renseigné |
| `adresse` | text | optionnel |
| `origine_contact` | string | comment le client nous a connus |
| `commentaires` | text | notes internes |
| `avantage_type` | enum(`aucun`,`pourcentage`,`montant`) | `aucun` par défaut |
| `avantage_valeur` | decimal(8,2) | ≥ 0, interprétation selon `avantage_type` |
| `avantage_expiration` | datetime | optionnel, date limite d’usage |
| `avantage_applique` | boolean | vrai une fois déduit sur facture |
| `avantage_applique_le` | datetime | optionnel |
| `created_at` / `updated_at` | timestamps | |

Note : `decimal(8,2)` = 8 chiffres au total dont 2 décimales (ex. `999999.99` maximum).

Règles métier :
1. `avantage_type = 'pourcentage'` ⇒ `0 < avantage_valeur ≤ 100`.
2. `avantage_type = 'montant'` ⇒ `avantage_valeur > 0`.
3. `avantage_type = 'aucun'` ⇒ `avantage_valeur = 0`. 
