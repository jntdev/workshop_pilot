<?php

namespace App\Http\Requests;

use App\Enums\VisitsPeriod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class WebsiteVisitsRequest extends FormRequest
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
            'period' => ['nullable', Rule::enum(VisitsPeriod::class)],
            'marque' => ['nullable', 'string', 'max:40'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'period.'.Enum::class => 'La période demandée est invalide.',
            'marque.string' => 'La marque doit être une chaîne de caractères.',
            'marque.max' => 'La marque ne peut pas dépasser 40 caractères.',
        ];
    }

    public function period(): VisitsPeriod
    {
        return $this->enum('period', VisitsPeriod::class) ?? VisitsPeriod::Last30Days;
    }

    public function marque(): ?string
    {
        return $this->validated('marque');
    }
}
