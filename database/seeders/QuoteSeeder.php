<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Quote;
use App\Models\QuoteLine;
use Illuminate\Database\Seeder;

class QuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::all();

        if ($clients->isEmpty()) {
            $this->command->warn('Aucun client trouvé. Exécutez d\'abord ClientSeeder.');

            return;
        }

        // Devis 1: Réparation complète pour Marie Dubois
        $quote1 = Quote::create([
            'client_id' => $clients->where('email', 'marie.dubois@example.com')->first()?->id ?? $clients->first()->id,
            'reference' => 'DEV-202511-0001',
            'status' => 'draft',
            'valid_until' => now()->addDays(15),
            'discount_type' => 'percent',
            'discount_value' => 10.00,
            'total_ht' => 162.00,
            'total_tva' => 32.40,
            'total_ttc' => 194.40,
            'margin_total_ht' => 72.00,
        ]);

        QuoteLine::create([
            'quote_id' => $quote1->id,
            'title' => 'Réparation frein avant',
            'reference' => 'FREIN-001',
            'purchase_price_ht' => 35.00,
            'sale_price_ht' => 70.00,
            'sale_price_ttc' => 84.00,
            'margin_amount_ht' => 35.00,
            'margin_rate' => 50.0000,
            'tva_rate' => 20.0000,
            'position' => 0,
        ]);

        QuoteLine::create([
            'quote_id' => $quote1->id,
            'title' => 'Changement chaîne',
            'reference' => 'CHAIN-001',
            'purchase_price_ht' => 20.00,
            'sale_price_ht' => 45.00,
            'sale_price_ttc' => 54.00,
            'margin_amount_ht' => 25.00,
            'margin_rate' => 55.5556,
            'tva_rate' => 20.0000,
            'position' => 1,
        ]);

        QuoteLine::create([
            'quote_id' => $quote1->id,
            'title' => 'Réglage dérailleur',
            'reference' => 'DER-001',
            'purchase_price_ht' => 5.00,
            'sale_price_ht' => 17.00,
            'sale_price_ttc' => 20.40,
            'margin_amount_ht' => 12.00,
            'margin_rate' => 70.5882,
            'tva_rate' => 20.0000,
            'position' => 2,
        ]);

        // Devis 2: Entretien pour Jean Martin
        $quote2 = Quote::create([
            'client_id' => $clients->where('email', 'jean.martin@example.com')->first()?->id ?? $clients->skip(1)->first()->id,
            'reference' => 'DEV-202511-0002',
            'status' => 'validated',
            'valid_until' => now()->addDays(10),
            'discount_type' => null,
            'discount_value' => null,
            'total_ht' => 85.00,
            'total_tva' => 17.00,
            'total_ttc' => 102.00,
            'margin_total_ht' => 45.00,
        ]);

        QuoteLine::create([
            'quote_id' => $quote2->id,
            'title' => 'Révision complète',
            'reference' => 'REV-001',
            'purchase_price_ht' => 15.00,
            'sale_price_ht' => 40.00,
            'sale_price_ttc' => 48.00,
            'margin_amount_ht' => 25.00,
            'margin_rate' => 62.5000,
            'tva_rate' => 20.0000,
            'position' => 0,
        ]);

        QuoteLine::create([
            'quote_id' => $quote2->id,
            'title' => 'Graissage câbles',
            'reference' => 'GRAI-001',
            'purchase_price_ht' => 5.00,
            'sale_price_ht' => 15.00,
            'sale_price_ttc' => 18.00,
            'margin_amount_ht' => 10.00,
            'margin_rate' => 66.6667,
            'tva_rate' => 20.0000,
            'position' => 1,
        ]);

        QuoteLine::create([
            'quote_id' => $quote2->id,
            'title' => 'Contrôle freins',
            'reference' => 'CTRL-001',
            'purchase_price_ht' => 10.00,
            'sale_price_ht' => 30.00,
            'sale_price_ttc' => 36.00,
            'margin_amount_ht' => 20.00,
            'margin_rate' => 66.6667,
            'tva_rate' => 20.0000,
            'position' => 2,
        ]);

        // Devis 3: Changement pneus pour Sophie Bernard
        $quote3 = Quote::create([
            'client_id' => $clients->where('email', 'sophie.bernard@example.com')->first()?->id ?? $clients->skip(2)->first()->id,
            'reference' => 'DEV-202511-0003',
            'status' => 'draft',
            'valid_until' => now()->addDays(20),
            'discount_type' => 'amount',
            'discount_value' => 15.00,
            'total_ht' => 105.00,
            'total_tva' => 21.00,
            'total_ttc' => 126.00,
            'margin_total_ht' => 55.00,
        ]);

        QuoteLine::create([
            'quote_id' => $quote3->id,
            'title' => 'Pneu avant 700x28',
            'reference' => 'PNEU-700-28',
            'purchase_price_ht' => 25.00,
            'sale_price_ht' => 50.00,
            'sale_price_ttc' => 60.00,
            'margin_amount_ht' => 25.00,
            'margin_rate' => 50.0000,
            'tva_rate' => 20.0000,
            'position' => 0,
        ]);

        QuoteLine::create([
            'quote_id' => $quote3->id,
            'title' => 'Pneu arrière 700x28',
            'reference' => 'PNEU-700-28',
            'purchase_price_ht' => 25.00,
            'sale_price_ht' => 50.00,
            'sale_price_ttc' => 60.00,
            'margin_amount_ht' => 25.00,
            'margin_rate' => 50.0000,
            'tva_rate' => 20.0000,
            'position' => 1,
        ]);

        QuoteLine::create([
            'quote_id' => $quote3->id,
            'title' => 'Pose pneus',
            'reference' => 'POSE-001',
            'purchase_price_ht' => 0.00,
            'sale_price_ht' => 20.00,
            'sale_price_ttc' => 24.00,
            'margin_amount_ht' => 20.00,
            'margin_rate' => 100.0000,
            'tva_rate' => 20.0000,
            'position' => 2,
        ]);

        // Devis 4: Réparation roue pour Thomas Petit
        $quote4 = Quote::create([
            'client_id' => $clients->where('email', 'thomas.petit@example.com')->first()?->id ?? $clients->skip(3)->first()->id,
            'reference' => 'DEV-202511-0004',
            'status' => 'draft',
            'valid_until' => now()->addDays(12),
            'discount_type' => null,
            'discount_value' => null,
            'total_ht' => 65.00,
            'total_tva' => 13.00,
            'total_ttc' => 78.00,
            'margin_total_ht' => 35.00,
        ]);

        QuoteLine::create([
            'quote_id' => $quote4->id,
            'title' => 'Centrage roue arrière',
            'reference' => 'CENTR-001',
            'purchase_price_ht' => 10.00,
            'sale_price_ht' => 30.00,
            'sale_price_ttc' => 36.00,
            'margin_amount_ht' => 20.00,
            'margin_rate' => 66.6667,
            'tva_rate' => 20.0000,
            'position' => 0,
        ]);

        QuoteLine::create([
            'quote_id' => $quote4->id,
            'title' => 'Remplacement rayons',
            'reference' => 'RAY-001',
            'purchase_price_ht' => 20.00,
            'sale_price_ht' => 35.00,
            'sale_price_ttc' => 42.00,
            'margin_amount_ht' => 15.00,
            'margin_rate' => 42.8571,
            'tva_rate' => 20.0000,
            'position' => 1,
        ]);

        // Devis 5: Installation accessoires pour Claire Robert
        $quote5 = Quote::create([
            'client_id' => $clients->where('email', 'claire.robert@example.com')->first()?->id ?? $clients->skip(4)->first()->id,
            'reference' => 'DEV-202511-0005',
            'status' => 'validated',
            'valid_until' => now()->addDays(30),
            'discount_type' => 'percent',
            'discount_value' => 5.00,
            'total_ht' => 133.00,
            'total_tva' => 26.60,
            'total_ttc' => 159.60,
            'margin_total_ht' => 63.00,
        ]);

        QuoteLine::create([
            'quote_id' => $quote5->id,
            'title' => 'Éclairage LED avant',
            'reference' => 'LED-AV-001',
            'purchase_price_ht' => 25.00,
            'sale_price_ht' => 45.00,
            'sale_price_ttc' => 54.00,
            'margin_amount_ht' => 20.00,
            'margin_rate' => 44.4444,
            'tva_rate' => 20.0000,
            'position' => 0,
        ]);

        QuoteLine::create([
            'quote_id' => $quote5->id,
            'title' => 'Éclairage LED arrière',
            'reference' => 'LED-AR-001',
            'purchase_price_ht' => 20.00,
            'sale_price_ht' => 38.00,
            'sale_price_ttc' => 45.60,
            'margin_amount_ht' => 18.00,
            'margin_rate' => 47.3684,
            'tva_rate' => 20.0000,
            'position' => 1,
        ]);

        QuoteLine::create([
            'quote_id' => $quote5->id,
            'title' => 'Garde-boue avant/arrière',
            'reference' => 'GARDE-001',
            'purchase_price_ht' => 15.00,
            'sale_price_ht' => 35.00,
            'sale_price_ttc' => 42.00,
            'margin_amount_ht' => 20.00,
            'margin_rate' => 57.1429,
            'tva_rate' => 20.0000,
            'position' => 2,
        ]);

        QuoteLine::create([
            'quote_id' => $quote5->id,
            'title' => 'Pose accessoires',
            'reference' => 'POSE-ACC-001',
            'purchase_price_ht' => 10.00,
            'sale_price_ht' => 22.00,
            'sale_price_ttc' => 26.40,
            'margin_amount_ht' => 12.00,
            'margin_rate' => 54.5455,
            'tva_rate' => 20.0000,
            'position' => 3,
        ]);

        $this->command->info('5 devis créés avec succès avec leurs lignes.');
    }
}
