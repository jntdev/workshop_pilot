<?php

namespace App\Mail;

use App\Models\LocationContract;
use App\Services\ContractPdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractSignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public LocationContract $contract) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre contrat de location – Les vélos d\'Armor',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contract-signed',
        );
    }

    /** @return array<int, Attachment> */
    public function attachments(): array
    {
        $pdfService = app(ContractPdfService::class);
        $pdf = $pdfService->download($this->contract);

        return [
            Attachment::fromData(
                fn () => \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.location-contract', ['contract' => $this->contract])->output(),
                "contrat-location-{$this->contract->reservation_id}.pdf"
            )->withMime('application/pdf'),
        ];
    }
}
