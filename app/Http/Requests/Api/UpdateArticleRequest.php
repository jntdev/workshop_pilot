<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'article_subcategory_id' => ['nullable', 'integer', 'exists:article_subcategories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'reference' => ['sometimes', 'string', 'max:100', "unique:articles,reference,{$id}"],
            'designation' => ['sometimes', 'string', 'max:255'],
            'purchase_price_ht' => ['sometimes', 'integer', 'min:0'],
            'sale_price_ht' => ['sometimes', 'integer', 'min:0'],
            'tva_rate' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'unit' => ['sometimes', 'string', 'max:50'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'reference.unique' => 'Cette référence est déjà utilisée.',
        ];
    }
}
