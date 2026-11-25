<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Throwable;

/**
 * Trace de chaque mail parti du workshop dans le canal de log « mails » (storage/logs/laravel-mails.log) :
 * destinataires, objet, pièces jointes et, pour un devis ou une facture, son contenu résumé.
 * Sans cette trace, rien ne permet de savoir après coup si un devis ou une facture a été envoyé.
 */
class SentMailLogger
{
    private const MAX_LINES = 30;

    public function handle(MessageSent $event): void
    {
        $message = $event->sent->getOriginalMessage();
        $data = $event->data;

        if (! $message instanceof Email) {
            Log::channel('mails')->info('Mail envoyé', ['mailable' => $data['__laravel_mailable'] ?? null]);

            return;
        }

        $context = array_filter([
            'to' => $this->addresses($message->getTo()),
            'cc' => $this->addresses($message->getCc()),
            'bcc' => $this->addresses($message->getBcc()),
            'from' => $this->addresses($message->getFrom()),
            'attachments' => array_values(array_map(
                fn (DataPart $part) => $part->getFilename(),
                array_filter($message->getAttachments(), fn (DataPart $part) => $part->getDisposition() !== 'inline'),
            )),
            'mailable' => $data['__laravel_mailable'] ?? null,
            'quote' => ($data['quote'] ?? null) instanceof Quote ? $this->quoteSummary($data['quote']) : null,
        ], fn ($value) => $value !== null && $value !== []);

        Log::channel('mails')->info('Mail envoyé : '.$message->getSubject(), $context);
    }

    /**
     * Échec d'envoi d'un devis ou d'une facture (l'erreur est aussi renvoyée à l'écran).
     */
    public static function failed(Quote $quote, string $to, Throwable $e): void
    {
        Log::channel('mails')->error('Échec de l\'envoi du mail : '.($quote->isInvoice() ? 'Facture' : 'Devis').' '.$quote->reference, [
            'to' => [$to],
            'quote_id' => $quote->id,
            'error' => $e->getMessage(),
        ]);
    }

    /**
     * @param  Address[]  $addresses
     * @return list<string>
     */
    private function addresses(array $addresses): array
    {
        return array_map(fn (Address $address) => $address->getAddress(), $addresses);
    }

    /**
     * @return array<string, mixed>
     */
    private function quoteSummary(Quote $quote): array
    {
        $lines = $quote->lines()->orderBy('position')->get();

        return array_filter([
            'id' => $quote->id,
            'reference' => $quote->reference,
            'type' => $quote->isInvoice() ? 'facture' : 'devis',
            'client_id' => $quote->client_id,
            'total_ttc' => $quote->total_ttc,
            'lines_count' => $lines->count(),
            'lines' => $lines->take(self::MAX_LINES)
                ->map(fn ($line) => trim(sprintf('%s × %s = %s € TTC', $line->title, (float) $line->quantity, $line->line_total_ttc)))
                ->all(),
            'email_note' => $quote->email_note !== null ? mb_strimwidth($quote->email_note, 0, 500, '…') : null,
        ], fn ($value) => $value !== null);
    }
}
