<?php

namespace App\Http\Requests;

use App\Enums\WorkMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMessageRequest extends FormRequest
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
            'author_mode' => ['required', Rule::enum(WorkMode::class)],
            'recipient_mode' => ['nullable', Rule::enum(WorkMode::class)],
            'category' => ['required', Rule::in(['accueil', 'atelier', 'location', 'autre'])],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'content' => ['required', 'string', 'min:1'],
        ];
    }
}
