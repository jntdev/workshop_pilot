<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LocationContract;
use App\Models\Reservation;
use App\Services\ContractPdfService;
use App\Services\ContractService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ContractController extends Controller
{
    public function __construct(
        private ContractService $contractService,
        private ContractPdfService $pdfService,
    ) {}

    public function generate(Request $request, Reservation $reservation): JsonResponse
    {
        $validated = $request->validate([
            'accessories' => ['required', 'array'],
            'caution_amount' => ['required', 'integer', 'min:0'],
            'return_time_text' => ['required', 'string', 'max:100'],
            'operator_name' => ['required', 'string', 'in:Nicolas,Jonathan'],
            'client_email' => ['nullable', 'email'],
        ]);

        if (! empty($validated['client_email']) && $reservation->client) {
            $reservation->client->update(['email' => $validated['client_email']]);
        }

        $contract = $this->contractService->generate(
            reservation: $reservation,
            accessories: $validated['accessories'],
            cautionAmount: $validated['caution_amount'],
            returnTimeText: $validated['return_time_text'],
            operatorName: $validated['operator_name'],
        );

        return response()->json([
            'contract' => $this->formatContract($contract),
            'url' => url("/location/contrat/{$contract->token}"),
        ], 201);
    }

    public function status(Reservation $reservation): JsonResponse
    {
        $contract = $reservation->latestContract;

        if (! $contract) {
            return response()->json(['contract' => null]);
        }

        return response()->json(['contract' => $this->formatContract($contract)]);
    }

    public function sign(Request $request, string $token): JsonResponse
    {
        $contract = LocationContract::where('token', $token)->firstOrFail();

        $validated = $request->validate([
            'signer_name' => ['required', 'string', 'max:255'],
            'signature_image' => ['required', 'string'],
        ]);

        $this->contractService->sign(
            contract: $contract,
            signerName: $validated['signer_name'],
            signatureBase64: $validated['signature_image'],
        );

        return response()->json(['signed_at' => $contract->fresh()->signed_at]);
    }

    public function downloadPdf(string $token): Response
    {
        $contract = LocationContract::where('token', $token)->firstOrFail();

        if (! $contract->isSigned()) {
            abort(403, 'Le contrat n\'est pas encore signé.');
        }

        return $this->pdfService->download($contract);
    }

    /** @return array<string, mixed> */
    private function formatContract(LocationContract $contract): array
    {
        return [
            'id' => $contract->id,
            'token' => $contract->token,
            'operator_name' => $contract->operator_name,
            'caution_amount' => $contract->caution_amount,
            'return_time_text' => $contract->return_time_text,
            'accessories' => $contract->accessories,
            'signed_at' => $contract->signed_at?->toIso8601String(),
            'signer_name' => $contract->signer_name,
            'expires_at' => $contract->expires_at->toIso8601String(),
            'is_pending' => $contract->isPending(),
            'is_expired' => $contract->isExpired(),
            'is_signed' => $contract->isSigned(),
        ];
    }
}
