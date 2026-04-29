<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'attraction_id' => ['required', 'integer', 'exists:attractions,id'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000', 'regex:/^[^<>]*$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'attraction_id.required' => 'Attraction is required.',
            'attraction_id.exists' => 'Invalid attraction selected.',

            'rating.required' => 'Please select a rating.',
            'rating.between' => 'Rating must be between 1 and 5 stars.',

            'comment.max' => 'Comment must not exceed 2000 characters.',
            'comment.regex' => 'Comment contains invalid characters.',
        ];
    }
}