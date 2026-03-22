<?php

namespace Database\Seeders;

use App\Models\Bike;
use App\Models\Client;
use App\Models\Reservation;
use App\Models\ReservationPayment;
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

        // Réservation 1: Famille en vacances (bleu - couleur 0) - commence demain
        $this->createReservation(
            client: $clients->random(),
            startDate: $today->copy()->addDays(1),
            endDate: $today->copy()->addDays(4),
            bikeNames: ['EMb1', 'EMb2', 'Sb1', 'Sb2'],
            bikesByName: $bikesByName,
            color: 0,
            prixTotal: 420.00,
            statut: 'reserve',
            commentaires: 'Famille de 4, vacances été',
            livraison: true,
            adresseLivraison: 'Camping Les Mouettes, 22700 Perros-Guirec',
        );

        // Réservation 2: Couple week-end (rouge - couleur 1) - dans 3 jours
        $this->createReservation(
            client: $clients->random(),
            startDate: $today->copy()->addDays(3),
            endDate: $today->copy()->addDays(5),
            bikeNames: ['ELb1', 'ELb2'],
            bikesByName: $bikesByName,
            color: 1,
            prixTotal: 120.00,
            statut: 'en_attente_acompte',
            acompteDemande: true,
            acompteMontant: 36.00,
        );

        // Réservation 3: Groupe randonnée (vert - couleur 2) - dans 5 jours
        $this->createReservation(
            client: $clients->random(),
            startDate: $today->copy()->addDays(5),
            endDate: $today->copy()->addDays(7),
            bikeNames: ['Mb1', 'Mb2', 'Mb3', 'Mh1', 'Lb1', 'Lb2'],
            bikesByName: $bikesByName,
            color: 2,
            prixTotal: 540.00,
            statut: 'reserve',
            commentaires: 'Groupe rando GR34',
        );

        // Réservation 4: Location courte (orange - couleur 3) - aujourd'hui
        $this->createReservation(
            client: $clients->random(),
            startDate: $today->copy(),
            endDate: $today->copy()->addDays(1),
            bikeNames: ['ESb1'],
            bikesByName: $bikesByName,
            color: 3,
            prixTotal: 45.00,
            statut: 'en_cours',
            acompteDemande: true,
            acompteMontant: 45.00,
            acomptePaye: true,
        );

        // Réservation 5: Semaine complète (violet - couleur 4) - commence dans 4 jours
        $this->createReservation(
            client: $clients->random(),
            startDate: $today->copy()->addDays(4),
            endDate: $today->copy()->addDays(7),
            bikeNames: ['ELh1', 'ELh2', 'ELb3', 'ELh3'],
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

        // Réservation 6: Location passée payée (rose - couleur 5) - terminée hier
        $reservation6 = $this->createReservation(
            client: $clients->random(),
            startDate: $today->copy()->subDays(4),
            endDate: $today->copy()->subDays(1),
            bikeNames: ['Lh1', 'Lh2'],
            bikesByName: $bikesByName,
            color: 5,
            prixTotal: 150.00,
            statut: 'paye',
            paiementFinalLe: $today->copy()->subDays(1),
        );
        // Paiement intégral en CB
        ReservationPayment::create([
            'reservation_id' => $reservation6->id,
            'amount' => 150.00,
            'method' => 'cb',
            'paid_at' => $today->copy()->subDays(1),
            'note' => 'Paiement complet au retour',
        ]);

        // Réservation 7: Location en cours (turquoise - couleur 6) - depuis hier
        $reservation7 = $this->createReservation(
            client: $clients->random(),
            startDate: $today->copy()->subDays(1),
            endDate: $today->copy()->addDays(2),
            bikeNames: ['EMb3', 'Mh2'],
            bikesByName: $bikesByName,
            color: 6,
            prixTotal: 175.00,
            statut: 'en_cours',
            acompteDemande: true,
            acompteMontant: 50.00,
            acomptePaye: true,
        );
        // Acompte payé en espèces
        ReservationPayment::create([
            'reservation_id' => $reservation7->id,
            'amount' => 50.00,
            'method' => 'liquide',
            'paid_at' => $today->copy()->subDays(3),
            'note' => 'Acompte à la réservation',
        ]);

        // Réservation 8: Annulée (orange foncé - couleur 7) - était prévue dans 2 jours
        $this->createReservation(
            client: $clients->random(),
            startDate: $today->copy()->addDays(2),
            endDate: $today->copy()->addDays(4),
            bikeNames: ['ESb2', 'ESb3'],
            bikesByName: $bikesByName,
            color: 7,
            prixTotal: 200.00,
            statut: 'annule',
            raisonAnnulation: 'Client a annulé pour raisons personnelles',
        );

        // Réservation 9: Grande famille (indigo - couleur 8) - dans 6 jours
        $this->createReservation(
            client: $clients->random(),
            startDate: $today->copy()->addDays(6),
            endDate: $today->copy()->addDays(9),
            bikeNames: ['EMh1', 'EMh2', 'ELh4', 'Sh1'],
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

        // Réservation 10: Week-end sportif (lime - couleur 9) - ce week-end
        $this->createReservation(
            client: $clients->random(),
            startDate: $today->copy()->addDays(2),
            endDate: $today->copy()->addDays(4),
            bikeNames: ['Lh3', 'Mh3'],
            bikesByName: $bikesByName,
            color: 9,
            prixTotal: 135.00,
            statut: 'reserve',
            commentaires: 'Sortie club cycliste',
        );

        // Réservation 11: Payée avec paiements multiples (cyan - couleur 10) - terminée il y a 3 jours
        $reservation11 = $this->createReservation(
            client: $clients->random(),
            startDate: $today->copy()->subDays(6),
            endDate: $today->copy()->subDays(3),
            bikeNames: ['EMb4', 'EMh3'],
            bikesByName: $bikesByName,
            color: 10,
            prixTotal: 280.00,
            statut: 'paye',
            paiementFinalLe: $today->copy()->subDays(3),
            commentaires: 'Paiement en plusieurs fois',
        );
        // Acompte CB
        ReservationPayment::create([
            'reservation_id' => $reservation11->id,
            'amount' => 100.00,
            'method' => 'cb',
            'paid_at' => $today->copy()->subDays(10),
            'note' => 'Acompte',
        ]);
        // Solde en espèces
        ReservationPayment::create([
            'reservation_id' => $reservation11->id,
            'amount' => 180.00,
            'method' => 'liquide',
            'paid_at' => $today->copy()->subDays(3),
            'note' => 'Solde au retour',
        ]);

        // Réservation 12: Payée par chèque (magenta - couleur 11) - terminée il y a 2 jours
        $reservation12 = $this->createReservation(
            client: $clients->random(),
            startDate: $today->copy()->subDays(5),
            endDate: $today->copy()->subDays(2),
            bikeNames: ['ESh1', 'ESh2'],
            bikesByName: $bikesByName,
            color: 11,
            prixTotal: 200.00,
            statut: 'paye',
            paiementFinalLe: $today->copy()->subDays(2),
        );
        ReservationPayment::create([
            'reservation_id' => $reservation12->id,
            'amount' => 200.00,
            'method' => 'cheque',
            'paid_at' => $today->copy()->subDays(2),
            'note' => 'Chèque n°1234567',
        ]);

        // Réservation 13: Payée par virement (jaune - couleur 12) - terminée hier
        $reservation13 = $this->createReservation(
            client: $clients->random(),
            startDate: $today->copy()->subDays(3),
            endDate: $today->copy()->subDays(1),
            bikeNames: ['Sh2'],
            bikesByName: $bikesByName,
            color: 12,
            prixTotal: 350.00,
            statut: 'paye',
            paiementFinalLe: $today->copy()->subDays(1),
            commentaires: 'Entreprise - facture',
        );
        ReservationPayment::create([
            'reservation_id' => $reservation13->id,
            'amount' => 350.00,
            'method' => 'virement',
            'paid_at' => $today->copy()->subDays(5),
            'note' => 'Virement reçu avant location',
        ]);

        // Réservation 14: Payée avec 3 paiements (teal - couleur 13) - terminée il y a 4 jours
        $reservation14 = $this->createReservation(
            client: $clients->random(),
            startDate: $today->copy()->subDays(7),
            endDate: $today->copy()->subDays(4),
            bikeNames: ['ELb1', 'ELb2'],
            bikesByName: $bikesByName,
            color: 13,
            prixTotal: 490.00,
            statut: 'paye',
            paiementFinalLe: $today->copy()->subDays(4),
            commentaires: 'Location longue durée',
        );
        ReservationPayment::create([
            'reservation_id' => $reservation14->id,
            'amount' => 150.00,
            'method' => 'cb',
            'paid_at' => $today->copy()->subDays(10),
            'note' => 'Acompte',
        ]);
        ReservationPayment::create([
            'reservation_id' => $reservation14->id,
            'amount' => 200.00,
            'method' => 'liquide',
            'paid_at' => $today->copy()->subDays(7),
            'note' => 'Au départ',
        ]);
        ReservationPayment::create([
            'reservation_id' => $reservation14->id,
            'amount' => 140.00,
            'method' => 'cb',
            'paid_at' => $today->copy()->subDays(4),
            'note' => 'Solde au retour',
        ]);

        // Réservation 15: En cours avec acompte (olive - couleur 14) - depuis hier
        $reservation15 = $this->createReservation(
            client: $clients->random(),
            startDate: $today->copy()->subDays(1),
            endDate: $today->copy()->addDays(3),
            bikeNames: ['EMb1', 'EMb2'],
            bikesByName: $bikesByName,
            color: 14,
            prixTotal: 180.00,
            statut: 'en_cours',
            acompteDemande: true,
            acompteMontant: 60.00,
            acomptePaye: true,
        );
        ReservationPayment::create([
            'reservation_id' => $reservation15->id,
            'amount' => 60.00,
            'method' => 'cb',
            'paid_at' => $today->copy()->subDays(5),
            'note' => 'Acompte 30%',
        ]);

        $this->command->info('15 réservations créées (toutes dans les 7 prochains jours)');
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
    ): Reservation {
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
        return Reservation::create([
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
