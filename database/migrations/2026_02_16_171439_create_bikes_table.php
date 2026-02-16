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
        Schema::create('bikes', function (Blueprint $table) {
            $table->id();
            $table->string('bike_type_id', 50);
            $table->string('label'); // Nom affiché (ex: "VAE M-1")
            $table->enum('status', ['OK', 'HS'])->default('OK');
            $table->text('notes')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('bike_type_id')
                ->references('id')
                ->on('bike_types')
                ->onDelete('cascade');

            $table->index(['bike_type_id', 'status']);
        });

        // Migrer les vélos depuis config/bikes.php vers la table bikes
        $this->migrateFromConfig();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bikes');
    }

    /**
     * Migrer les vélos depuis la config vers la base de données.
     */
    private function migrateFromConfig(): void
    {
        $fleet = config('bikes.fleet');

        if (! $fleet) {
            return;
        }

        $sortOrder = 0;
        foreach ($fleet as $bike) {
            $bikeTypeId = $bike['category'].'_'.strtolower($bike['size']).$bike['frame_type'];

            DB::table('bikes')->insert([
                'bike_type_id' => $bikeTypeId,
                'label' => $bike['label'],
                'status' => $bike['status'],
                'notes' => $bike['notes'],
                'sort_order' => $sortOrder++,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
