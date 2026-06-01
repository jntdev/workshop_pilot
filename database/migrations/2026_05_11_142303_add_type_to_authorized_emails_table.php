<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('authorized_emails', function (Blueprint $table) {
            $table->string('type', 20)->default('admin')->after('email');
        });

        DB::table('authorized_emails')->update(['type' => 'admin']);
    }

    public function down(): void
    {
        Schema::table('authorized_emails', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
