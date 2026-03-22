<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ajouter les colonnes FK
        Schema::table('bikes', function (Blueprint $table) {
            $table->foreignId('bike_category_id')->nullable()->after('id');
            $table->foreignId('bike_size_id')->nullable()->after('bike_category_id');
        });

        // 2. Migrer les données existantes
        $categoryMap = [];
        $sizeMap = [];

        // Créer les catégories depuis les valeurs existantes
        $categories = DB::table('bikes')->select('category')->distinct()->pluck('category');
        $sortOrder = 0;
        foreach ($categories as $categoryName) {
            $hasBattery = $categoryName === 'VAE';
            $color = match ($categoryName) {
                'VAE' => '#FFD233',
                'VTC' => '#005D66',
                default => '#888888',
            };

            $id = DB::table('bike_categories')->insertGetId([
                'name' => $categoryName,
                'color' => $color,
                'has_battery' => $hasBattery,
                'sort_order' => $sortOrder++,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $categoryMap[$categoryName] = $id;
        }

        // Créer les tailles depuis les valeurs existantes
        $sizes = DB::table('bikes')->select('size')->distinct()->pluck('size');
        $sizeColors = [
            'S' => '#6366f1',
            'M' => '#8b5cf6',
            'L' => '#a855f7',
            'XL' => '#c084fc',
        ];
        $sizeOrder = ['S' => 0, 'M' => 1, 'L' => 2, 'XL' => 3];

        foreach ($sizes as $sizeName) {
            $id = DB::table('bike_sizes')->insertGetId([
                'name' => $sizeName,
                'color' => $sizeColors[$sizeName] ?? '#888888',
                'sort_order' => $sizeOrder[$sizeName] ?? 99,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $sizeMap[$sizeName] = $id;
        }

        // 3. Mettre à jour les bikes avec les FK
        foreach ($categoryMap as $name => $id) {
            DB::table('bikes')->where('category', $name)->update(['bike_category_id' => $id]);
        }
        foreach ($sizeMap as $name => $id) {
            DB::table('bikes')->where('size', $name)->update(['bike_size_id' => $id]);
        }

        // 4. Supprimer les anciennes colonnes et ajouter les contraintes FK
        Schema::table('bikes', function (Blueprint $table) {
            $table->dropColumn(['category', 'size']);
        });

        Schema::table('bikes', function (Blueprint $table) {
            $table->foreign('bike_category_id')->references('id')->on('bike_categories')->onDelete('restrict');
            $table->foreign('bike_size_id')->references('id')->on('bike_sizes')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Ajouter les anciennes colonnes
        Schema::table('bikes', function (Blueprint $table) {
            $table->dropForeign(['bike_category_id']);
            $table->dropForeign(['bike_size_id']);
        });

        Schema::table('bikes', function (Blueprint $table) {
            $table->string('category', 10)->nullable()->after('id');
            $table->string('size', 10)->nullable()->after('category');
        });

        // 2. Restaurer les données
        $bikes = DB::table('bikes')
            ->join('bike_categories', 'bikes.bike_category_id', '=', 'bike_categories.id')
            ->join('bike_sizes', 'bikes.bike_size_id', '=', 'bike_sizes.id')
            ->select('bikes.id', 'bike_categories.name as category_name', 'bike_sizes.name as size_name')
            ->get();

        foreach ($bikes as $bike) {
            DB::table('bikes')->where('id', $bike->id)->update([
                'category' => $bike->category_name,
                'size' => $bike->size_name,
            ]);
        }

        // 3. Supprimer les colonnes FK
        Schema::table('bikes', function (Blueprint $table) {
            $table->dropColumn(['bike_category_id', 'bike_size_id']);
        });
    }
};
