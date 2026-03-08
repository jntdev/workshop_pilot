<?php

namespace App\Http\Requests;

use App\Enums\WorkMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReplyRequest extends FormRequest
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
            'content' => ['required', 'string', 'min:1'],
        ];
    }
}
