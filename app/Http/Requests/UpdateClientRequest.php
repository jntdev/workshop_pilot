<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
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
        $clientId = $this->route('id');

        return [
            'prenom' => ['required', 'string', 'max:255'],
            'nom' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', "unique:clients,email,{$clientId}"],
            'adresse' => ['nullable', 'string'],
            'origine_contact' => ['nullable', 'string', 'max:255'],
            'commentaires' => ['nullable', 'string'],
            'avantage_type' => ['required', 'in:aucun,pourcentage,montant'],
            'avantage_valeur' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'avantage_expiration' => ['nullable', 'date'],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'prenom.required' => 'Le prénom est obligatoire.',
            'nom.required' => 'Le nom est obligatoire.',
            'telephone.required' => 'Le téléphone est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'avantage_type.in' => 'Le type d\'avantage doit être : aucun, pourcentage ou montant.',
            'avantage_valeur.numeric' => 'La valeur de l\'avantage doit être un nombre.',
            'avantage_valeur.min' => 'La valeur de l\'avantage doit être positive.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $avantageType = $this->input('avantage_type');
            $avantageValeur = $this->input('avantage_valeur');

            if ($avantageType === 'pourcentage' && ($avantageValeur <= 0 || $avantageValeur > 100)) {
                $validator->errors()->add('avantage_valeur', 'Pour un pourcentage, la valeur doit être entre 0 et 100.');
            }

            if ($avantageType === 'montant' && $avantageValeur <= 0) {
                $validator->errors()->add('avantage_valeur', 'Pour un montant, la valeur doit être supérieure à 0.');
            }

            if ($avantageType === 'aucun' && $avantageValeur != 0) {
                $validator->errors()->add('avantage_valeur', 'Pour aucun avantage, la valeur doit être 0.');
            }
        });
    }
}
