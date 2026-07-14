<?php

namespace App\Http\Controllers;

use App\Models\LocationContract;
use App\Services\ContractPdfService;
use App\Services\ContractService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ContractPublicController extends Controller
{
    public function __construct(
        private ContractService $contractService,
        private ContractPdfService $pdfService,
    ) {}

    public function show(string $token): View
    {
        $contract = LocationContract::where('token', $token)->firstOrFail();

        $state = match (true) {
            $contract->isSigned() => 'signed',
            $contract->isExpired() => 'expired',
            default => 'pending',
        };

        return view('contract.show', compact('contract', 'state'));
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

        return response()->json(['success' => true]);
    }

    public function pdf(string $token): Response
    {
        $contract = LocationContract::where('token', $token)->firstOrFail();

        if (! $contract->isSigned()) {
            abort(403, 'Le contrat n\'est pas encore signé.');
        }

        return $this->pdfService->download($contract);
    }
}
