<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleLineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'article_id' => ['nullable', 'integer', 'exists:articles,id'],
            'designation' => ['required_without:article_id', 'string', 'max:255'],
            'purchase_price_ht' => ['required_without:article_id', 'integer', 'min:0'],
            'unit_price_ttc' => ['required_without:article_id', 'integer', 'min:0'],
            'quantity' => ['nullable', 'numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        return [
            'designation.required_without' => 'La désignation est obligatoire pour une ligne libre.',
            'purchase_price_ht.required_without' => 'Le prix d\'achat est obligatoire pour une ligne libre.',
            'unit_price_ttc.required_without' => 'Le prix de vente est obligatoire pour une ligne libre.',
        ];
    }
}
