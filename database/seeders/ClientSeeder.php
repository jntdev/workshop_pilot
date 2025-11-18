<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer quelques clients avec des données réalistes
        \App\Models\Client::factory()->create([
            'prenom' => 'Marie',
            'nom' => 'Dubois',
            'email' => 'marie.dubois@example.com',
            'telephone' => '0612345678',
            'adresse' => '15 rue de la République, 75011 Paris',
            'avantage_type' => 'pourcentage',
            'avantage_valeur' => 10.00,
            'avantage_expiration' => now()->addMonths(3),
        ]);

        \App\Models\Client::factory()->create([
            'prenom' => 'Jean',
            'nom' => 'Martin',
            'email' => 'jean.martin@example.com',
            'telephone' => '0623456789',
            'adresse' => '42 avenue des Champs, 69001 Lyon',
            'avantage_type' => 'montant',
            'avantage_valeur' => 25.00,
            'avantage_expiration' => now()->addMonths(6),
        ]);

        \App\Models\Client::factory()->create([
            'prenom' => 'Sophie',
            'nom' => 'Bernard',
            'email' => 'sophie.bernard@example.com',
            'telephone' => '0634567890',
            'adresse' => '8 place du Marché, 33000 Bordeaux',
            'avantage_type' => 'aucun',
            'avantage_valeur' => 0,
        ]);

        \App\Models\Client::factory()->create([
            'prenom' => 'Thomas',
            'nom' => 'Petit',
            'email' => 'thomas.petit@example.com',
            'telephone' => '0645678901',
            'adresse' => '23 boulevard Victor Hugo, 44000 Nantes',
            'avantage_type' => 'pourcentage',
            'avantage_valeur' => 15.00,
            'avantage_expiration' => now()->addYear(),
        ]);

        \App\Models\Client::factory()->create([
            'prenom' => 'Claire',
            'nom' => 'Robert',
            'email' => 'claire.robert@example.com',
            'telephone' => '0656789012',
            'adresse' => '67 rue Saint-Jean, 31000 Toulouse',
            'avantage_type' => 'aucun',
            'avantage_valeur' => 0,
        ]);

        // Créer 7 autres clients aléatoires
        \App\Models\Client::factory()->count(7)->create();
    }
}
