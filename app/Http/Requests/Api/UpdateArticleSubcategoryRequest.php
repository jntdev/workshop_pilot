<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateArticleSubcategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');
        $categoryId = $this->input('article_category_id');

        return [
            'article_category_id' => ['sometimes', 'integer', 'exists:article_categories,id'],
            'name' => [
                'sometimes',
                'string',
                'max:100',
                Rule::unique('article_subcategories')
                    ->where('article_category_id', $categoryId)
                    ->ignore($id),
            ],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Cette sous-catégorie existe déjà dans cette catégorie.',
        ];
    }
}
