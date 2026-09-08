# Modèle de données & configuration — Feature 28

## Configuration (pas de nouvelle table)

Identifiants FTP en variables d'environnement, jamais en dur dans le code (règle CLAUDE.md : `env()` interdit hors fichiers `config/`).

`.env` (jamais commité, déjà dans `.gitignore`) :
```
FTP_CGN_HOST=
FTP_CGN_PORT=21
FTP_CGN_USERNAME=
FTP_CGN_PASSWORD=
FTP_CGN_REMOTE_PATH=
```

**Vérifié en conditions réelles** (connexion FTP effective, lecture seule, aucune modification côté serveur distant) : le fichier distant est une archive **`StockNouveautesCgn.zip`** (~1,4 Mo), pas un CSV direct. Elle contient un seul fichier, `StockNouveautesCgn022252.csv` (~7 Mo décompressé), dont les en-têtes et la structure ligne par ligne sont identiques à la copie actuellement committée dans le repo. `FTP_CGN_REMOTE_PATH` pointe donc vers le `.zip`, et le service doit décompresser l'archive après téléchargement avant d'invoquer `catalogue:import-cgn` (voir ci-dessous, étape 2 révisée).

**Décision d'implémentation (écart volontaire par rapport à l'esquisse initiale de ce document)** : plutôt qu'une entrée unique dans `config/services.php`, la connexion FTP est déclarée comme un disque Laravel natif dans `config/filesystems.php` (driver `ftp` déjà supporté nativement par le framework depuis l'ajout de `league/flysystem-ftp`), accessible via `Storage::disk('cgn_ftp')` — approche plus idiomatique Laravel, testable nativement (`Storage::fake('cgn_ftp')`), sans Flysystem manuel dans le service. Seul `remote_path` (qui n'est pas un détail de connexion mais un paramètre de synchronisation) reste dans `services.php` :

`config/filesystems.php`, nouveau disque :
```php
'cgn_ftp' => [
    'driver' => 'ftp',
    'host' => env('FTP_CGN_HOST'),
    'port' => (int) env('FTP_CGN_PORT', 21),
    'username' => env('FTP_CGN_USERNAME'),
    'password' => env('FTP_CGN_PASSWORD'),
    'passive' => true,
    'timeout' => 30,
    'throw' => false,
],
```

`config/services.php`, nouvelle entrée :
```php
'cgn_ftp' => [
    'remote_path' => env('FTP_CGN_REMOTE_PATH'),
],
```

**Piège rencontré et corrigé** : `env('FTP_CGN_PORT', 21)` retourne une chaîne de caractères depuis `.env`, alors que `League\Flysystem\Ftp\FtpConnectionOptions` exige un `int` strict au niveau du constructeur — cast `(int)` obligatoire dans la config, sinon `TypeError` à la première connexion.

`.env.example` : ajouter les 5 clés avec valeurs vides, pour que la présence de la fonctionnalité soit visible dans le repo sans exposer de vrais identifiants.

## Dépendance ajoutée

`league/flysystem-ftp` (FTP classique, confirmé par l'utilisateur — pas SFTP). Package officiel Flysystem déjà utilisé en interne par Laravel pour le disque `local`/`public` existant, donc cohérent avec l'écosystème déjà en place. **Nécessite validation utilisateur avant ajout au `composer.json`** (règle CLAUDE.md : ne pas changer les dépendances sans approbation).

## Service dédié : `App\Services\Catalogue\CgnFtpSyncService`

Isolé du contrôleur pour rester testable sans mocker toute la couche HTTP. Une seule méthode publique :

```php
public function sync(): array
{
    // 1. Connexion FTP (config('services.cgn_ftp')), télécharge l'archive distante
    //    (FTP_CGN_REMOTE_PATH, un .zip) vers un fichier temporaire.
    // 2. Décompresse l'archive (ZipArchive), extrait StockNouveautesCgn022252.csv
    //    directement à la racine du repo — même chemin que le défaut actuel de la
    //    commande, pas de nouveau nom de fichier à gérer ailleurs dans le code
    //    existant. Écrase le fichier local existant (voir arbitrage 2).
    // 3. Supprime le fichier temporaire (.zip téléchargé), qu'il y ait eu succès ou
    //    échec de l'extraction — jamais de fichier temporaire laissé derrière soi.
    // 4. Appelle Artisan::call('catalogue:import-cgn', ['path' => $localPath]).
    // 5. Retourne un résumé structuré (succès/échec, message, compteurs si succès).
}
```

Réponse structurée retournée par le service (et relayée telle quelle par le contrôleur en JSON) :
```php
[
    'success' => bool,
    'message' => string,       // toujours présent, lisible directement à l'utilisateur
    'imported_at' => string|null,  // ISO 8601, uniquement si succès
]
```

Le résumé chiffré du message de succès (nombre d'articles créés, total après import) est calculé par le service lui-même via un delta `Article::count()` avant/après l'appel à `Artisan::call()` — pas en modifiant `ImportCgnCatalogue` (dont les compteurs internes restent privés, sans accesseur public, conformément à l'arbitrage 4 qui interdit toute modification de cette commande).

## Route & contrôleur

`routes/api.php`, nouveau groupe "Catalogue — Synchronisation CGN (feature 28)" :
```php
Route::post('/catalogue/sync-cgn', [CatalogueSyncController::class, 'syncCgn']);
```

`App\Http\Controllers\Api\CatalogueSyncController::syncCgn(CgnFtpSyncService $service): JsonResponse` — délègue entièrement au service, traduit son résultat en code HTTP (200 si `success`, 502 sinon quelle que soit la cause de l'échec — FTP/réseau ou import après téléchargement réussi. Un code unique plutôt qu'une distinction 500/502 : le frontend affiche le même type de message dans les deux cas, arbitrage 5, donc pas de valeur à distinguer côté HTTP pour l'instant).

## Frontend

`resources/js/Pages/Stock/Index.tsx` : nouveau bouton dans `stock-page__header-actions`, état local `isSyncing`/`syncResult` (`{ success: boolean; message: string } | null`), `POST /api/catalogue/sync-cgn` au clic, bouton désactivé pendant la requête, message de résultat affiché sous le header (auto-masqué au clic suivant ou après quelques secondes — détail d'implémentation, pas structurant).
