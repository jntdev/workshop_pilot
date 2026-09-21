<?php

namespace App\Http\Requests;

use App\Enums\QuoteCommentRecipient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreQuoteCommentRequest extends FormRequest
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
            'recipient_label' => ['required', new Enum(QuoteCommentRecipient::class)],
            'content' => ['required', 'string', 'min:1'],
        ];
    }
}
