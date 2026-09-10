<?php

use App\Enums\ArticleCategorySource;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * IDs des catégories créées manuellement par l'utilisateur avant la mise en place
     * du flag `source` (vérifié le 2026-09-10 sur la base de dev/prod : freinage,
     * accessoirs, entretient, transmission, pneumatique). Toute autre catégorie déjà
     * existante à cette date provient de l'import CGN et reste donc `catalogue` (valeur
     * par défaut de la colonne). Ce backfill est ponctuel et lié à cet état précis :
     * sans effet sur une base fraîchement migrée qui ne contient pas ces IDs.
     *
     * @var list<int>
     */
    private const LEGACY_MANUAL_CATEGORY_IDS = [1, 9, 10, 11, 12];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('article_categories', function (Blueprint $table) {
            $table->enum('source', array_map(fn (ArticleCategorySource $c) => $c->value, ArticleCategorySource::cases()))
                ->default(ArticleCategorySource::Catalogue->value)
                ->after('name');
        });

        DB::table('article_categories')
            ->whereIn('id', self::LEGACY_MANUAL_CATEGORY_IDS)
            ->update(['source' => ArticleCategorySource::Manual->value]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('article_categories', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};
