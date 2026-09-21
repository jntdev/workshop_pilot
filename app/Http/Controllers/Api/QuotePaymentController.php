<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuotePaymentRequest;
use App\Models\Quote;
use App\Models\QuotePayment;
use Illuminate\Http\JsonResponse;

class QuotePaymentController extends Controller
{
    public function index(Quote $quote): JsonResponse
    {
        return response()->json(
            $quote->payments->map(fn (QuotePayment $payment) => $this->formatPayment($payment))
        );
    }

    public function store(StoreQuotePaymentRequest $request, Quote $quote): JsonResponse
    {
        $validated = $request->validated();

        $payment = $quote->payments()->create([
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'paid_at' => $validated['paid_at'],
            'note' => $validated['note'] ?? null,
        ]);

        $quote->refresh();
        $quote->recalculatePaidAt();

        return response()->json([
            'payment' => $this->formatPayment($payment),
            'total_paid' => $quote->totalPaid(),
            'paid_at' => $quote->fresh()->paid_at?->format('Y-m-d'),
        ], 201);
    }

    public function destroy(QuotePayment $payment): JsonResponse
    {
        $quote = $payment->quote;
        $payment->delete();

        $quote->refresh();
        $quote->recalculatePaidAt();

        return response()->json([
            'total_paid' => $quote->totalPaid(),
            'paid_at' => $quote->fresh()->paid_at?->format('Y-m-d'),
        ]);
    }

    protected function formatPayment(QuotePayment $payment): array
    {
        return [
            'id' => $payment->id,
            'quote_id' => $payment->quote_id,
            'amount' => (float) $payment->amount,
            'method' => $payment->method,
            'paid_at' => $payment->paid_at->format('Y-m-d\TH:i'),
            'note' => $payment->note,
        ];
    }
}
