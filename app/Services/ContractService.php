<?php

namespace App\Services;

use App\Mail\ContractSignedMail;
use App\Models\LocationContract;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ContractService
{
    public function __construct(private ContractPdfService $pdfService) {}

    public function generate(
        Reservation $reservation,
        array $accessories,
        int $cautionAmount,
        string $returnTimeText,
        string $operatorName,
    ): LocationContract {
        $reservation->load('client');

        $contractData = [
            'client_name' => $reservation->client ? "{$reservation->client->prenom} {$reservation->client->nom}" : 'Client inconnu',
            'client_email' => $reservation->client?->email,
            'date_reservation' => $reservation->date_reservation->format('d/m/Y'),
            'date_retour' => $reservation->date_retour->format('d/m/Y'),
            'return_time_text' => $returnTimeText,
            'accessories' => $accessories,
            'caution_amount' => $cautionAmount,
            'operator_name' => $operatorName,
            'reservation_id' => $reservation->id,
        ];

        return DB::transaction(function () use ($reservation, $accessories, $cautionAmount, $returnTimeText, $operatorName, $contractData) {
            return LocationContract::create([
                'reservation_id' => $reservation->id,
                'token' => Str::uuid()->toString(),
                'accessories' => $accessories,
                'caution_amount' => $cautionAmount,
                'return_time_text' => $returnTimeText,
                'operator_name' => $operatorName,
                'contract_data' => $contractData,
                'expires_at' => now()->addHours(24),
            ]);
        });
    }

    public function sign(LocationContract $contract, string $signerName, string $signatureBase64): void
    {
        if ($contract->isSigned()) {
            abort(409, 'Ce contrat a déjà été signé.');
        }

        if ($contract->isExpired()) {
            abort(410, 'Ce lien a expiré.');
        }

        DB::transaction(function () use ($contract, $signerName, $signatureBase64) {
            $contract->update([
                'signed_at' => now(),
                'signer_name' => $signerName,
                'signature_image' => $signatureBase64,
            ]);

            $pdfPath = $this->pdfService->generateAndStore($contract);

            $contract->update(['pdf_path' => $pdfPath]);

            $contract->reservation()->update(['contract_signed_at' => now()]);
        });

        $email = $contract->contract_data['client_email'] ?? null;

        if ($email) {
            Mail::to($email)->send(new ContractSignedMail($contract));
        }
    }
}
