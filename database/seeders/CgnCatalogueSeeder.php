<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class CgnCatalogueSeeder extends Seeder
{
    use WithoutModelEvents;

    private const CSV_PATH = 'StockNouveautesCgn022252.csv';

    /**
     * Réimporte le catalogue fournisseur CGN depuis le CSV commis dans le repo
     * (voir `catalogue:import-cgn`), sans connexion FTP. Utile après un
     * `php artisan test`, qui vide la base de dev.
     */
    public function run(): void
    {
        if (! file_exists(base_path(self::CSV_PATH))) {
            $this->command?->error('Fichier introuvable : '.self::CSV_PATH);

            return;
        }

        Artisan::call('catalogue:import-cgn', ['path' => self::CSV_PATH], $this->command?->getOutput());
    }
}
