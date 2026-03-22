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
        Schema::create('bike_types', function (Blueprint $table) {
            $table->string('id', 50)->primary(); // VAE_sb, VTC_mh, etc.
            $table->enum('category', ['VAE', 'VTC']);
            $table->enum('size', ['S', 'M', 'L', 'XL']);
            $table->enum('frame_type', ['b', 'h']); // b = bas, h = haut
            $table->string('label');
            $table->unsignedInteger('stock')->default(0); // Nombre total de vélos de ce type
            $table->timestamps();
        });

        // Seed avec les types extraits de config/bikes.php
        $this->seedBikeTypes();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bike_types');
    }

    /**
     * Seed les bike_types à partir de la config bikes.
     */
    private function seedBikeTypes(): void
    {
        $fleet = config('bikes.fleet');
        $types = [];

        foreach ($fleet as $bike) {
            $typeId = $bike['category'].'_'.strtolower($bike['size']).$bike['frame_type'];

            if (! isset($types[$typeId])) {
                $frameLabel = $bike['frame_type'] === 'b' ? 'cadre bas' : 'cadre haut';
                $types[$typeId] = [
                    'id' => $typeId,
                    'category' => $bike['category'],
                    'size' => $bike['size'],
                    'frame_type' => $bike['frame_type'],
                    'label' => "{$bike['category']} {$bike['size']} {$frameLabel}",
                    'stock' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Compter tous les vélos (OK et HS)
            $types[$typeId]['stock']++;
        }

        DB::table('bike_types')->insert(array_values($types));
    }
};
