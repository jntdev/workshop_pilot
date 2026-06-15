<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:manual_in,manual_out'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price_ht' => ['nullable', 'integer', 'min:0'],
            'note' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Le type de mouvement est obligatoire.',
            'type.in' => 'Seules les entrées et sorties manuelles sont autorisées.',
            'quantity.required' => 'La quantité est obligatoire.',
            'quantity.min' => 'La quantité doit être au moins 1.',
        ];
    }
}
