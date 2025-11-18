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
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            // SQLite: Utiliser TEXT et vérifier les valeurs au niveau de l'application
            Schema::table('quotes', function (Blueprint $table) {
                $table->string('status_new')->after('status')->nullable();
            });

            DB::statement("UPDATE quotes SET status_new = 'brouillon' WHERE status = 'draft'");
            DB::statement("UPDATE quotes SET status_new = 'prêt' WHERE status = 'validated'");

            Schema::table('quotes', function (Blueprint $table) {
                $table->dropColumn('status');
            });

            Schema::table('quotes', function (Blueprint $table) {
                $table->renameColumn('status_new', 'status');
            });

            DB::statement("UPDATE quotes SET status = 'brouillon' WHERE status IS NULL");
        } else {
            // MySQL: Utiliser ENUM
            Schema::table('quotes', function (Blueprint $table) {
                $table->string('status_new')->after('status')->nullable();
            });

            DB::statement("UPDATE quotes SET status_new = 'brouillon' WHERE status = 'draft'");
            DB::statement("UPDATE quotes SET status_new = 'prêt' WHERE status = 'validated'");

            Schema::table('quotes', function (Blueprint $table) {
                $table->dropColumn('status');
            });

            Schema::table('quotes', function (Blueprint $table) {
                $table->renameColumn('status_new', 'status');
            });

            DB::statement("ALTER TABLE quotes MODIFY COLUMN status ENUM('brouillon', 'prêt', 'modifiable', 'facturé') NOT NULL DEFAULT 'brouillon'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement("UPDATE quotes SET status = 'draft' WHERE status = 'brouillon'");
            DB::statement("UPDATE quotes SET status = 'validated' WHERE status = 'prêt'");
        } else {
            DB::statement("ALTER TABLE quotes MODIFY COLUMN status ENUM('draft', 'validated') NOT NULL DEFAULT 'draft'");
            DB::statement("UPDATE quotes SET status = 'draft' WHERE status = 'brouillon'");
            DB::statement("UPDATE quotes SET status = 'validated' WHERE status = 'prêt'");
        }
    }
};
