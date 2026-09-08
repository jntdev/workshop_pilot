<?php

namespace App\Services\Catalogue;

use App\Models\Article;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;
use ZipArchive;

class CgnFtpSyncService
{
    private const LOCAL_CSV_FILENAME = 'StockNouveautesCgn022252.csv';

    private string $destinationDir;

    public function __construct(?string $destinationDir = null)
    {
        $this->destinationDir = $destinationDir ?? base_path();
    }

    /**
     * @return array{success: bool, message: string, imported_at: string|null}
     */
    public function sync(): array
    {
        $tempZipPath = tempnam(sys_get_temp_dir(), 'cgn_sync_');

        try {
            $this->downloadRemoteArchive($tempZipPath);
            $csvPath = $this->extractCsv($tempZipPath);
        } catch (Throwable $e) {
            @unlink($tempZipPath);
            Log::error('Synchronisation FTP CGN : échec du téléchargement ou de l\'extraction.', ['exception' => $e]);

            return [
                'success' => false,
                'message' => 'Connexion au FTP CGN impossible ou fichier distant introuvable.',
                'imported_at' => null,
            ];
        }

        @unlink($tempZipPath);

        $articleCountBefore = Article::count();

        try {
            $exitCode = Artisan::call('catalogue:import-cgn', ['path' => $csvPath]);
        } catch (Throwable $e) {
            Log::error('Synchronisation FTP CGN : échec de la commande d\'import.', ['exception' => $e]);

            return [
                'success' => false,
                'message' => "L'import du catalogue a échoué après téléchargement du fichier.",
                'imported_at' => null,
            ];
        }

        if ($exitCode !== 0) {
            Log::error('Synchronisation FTP CGN : la commande d\'import a retourné un code d\'échec.', ['exit_code' => $exitCode]);

            return [
                'success' => false,
                'message' => "L'import du catalogue a échoué après téléchargement du fichier.",
                'imported_at' => null,
            ];
        }

        $articlesCreated = Article::count() - $articleCountBefore;

        return [
            'success' => true,
            'message' => "Catalogue CGN synchronisé et importé avec succès ({$articlesCreated} nouveaux articles, ".Article::count().' au total).',
            'imported_at' => now()->toIso8601String(),
        ];
    }

    private function downloadRemoteArchive(string $localZipPath): void
    {
        $remotePath = config('services.cgn_ftp.remote_path');

        if (! $remotePath) {
            throw new RuntimeException('Chemin distant FTP CGN non configuré.');
        }

        $disk = Storage::disk('cgn_ftp');

        if (! $disk->exists($remotePath)) {
            throw new RuntimeException("Fichier distant introuvable : {$remotePath}");
        }

        $contents = $disk->get($remotePath);

        if ($contents === null || $contents === false) {
            throw new RuntimeException("Impossible de lire le fichier distant : {$remotePath}");
        }

        file_put_contents($localZipPath, $contents);
    }

    private function extractCsv(string $zipPath): string
    {
        $zip = new ZipArchive;

        if ($zip->open($zipPath) !== true) {
            throw new RuntimeException('Archive ZIP invalide ou corrompue.');
        }

        $entryName = null;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);

            if ($name !== false && str_ends_with(strtolower($name), '.csv')) {
                $entryName = $name;
                break;
            }
        }

        if ($entryName === null) {
            $zip->close();
            throw new RuntimeException("Aucun fichier CSV trouvé dans l'archive.");
        }

        $contents = $zip->getFromName($entryName);
        $zip->close();

        if ($contents === false) {
            throw new RuntimeException("Impossible de lire le contenu du fichier CSV dans l'archive.");
        }

        $finalPath = $this->destinationDir.DIRECTORY_SEPARATOR.self::LOCAL_CSV_FILENAME;
        file_put_contents($finalPath, $contents);

        return $finalPath;
    }
}
