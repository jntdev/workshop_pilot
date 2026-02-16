<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Reservation;
use App\Models\ReservationItem;
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

        $fleet = config('bikes.fleet');
        $today = Carbon::today();

        // Réservation 1: Famille en vacances (bleu - couleur 0)
        $this->createReservation(
            client: $clients->random(),
            startDate: $today->copy()->addDays(2),
            endDate: $today->copy()->addDays(8),
            bikes: ['vae-m-01', 'vae-m-02', 'vtc-s-01', 'vtc-s-02'],
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
            bikes: ['vae-l-01', 'vae-l-02'],
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
            bikes: ['vtc-m-01', 'vtc-m-02', 'vtc-m-03', 'vtc-m-04', 'vtc-l-01', 'vtc-l-02'],
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
            bikes: ['vae-s-01'],
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
            bikes: ['vae-xl-01', 'vae-xl-02', 'vae-l-03', 'vae-l-04'],
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
            bikes: ['vtc-l-03', 'vtc-l-04'],
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
            bikes: ['vae-m-03', 'vtc-m-05'],
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
            bikes: ['vae-s-02', 'vae-s-04'],
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
            bikes: ['vae-s-01', 'vae-m-05', 'vae-m-06', 'vae-l-05', 'vae-l-06', 'vtc-s-03', 'vtc-m-06'],
            color: 8,
            prixTotal: 840.00,
            statut: 'en_attente_acompte',
            acompteDemande: true,
            acompteMontant: 250.00,
            commentaires: 'Grande famille - 7 personnes',
            livraison: true,
            adresseLivraison: 'Villa Les Hortensias, 22560 Trebeurden',
        );

        // Réservation 10: Week-end sportif (lime - couleur 9)
        $this->createReservation(
            client: $clients->random(),
            startDate: $today->copy()->addDays(12),
            endDate: $today->copy()->addDays(14),
            bikes: ['vtc-xl-01', 'vtc-xl-02', 'vtc-xl-03'],
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
     * Génère le label court d'un vélo.
     */
    private function bikeToLabel(array $bike): string
    {
        $prefix = $bike['category'] === 'VAE' ? 'E' : '';
        $parts = explode('-', $bike['id']);
        $num = count($parts) >= 3 ? (int) $parts[2] : 0;

        return $prefix.$bike['size'].$bike['frame_type'].$num;
    }

    /**
     * Crée une réservation complète.
     */
    private function createReservation(
        Client $client,
        Carbon $startDate,
        Carbon $endDate,
        array $bikes,
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
        $fleet = collect(config('bikes.fleet'))->keyBy('id');
        $dates = $this->generateDates($startDate, $endDate);

        // Construire la sélection
        $selection = [];
        foreach ($bikes as $bikeId) {
            $bike = $fleet->get($bikeId);
            if ($bike) {
                $selection[] = [
                    'bike_id' => $bikeId,
                    'label' => $this->bikeToLabel($bike),
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'dates' => $dates,
                    'is_hs' => $bike['status'] === 'HS',
                ];
            }
        }

        // Créer la réservation
        $reservation = Reservation::create([
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

        // Créer les items (groupés par type)
        $itemsByType = [];
        foreach ($bikes as $bikeId) {
            $bike = $fleet->get($bikeId);
            if ($bike) {
                $typeId = $bike['category'].'_'.strtolower($bike['size']).$bike['frame_type'];
                $itemsByType[$typeId] = ($itemsByType[$typeId] ?? 0) + 1;
            }
        }

        foreach ($itemsByType as $typeId => $quantite) {
            ReservationItem::create([
                'reservation_id' => $reservation->id,
                'bike_type_id' => $typeId,
                'quantite' => $quantite,
            ]);
        }
    }
}
