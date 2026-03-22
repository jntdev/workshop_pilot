<?php

namespace Database\Seeders;

use App\Models\Bike;
use App\Models\Client;
use App\Models\Reservation;
use App\Models\ReservationPayment;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MassReservationSeeder extends Seeder
{
    private array $statuts = ['reserve', 'en_attente_acompte', 'en_cours', 'paye'];

    private array $paymentMethods = ['cb', 'liquide', 'cheque', 'virement'];

    private array $creneaux = ['Matin (9h-12h)', 'Après-midi (14h-18h)', 'Soir (18h-20h)'];

    private array $commentaires = [
        'Vacances en famille',
        'Week-end entre amis',
        'Randonnée GR34',
        'Tour de Bretagne',
        'Balade côtière',
        'Client régulier',
        'Première location',
        'Groupe entreprise',
        'Anniversaire',
        'Lune de miel',
        null,
        null,
        null,
    ];

    /**
     * Run the database seeds.
     * Crée 500 réservations entre février et octobre 2026.
     */
    public function run(): void
    {
        $clients = Client::all();

        if ($clients->isEmpty()) {
            $this->command->warn('Aucun client trouvé. Création de 100 clients...');
            Client::factory()->count(100)->create();
            $clients = Client::all();
        }

        $bikes = Bike::ordered()->get();

        if ($bikes->isEmpty()) {
            $this->command->warn('Aucun vélo trouvé. Lancez BikeSeeder d\'abord.');

            return;
        }

        $this->command->info('Création de 500 réservations (février - octobre 2026)...');

        $startPeriod = Carbon::create(2026, 2, 1);
        $endPeriod = Carbon::create(2026, 10, 31);

        $bikeIds = $bikes->pluck('id')->toArray();
        $bikesByName = $bikes->keyBy('name');

        $progressBar = $this->command->getOutput()->createProgressBar(500);
        $progressBar->start();

        for ($i = 0; $i < 500; $i++) {
            $this->createRandomReservation(
                clients: $clients,
                bikeIds: $bikeIds,
                bikesByName: $bikesByName,
                startPeriod: $startPeriod,
                endPeriod: $endPeriod,
            );

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->newLine();
        $this->command->info('500 réservations créées avec succès !');
    }

    private function createRandomReservation(
        $clients,
        array $bikeIds,
        $bikesByName,
        Carbon $startPeriod,
        Carbon $endPeriod,
    ): Reservation {
        $faker = fake();

        // Date de début aléatoire dans la période
        $daysInPeriod = $startPeriod->diffInDays($endPeriod);
        $startDate = $startPeriod->copy()->addDays(rand(0, $daysInPeriod - 7));

        // Durée de location: 1 à 14 jours (plus souvent 2-5 jours)
        $duration = $this->weightedRandom([
            1 => 5,
            2 => 20,
            3 => 25,
            4 => 20,
            5 => 15,
            6 => 5,
            7 => 5,
            8 => 2,
            9 => 1,
            10 => 1,
            14 => 1,
        ]);
        $endDate = $startDate->copy()->addDays($duration);

        // Sélection de 1 à 4 vélos (plus souvent 1-2)
        $bikeCount = $this->weightedRandom([
            1 => 40,
            2 => 35,
            3 => 15,
            4 => 10,
        ]);
        $selectedBikeIds = $faker->randomElements($bikeIds, min($bikeCount, count($bikeIds)));

        // Construire la sélection
        $selection = [];
        $dates = $this->generateDates($startDate, $endDate);

        foreach ($selectedBikeIds as $bikeId) {
            $bike = $bikesByName->first(fn ($b) => $b->id === $bikeId);
            if ($bike) {
                $selection[] = [
                    'bike_id' => 'bike_'.$bike->id,
                    'label' => $bike->name,
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'dates' => $dates,
                    'is_hs' => $bike->status === 'HS',
                ];
            }
        }

        // Prix entre 30 et 800 euros
        $prixTotal = round($faker->randomFloat(2, 30 * count($selectedBikeIds), 100 * count($selectedBikeIds) * $duration), 2);

        // Statut
        $statut = $faker->randomElement($this->statuts);

        // Livraison (20% des cas)
        $livraison = $faker->boolean(20);
        $recuperation = $livraison ? $faker->boolean(80) : $faker->boolean(10);

        // Acompte (50% des cas pour les montants > 100)
        $acompteDemande = $prixTotal > 100 && $faker->boolean(50);
        $acompteMontant = $acompteDemande ? round($prixTotal * 0.3, 2) : null;
        $acomptePaye = $acompteDemande && $faker->boolean(70);

        // Couleur aléatoire (0-29)
        $color = rand(0, 29);

        $reservation = Reservation::create([
            'client_id' => $clients->random()->id,
            'date_contact' => $startDate->copy()->subDays(rand(1, 30)),
            'date_reservation' => $startDate,
            'date_retour' => $endDate,
            'livraison_necessaire' => $livraison,
            'adresse_livraison' => $livraison ? $faker->address() : null,
            'contact_livraison' => $livraison ? $faker->phoneNumber() : null,
            'creneau_livraison' => $livraison ? $faker->randomElement($this->creneaux) : null,
            'recuperation_necessaire' => $recuperation,
            'adresse_recuperation' => $recuperation ? $faker->address() : null,
            'contact_recuperation' => $recuperation ? $faker->phoneNumber() : null,
            'creneau_recuperation' => $recuperation ? $faker->randomElement($this->creneaux) : null,
            'prix_total_ttc' => $prixTotal,
            'acompte_demande' => $acompteDemande,
            'acompte_montant' => $acompteMontant,
            'acompte_paye_le' => $acomptePaye ? $startDate->copy()->subDays(rand(1, 14)) : null,
            'paiement_final_le' => $statut === 'paye' ? $endDate : null,
            'statut' => $statut,
            'raison_annulation' => null,
            'commentaires' => $faker->randomElement($this->commentaires),
            'selection' => $selection,
            'color' => $color,
        ]);

        // Ajouter des paiements pour les réservations payées
        if ($statut === 'paye') {
            $this->createPayments($reservation, $prixTotal, $endDate);
        } elseif ($acomptePaye && $acompteMontant) {
            // Acompte payé
            ReservationPayment::create([
                'reservation_id' => $reservation->id,
                'amount' => $acompteMontant,
                'method' => $faker->randomElement($this->paymentMethods),
                'paid_at' => $startDate->copy()->subDays(rand(1, 14)),
                'note' => 'Acompte',
            ]);
        }

        return $reservation;
    }

    private function createPayments(Reservation $reservation, float $total, Carbon $endDate): void
    {
        $faker = fake();

        // 1 à 3 paiements
        $paymentCount = $this->weightedRandom([
            1 => 60,
            2 => 30,
            3 => 10,
        ]);

        $remaining = $total;
        $paymentDate = $endDate->copy();

        for ($i = 0; $i < $paymentCount; $i++) {
            if ($remaining <= 0) {
                break;
            }

            // Dernier paiement = reste
            if ($i === $paymentCount - 1) {
                $amount = $remaining;
            } else {
                $amount = round($faker->randomFloat(2, $remaining * 0.3, $remaining * 0.7), 2);
            }

            ReservationPayment::create([
                'reservation_id' => $reservation->id,
                'amount' => $amount,
                'method' => $faker->randomElement($this->paymentMethods),
                'paid_at' => $paymentDate->copy()->subDays(rand(0, 5)),
                'note' => $i === 0 ? null : ($i === 1 ? 'Complément' : 'Solde'),
            ]);

            $remaining -= $amount;
            $paymentDate->subDays(rand(1, 7));
        }
    }

    private function generateDates(Carbon $start, Carbon $end): array
    {
        $dates = [];
        $current = $start->copy();

        while ($current->lte($end)) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }

        return $dates;
    }

    /**
     * Retourne une valeur aléatoire pondérée.
     *
     * @param  array<int, int>  $weights  [value => weight]
     */
    private function weightedRandom(array $weights): int
    {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);

        foreach ($weights as $value => $weight) {
            $random -= $weight;
            if ($random <= 0) {
                return $value;
            }
        }

        return array_key_first($weights);
    }
}
