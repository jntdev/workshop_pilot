<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'article_subcategory_id' => ['nullable', 'integer', 'exists:article_subcategories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'reference' => ['required', 'string', 'max:100', 'unique:articles,reference'],
            'designation' => ['required', 'string', 'max:255'],
            'purchase_price_ht' => ['required', 'integer', 'min:0'],
            'sale_price_ht' => ['required', 'integer', 'min:0'],
            'tva_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'unit' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'reference.required' => 'La référence est obligatoire.',
            'reference.unique' => 'Cette référence est déjà utilisée.',
            'designation.required' => 'La désignation est obligatoire.',
            'purchase_price_ht.required' => 'Le prix d\'achat est obligatoire.',
            'sale_price_ht.required' => 'Le prix de vente est obligatoire.',
            'tva_rate.required' => 'Le taux de TVA est obligatoire.',
        ];
    }
}
