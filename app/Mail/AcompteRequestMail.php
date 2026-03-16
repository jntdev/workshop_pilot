<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AcompteRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $dateReservation;

    public string $dateRetour;

    public string $clientNom;

    public function __construct(
        object $reservationData,
        public float $montantAcompte
    ) {
        // Extraire les données (fonctionne avec Reservation ou stdClass)
        $this->dateReservation = is_string($reservationData->date_reservation)
            ? $reservationData->date_reservation
            : $reservationData->date_reservation->format('Y-m-d');

        $this->dateRetour = is_string($reservationData->date_retour)
            ? $reservationData->date_retour
            : $reservationData->date_retour->format('Y-m-d');

        $client = $reservationData->client ?? null;
        $this->clientNom = $client
            ? strtoupper($client->nom ?? '') . ' ' . ($client->prenom ?? '')
            : 'Client';
    }

    public function envelope(): Envelope
    {
        $dateDebut = date('d/m/Y', strtotime($this->dateReservation));
        $dateFin = date('d/m/Y', strtotime($this->dateRetour));

        return new Envelope(
            subject: "Demande d'acompte - Réservation du {$dateDebut} au {$dateFin}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.acompte-request',
            with: [
                'montantAcompte' => number_format($this->montantAcompte, 2, ',', ' '),
                'clientNom' => trim($this->clientNom),
                'rib' => config('location.rib'),
            ],
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
