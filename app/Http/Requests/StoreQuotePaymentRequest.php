<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuotePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'in:cb,liquide,cheque,virement,autre'],
            'paid_at' => ['required', 'date'],
            'note' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Le montant du paiement est obligatoire.',
            'amount.numeric' => 'Le montant du paiement doit être un nombre.',
            'amount.min' => 'Le montant du paiement doit être supérieur à 0.',
            'method.required' => 'Le mode de paiement est obligatoire.',
            'method.in' => 'Le mode de paiement doit être : CB, Espèces, Chèque, Virement ou Autre.',
            'paid_at.required' => 'La date du paiement est obligatoire.',
            'paid_at.date' => 'La date du paiement doit être une date valide.',
        ];
    }
}
