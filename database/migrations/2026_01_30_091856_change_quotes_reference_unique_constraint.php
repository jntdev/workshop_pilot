<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Change the unique constraint on 'reference' to allow quotes and invoices
     * to have the same reference number (separate sequences).
     */
    public function up(): void
    {
        // 1. Drop the existing unique constraint on 'reference'
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropUnique(['reference']);
        });

        // 2. Add a generated column 'is_invoice' (1 if invoiced_at is not null, 0 otherwise)
        DB::statement('ALTER TABLE quotes ADD COLUMN is_invoice TINYINT(1) GENERATED ALWAYS AS (invoiced_at IS NOT NULL) STORED');

        // 3. Add composite unique constraint on (reference, is_invoice)
        Schema::table('quotes', function (Blueprint $table) {
            $table->unique(['reference', 'is_invoice'], 'quotes_reference_type_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Drop the composite unique constraint
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropUnique('quotes_reference_type_unique');
        });

        // 2. Drop the generated column
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn('is_invoice');
        });

        // 3. Restore the original unique constraint on 'reference'
        Schema::table('quotes', function (Blueprint $table) {
            $table->unique('reference');
        });
    }
};
