<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFavoriteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'attraction_id' => ['required', 'integer', 'exists:attractions,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'attraction_id.required' => 'Attraction is required.',
            'attraction_id.exists' => 'This attraction does not exist.',
        ];
    }
}