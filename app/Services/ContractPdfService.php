<?php

namespace App\Services;

use App\Models\LocationContract;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ContractPdfService
{
    public function generateAndStore(LocationContract $contract): string
    {
        $pdf = Pdf::loadView('pdf.location-contract', ['contract' => $contract]);
        $pdf->setPaper('A4', 'portrait');

        $path = "contracts/{$contract->token}.pdf";
        Storage::put($path, $pdf->output());

        return $path;
    }

    public function stream(LocationContract $contract): Response
    {
        $pdf = Pdf::loadView('pdf.location-contract', ['contract' => $contract]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream("contrat-location-{$contract->reservation_id}.pdf");
    }

    public function download(LocationContract $contract): Response
    {
        $pdf = Pdf::loadView('pdf.location-contract', ['contract' => $contract]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("contrat-location-{$contract->reservation_id}.pdf");
    }
}
