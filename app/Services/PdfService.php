<?php

namespace App\Services;

use App\Models\Quote;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PdfService
{
    public function generateQuotePdf(Quote $quote, bool $inline = false): Response
    {
        $pdf = Pdf::loadView('pdf.quote', ['quote' => $quote]);

        $filename = $this->getQuoteFilename($quote);

        return $inline ? $pdf->stream($filename) : $pdf->download($filename);
    }

    public function generateInvoicePdf(Quote $quote, bool $inline = false): Response
    {
        if (! $quote->isInvoice()) {
            abort(400, 'Ce document n\'est pas une facture.');
        }

        $pdf = Pdf::loadView('pdf.quote', ['quote' => $quote]);

        $filename = $this->getInvoiceFilename($quote);

        return $inline ? $pdf->stream($filename) : $pdf->download($filename);
    }

    private function getQuoteFilename(Quote $quote): string
    {
        return sprintf('devis-%s.pdf', $quote->reference);
    }

    private function getInvoiceFilename(Quote $quote): string
    {
        return sprintf('facture-%s.pdf', $quote->reference);
    }
}
