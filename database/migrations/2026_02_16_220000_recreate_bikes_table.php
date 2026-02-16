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
        // Si la table n'existe pas, la créer
        if (! Schema::hasTable('bikes')) {
            Schema::create('bikes', function (Blueprint $table) {
                $table->id();
                $table->enum('category', ['VAE', 'VTC']);
                $table->enum('size', ['S', 'M', 'L', 'XL']);
                $table->enum('frame_type', ['b', 'h']);
                $table->string('name', 100);
                $table->enum('status', ['OK', 'HS'])->default('OK');
                $table->text('notes')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->index(['category', 'size', 'frame_type', 'status']);
            });

            return;
        }

        // Si la table existe, corriger sa structure
        // Renommer 'label' en 'name' si nécessaire
        if (Schema::hasColumn('bikes', 'label') && ! Schema::hasColumn('bikes', 'name')) {
            Schema::table('bikes', function (Blueprint $table) {
                $table->renameColumn('label', 'name');
            });
        }

        // Supprimer bike_type_id si elle existe
        if (Schema::hasColumn('bikes', 'bike_type_id')) {
            // Vérifier si la FK existe avant de la supprimer
            $fkExists = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'bikes'
                AND CONSTRAINT_TYPE = 'FOREIGN KEY'
                AND CONSTRAINT_NAME = 'bikes_bike_type_id_foreign'
            ");

            if (count($fkExists) > 0) {
                Schema::table('bikes', function (Blueprint $table) {
                    $table->dropForeign(['bike_type_id']);
                });
            }

            Schema::table('bikes', function (Blueprint $table) {
                $table->dropColumn('bike_type_id');
            });
        }

        // Ajouter les colonnes manquantes
        if (! Schema::hasColumn('bikes', 'category')) {
            Schema::table('bikes', function (Blueprint $table) {
                $table->enum('category', ['VAE', 'VTC'])->default('VAE')->after('id');
            });
        }

        if (! Schema::hasColumn('bikes', 'size')) {
            Schema::table('bikes', function (Blueprint $table) {
                $table->enum('size', ['S', 'M', 'L', 'XL'])->default('M')->after('category');
            });
        }

        if (! Schema::hasColumn('bikes', 'frame_type')) {
            Schema::table('bikes', function (Blueprint $table) {
                $table->enum('frame_type', ['b', 'h'])->default('b')->after('size');
            });
        }

        if (! Schema::hasColumn('bikes', 'name')) {
            Schema::table('bikes', function (Blueprint $table) {
                $table->string('name', 100)->default('')->after('frame_type');
            });
        }

        if (! Schema::hasColumn('bikes', 'status')) {
            Schema::table('bikes', function (Blueprint $table) {
                $table->enum('status', ['OK', 'HS'])->default('OK')->after('name');
            });
        }

        if (! Schema::hasColumn('bikes', 'notes')) {
            Schema::table('bikes', function (Blueprint $table) {
                $table->text('notes')->nullable()->after('status');
            });
        }

        if (! Schema::hasColumn('bikes', 'sort_order')) {
            Schema::table('bikes', function (Blueprint $table) {
                $table->unsignedInteger('sort_order')->default(0)->after('notes');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bikes');
    }
};
