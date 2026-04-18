<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreationRequestOffre extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
       return [
    'titre' => 'required|string|min:4',
    'description' => 'nullable|string',
    'localisation' => 'required|string',
    'type' => 'nullable|string|in:CDI,CDD,stage',
    'actif' => 'boolean'
];
    }
}
