<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryArticleRequest extends FormRequest
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
            'barcode' => ['required', 'string', 'max:64', 'unique:articles,reference'],
            'designation' => ['required', 'string', 'max:255'],
            'article_subcategory_id' => ['required', 'integer', 'exists:article_subcategories,id'],
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'purchase_price_ht' => ['required', 'integer', 'min:0'],
            'sale_price_ttc' => ['required', 'integer', 'min:0'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'image_url' => ['required', 'string', 'max:500', 'starts_with:/storage/articles/'],
            'attributes' => ['sometimes', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'barcode.required' => 'Le code-barres scanné est obligatoire.',
            'barcode.unique' => 'Un article existe déjà avec ce code-barres.',
            'designation.required' => 'La désignation est obligatoire.',
            'article_subcategory_id.required' => 'La sous-catégorie est obligatoire.',
            'brand_id.required' => 'La marque est obligatoire.',
            'purchase_price_ht.required' => 'Le prix d\'achat est obligatoire.',
            'sale_price_ttc.required' => 'Le prix de vente est obligatoire.',
            'quantity.required' => 'La quantité comptée est obligatoire.',
            'quantity.min' => 'La quantité doit être au moins 0,01.',
            'image_url.required' => 'Une photo est obligatoire.',
            'image_url.starts_with' => 'La photo doit provenir de l\'upload dédié.',
        ];
    }
}
