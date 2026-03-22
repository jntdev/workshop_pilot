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
    private array $bikeDescriptions = [
        'VTT Rockrider 540 bleu',
        'Vélo de ville Elops 520 noir',
        'Vélo de route Triban RC520 rouge',
        'VAE Riverside 500E vert',
        'Gravel Triban GRVL 120 orange',
        'VTC Riverside 920 anthracite',
        'Vélo cargo Longtail gris',
        'VTT électrique E-ST 500 blanc',
        'Vélo pliant Tilt 500 bleu',
        'BMX Wipe 520 noir',
        'Vélo enfant 24 pouces rose',
        'Tandem ville beige',
        'Vélo de route carbone',
        'Fat bike sable',
        'Vélo hollandais vert foncé',
    ];

    private array $receptionComments = [
        'Révision complète avant saison',
        'Freins qui grincent',
        'Chaîne usée à remplacer',
        'Dérailleur mal réglé',
        'Crevaison + vérification générale',
        'Changement des pneus usés',
        'Roue voilée après chute',
        'Câbles de frein à remplacer',
        'Pédalier qui fait du bruit',
        'Direction dure',
        'Entretien annuel',
        'Préparation pour vélotaf',
        'Changement transmission complète',
        'Installation porte-bagages',
        'Réglage suspension',
    ];

    public function run(): void
    {
        $calculator = new QuoteCalculator;

        // Générer des factures pour les 6 derniers mois dynamiquement
        $months = [];
        $currentDate = now();

        // Générer pour les 6 derniers mois incluant le mois actuel
        for ($i = 5; $i >= 0; $i--) {
            $date = $currentDate->copy()->subMonths($i);
            $months[] = [
                'year' => $date->year,
                'month' => $date->month,
                'count' => rand(10, 20),
            ];
        }

        // Ajouter le mois correspondant de l'année précédente pour comparaison
        $lastYear = $currentDate->copy()->subYear();
        $months[] = [
            'year' => $lastYear->year,
            'month' => $lastYear->month,
            'count' => rand(8, 15),
        ];

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
                    'bike_description' => $this->bikeDescriptions[array_rand($this->bikeDescriptions)],
                    'reception_comment' => $this->receptionComments[array_rand($this->receptionComments)],
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
                    $quantity = rand(1, 3);

                    $calculated = $calculator->fromMarginRate(
                        (string) $purchasePriceHt,
                        (string) $marginRate,
                        (string) $tvaRate
                    );

                    // Calculate line totals (unit prices × quantity)
                    $lineTotals = $calculator->calculateLineTotals(
                        (string) $quantity,
                        (string) $purchasePriceHt,
                        $calculated['sale_price_ht'],
                        $calculated['sale_price_ttc'],
                        $calculated['margin_amount_ht']
                    );

                    QuoteLine::create([
                        'quote_id' => $quote->id,
                        'title' => fake()->words(3, true),
                        'reference' => fake()->optional(0.6)->bothify('REF-####'),
                        'quantity' => $quantity,
                        'purchase_price_ht' => $purchasePriceHt,
                        'sale_price_ht' => $calculated['sale_price_ht'],
                        'sale_price_ttc' => $calculated['sale_price_ttc'],
                        'margin_amount_ht' => $calculated['margin_amount_ht'],
                        'margin_rate' => $calculated['margin_rate'],
                        'tva_rate' => $tvaRate,
                        'line_purchase_ht' => $lineTotals['line_purchase_ht'],
                        'line_margin_ht' => $lineTotals['line_margin_ht'],
                        'line_total_ht' => $lineTotals['line_total_ht'],
                        'line_total_ttc' => $lineTotals['line_total_ttc'],
                        'position' => $j,
                    ]);

                    // Use line totals for quote totals
                    $totalHt += (float) $lineTotals['line_total_ht'];
                    $totalTtc += (float) $lineTotals['line_total_ttc'];
                    $totalTva += (float) $lineTotals['line_total_ttc'] - (float) $lineTotals['line_total_ht'];
                    $marginTotal += (float) $lineTotals['line_margin_ht'];
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
