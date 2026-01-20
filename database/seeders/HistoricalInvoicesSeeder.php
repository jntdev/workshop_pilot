<?php

namespace Database\Seeders;

use App\Enums\QuoteStatus;
use App\Models\Client;
use App\Models\Quote;
use App\Models\QuoteLine;
use App\Services\Quotes\QuoteCalculator;
use Illuminate\Database\Seeder;

class HistoricalInvoicesSeeder extends Seeder
{
    public function run(): void
    {
        $calculator = new QuoteCalculator;

        // Générer des factures pour les 6 derniers mois (août 2025 à janvier 2026)
        $months = [
            ['year' => 2025, 'month' => 8, 'count' => 12],  // Août 2025
            ['year' => 2025, 'month' => 9, 'count' => 15],  // Septembre 2025
            ['year' => 2025, 'month' => 10, 'count' => 18], // Octobre 2025
            ['year' => 2025, 'month' => 11, 'count' => 14], // Novembre 2025
            ['year' => 2025, 'month' => 12, 'count' => 20], // Décembre 2025
            ['year' => 2026, 'month' => 1, 'count' => 8],   // Janvier 2026 (en cours)
        ];

        // Générer aussi quelques factures pour l'année précédente (janvier 2025)
        $months[] = ['year' => 2025, 'month' => 1, 'count' => 10];

        $clients = Client::all();

        if ($clients->isEmpty()) {
            $this->command->warn('Aucun client trouvé. Création de clients de démonstration...');
            $clients = Client::factory(5)->create();
        }

        $totalInvoices = 0;

        foreach ($months as $monthData) {
            $year = $monthData['year'];
            $month = $monthData['month'];
            $count = $monthData['count'];

            // Générer les factures pour ce mois
            for ($i = 1; $i <= $count; $i++) {
                $client = $clients->random();

                // Date aléatoire dans le mois
                $day = rand(1, min(28, cal_days_in_month(CAL_GREGORIAN, $month, $year)));
                $invoicedAt = now()->setYear($year)->setMonth($month)->setDay($day)->setHour(rand(9, 17))->setMinute(rand(0, 59));

                // Compter les factures créées ce jour-là pour générer la référence
                $datePrefix = $invoicedAt->format('Ymd');
                $countToday = Quote::whereNotNull('invoiced_at')
                    ->whereDate('invoiced_at', $invoicedAt->toDateString())
                    ->count();
                $reference = sprintf('%s-%d', $datePrefix, $countToday + 1);

                // Créer la facture
                $quote = Quote::create([
                    'client_id' => $client->id,
                    'reference' => $reference,
                    'status' => QuoteStatus::Invoiced,
                    'invoiced_at' => $invoicedAt,
                    'valid_until' => $invoicedAt->copy()->addDays(15),
                    'total_ht' => '0.00',
                    'total_tva' => '0.00',
                    'total_ttc' => '0.00',
                    'margin_total_ht' => '0.00',
                ]);

                // Forcer les timestamps pour qu'ils correspondent au mois
                $quote->created_at = $invoicedAt->copy()->subDays(rand(1, 7));
                $quote->updated_at = $invoicedAt;
                $quote->saveQuietly();

                // Générer entre 1 et 5 lignes par facture
                $lineCount = rand(1, 5);
                $totalHt = 0;
                $totalTva = 0;
                $totalTtc = 0;
                $marginTotal = 0;

                for ($j = 0; $j < $lineCount; $j++) {
                    $purchasePriceHt = rand(50, 500);
                    $marginRate = rand(20, 50); // Marge entre 20% et 50%
                    $tvaRate = 20;

                    $calculated = $calculator->fromMarginRate(
                        (string) $purchasePriceHt,
                        (string) $marginRate,
                        (string) $tvaRate
                    );

                    QuoteLine::create([
                        'quote_id' => $quote->id,
                        'title' => fake()->words(3, true),
                        'reference' => fake()->optional(0.6)->bothify('REF-####'),
                        'quantity' => rand(1, 3),
                        'purchase_price_ht' => $purchasePriceHt,
                        'sale_price_ht' => $calculated['sale_price_ht'],
                        'sale_price_ttc' => $calculated['sale_price_ttc'],
                        'margin_amount_ht' => $calculated['margin_amount_ht'],
                        'margin_rate' => $calculated['margin_rate'],
                        'tva_rate' => $tvaRate,
                        'position' => $j,
                    ]);

                    $totalHt += (float) $calculated['sale_price_ht'];
                    $totalTva += ((float) $calculated['sale_price_ttc'] - (float) $calculated['sale_price_ht']);
                    $totalTtc += (float) $calculated['sale_price_ttc'];
                    $marginTotal += (float) $calculated['margin_amount_ht'];
                }

                // Mettre à jour les totaux de la facture
                $quote->update([
                    'total_ht' => number_format($totalHt, 2, '.', ''),
                    'total_tva' => number_format($totalTva, 2, '.', ''),
                    'total_ttc' => number_format($totalTtc, 2, '.', ''),
                    'margin_total_ht' => number_format($marginTotal, 2, '.', ''),
                ]);

                $totalInvoices++;
            }
        }

        $this->command->info("✓ {$totalInvoices} factures historiques créées avec succès.");
    }
}
