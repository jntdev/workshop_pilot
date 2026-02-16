<?php

namespace Database\Seeders;

use App\Models\Bike;
use App\Models\Client;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::all();

        if ($clients->isEmpty()) {
            $this->command->warn('Aucun client trouvé. Lancez ClientSeeder d\'abord.');

            return;
        }

        $bikes = Bike::ordered()->get();

        if ($bikes->isEmpty()) {
            $this->command->warn('Aucun vélo trouvé. Lancez BikeSeeder d\'abord.');

            return;
        }

        // Indexer les vélos par nom pour faciliter la sélection
        $bikesByName = $bikes->keyBy('name');

        $today = Carbon::today();

        // Réservation 1: Famille en vacances (bleu - couleur 0)
        $this->createReservation(
            client: $clients->random(),
            startDate: $today->copy()->addDays(2),
            endDate: $today->copy()->addDays(8),
            bikeNames: ['EM1', 'EM2', 'S1', 'S2'],
            bikesByName: $bikesByName,
            color: 0,
            prixTotal: 420.00,
            statut: 'reserve',
            commentaires: 'Famille de 4, vacances été',
            livraison: true,
            adresseLivraison: 'Camping Les Mouettes, 22700 Perros-Guirec',
        );

        // Réservation 2: Couple week-end (rouge - couleur 1)
        $this->createReservation(
            client: $clients->random(),
            startDate: $today->copy()->addDays(5),
            endDate: $today->copy()->addDays(7),
            bikeNames: ['EL1', 'EL2'],
            bikesByName: $bikesByName,
            color: 1,
            prixTotal: 120.00,
            statut: 'en_attente_acompte',
            acompteDemande: true,
            acompteMontant: 36.00,
        );

        // Réservation 3: Groupe randonnée (vert - couleur 2)
        $this->createReservation(
            client: $clients->random(),
            startDate: $today->copy()->addDays(10),
            endDate: $today->copy()->addDays(14),
            bikeNames: ['M1', 'M2', 'M3', 'M4', 'L1', 'L2'],
            bikesByName: $bikesByName,
            color: 2,
            prixTotal: 540.00,
            statut: 'reserve',
            commentaires: 'Groupe rando GR34',
        );

        // Réservation 4: Location courte (orange - couleur 3)
        $this->createReservation(
            client: $clients->random(),
            startDate: $today->copy()->addDays(1),
            endDate: $today->copy()->addDays(2),
            bikeNames: ['ES1'],
            bikesByName: $bikesByName,
            color: 3,
            prixTotal: 45.00,
            statut: 'en_cours',
            acompteDemande: true,
            acompteMontant: 45.00,
            acomptePaye: true,
        );

        // Réservation 5: Semaine complète (violet - couleur 4)
        $this->createReservation(
            client: $clients->random(),
            startDate: $today->copy()->addDays(15),
            endDate: $today->copy()->addDays(22),
            bikeNames: ['EXL1', 'EXL2', 'EL3', 'EL4'],
            bikesByName: $bikesByName,
            color: 4,
            prixTotal: 560.00,
            statut: 'reserve',
            commentaires: 'Tour de Bretagne',
            livraison: true,
            adresseLivraison: 'Gare SNCF Lannion',
            recuperation: true,
            adresseRecuperation: 'Gare SNCF Brest',
        );

        // Réservation 6: Location passée payée (rose - couleur 5)
        $this->createReservation(
            client: $clients->random(),
            startDate: $today->copy()->subDays(10),
            endDate: $today->copy()->subDays(5),
            bikeNames: ['L3', 'L4'],
            bikesByName: $bikesByName,
            color: 5,
            prixTotal: 150.00,
            statut: 'paye',
            paiementFinalLe: $today->copy()->subDays(5),
        );

        // Réservation 7: Location en cours (turquoise - couleur 6)
        $this->createReservation(
            client: $clients->random(),
            startDate: $today->copy()->subDays(2),
            endDate: $today->copy()->addDays(3),
            bikeNames: ['EM3', 'M5'],
            bikesByName: $bikesByName,
            color: 6,
            prixTotal: 175.00,
            statut: 'en_cours',
            acompteDemande: true,
            acompteMontant: 50.00,
            acomptePaye: true,
        );

        // Réservation 8: Annulée (orange foncé - couleur 7)
        $this->createReservation(
            client: $clients->random(),
            startDate: $today->copy()->addDays(20),
            endDate: $today->copy()->addDays(25),
            bikeNames: ['ES2', 'ES3'],
            bikesByName: $bikesByName,
            color: 7,
            prixTotal: 200.00,
            statut: 'annule',
            raisonAnnulation: 'Client a annulé pour raisons personnelles',
        );

        // Réservation 9: Grande famille (indigo - couleur 8)
        $this->createReservation(
            client: $clients->random(),
            startDate: $today->copy()->addDays(25),
            endDate: $today->copy()->addDays(32),
            bikeNames: ['ES1', 'EM5', 'EM6', 'EL6', 'S3'],
            bikesByName: $bikesByName,
            color: 8,
            prixTotal: 840.00,
            statut: 'en_attente_acompte',
            acompteDemande: true,
            acompteMontant: 250.00,
            commentaires: "Grande famille - 5 personnes\nBesoin de 2 sièges enfants",
            livraison: true,
            adresseLivraison: 'Villa Les Hortensias, 22560 Trebeurden',
        );

        // Réservation 10: Week-end sportif (lime - couleur 9)
        $this->createReservation(
            client: $clients->random(),
            startDate: $today->copy()->addDays(12),
            endDate: $today->copy()->addDays(14),
            bikeNames: ['XL1', 'XL2', 'XL3'],
            bikesByName: $bikesByName,
            color: 9,
            prixTotal: 135.00,
            statut: 'reserve',
            commentaires: 'Sortie club cycliste',
        );

        $this->command->info('10 réservations créées avec succès.');
    }

    /**
     * Génère les dates entre deux dates.
     */
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
     * Crée une réservation complète.
     *
     * @param  \Illuminate\Support\Collection<string, Bike>  $bikesByName
     */
    private function createReservation(
        Client $client,
        Carbon $startDate,
        Carbon $endDate,
        array $bikeNames,
        $bikesByName,
        int $color,
        float $prixTotal,
        string $statut,
        ?string $commentaires = null,
        bool $livraison = false,
        ?string $adresseLivraison = null,
        bool $recuperation = false,
        ?string $adresseRecuperation = null,
        bool $acompteDemande = false,
        ?float $acompteMontant = null,
        bool $acomptePaye = false,
        ?Carbon $paiementFinalLe = null,
        ?string $raisonAnnulation = null,
    ): void {
        $dates = $this->generateDates($startDate, $endDate);

        // Construire la sélection avec les vrais bike_id (bike_X)
        $selection = [];
        foreach ($bikeNames as $bikeName) {
            $bike = $bikesByName->get($bikeName);
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

        // Créer la réservation
        Reservation::create([
            'client_id' => $client->id,
            'date_contact' => now()->subDays(rand(1, 30)),
            'date_reservation' => $startDate,
            'date_retour' => $endDate,
            'livraison_necessaire' => $livraison,
            'adresse_livraison' => $adresseLivraison,
            'contact_livraison' => $livraison ? $client->telephone : null,
            'creneau_livraison' => $livraison ? 'Matin (9h-12h)' : null,
            'recuperation_necessaire' => $recuperation,
            'adresse_recuperation' => $adresseRecuperation,
            'contact_recuperation' => $recuperation ? $client->telephone : null,
            'creneau_recuperation' => $recuperation ? 'Après-midi (14h-18h)' : null,
            'prix_total_ttc' => $prixTotal,
            'acompte_demande' => $acompteDemande,
            'acompte_montant' => $acompteMontant,
            'acompte_paye_le' => $acomptePaye ? now()->subDays(rand(1, 5)) : null,
            'paiement_final_le' => $paiementFinalLe,
            'statut' => $statut,
            'raison_annulation' => $raisonAnnulation,
            'commentaires' => $commentaires,
            'selection' => $selection,
            'color' => $color,
        ]);
    }
}
