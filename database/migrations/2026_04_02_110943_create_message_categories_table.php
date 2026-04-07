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
        Schema::create('message_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique();
            $table->string('label', 100);
            $table->string('color', 7)->default('#6b7280');
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // Insert default categories
        $now = now();
        DB::table('message_categories')->insert([
            ['slug' => 'accueil', 'label' => 'Accueil', 'color' => '#3b82f6', 'position' => 1, 'is_default' => false, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'atelier', 'label' => 'Atelier', 'color' => '#f59e0b', 'position' => 2, 'is_default' => false, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'location', 'label' => 'Location', 'color' => '#10b981', 'position' => 3, 'is_default' => false, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'autre', 'label' => 'Autre', 'color' => '#6b7280', 'position' => 99, 'is_default' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Add foreign key to messages table
        Schema::table('messages', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('recipient_user_id');
        });

        // Migrate existing category values to category_id
        $categories = DB::table('message_categories')->pluck('id', 'slug');
        foreach ($categories as $slug => $id) {
            DB::table('messages')->where('category', $slug)->update(['category_id' => $id]);
        }

        // Add foreign key constraint
        Schema::table('messages', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('message_categories')->nullOnDelete();
        });

        // Drop old category column
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add category column
        Schema::table('messages', function (Blueprint $table) {
            $table->string('category', 50)->nullable()->after('recipient_user_id');
        });

        // Migrate category_id back to category
        $categories = DB::table('message_categories')->pluck('slug', 'id');
        foreach ($categories as $id => $slug) {
            DB::table('messages')->where('category_id', $id)->update(['category' => $slug]);
        }

        // Drop foreign key and column
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });

        Schema::dropIfExists('message_categories');
    }
};
