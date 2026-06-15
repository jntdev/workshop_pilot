<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreArticleSubcategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'article_category_id' => ['required', 'integer', 'exists:article_categories,id'],
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('article_subcategories')->where('article_category_id', $this->input('article_category_id')),
            ],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'article_category_id.required' => 'La catégorie est obligatoire.',
            'article_category_id.exists' => 'Cette catégorie n\'existe pas.',
            'name.required' => 'Le nom de la sous-catégorie est obligatoire.',
            'name.unique' => 'Cette sous-catégorie existe déjà dans cette catégorie.',
        ];
    }
}
