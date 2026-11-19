<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnswerAssistantMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'answer' => ['required', 'string', 'min:1', 'max:20000'],
            'session_id' => ['nullable', 'string', 'max:255'],
            'consultations' => ['nullable', 'array', 'max:50'],
            'consultations.*' => ['string', 'max:300'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'answer.required' => 'La réponse est vide.',
        ];
    }
}
